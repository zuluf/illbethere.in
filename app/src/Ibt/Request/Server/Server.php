<?php

namespace Ibt\Request;

/**
 *	Class Server
 *
 *  notes: https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet
 */
class Server {

	/**
	 * Static server object
	 *
	 * @var object
	 */
	private static $_server;

	/**
	 * Returns current server object
	 *
	 * @return object
	 */
	public static function get() {
		if ( ! empty ( static::$_server ) ) {
			return static::$_server;
		}

		return static::parse();
	}

	/**
	 * Parses server data to static variable
	 *
	 * @return object
	 */
	private static function parse() {

		$server = (object) $_SERVER;

		static::$_server = (object) array (
			'http_host' => null,
			'http_user_agent' => '',
			'server_port' => null,
			'request_uri' => '/',
			'remote_addr' => null,
			'content_type' => '',
			'request_method' => 'GET',
			'query_string' => '',
		);

		foreach (static::$_server as $key => $value) {
			if ( isset ( $server->{strtoupper($key) } ) ) {
				static::$_server->{$key} = $server->{ strtoupper($key) };
			}
		}

		return static::$_server;
	}
}