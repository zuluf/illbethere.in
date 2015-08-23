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

		$panoramio = parent::get( $where, $unique );

		if ( ! empty ( $panoramio ) ) {
			$panoramio = ! is_array ( $panoramio ) ? array ( $panoramio ) : $panoramio;

			foreach ( $panoramio as & $value ) {
				$value->photos = array_values((array) json_decode( base64_decode( $value->photos ) ) );
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
				'photos' => base64_encode( json_encode( $data->photos, JSON_UNESCAPED_UNICODE ) )
			);

			$panoramio_id = parent::insert ( $insert );

			if ( ! empty ( $panoramio_id ) ) {
				return static::get ( array ( 'panoramio_id' => $panoramio_id ), true );
			}
		}

		return false;
	}
}