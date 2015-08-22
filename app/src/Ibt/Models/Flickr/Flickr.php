<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Flickr
 *
 */
class Flickr extends Models {

	/**
	 * Flickr model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_flickr';

	/**
	 * Flickr model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'flickr_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Flickr
	 */
	protected static $_instance;

	/**
	 * Calls the parent get method and parses the result row set
	 *
	 * @param  array 	$where
	 * @param  array 	$unique
	 * @return array|object
	 */
	public static function get ( $where = array(), $unique = false ) {

		$flickr = parent::get( $where, $unique );

		if ( ! empty ( $flickr ) ) {
			$flickr = ! is_array ( $flickr ) ? array ( $flickr ) : $flickr;

			foreach ( $flickr as & $value ) {
				$value->photos = array_values((array) json_decode( base64_decode( $value->photos ) ) );
			}

			return $unique ? array_shift( $flickr ) : $flickr;
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
				'photos' => base64_encode( json_encode( $data->photos, JSON_UNESCAPED_UNICODE ) )
			);

			$flickr_id = parent::insert ( $insert );

			if ( ! empty ( $flickr_id ) ) {
				return static::get ( array ( 'flickr_id' => $flickr_id ), true );
			}
		}

		return false;
	}
}