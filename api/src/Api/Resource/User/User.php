<?php

namespace Api\Resource;

use \Ibt\User as Users;

class User {

	public static function get () {
		return Users::get();
	}
}