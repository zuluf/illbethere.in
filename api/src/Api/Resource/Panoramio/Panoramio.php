<?php

namespace Api\Resource;

use \Api\Config;
use \Ibt\Errors;
use \Ibt\Models\Locations;
use \Ibt\Models\Panoramio as Model;

/**
 * Class Api\Resource\Panoramio
 *
 */
class Panoramio {

	/**
	 * Panoramio default api url
	 *
	 * @var string;
	 */
	const api_uri = "http://www.panoramio.com/map/get_panoramas.php";

	/**
	 * Panoramio photo root uri
	 *
	 * @var string;
	 */
	const photo_uri = "http://www.panoramio.com/photo/";

	/**
	 * Default request params
	 *
	 * @var array;
	 */
	private static $_defaultParams = array (
		'set' => 'public',
		'from' => 0,
		'to' => 30,
		'minx' => false,
		'miny' => false,
		'maxx' => false,
		'maxy' => false,
		'size' => 'original'
	);

	/**
	 * Returns the given location panoramio photo search result
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

		$photos = Model::get ( array ( 'location_id' => $location->location_id ), true );
		if ( ! empty ( $photos ) ) {
			return $photos;
		}

		$params = static::$_defaultParams;
		$params['minx'] = $location->longitude - 0.02;
		$params['maxx'] = $location->longitude + 0.02;
		$params['miny'] = $location->latitude - 0.02;
		$params['maxy'] = $location->latitude + 0.02;

		// get original size photos
		$original = static::request( $params );
		if ( empty( $original ) || ! isset( $original->photos ) ) {
			return false;
		}

		// get thumbnail size photos
		$params['size'] = 'small';
		$small = static::request( $params );

		if ( ! empty( $small ) && isset( $small->photos ) ) {
			$thumbnails = array();

			foreach ( $small->photos as $index => $photo) {
				$photo_id = isset( $photo[ 'photo_id' ] ) && ! empty( $photo[ 'photo_id' ] ) ? $photo[ 'photo_id' ] : false;
				$height = isset( $photo[ 'height' ] ) && ! empty( $photo[ 'height' ] ) ? (int) $photo[ 'height' ] : false;

				if ( ! $photo_id || ! $height || $height < 140 ) {
					continue;
				}

				$thumbnails[ $photo_id ] = $photo[ 'photo_file_url' ];
			}

			foreach ( $original->photos as $index => $photo) {
				$photo_id = isset( $photo[ 'photo_id' ] ) && ! empty( $photo[ 'photo_id' ] ) ? $photo[ 'photo_id' ] : false;

				if ( ! $photo_id || ! isset( $thumbnails[ $photo_id ] ) ) {
					continue;
				}

				$original->photos[ $index ][ 'thumb_file_url' ] = $thumbnails[ $photo_id ];
			}

			return static::save( $location, $original );
		}

		return false;
	}

	/**
	 * Saves the flickr request to the database
	 *
	 * @return object
	 */
	private static function save ( $location, $response ) {
		if ( empty( $location ) || empty( $response ) ) {
			return false;
		}

		if ( isset ( $response->photos ) && ! empty( $response->photos ) ) {
			$insert = (object) array(
				'location_id' => $location->location_id,
				'photos' => $response->photos
			);

			return Model::insert ( $insert );
		}

		return false;
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