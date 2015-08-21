<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Pages
 *
 */
class Pages extends Models {

	/**
	 * Pages model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_pages';

	/**
	 * Pages model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'page_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Pages
	 */
	protected static $_instance;
}