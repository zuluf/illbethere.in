<?php

namespace Ibt;

/**
 *	Class Locale
 *
 */
class Locale {

	/**
	 * Static default locale config
	 *
	 * @var array
	 */
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

	/**
	 * Static locale domain
	 *
	 * @var string
	 */
	private static $_domain;

	/**
	 * Static messages collection
	 *
	 * @var array
	 */
	private static $_messages;

	/**
	 * Loads locale json file, and populates static domain and messages class properties
	 *
	 * @return array
	 */
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

	/**
	 * Returns the loaded locale text for a given string, or the string if the locale message is not defined
	 *
	 * @return string
	 */
	public static function gettext ( $locale = "" ) {
		$translate = isset( static::$_messages[$locale] ) && is_array(static::$_messages[$locale]) ? static::$_messages[$locale] : $locale;

		if ( ! empty( $translate ) && is_array( $translate ) ) {
			return array_pop ( $translate );
		}

		return $locale;
	}
}