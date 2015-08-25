<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Locations
 *
 */
class Locations extends Models {

	/**
	 * Locations model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_locations';

	/**
	 * Locations model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'location_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Locations
	 */
	protected static $_instance;

	/**
	 * Searches for a location by name, country and region
	 *
	 * @param  string $query
	 * @return array
	 */
	public static function find ( $query = "" ) {

		$locations = array();

		if ( empty( $query ) || ! is_string( $query ) ) {
			return $locations;
		}

		$search = static::urlencode( $query );

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

		$locations = static::fetch( $query );

		if ( ! empty( $locations ) ) {
			// remove duplicate coordinates

			$lnglats = array();
			foreach($locations as $index => $location) {

				if ( !isset($lnglats[ "" . $location->longitude . $location->latitude ])) {
					$lnglats[ "" . $location->longitude . $location->latitude ] = $location->location_id;
				} else {
					unset($locations[ $index ]);
				}
			}
		}

		return array_values( $locations );
	}

	/**
	 * Update location rank value; Accepts positive and negative integers
	 *
	 * @param  int 	$location_id
	 * @param  int 	$rank
	 * @return int|bool
	 */
	public function rank ( $rank = 0 ) {

		$rank = (int) $rank;
		if ( $rank === 0 ) {
			return false;
		}

		$rank = $this->rank + $rank;

		$this->save( array ( 'rank' => $rank ) );

		return $rank;
	}
}