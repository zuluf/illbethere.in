<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Panoramio
 *
 */
class Panoramio extends Models {

	/**
	 * Panoramio model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_panoramio';

	/**
	 * Panoramio model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'panoramio_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Panoramio
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

		$panoramio = parent::get( $where );

		if ( ! empty ( $panoramio ) ) {
			foreach ( $panoramio as & $value ) {
				$value->photos = array_values((array) static::decode( $value->photos ) );
			}

			return $unique ? array_shift( $panoramio ) : $panoramio;
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