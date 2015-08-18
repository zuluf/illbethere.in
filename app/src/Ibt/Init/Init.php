<?php

namespace Ibt;

use \Ibt\User;
use \Ibt\Locale;
use \Ibt\Templates;
use \Ibt\Events;
use \Ibt\Router;
use \Ibt\Scripts;

/**
 *	Class Init
 *
 */
class Init {

	public static function app () {

		Config::load();
		User::load();

		Scripts::register();

		Events::register( 'render_content', function () {
			echo Events::fire( 'content' );
		});

		Events::fire('init');

		Router::fire();

		if( function_exists('register_shutdown_function') ){
			register_shutdown_function( function () {
				return static::shutdown();
			});
		}
	}

	public static function shutdown () {
		Events::fire ( 'shutdown' );
	}
}