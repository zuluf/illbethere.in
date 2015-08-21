<?php

namespace Api;

/**
 * Registers the Api namespace class file load function
 */
spl_autoload_register(
	function ( $class ) {
		if ( class_exists( $class ) || interface_exists( $class ) ) {
			return;
		}

		$name = explode( '\\', $class );
		$className = DIRECTORY_SEPARATOR . array_pop ( $name ) . '.php';
		$base = dirname ( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR;
		$file = str_replace('\\', '/', $base . $class . $className);

		if ( is_file ( $file ) ) {
			require $file;
		}
	}
);