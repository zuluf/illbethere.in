<?php

namespace Ibt;

use \DateTime;

/**
 *	Class \Ibt\Date
 *
 */
class Date extends DateTime {

	/**
	 * Static config object
	 *
	 * @var object
	 */
	private static $_instance;

	/**
	 * Supported formats
	 *
	 * @var array
	 */
	private static $_formats = array (
		'mysqli' => "Y-m-d H:i:s"
	);

	/**
	 * Returns static class instance
	 *
	 * @return string
	 */
	public static function mysqli ( $timestamp = null ) {

		$timestamp = !empty( $timestamp ) && is_numeric( $timestamp ) ? $timestamp : static::instance()->getTimestamp();

		return date( static::$_formats[ 'mysqli' ], $timestamp );
	}

	/**
	 * Returns static class instance
	 *
	 * @return \Ibt\Date
	 */
	public static function instance() {
		if ( empty( static::$_instance ) ) {
			static::$_instance = new static;
		}

		return static::$_instance;
	}
}