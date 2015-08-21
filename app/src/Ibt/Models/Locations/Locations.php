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
}