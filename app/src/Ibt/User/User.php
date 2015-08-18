<?php

namespace Ibt;

use \Ibt\Locale;
use \Ibt\Models\Users;
use \Ibt\Helpers\Crypt;

/**
 *	Class IBT User
 */
class User {

	public static function load () {
		Locale::load( 'en-US' );
	}

	public static function get () {
		$defaults = array (
			'user_id' => null,
			'name' => ''
		);

		return $defaults;
	}
}