<?php

namespace Api;

use \Ibt\Request;
use \Ibt\Errors;
use \Ibt\Events;
use \Ibt\Helpers;

/**
 * Class Api\Response
 *
 */
class Response {

	/**
	 * Static response data holder
	 *
	 * @var mixed
	 */
	private static $_response;

	/**
	 * Loads the request instance, parses the path namespace and calls the path's resource method
	 *
	 * @return void
	 */
	public static function load () {

		$request = Request::get();

		$request->class = Helpers::camelClass( $request->resource, 'Api\Resource' );

		$action = array_shift( $request->params );

		if ( ! Errors::hasErrors() ) {
			if ($request) {
				static::$_response = call_user_func ( array ( $request->class , $action ), array_shift ( $request->params ), $request->data );
			} else {
				Errors::set('router', 'Resource not found' );
			}
		}
	}

	/**
	 * Ends the request and outputs the json result of the resource
	 *
	 * @return void
	 */
	public static function fire ( $startTime = 0 ) {

		header('Content-Type: application/json');

		$response = array(
			'error' => Errors::get(),
			'data' => static::$_response,
			'time' => ( microtime() - $startTime )
		);

		Events::fire ( 'shutdown' );

		die( json_encode($response) );
	}
}