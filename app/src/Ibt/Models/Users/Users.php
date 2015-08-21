<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Users
 *
 */
class Users extends Models {

	/**
	 * Users model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_users';

	/**
	 * Users model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'user_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Users
	 */
	protected static $_instance;
}