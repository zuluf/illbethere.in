<?php

namespace Ibt\Models;

use Ibt\Models;
use Ibt\Models\Settings;

/**
 *	Class Ibt\Models\Twitter
 *
 */
class Twitter extends Models {

	/**
	 * Twitter model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_location_tweets';

	/**
	 * Twitter model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'tweets_id';

	/**
	 * Static instance property for holding the static model instance
	 *
	 * @var \Ibt\Models\Twitter
	 */
	protected static $_instance;

	/**
	 * Returns current active bearer token from the app settings
	 *
	 * @return string
	 */
	public static function getBearerToken () {
		return Settings::get( 'twitter_bearer' );
	}

	/**
	 * Returns current active bearer token from the app settings
	 *
	 * @return string
	 */
	public static function saveBearerToken ( $bearer = "" ) {
		return Settings::add( 'twitter_bearer', $bearer );
	}

	/**
	 * Calls the parent get method and parses the result row set
	 *
	 * @param  array 	$where
	 * @param  array 	$unique
	 * @return array|object
	 */
	public static function get ( $where = array(), $unique = false ) {

		$tweets = parent::get( $where );

		if ( ! empty ( $tweets ) ) {
			foreach ( $tweets as & $value ) {
				$value->tweets = array_values((array) static::decode( $value->tweets ) );
			}

			return $unique ? array_shift( $tweets ) : $tweets;
		}

		return false;
	}

	/**
	 * Prepares the given data for the database insert; Data should contain column_name => value properties
	 *
	 * @param  array 		$data
	 * @return object|bool 	Returns false on failure
	 */
	public static function insert ( $data = false ) {

		if ( ! empty ( $data ) ) {
			$insert = array (
				'location_id' => (int) $data->location_id,
				'tweets' => static::encode( $data->tweets )
			);

			return parent::insert ( $insert );
		}

		return false;
	}
}