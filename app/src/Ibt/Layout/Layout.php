<?php

namespace Ibt;

use \Ibt\Scripts;
use \Ibt\Events;
use \Ibt\Config;

/**
 *	Class Layout
 *
 */
class Layout {

	/**
	 * Loads default layout files (content, header, footer) depending on the given $config params
	 *
	 * @param  array   $config
	 * @param  bool    $include
	 * @return string
	 */
	public static function load ( $config = array(), $include = true ) {

		$defaults = array(
			'header' => false,
			'content' => false,
			'footer' => false
		);

		$config = array_merge( $defaults, (array) $config );

		$loadDir = __DIR__ . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR;

		$load = array();

		foreach( $config as $_file => $_include ) {
			if ( $_include ) {
				$load[] = static::loadFile( $loadDir . $_file . '.php', $include );
			}
		}

		return implode('', $load);
	}

	/**
	 * Loads file by given filepath; returns the content if the $include param is false
	 *
	 * @param  string   $filepath
	 * @param  bool     $include
	 * @return content|void
	 */
	private static function loadFile ( $filepath = false, $include = true ) {

		if ( is_file ( $filepath ) ) {

			if ( $include ) {
				include ( $filepath );
			} else {
				ob_start();

				include ( $filepath );

				$load = ob_get_contents();

				ob_end_clean();

				return $load;
			}
		}
	}
}
