<?php

namespace Ibt;

/**
 * Class Helpers
 */

class Helpers {

	/**
	 * Camelize "example-class" or "Example_Class" to ExampleClass; add's class namespace if param provided
	 *
	 * @param  string  $className
	 * @param  string  $namespace
	 * @return string
	 */
	public static function camelClass ( $className, $namespace = "" ) {

		if ( ! empty( $namespace ) ) {
			$namespace = '\\' . trim ( (string) $namespace, '\\' ) . '\\';
		}

		$className = str_replace( array( '_' ), '-', $className );

		return $namespace . implode ('', array_map( 'ucfirst', explode( '-', $className ) ) );
	}
}