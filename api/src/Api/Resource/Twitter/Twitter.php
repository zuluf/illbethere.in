<?php

namespace Api\Resource;

use \Api\Config;
use \Ibt\Errors;
use \Ibt\Models\Locations;
use \Ibt\Models\Twitter as Model;

/**
 * Class Api\Resource\Twitter
 *
 */
class Twitter {

	/**
	 * Twitter bearer token uri
	 *
	 * @var string;
	 */
	const token_uri = "https://api.twitter.com/oauth2/token";

	/**
	 * Twitter search/tweets api uri
	 *
	 * @var string;
	 */
	const search_uri = "https://api.twitter.com/1.1/search/tweets.json";

	/**
	 * Twitter application/rate_limit_status.json api uri
	 *
	 * @var string;
	 */
	const limit_uri = "https://api.twitter.com/1.1/application/rate_limit_status.json";

	/**
	 * Returns tweets for the location
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

		$tweets = Model::get( array( 'location_id' => (int) $location_id ), true );
		if ( ! empty ( $tweets ) ) {
			// add update time check for 15 minute intervals
			return $tweets;
		}

		$search = static::search ( $location );

		if ( ! empty( $search ) && isset( $search->statuses ) ) {
			if ( empty( $search->statuses ) ) {
				$location->rank( -1 );
			}

			return static::insert( $search->statuses, $location );
		}

		return false;
	}

	/**
	 * Inserts tweetset for the given location
	 *
	 * @param  array 	$tweets
	 * @return string
	 */
	private static function insert ( $tweets = array(), Locations $location ) {
		$insert = (object) array (
			'location_id' => $location->location_id,
			'tweets' => $tweets,
		);

		return Model::insert( $insert );
	}

	/**
	 * Checks if we have a bearer token in the database.
	 * If not performs oauth2/token request and save's it to the database
	 *
	 * Here we are using application-only twitter api authorization, so we only need the bearer token
	 * to perform tweets/search requests; https://dev.twitter.com/oauth/application-only
	 *
	 * @param  bool 	$getNew
	 * @return string
	 */
	private static function authorize ( $getNew = false ) {

		if ( ! $getNew ) {
			$bearer = Model::getBearerToken();
		}

		if ( $getNew || empty( $bearer ) ) {

			$auth = static::auth();

			if ( ! empty( $auth ) && isset( $auth->token_type ) && isset( $auth->access_token ) ) {
				if ( $auth->token_type === "bearer" ) {
					return Model::saveBearerToken( strip_tags( $auth->access_token ) );
				}
			}
		}

		return false;
	}

	/**
	 * Returns twitter encoded consumerKey and consumerSecret ready for a signature request
	 *
	 * @return string
	 */
	private static function encodeKeys () {

		$config = Config::get( 'twitter' );

		if ( empty ( $config ) ) {
			return false;
		}

		if ( ! isset( $config->consumerKey ) || ! isset( $config->consumerSecret ) ) {
			return false;
		}

		return base64_encode( urlencode( $config->consumerKey ) . ':' . urlencode( $config->consumerSecret ) );
	}

	/**
	 * Returns json_decoded twitter api response
	 *
	 * @return object
	 */
	private static function auth () {

		$content = http_build_query ( array (
			'grant_type' => 'client_credentials'
		) );

		$context = array (
			'http' => array (
				'method' => 'POST',
				'header' => array (
					"User-Agent: illbethere.in",
					"Authorization: Basic " . static::encodeKeys(),
					"Content-Length: ". strlen( $content ),
					"Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
				),
				'timeout' => 5,
				'content' => $content
			)
		);

		return static::request ( static::token_uri,  $context );
	}

	/**
	 * Returns json_decoded twitter api response
	 *
	 * @return object
	 */
	private static function search ( $location = object ) {

		if ( empty( $location ) ) {
			return false;
		}

		$token = Model::getBearerToken();
		if ( empty( $token ) ) {
			$token = static::authorize();
		}

		$content = http_build_query ( array (
			'q' => urlencode( '#' . $location->name . ' -RT -rent -property :)' )
		) );

		$context = array (
			'http' => array (
				'method' => 'GET',
				'header' => array (
					"User-Agent: illbethere.in",
					"Authorization: Bearer " . $token,
					"Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
				)
			)
		);

		return static::request ( static::search_uri . '?' . $content,  $context );
	}

	/**
	 * Returns json_decoded twitter api response
	 *
	 * @return object
	 */
	private static function request ( $uri, $context = array() ) {

		if ( empty ( $uri ) || empty ( $context ) ) {
			return false;
		}

		/**
		 * For further reference, since 5.6 verify_peer is by default set to true;
		 * just in case, set verify options to true for the request context
		 */
		$ssl = Config::get( 'ssl' );
		if ( ! empty( $ssl ) && isset( $ssl[ 'cafile' ] ) && is_file( $ssl[ 'cafile' ] ) ) {
			$context[ 'ssl' ] = array (
				"verify_peer" => true,
				"verify_peer_name" => true,
				'cafile' => $ssl[ 'cafile' ]
			);
		}

		$context = stream_context_create( $context );

		$response = file_get_contents( $uri, false, $context );
		if ( ! empty ( $response ) ) {
			return (object) json_decode( $response, true );
		}

		return false;
	}
}