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

		$flickr = parent::get( $where );

		if ( ! empty ( $flickr ) ) {
			foreach ( $flickr as & $value ) {
				$value->photos = array_values((array) static::decode( $value->photos ) );
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
				'photos' => static::encode( $data->photos )
			);

			return parent::insert ( $insert );
		}

		return false;
	}
}