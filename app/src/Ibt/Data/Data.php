<?php

namespace Ibt;

use \mysqli;
use \Ibt\Config;
use \Ibt\Errors;

/**
 *	Class Data
 *
 */
class Data {

	/**
	 * Database connection instance
	 *
	 * @var string
	 */
	private static $_base;

	/**
	 * Opens the database connection from the app config params, and stores the instance in the static $_base
	 *
	 * @return bool
	 */
	public static function connect () {

		if ( static::isConnected() ) {
			return true;
		}

		$config = Config::get( 'db' );

		if ( empty( $config ) ) {

			Errors::set('database', 'Could not connect to database: database config missing', true);

			return false;
		}

		$mysqli = @new \mysqli( $config->host, $config->user, $config->pass, $config->base, $config->port );

		if( isset( $mysqli->connect_error ) ) {

			Errors::set('database', 'Could not connect to database: ' . $mysqli->connect_error, true);

			return false;
		}

		Events::register( 'shutdown', function () {
			return static::shutdown();
		});

		static::$_base = $mysqli;
		static::$_base->set_charset( "utf8" );

		return true;
	}

	/**
	 * Shutdown event callback for closing the database connection
	 *
	 * @return bool
	 */
	public static function shutdown() {
		if ( ! static::isConnected() ) {
			return false;
		}

		static::$_base->kill( static::$_base->thread_id );

		return static::$_base->close();
	}

	/**
	 * Executes the mysql query on the current database connection
	 *
	 * @param  string  $query mysql query to execute
	 * @return mixed
	 */
	public static function query ( $query = '' ){

		if ( ! static::connect() ) {
			return (object) array(
				'error' => 'Could not connect to database',
				'error_list' => array ()
			);
		}

		if ( empty( $query ) ) {
			return (object) array(
				'error' => 'Can not execute empty query',
				'error_list' => array ()
			);
		}

		$result = static::$_base->query( $query );
		$return = array();

		if ( isset( static::$_base->error ) && !empty( static::$_base->error ) ) {
			return (object) array(
				'error' => static::$_base->error,
				'error_list' => static::$_base->error_list
			);
		} else {
			if ( is_object( $result ) ) {
				while($row = $result->fetch_object()){
					$return[] = $row;
				}
			}
		}

		return $return;
	}

	/**
	 * Returns the last executed query autoincrement insert id
	 *
	 * @return int
	 */
	public static function insertId () {

		if ( static::isConnected() ) {
			if ( isset( static::$_base->insert_id ) ) {
				return static::$_base->insert_id;
			}
		}

		return 0;
	}

	/**
	 * Returns the current state of the connection, true if the connection has a valid thread_id, otherwise false
	 *
	 * @return bool
	 */
	public static function isConnected () {
		if ( ! empty( static::$_base ) && isset ( static::$_base->thread_id ) && ! empty ( static::$_base->thread_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Executes mysqli_real_escape_string on the given parameter
	 *
	 * @param  string  $string
	 * @return bool
	 */
	public static function escape ( $string = "" ) {
		if ( static::connect() ) {
			return static::$_base->real_escape_string( $string );
		}

		return $string;
	}
}