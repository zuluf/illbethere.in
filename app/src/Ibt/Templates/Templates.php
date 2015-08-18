<?php

namespace Ibt;

use \DirectoryIterator;
use \Ibt\Locale;
/**
 *	Class Templates
 *
 */
class Templates {

	private static $_templates = array();

	private static $_engine;

	private static $_ext = '.mustache';

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

	public static function get ( $templates = "" ) {
		return static::load(new DirectoryIterator( __templates__ . $templates ) );
	}

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
				static::$_templates[$name] = file_get_contents($child->getPathname());
			}
		}

		return static::$_templates;
	}
}