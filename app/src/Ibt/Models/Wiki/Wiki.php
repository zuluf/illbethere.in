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
	 * Renders front-end application config, templates, locale and current user
	 *
	 * @return void
	 */
	public static function get ( $where = array(), $object = false ) {
		$wiki = parent::get( $where, $object );

		if ( ! empty ( $wiki ) ) {
			if ( is_array ( $wiki ) ) {
				foreach ($wiki as & $value) {
					$value->geosearch = array_slice (array_values((array) json_decode( base64_decode( $value->geosearch ) ) ), 0, 10);
					$value->fullurl = urldecode( $value->fullurl );
				}
			} else {
				$wiki->geosearch =  array_slice (array_values((array) json_decode( base64_decode( $wiki->geosearch ) ) ), 0, 10);
				$wiki->fullurl = urldecode( $wiki->fullurl );
			}
		}
		return $wiki;
	}

	public static function insert ( $wikipage = false ) {

		if ( ! empty ( $wikipage ) ) {
			$insert = array (
				'location_id' => (int) $wikipage->location_id,
				'page_id' => (int) $wikipage->page_id,
				'title' => parent::escape ( trim ( $wikipage->title ) ),
				'fullurl' => urlencode ( $wikipage->fullurl ),
				'extract' => parent::escape ( trim ( $wikipage->extract ) ),
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