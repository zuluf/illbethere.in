<?php

namespace Api;

use \Api\Config;
use \Api\Response;

/**
 *	Class Api\Init
 *
 */
class Init {

	/**
	 * Unix timestamp start of the request
	 *
	 * @var int
	 */
	public static $_start;

	/**
	 * Loads the config, starts the request parse and registers the shutdown function
	 *
	 * @return void
	 */
	public static function api () {

		static::$_start = microtime();

		Config::load();

		Response::load();

		register_shutdown_function( function () {
			return Response::fire( static::$_start );
		});
	}
}