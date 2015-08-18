<?php

namespace Ibt\Pages;

use \Ibt\Pages;
use \Ibt\Templates;

/**
 *	Class Page
 *
 */
class Page extends Pages {

	/**
	 * Prepares template params and renders the page template file to html
	 *
	 * @return string
	 */
	public static function render () {

		static::$_template = "pages/page/" . array_shift( static::$_params );
		static::$_data = array( 'params' => static::$_params );

		return parent::render();
	}
}