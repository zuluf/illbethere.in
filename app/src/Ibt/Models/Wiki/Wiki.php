<?php

namespace Ibt\Models;

use Ibt\Models;

/**
 *	Class Ibt\Models\Wiki
 *
 */
class Wiki extends Models {

	/**
	 * Wiki model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_wiki';

	/**
	 * Wiki model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'wiki_id';

	/**
	 * Static instance property for holding the model instance
	 *
	 * @var \Ibt\Models\Wiki
	 */
	protected static $_instance;

	/**
	 * Calls the parent get method and parses the result row set
	 *
	 * @param  array 	$where
	 * @param  array 	$unique
	 * @return array|object|bool
	 */
	public static function get ( $where = array(), $unique = false ) {
		$wiki = parent::get( $where, $unique );

		if ( ! empty ( $wiki ) ) {
			$wiki = ! is_array ( $wiki ) ? array ( $wiki ) : $wiki;

			foreach ( $wiki as & $value ) {
				$value->geosearch = array_slice (array_values((array) json_decode( base64_decode( $value->geosearch ) ) ), 0, 10);
				$value->fullurl = urldecode( $value->fullurl );
				$value->extract = stripslashes( $value->extract );
			}

			return $unique ? array_shift( $wiki ) : $wiki;
		}

		return false;
	}

	/**
	 * Prepares the given data for the database insert; Data should contain column_name => value properties
	 *
	 * @param  array 		$wikipage
	 * @return object|bool 	Returns false on failure
	 */
	public static function insert ( $wikipage = false ) {

		if ( ! empty ( $wikipage ) ) {
			$insert = array (
				'location_id' => (int) $wikipage->location_id,
				'page_id' => (int) $wikipage->page_id,
				'title' => trim ( $wikipage->title ),
				'fullurl' => urlencode ( $wikipage->fullurl ),
				'extract' => preg_replace('/[\r\n\t]/', ' ', trim ( $wikipage->extract ) ),
				'geosearch' => base64_encode( json_encode( $wikipage->geosearch, JSON_UNESCAPED_UNICODE ) )
			);

			$wiki_id = parent::insert ( $insert );

			if ( ! empty ( $wiki_id ) ) {
				return static::get ( array ( 'wiki_id' => $wiki_id ), true );
			}
		}

		return false;
	}
}