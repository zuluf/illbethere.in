<?php

namespace Api\Resource;

use \Ibt\Models\Users;
use \Ibt\Helpers\Crypt;
use \Ibt\User;
use \Ibt\Request;
use \Ibt\Session;

class Ping {

	public static function me ( $query = false, $data = array() ) {
		return Crypt::verify($data['pass'], $data['hash']);
	}

	public static function hs ( $query = false, $data = array() ) {
		return Crypt::hash( isset($data['string']) ? $data['string'] : '' );
	}

	public static function user ( $query = false, $data = array() ) {
		return User::get();
	}

	public static function ext () {
		return false; // get_loaded_extensions();
	}

	public static function rq () {
		return false; // Request::get();
	}

	public static function session ( $query = false, $data = array() ) {
		// Session::destroy();
		Session::start();
	}

	public static function destroy ( $query = false, $data = array() ) {
		Session::destroy();
	}

	public static function url ( $query = false, $data = array() ) {
		return base64_decode('IjxzY3JpcHQgdHlwZT1cInRleHRcL2phdmFzY3JpcHRcIj5hbGVydCgxKTs8XC9zY3JpcHQ Ig==' );
	}
}