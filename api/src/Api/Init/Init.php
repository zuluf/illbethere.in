<?php

namespace Api;

use \Api\Config;
use \Ibt\Request;
use \Ibt\Errors;
use \Ibt\Events;
use \Ibt\Helpers;

/**
 *	Class Init
 *
 */
class Init {

	public static $_response;

	public static $_start;

	public static function api () {

		Config::load();

		static::$_start = microtime();

		static::register();

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

	private static function register () {
		register_shutdown_function( function () {
			return static::fire();
		});
	}

	public static function fire () {

		header('Content-Type: application/json');

		$response = array(
			'error' => Errors::get(),
			'data' => static::$_response,
			'time' => ( microtime() - static::$_start )
		);

		Events::fire ( 'shutdown' );

		die( json_encode($response) );
	}
}