<?php

namespace Ibt;

use \Exception;

/**
 *	Class Errors
 *
 */
class Errors {

	/**
	 * Static runtime errors collection
	 *
	 * @var array
	 */
	protected static $_errors = array();

	/**
	 * Log file name
	 *
	 * @var string
	 */
	protected static $_logFile = 'log';

	/**
	 * Returns current errors collection, or a specific type errors for the given type
	 *
	 * @param  string  $type
	 * @return array
	 */
	public static function get ( $type = false ) {

		if ( is_string( $type ) && ! empty( $type ) ) {
			return isset ( static::$_errors [ $type ] ) ? static::$_errors[ $type ] : array ();
		}

		return static::$_errors;
	}

	/**
	 * Checks if there are error messages in the current collection
	 *
	 * @return bool
	 */
	public static function hasErrors () {
		return ! empty( static::$_errors );
	}

	/**
	 * Adds the error to the current runtime collection
	 *
	 * @param  string $type    defines the overall system error scope
	 * @param  string $mesage  error message text
	 * @param  boolean $log    if true, error will be logged to the log file with the timestamp
	 * @return void
	 */
	public static function set( $type = "", $message = "", $log = false ){
		if ( !empty($type) ) {

			if ( ! isset( static::$_errors[ $type ] ) ) {
				static::$_errors[ $type ] = array ();
			}

			if ( ! in_array( $message, static::$_errors[ $type ] ) ) {
				static::$_errors[ $type ] [] = $message;

				if ( $log ) {
					static::log ( $message, $type );
				}
			}
		}
	}

	/**
	 * Logs messages to the error log file
	 *
	 * @param  mixed $mixed    value to log
	 * @param  string $label
	 * @return bool
	 */
	public static function log ( $mixed = "", $label = "" ) {

		$label = ! empty( $label ) ? microtime( true ) . ' ' . $label : microtime( true );

		$logFile = fopen( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . static::$_logFile, 'a' );

		$bytes = fwrite( $logFile, $label . ': ' . print_r( $mixed, true ) . PHP_EOL );

		fclose( $logFile );

		return !! $bytes;
	}
}