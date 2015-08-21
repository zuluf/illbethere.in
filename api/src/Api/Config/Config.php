<?php

namespace Api;

use \Ibt\Config as IbtConfig;

/**
 * Class Api\Config
 *
 */
class Config {

	public static function load () {
		return static::defineGlobals ( IbtConfig::load() );
	}

	private static function defineGlobals ( $config = false ) {
		return $config;
	}
}