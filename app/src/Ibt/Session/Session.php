<?php

namespace Ibt;

/**
 *  Session handler class
 *
 *  Notes: https://www.owasp.org/index.php/Session_Management_Cheat_Sheet
 */

class Session {

	public static function start () {
		$params = session_get_cookie_params();
		$params['secure'] = __secure__;
		$params['httponly'] = false;

		session_name('ibt' . time());
		session_set_cookie_params( $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		session_start();
	}

	public static function destroy () {
		if ( session_id() ) {
			session_destroy();
		}
	}
}