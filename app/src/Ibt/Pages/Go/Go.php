<?php

namespace Ibt\Pages;

use \Ibt\Pages;
use \Ibt\Errors;
use \Ibt\Templates;
use \Ibt\Models\Locations;

/**
 *	Class \Ibt\Pages\Go
 *
 */
class Go extends Pages {

	/**
	 * Page template file path
	 *
	 * @var string
	 */
	protected static $_template = "pages/go/location";

	/**
	 * Prepares template params and renders the page template file to html
	 *
	 * @return string
	 */
	public static function render () {

		$location = array (
			'location_id' => null
		);

		if ( ! empty( static::$_params ) ) {
			$location = Locations::get( array('location_id' => array_shift( static::$_params ) ), true );

			if ( ! empty( $location ) ) {
				$location->rank = $location->rank(1);
			}
		}

		static::$_data = (array) $location;

		return parent::render();
	}
}