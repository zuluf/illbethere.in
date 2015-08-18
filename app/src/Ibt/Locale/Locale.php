<?php

namespace Ibt;

use \Ibt\Errors;

/**
 *	Class Locale
 *
 */
class Locale {

	private static $_locale = array (
		'domain' => 'ibt',
		'locale_data' => array (
			'ibt' => array (
				"" => array (
					"domain" => "ibt",
					"lang" => "en",
					"plural_forms" => "nplurals=2; plural=(n != 1);"
				)
			)
		)
	);

	private static $_domain;

	private static $_messages;

	public static function load ( $lang = "en-US" ) {

		if ( empty ( static::$_messages ) ) {
			$locale = @file_get_contents( __DIR__ . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $lang . '.json' );

			static::$_locale = !empty($locale) ? json_decode($locale, true) : static::$_locale;
			static::$_domain = static::$_locale['domain'];
			static::$_messages = static::$_locale['locale_data'][static::$_domain];

			unset (static::$_messages['']);
		}

		return static::$_locale;
	}

	public static function gettext ( $locale = "" ) {
		$translate = isset( static::$_messages[$locale] ) && is_array(static::$_messages[$locale]) ? static::$_messages[$locale] : $locale;

		if ( ! empty( $translate ) && is_array( $translate ) ) {
			return array_pop ( $translate );
		}

		return $locale;
	}
}