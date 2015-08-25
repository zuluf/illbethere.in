<?php

namespace Api\Resource;

use \Api\Config;
use \Ibt\Errors;
use \Ibt\Models\Locations;
use \Ibt\Models\Flickr as Model;

/**
 * Class Api\Resource\Flickr
 *
 */
class Flickr {

	/**
	 * Flickr default api url
	 *
	 * @var string;
	 */
	const api_uri = "https://api.flickr.com/services/rest";

	/**
	 * Flickr photo root uri
	 *
	 * @var string;
	 */
	const photo_uri = "https://www.flickr.com/photos/";

	/**
	 * Text search request params
	 *
	 * @var array;
	 */
	private static $_textParams = array (
		'sort' => 'relevance',
		'parse_tags' => 1,
		'content_type' => 7,
		'extras' => 'isfavorite,license,media,owner_name,
			path_alias,realname,rotation,url_l,url_m,url_s',
		'per_page' => 15,
		'safe_search' => 3,
		'page' => 1,
		'lang' => 'en-US',
		'media' => 'photos',
		'text' => '',
		'advanced' => 1,
		'method' => 'flickr.photos.search',
		'api_key' => '',
		'format' => 'json',
		'hermes' => false,
		'nojsoncallback' => 1,
	);

	/**
	 * Location search request params
	 *
	 * @var array;
	 */
	private static $_locationParams = array (
		'lat' => false,
		'lon' => false,
		'accuracy' => 10,
		'content_type' => 7,
		'extras' => 'media,owner_name,
			path_alias,realname,rotation,url_l,url_m,url_s',
		'per_page' => 15,
		'safe_search' => 3,
		'page' => 1,
		'lang' => 'en-US',
		'media' => 'photos',
		'text' => '',
		'advanced' => 1,
		'method' => 'flickr.photos.geo.photosForLocation',
		'api_key' => '',
		'format' => 'json',
		'nojsoncallback' => 1,
	);

	/**
	 * Returns the given location flicker photo search result
	 *
	 * @return bool|object
	 */
	public static function location ( $location_id = false ) {

		$location_id = (int) $location_id;
		if ( empty( $location_id ) ) {
			return false;
		}

		$location = Locations::get ( array ( 'location_id' => $location_id ), true );
		if ( empty( $location ) ) {
			return false;
		}

		$flickr = Model::get ( array ( 'location_id' => $location->location_id ), true );
		if ( ! empty ( $flickr ) ) {
			return $flickr;
		}

		$config = Config::get ( 'flickr' );
		if ( empty( $config ) || ! isset ( $config->apiKey ) || empty( $config->apiKey ) ) {
			return false;
		}

		$params = static::$_textParams;
		$params['text'] = $location->name;
		$params['api_key'] = $config->apiKey;

		$response = static::request( $params );

		if ( ! empty( $response ) ) {
			return static::save ( $location, $response );
		}

		return false;
	}

	/**
	 * Saves the flickr request to the database
	 *
	 * @return object
	 */
	private static function save ( $location, $response ) {
		// https://www.flickr.com/photos/{pathalias}||{owner}/{id}/

		if ( empty( $location ) || empty( $response ) ) {
			return false;
		}

		$photos = isset ( $response->photos ) && ! empty( $response->photos ) ? $response->photos : false;
		if ( empty( $photos ) || ! isset( $photos[ 'photo' ] ) ) {
			return false;
		}

		$insert = (object) array(
			'location_id' => $location->location_id,
			'photos' => array ()
		);

		if ( ! empty( $photos[ 'photo' ] ) ) {
			foreach ( $photos[ 'photo' ] as $photo) {
				$insert->photos[] = array (
					'photo_id' => $photo[ 'id' ],
					'owner_id' => $photo[ 'owner' ],
					'owner_name' => $photo[ 'ownername' ],
					'pathalias' => isset ( $photo[ 'pathalias' ] ) ? $photo[ 'pathalias' ] : "",
					'photo_uri' => static::photo_uri .
						(! empty( $photo[ 'pathalias' ] ) ? $photo[ 'pathalias' ] : $photo[ 'owner' ] ) . '/' . $photo[ 'id' ],
					'title' => $photo[ 'title' ],
					'large' => isset ( $photo[ 'url_l' ] ) ? $photo[ 'url_l' ] : "",
					'medium' => isset ( $photo[ 'url_m' ] ) ? $photo[ 'url_m' ] : "",
					'small' => isset ( $photo[ 'url_s' ] ) ? $photo[ 'url_s' ] : ""
				);
			}
		}

		return Model::insert ( $insert );
	}

	/**
	 * Returns json_decoded flickr api response
	 *
	 * @return object
	 */
	private static function request ( $params = array() ) {

		if ( empty ( $params ) ) {
			return false;
		}

		$flickr_api = static::api_uri . '?' . http_build_query( $params );

		$options = array (
			'http' => array (
				'method' => 'GET',
				'header' => "User-Agent: I'll be there in (illbethere.in), please contact me at lazarevic.net@gmail.com for any info. You guys ROCK!\n"
			)
		);

		$context = stream_context_create( $options );

		$response = file_get_contents( $flickr_api, false, $context );

		if ( ! empty ( $response ) ) {
			return (object) json_decode( $response, true );
		}

		return false;
	}
}