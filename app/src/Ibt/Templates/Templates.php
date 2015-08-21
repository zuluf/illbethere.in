<?php

namespace Ibt;

use \DirectoryIterator;
use \Ibt\Locale;
/**
 *	Class Templates
 *
 */
class Templates {

	/**
	 * Static templates collection
	 *
	 * @var array
	 */
	private static $_templates = array();

	/**
	 * Holding the current template engine instance
	 *
	 * @var Mustache_Engine
	 */
	private static $_engine;

	/**
	 * Template files extension
	 *
	 * @var string
	 */
	private static $_ext = '.mustache';

	/**
	 * Returns the rendered template file as pure html
	 *
	 * @param  string      	  $template Path to the template file to render
	 * @param  array|object   $data     Data to pass to the template
	 * @return string
	 */
	public static function render ( $template = false , $data = array() ) {

		$template = __templates__ . $template . static::$_ext;
		$engine = static::engine();

		if ( $template && file_exists($template) ) {
			$template = file_get_contents($template);

			if ( ! empty ( $template ) ) {
				return $engine->render( $template, $data );
			}
		}

		return "";
	}

	/**
	 * Returns array of the template file content from tthe given directory
	 *
	 * @param  string      	  $dir Path to the template directory to load
	 * @return array
	 */
	public static function get ( $dir = "" ) {
		return static::load(new DirectoryIterator( __templates__ . $dir ) );
	}

	/**
	 * Returns the current template rendering engine; initializes the engine
	 *
	 * @return Mustache_Engine
	 */
	private static function engine(){
		if (!static::$_engine) {
			if ( !class_exists( 'Mustache_Autoloader' ) ) {
				require "Mustache/Autoloader.php";
				\Mustache_Autoloader::register();
			}

			static::$_engine = new \Mustache_Engine ( array (
				'helpers' => array (
					'_t' => function ( $string = '') {
						return Locale::gettext ( $string );
					}
				)
			));
		}

		return static::$_engine;
	}

	/**
	 * Recursively loads template contents from the given directory, and returns the array ( template/file/path => template/file/content )
	 *
	 * @param  DirectoryIterator
	 * @return array
	 */
	private static function load (DirectoryIterator $iterator) {
		$templates = array();

		foreach ($iterator as $key => $child) {
			if ($child->isDot()) {
				continue;
			}

			$name = str_replace( rtrim( __templates__, '/' ), '', $child->getPathname());
			$name = str_replace( static::$_ext, '', $name);
			$name = trim($name, '\\ /');
			$name = str_replace( '\\', '.', $name);
			$name = str_replace( '/', '.', $name);

			if ($child->isDir()) {
				$subit = new DirectoryIterator($child->getPathname());
				$templates = static::load($subit);
			} else {
				static::$_templates[$name] = file_get_contents( $child->getPathname() );
			}
		}

		return static::$_templates;
	}
}