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

	protected static $_scripts  = array();

	protected static $_ext = '.js';


	public static function get ( $scripts = "" ) {

		static::$_scripts = array();

		static::iterate( new DirectoryIterator( __scripts__ . $scripts ) );

		return array_keys( static::$_scripts );
	}

	public static function register () {

		Events::register( 'header', function () {
			return static::header();
		});

		Events::register( 'scripts', function () {
			return static::load();
		});
	}

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

	public static function header () {

		$config = array(
			'page' => Router::getPage(),
			'user' => User::get(),
			'locales' => Locale::load( "en-US" ),
			'templates' => Templates::get( 'widgets' ),
			'config' => array(
				'api' => __host__ . 'api/',
				'app' => __host__,
				'gkey' => __google_key__
			)
		);

		echo '<script type="text/javascript"> window.ibt = '. json_encode( $config, true ) .'; </script>';
	}

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