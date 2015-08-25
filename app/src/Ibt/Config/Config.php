<?php

namespace Ibt;

use \Ibt\Errors;

/**
 *	Class Config
 *
 */
class Config {

	/**
	 * Static config object
	 *
	 * @var object
	 */
	private static $_config;

	/**
	 * Returns app config object or a selected item from a $config param
	 *
	 * @param  string  $config
	 * @return mixed
	 */
	public static function get ( $config = false ) {

		$_config = static::load();

		if ( ! empty( $config ) && isset( $_config->{ $config } ) ) {
			return $_config->{ $config };
		}

		return $_config;
	}

	/**
	 * Loads config object from the config file
	 * Config file permissions should be set to 0400, fallback: 0440, 0600, 0640
	 *
	 * @return object
	 */
	public static function load () {
		if ( empty( static::$_config ) ) {

			$config = array();

			if ( is_file( __DIR__ . DIRECTORY_SEPARATOR . 'files/file.php' ) ) {
				$config = include __DIR__ . DIRECTORY_SEPARATOR . 'files/file.php'; // dont forget to set permissions to 0400
			} else {
				Errors::log('config', 'Your config file is missing!');
				Errors::set('system', 'Something went wrong. <br /> Please refresh the page and if the issue continues, find a better website to waste your time :)');
			}

			return static::parse( $config );
		}

		return static::$_config;
	}

	/**
	 * Parse config, and merge with defaults
	 *
	 * @param  mixed  $config
	 * @return object
	 */
	private static function parse ( $config ) {

		$defaults = array (
			'db' => array (
				'host' => 'localhost',
				'port' => 3306,
				'user' => '',
				'pass' => '',
				'base' => 'illbethere'
			),

			'host' => array (
				'uri' => 'localhost',
				'secure' => false
			),

			'environment' => 'development',

			'errors' => array (
				'display' => 0 // use error_reporting named constants (E_ALL, E_WARNING, etc)
			),

			'globals' => array ()
		);

		if ( ! empty( $config ) ) {
			$config = (object) array_merge( $defaults, (array) $config );
		} else {
			$config = (object) $defaults;
		}

		$config->db = (object) $config->db;
		$config->host = (object) $config->host;
		$config->errors = (object) $config->errors;
		$config->globals = (object) $config->globals;
		$config->host->secure = ! $config->host->secure ? false : true;

		if ( ! isset( $config->host->port ) || ! in_array( $config->host->port, array ( 80, 443 ), true ) ) {
			$config->host->port = $config->host->secure ? 443 : 80;
		}

		if ( ! isset( $config->host->scheme ) ) {
			$config->host->scheme = $config->host->secure ? 'https:' : 'http:';
		}

		if ( ! empty( $config->errors->display ) ) {
			error_reporting( (int) $config->errors->display );
		} else {
			error_reporting( 0 );
		}

		$config->host->full_url = rtrim( $config->host->scheme . '//' . $config->host->uri ) . '/';

		static::defineGlobals( $config );

		return static::$_config = $config;
	}

	/**
	 * Defines global constants used throughout the app
	 *
	 * @param  mixed  $config
	 * @return void
	 */
	private static function defineGlobals ( $config = false ) {

		if ( isset ( $config->globals ) && ! empty ( $config->globals ) ) {
			foreach ( $config->globals as $_key => $_value ) {
				if ( ! defined ( $_key) ) {
					define ( $_key, $_value );
				}
			}
		}

		if( ! defined( '__host__' ) ) {
			define( '__host__', $config->host->full_url );
		}

		if( ! defined( '__assets__' ) ) {
			define( '__assets__', __host__ . 'app/assets/' );
		}

		if( ! defined( '__root__' ) ) {
			define( '__root__', rtrim( dirname( dirname( dirname( dirname( __DIR__ ) ) ) ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR );
		}

		if( ! defined( '__app__' ) ) {
			define( '__app__', __root__ . 'app/' );
		}

		if( ! defined( '__assets_dir__' ) ) {
			define( '__assets_dir__', __app__ . 'assets/' );
		}

		if( ! defined( '__templates__' ) ) {
			define( '__templates__', __assets_dir__ . 'tpl/' );
		}

		if( ! defined( '__scripts__' ) ) {
			define( '__scripts__', __assets_dir__ . 'js/' );
		}
	}
}