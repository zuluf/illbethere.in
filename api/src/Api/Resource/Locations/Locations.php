<?php

namespace Api\Resource;

use \Ibt\Models\Locations as Location;
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

		$locations = array();
		$lnglats = array();

		if ( !empty( $query ) && is_string( $query ) ) {

			$search = Location::urlencode( $query );

			$query = str_replace(' ', '%', $search);

			$query = "SELECT `location_id`, `name`, `country_long`, `country_short`, `longitude`, `latitude`, `region`, CONCAT (`name`, ' ', `country_long`) as `match`
					FROM `ibt_locations`
					WHERE
					   	`search` LIKE '{$query}%'
					GROUP BY `region`, `name`, `country_id`
					ORDER BY `rank` DESC, case
						when `name` = '{$search}' then 1
						when `name` = '{$search}%' then 2
						when `name` = '%{$search}%' then 3
						when `name` = '{$search}%' then 4
						when `match` = '{$search}' then 5
						when `match` LIKE '{$search}%' then 6
						when `match` like '%{$search}' then 7
						when `match` like '%{$search}%' then 8
						else 9
					end ASC, country_long ASC
					LIMIT 15";
			\Ibt\Errors::log ($query);
			$locations = Location::fetch( $query );

			foreach($locations as $index => $location) {
				if ( !isset($lnglats["" . $location->longitude . $location->latitude])) {
					$lnglats["" . $location->longitude . $location->latitude] = $location->location_id;
				} else {
					unset($locations[$index]);
				}
			}
		}

		// return array_values, json_encode will convert it into objects
		return array_values( $locations );
	}

	/**
	 * Returns the \Ibt\Models\Locations object for the given location_id or false if there is no match
	 *
	 * @return mixed
	 */
	public static function get ( $location_id = false ) {
		if ( ! empty ($location_id) ) {
			return Location::get( array('location_id' => $location_id), true );
		}

		return false;
	}
}