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

	public static $_request;

	public static function getPage () {
		return static::$_request->page;
	}

	public static function fire () {

		$request = Request::get();

		$request->page = Helpers::camelClass( $request->resource );
		$request->class = 'Ibt\\Pages\\' . $request->page;

		if( ! class_exists( $request->class ) ) {

			$request->page = 'NotFound';
			$request->class = 'Ibt\\Pages\\NotFound';
		}

		static::$_request = $request;

		static::doLayout();
	}

	private static function register() {

		$callable = array ( static::$_request->class, 'content' );

		if ( ! is_callable( $callable ) ) {
			Errors::set('controller', 'Page controller not defined: ' . static::$_request->class, true);
		}

		call_user_func( $callable, static::$_request->params, static::$_request->data );

		Events::register( 'content', function ( $content ) {
			return static::errors( $content );
		}, array(), -1);
	}

	private static function doLayout() {

		static::register();

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
			return Layout::load(array(
				'header' => true,
				'content' => true,
				'footer' => true
			));
		}
	}

	public static function errors ( $content = "" ) {

		if ( Errors::hasErrors() ) {
			foreach ( Errors::get() as $key => $value) {
				$content .= Templates::render('widgets/messages/error', array ( 'message' => implode('<br />', ( is_array($value) ? $value : array($value) ))));
			}
		}

		return $content;
	}
}