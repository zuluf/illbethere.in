<?php

namespace Ibt;

use \DirectoryIterator;
use \Ibt\Router;
use \Ibt\User;
use \Ibt\Locale;
use \Ibt\Templates;

/**
 *	Class Scripts
 *
 */
class Scripts {

	/**
	 * Static script url's collection
	 *
	 * @var array
	 */
	protected static $_scripts  = array();

	/**
	 * Default script file extension
	 *
	 * @var array
	 */
	protected static $_ext = '.js';

	/**
	 * Returns an array of url's for the found javascript files
	 *
	 * @param  string  $dir app/assets/js/{directory} to iterate
	 * @return array
	 */
	public static function get ( $dir = "" ) {

		static::$_scripts = array();

		static::iterate( new DirectoryIterator( __scripts__ . $dir ) );

		return array_keys( static::$_scripts );
	}

	/**
	 * Registers a script event for loading the script files on runtime
	 *
	 * @return void
	 */
	public static function register () {

		Events::register( 'scripts', function () {
			return static::load();
		});
	}

	/**
	 * Called by the static::get method to perform a recursive search for script files on the given DirectoryIterator
	 *
	 * @param  DirectoryIterator
	 * @return array
	 */
	private static function iterate (DirectoryIterator $iterator) {
		$scripts = array();

		foreach ($iterator as $key => $child) {
			if ($child->isDot()) {
				continue;
			}

			$name = str_replace( rtrim(__scripts__, '/'), '', $child->getPathname());
			$name = str_replace( '\\', '/', $name);
			$name = trim( $name, '/');

			if ($child->isDir()) {
				$scripts = static::iterate( new DirectoryIterator($child->getPathname()) );
			} else {
				static::$_scripts[ __assets__ . 'js/' . $name] = 1;
			}
		}

		return $scripts;
	}

	/**
	 * Outputs the <script></script> tags for loading the application javascript libs
	 *
	 * @return void
	 */
	public static function load () {
		$load = array(
			'https://maps.googleapis.com/maps/api/js?key=' . __google_key__
		);

		if ( Config::get( 'environment' ) === "development" ) {
			// do not change the order of the load
			$scripts = array(
				static::get( 'src/libs/' ),
				static::get( 'src/ibt/' ),
				static::get( 'src/models/' ),
				static::get( 'src/widgets/' ),
				static::get( 'src/scripts/' ),
				static::get( 'src/pages/' )
			);

			foreach ($scripts as $dir) {
				foreach ($dir as $file) {
					$load[] = $file;
				}
			}

			$load[] = __assets__ . 'js/app.js';
		} else {
			$load[] = __assets__ . 'dist/app.min.js';
		}

		foreach( $load as $script ) {
			echo '<script type="text/javascript" src="' . $script . '"></script>';
		}
	}
}