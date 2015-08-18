<?php

namespace Ibt;

use \Ibt\Config;
use \Ibt\Errors;
use \Ibt\Request\Server;

/**
 *	Class Request
 *
 */
class Request {

	/**
	 * Static request object
	 *
	 * @var object
	 */
	private static $_request;

	/**
	 * Default page controller
	 *
	 * @var object
	 */
	private static $_defaultPage = 'home';

	/**
	 * Default request params
	 *
	 * @var object
	 */
	private static $_defaultParams = array();

	/**
	 * Allowed http request methods
	 *
	 * @var array
	 */
	private static $_allowedMethods = array(
		'HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'
	);

	/**
	 * Application subpaths
	 *
	 * @var array
	 */
	private static $_subPaths = array(
		'/api'
	);

	/**
	 * Returns current request object
	 *
	 * @return object
	 */
	public static function get() {

		if ( ! empty ( static::$_request ) ) {
			return static::$_request;
		}

		return static::parse();
	}

	/**
	 * Returns current request data
	 *
	 * @return object
	 */
	public static function data( $request = null ) {

		$request = ! empty( $request ) ? $request : static::get();
		$data = array();

		foreach ( array ('get', 'post', 'put', 'delete') as $method ) {
			if ( isset ( $request->{ $method } ) ) {
				$data = array_merge( $data, $request->{ $method } );
			}
		}

		return $data;
	}

	/**
	 * Redirects browser to a desired app path
	 *
	 * @return bool false if headers already sent
	 */
	public static function redirect ( $path = '/' ) {

		$host = Config::get( 'host' );

		$path = urldecode( $path );

		if ( ! headers_sent() ) {
			header('Location: ' . $host->full_url . trim( $path, '/' ) );
			die();
		}

		return false;
	}

	/**
	 * Parses request data to static variable
	 *
	 * @return object
	 */
	private static function parse () {

		$config = Config::get( 'host' );
		$server = Server::get();

		/**
		 * Redirect request if somebody is trying to access a domain with parameters not matching the app config
		 * Also, redirect if method suplied is not supported by the app
		 */
		if ( strtolower( $server->http_host ) !== strtolower( $config->uri ) ||
			 (int) $server->server_port !== (int) $config->port ||
			 ! in_array( $server->request_method, static::$_allowedMethods ) ) {

			Errors::log('Unauthorized', $_SERVER);
			static::redirect();
		}

		$request = new \stdClass;

		if ( ! empty ( $server->query_string ) ) {
			parse_str( $server->query_string, $request->get );
		}

		if ( $server->request_method === 'POST' ) {
			$request->post = $_POST;
		}

		if ( in_array( $server->request_method, array ( 'PUT', 'DELETE' ) ) && strpos( $server->content_type, 'application/x-www-form-urlencoded' ) === 0 ) {
			parse_str( file_get_contents( 'php://input' ), $request->{ strtolower( $server->request_method ) } );
		}

		$request->method = $server->request_method;
		$request->type = $server->content_type;
		$request->data = static::data( $request );
		$request->path = str_replace ( static::$_subPaths , '', $server->request_uri );
		$request->resource = static::$_defaultPage;
		$request->params = static::$_defaultParams;

		return ( static::$_request = static::parseRequestPath( $request ) );
	}

	/**
	 * Parses request_uri to request path params
	 *
	 * @return object
	 */
	public static function parseRequestPath ( $request = object ) {
		if ( isset ( $request->path ) && ! empty( $request->path ) ) {

			$url = parse_url( $request->path );

			if ( !empty( $url ) && isset( $url[ 'path' ] ) &&
				 !empty( $url[ 'path' ] ) && strlen( str_replace( '/', '', $url[ 'path' ] ) ) ) {
				$url = explode('/', trim( $url[ 'path' ], '/') );

				if ( ! empty( $url ) ) {
					$request->resource = array_shift( $url );
					$request->params = $url;
				}
			}
		}

		return $request;
	}
}