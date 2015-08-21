<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Countries
 *
 */
class Countries extends Models {

	/**
	 * Countries model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_countries';

	/**
	 * Countries model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'country_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Countries
	 */
	protected static $_instance;
}