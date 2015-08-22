<?php

namespace Api;

use \Ibt\Config as IbtConfig;

/**
 * Class Api\Config
 *
 */
class Config {

	/**
	 * Loads default app config object
	 *
	 * @return object
	 */
	public static function load () {
		return IbtConfig::load();
	}

	/**
	 * Returns app config object or a selected item from a $config param
	 *
	 * @param  string  $config
	 * @return mixed
	 */
	public static function get ( $config = false ) {
		return IbtConfig::get( $config );
	}
}