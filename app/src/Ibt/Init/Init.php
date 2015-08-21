<?php

namespace Ibt;

use \Ibt\User;
use \Ibt\Layout;
use \Ibt\Templates;
use \Ibt\Events;
use \Ibt\Router;

/**
 *	Class Init
 *
 */
class Init {

	/**
	 * Initiates the applicaton; Loads app config, prepares global user object, registers layout events
	 * starts the router and registers the shutdown function for cleaning up the app
	 *
	 * @return void
	 */
	public static function app () {

		Config::load();

		User::load();

		Layout::register();

		Events::fire( 'init' );

		Router::fire();

		if ( function_exists('register_shutdown_function') ) {
			register_shutdown_function( function () {
				return static::shutdown();
			});
		}
	}

	/**
	 * Triggers shutdown event callbacks (database celanup, ...)
	 *
	 * @return void
	 */
	public static function shutdown () {
		Events::fire ( 'shutdown' );
	}
}