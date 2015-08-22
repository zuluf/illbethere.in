<?php

namespace Api\Resource;

use \Ibt\Errors;
use \Ibt\Models\Locations;
use \Ibt\Models\Wiki as Wikies;

/**
 * Class Api\Resource\Wiki
 *
 */
class Wiki {

	/**
	 * Wikipedia default api url
	 *
	 * @var string;
	 */
	const wiki_uri = "https://en.wikipedia.org/w/api.php?format=json&action=query&continue&";

	/**
	 * Wikivoyage default api url
	 *
	 * @var string;
	 */
	const voyage_uri = "https://en.wikivoyage.org/w/api.php?format=json&action=query&continue&";

	/**
	 * Returns the given location wikipedia|wikivoyage info
	 *
	 * @return array
	 */
	public static function location ( $location_id = false ) {

		if ( empty($location_id) ) {
			return;
		}

		$location = Locations::get( array( 'location_id' => (int) $location_id ), true );

		if ( !empty($location) ) {
			$wiki = Wikies::get ( array( 'location_id' => $location->location_id ), true );

			if ( empty( $wiki ) ) {
				$wiki = static::addLocationWiki ( $location );
			}

			return ! empty($wiki) ? $wiki : array();
		}

		return array();
	}

	/**
	 * Triggers a wikipedia api search for the given location and inserts the new wikipedia info to the local database
	 *
	 * @return object
	 */
	public static function addLocationWiki ( $location = false ) {

		$page = static::wikiGeosearch( $location );

 		if ( ! empty ( $page ) ) {
			$insert = (object) array (
				'location_id' => $location->location_id,
				'page_id' => $page->pageid,
				'title' => $page->title,
				'fullurl' => $page->fullurl,
				'extract' => $page->extract,
				'geosearch' => $page->geosearch
			);

			return Wikies::insert ( $insert );
		}
	}

	/**
	 * Performs a wikipedia|wikivoyage api geosearch for the given location longitude/latitude info
	 * The function will first type the wikivoyage api, and if no wikivoyage page is found, function try's
	 * with the wikipedia
	 *
	 * @return object
	 */
	public static function wikiGeosearch ( $location = false ) {
		if ( empty( $location ) ) {
			return false;
		}

		$latitude = number_format($location->latitude, 6);
		$longitude = number_format($location->longitude, 9);

		$params = array (
			'list' => 'geosearch',
			'gsradius' => '10000',
			'gscoord' => "{$latitude}|{$longitude}",
			'gslimit' => '20'
		);

		$page = false;

		$geosearch = static::getVoyage ( $params );

		if ( !empty( $geosearch ) ) {
			if ( isset( $geosearch->query ) && ! empty ( $geosearch->query ) ) {

				$query = (object) $geosearch->query;

				if ( isset( $query->geosearch ) && ! empty ( $query->geosearch ) ) {
					foreach ($query->geosearch as $key => $result) {
						if ( strtolower( $result['title'] ) == strtolower( $location->name ) ) {
							$page = (object) $result;
							unset($query->geosearch[$key]);
						}
					}

					if ( empty ($page) ) {
						foreach ($query->geosearch as $key => $result) {
							if ( strstr( strtolower( $result['title'] ), strtolower( $location->name ) ) ) {
								$page = (object) $result;
								unset($query->geosearch[$key]);
							}
						}
					}

					if ( !empty( $page ) ) {
						if ( isset ( $page->pageid ) && ! empty( $page->pageid ) ) {
							$params = array (
								'pageids' => $page->pageid,
								'prop' => 'extracts|info',
								'exintro' => '',
								'explaintext' => '',
								'inprop' => 'url'
							);

							$wikipage = static::getVoyage ( $params );

							if ( isset ( $wikipage->query ) ) {
								$wikipage = (object) $wikipage->query;

								if ( isset( $wikipage->pages ) && ! empty ( $wikipage->pages ) ) {
									$wikipage = (object) array_shift( $wikipage->pages );

									foreach ($query->geosearch as &$value) {
										$value['fullurl'] = 'https://en.wikivoyage.org/?curid=' . $value['pageid'];
									}

									$wikipage->geosearch = $query->geosearch;

									if ( strstr( $wikipage->extract, 'For other places with the') ) {
										$extract = explode("\n", $wikipage->extract);
										array_shift( $extract );
										$wikipage->extract = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", " ", implode(" ", $extract));
									}

									return $wikipage;
								}
							}
						}
					}
				}
			}
		}
		// try wikipedia if !wikivoyage page
		if ( empty($page) ) {
			$geosearch = static::getWiki ( $params );

			if ( isset ($geosearch->query) && ! empty ( $geosearch->query) ) {
				$query = (object) $geosearch->query;

				if ( isset( $query->geosearch ) && ! empty ( $query->geosearch ) ) {

					foreach ($query->geosearch as $key => &$result) {
						$result['fullurl'] = 'https://en.wikipedia.org/?curid=' . $result['pageid'];

						if ( strstr( strtolower( $result['title'] ), strtolower( $location->name ) ) ) {
							$page = (object) $result;
							unset($query->geosearch[$key]);
						}
					}

					if ( !empty( $page ) ) {
						if ( isset ( $page->pageid ) && ! empty( $page->pageid ) ) {
							$params = array (
								'pageids' => $page->pageid,
								'prop' => 'extracts|info',
								'exintro' => '',
								'explaintext' => '',
								'inprop' => 'url'
							);

							$wikipage = static::getWiki ( $params );

							if ( isset ( $wikipage->query ) ) {
								$wikipage = (object) $wikipage->query;

								if ( isset( $wikipage->pages ) && ! empty ( $wikipage->pages ) ) {
									$wikipage = (object) array_shift( $wikipage->pages );
									$wikipage->geosearch = $query->geosearch;

									return $wikipage;
								}
							}
						}
					} else {
						return (object) array (
							'geosearch' => $query->geosearch,
							'title' => $location->name,
							'extract' => '',
							'fullurl' => '',
							'pageid' => 0,
						);
					}
				}
			}
		}

		return false;
	}

	/**
	 * Triggers the wikipedia api request
	 *
	 * @return object
	 */
	public static function getWiki ( $params = array() ) {
		return static::get( $params, static::wiki_uri);
	}

	/**
	 * Triggers the wikivoyage api request
	 *
	 * @return object
	 */
	public static function getVoyage ( $params = array() ) {
		return static::get( $params, static::voyage_uri);
	}

	/**
	 * Fires a request to wiki with the header info set
	 *
	 * @return object
	 */
	public static function get ( $params = array(), $url = false ) {
		if ( empty ( $params ) ) {
			return false;
		}

		$wiki_url =  ($url ? $url : static::wiki_uri) . http_build_query ( $params, '', '&' );
		$options = array (
			'http' => array (
				'method' => 'GET',
				'header' => "User-Agent: I'll be there in (illbethere.in), please contact me at lazarevic.net@gmail.com for any info. You guys ROCK!\n"
			)
		);

		$context = stream_context_create( $options );

		$response = file_get_contents( $wiki_url, false, $context );

		if ( !empty ( $response ) ) {
			return (object) json_decode($response, true);
		}

		return false;
	}
}