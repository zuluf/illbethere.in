<?php

namespace Api\Resource;

use \Ibt\Models\Locations as Model;
use \Ibt\Models\Countries;

/**
 * Class Api\Resource\Locations
 *
 */
class Locations {

	/**
	 * Finds the \Ibt\Models\Locations collection for the given query string
	 *
	 * @return array
	 */
	public static function find ( $query = false ) {
		return Model::find( $query );
	}

	/**
	 * Returns the \Ibt\Models\Locations object for the given location_id or false if there is no match
	 *
	 * @return mixed
	 */
	public static function get ( $location_id = false ) {
		if ( ! empty( $location_id ) ) {
			return Model::get( array('location_id' => $location_id), true );
		}

		return false;
	}
}