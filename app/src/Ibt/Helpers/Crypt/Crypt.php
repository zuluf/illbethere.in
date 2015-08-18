<?php

namespace Ibt\Helpers;

/**
 *	Class Crypt
 */
class Crypt {

	/**
	 * Number of iterations
	 *
	 * @var int
	 */
	protected static $_cost = 12;

	/**
	 * Hashes given string with password_hash, or a crypt fallback for php versions 5.3.0 <= 5.5.0
	 * Big thanks to https://github.com/ircmaxell/password_compat
	 *
	 * @param  string  $string
	 * @return string|bool 		hashed string or false on hash failure
	 */
	public static function hash ( $string = false ) {

		if ( empty( $string ) || ! is_string( $string ) ) {
			return false;
		}

		if ( function_exists( 'password_hash' ) ) {
			$hash = password_hash ( md5( $string ), PASSWORD_BCRYPT, array ( 'cost' => static::$_cost ) );
		} else {
			$format = sprintf("$2y$%02d$", static::$_cost);

			$salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);

			$base64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

			$bcrypt64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

			$salt = strtr( rtrim( base64_encode( $salt ), '=' ), $base64, $bcrypt64 );

			$salt = mb_substr( $salt, 0, 22, '8bit' );

			$hash = $format . $salt;

			$hash = crypt( md5( $string ), $hash );
		}

		if ( $hash && mb_strlen( $hash, '8bit' ) === 60 ) {
			return sprintf( "$2y$%02d$", static::$_cost ) . mb_substr( $hash, 7, 60, '8bit' );
		}

		return false;
	}

	/**
	 * Verifies the given $string against the given $hash with password_verify or a 5.3.0 <= 5.5.0 php versions
	 *
	 * @param  string  $string
	 * @param  string  $hash
	 * @return bool
	 */
	public static function verify ( $string = false, $hash = false) {
		if ( empty( $string ) || ! is_string( $string ) || empty( $hash ) || ! is_string( $hash ) ) {
			return false;
		}

		$hash = sprintf( "$2y$%02d$", static::$_cost ) . mb_substr( $hash, 7, 60, '8bit' );

		if ( function_exists('password_verify') ) {
			return password_verify ( md5 ( $string ), $hash );
		} else {
			$crypt = crypt( md5( $string ), $hash );
			$length = mb_strlen( $crypt, '8bit' );
			$status = null;

			for ( $i = 0; $i < $length; $i++ ) {
	            $status |= (ord($crypt[$i]) ^ ord($hash[$i]));
	        }

	        return $status === 0;
		}
	}
}