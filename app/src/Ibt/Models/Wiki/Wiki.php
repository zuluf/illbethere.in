<?php

namespace Ibt\Models;

use Ibt\Models;

class Wiki extends Models {

	protected static $_table = 'ibt_wiki';
	protected static $_primary = 'wiki_id';
	protected static $_instance;

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