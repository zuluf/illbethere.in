<?php

namespace Ibt;

use \Ibt\Request;
use \Ibt\Events;
use \Ibt\Errors;
use \Ibt\Helpers;

/**
 *	Class Router
 *
 */
class Router {

	/**
	 * Static request instance
	 *
	 * @var object
	 */
	public static $_request;

	/**
	 * Returns the current request class name
	 *
	 * @return string
	 */
	public static function getPage () {
		return static::$_request->page;
	}

	/**
	 * Parses the current request, and triggers the response
	 *
	 * @return string
	 */
	public static function fire () {

		$request = Request::get();

		$request->page = Helpers::camelClass( $request->resource );
		$request->class = 'Ibt\\Pages\\' . $request->page;

		if( ! class_exists( $request->class ) ) {

			$request->page = 'NotFound';
			$request->class = 'Ibt\\Pages\\NotFound';
		}

		static::$_request = $request;

		static::response();
	}

	/**
	 * Triggers the cuurent request page class method; registers the error content callback
	 *
	 * @return void
	 */
	private static function render() {

		$callable = array ( static::$_request->class, 'content' );

		if ( ! is_callable( $callable ) ) {
			Errors::set('controller', 'Page controller not defined: ' . static::$_request->class, true);
		}

		call_user_func( $callable, static::$_request->params, static::$_request->data );

		Events::register( 'content', function ( $content ) {
			return static::errors( $content );
		}, array(), -1);
	}

	/**
	 * Triggers the page layout, return's it to the caller or to the browser if page is requested on ajax
	 *
	 * @return mixed
	 */
	private static function response () {

		static::render();

		if ( static::$_request->type === 'application/json' ) {

			header('Content-Type: application/json');

			die ( json_encode ( array (
				'app' => array(
					'page' => static::$_request->page
				),
				'content' => Layout::load (
					array(
						'content' => true
					),
				false)
			)));

		} else {
			return Layout::load( array(
				'header' => true,
				'content' => true,
				'footer' => true
			) );
		}
	}

	/**
	 * Renders the accumulated errors to the page content
	 *
	 * @return string
	 */
	public static function errors ( $content = "" ) {

		if ( Errors::hasErrors() ) {
			foreach ( Errors::get() as $key => $value) {
				$content .= Templates::render('widgets/messages/error', array ( 'message' => implode('<br />', ( is_array($value) ? $value : array($value) ))));
			}
		}

		return $content;
	}
}