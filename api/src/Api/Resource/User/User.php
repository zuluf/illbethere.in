<?php

namespace Api\Resource;

use \Ibt\User as Users;

/**
 * Class Api\Resource\User
 *
 */
class User {

	/**
	 * Returns the current user instance
	 *
	 * @return object
	 */
	public static function get () {
		return Users::get();
	}
}