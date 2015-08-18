<?php

namespace Ibt;

use \Ibt\Templates;
use \Ibt\Events;

/**
 *	Class Pages
 *
 */
class Pages {

	/**
	 * Page template file path
	 *
	 * @var string
	 */
	protected static $_template;

	/**
	 * Page request params
	 *
	 * @var mixed
	 */
	protected static $_params;

	/**
	 * Page request data
	 *
	 * @var mixed
	 */
	protected static $_data;

	/**
	 * Renders the page template file to html with the request data
	 *
	 * @return closure
	 */
	public static function render () {
		return Templates::render( static::$_template, static::$_data );
	}

	/**
	 * Binds request params and data to the current page instance
	 * Registeres a `content` event to for page html parsing
	 *
	 * @param  array $params request params
	 * @param  array $data   request query data
	 * @return bool
	 */
	public static function content ( $params = array (), $data = array() ) {
		static::$_params = $params;
		static::$_data = $data;

		return Events::register( 'content', function ( $content ) {
			return static::render( $content );
		});
	}
}