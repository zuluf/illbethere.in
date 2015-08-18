<?php

namespace Ibt;

/**
 *	Class Events
 *
 */
class Events {

	/**
	 * Static events collection
	 *
	 * @var array
	 */
	protected static $_events = [];

	/**
	 * Register a new event callback.
	 *
	 * @param  string  $event
	 * @param  callable  $callback
	 * @param  mixed  $params
	 * @param  int  $priority
	 * @return bool
	 */
	public static function register( $event = "", $callback = false, $params = array(), $priority = 0 ){

		if( ! empty( $event ) ){
			if( !isset( static::$_events[$event] ) ){
				static::$_events[$event] = array();
			}

			if( is_callable($callback) ){
				static::$_events[$event][] = array(
					'callback' => $callback,
					'priority' => $priority,
					'params' => is_array($params) ? $params : array ($params)
				);

				return usort( static::$_events[$event], array( '\Ibt\Events', 'sort' ) );
			}
		}

		return false;
	}

	/**
	 * Fires given registered event.
	 *
	 * @param  string  $event
	 * @param  mixed  $params
	 * @return mixed
	 */
	public static function fire( $event = "", $params = array() ){

		if( empty( $event ) ){
			return false;
		}

		if( isset( static::$_events[ $event ] ) ) {

			$params = is_array($params) ? $params : array ( $params );

			foreach ( static::$_events[$event] as $func ) {

				if( is_callable( $func[ 'callback' ] ) ){

					$params = is_array( $params ) ? array_merge( $func[ 'params' ], $params ) : $params;

					$result = call_user_func( $func[ 'callback' ], $params );

					if ( gettype( $result ) !== 'NULL' ){
						$params = $result;
					}
				}
			}
		}

		return $params;
	}

	/**
	 * Sort event callbacks by priority.
	 *
	 * @param  array  $event
	 * @param  array  $next
	 * @return int
	 */
	public static function sort( $event, $next ){
		return $event['priority'] < $next['priority'] ? 1 : ( $event['priority'] === $next['priority'] ? 0 : -1 );
	}
}