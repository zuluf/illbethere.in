<?php

namespace Api\Resource;

use \Ibt\Models\Locations;
use \Ibt\Models\Wiki as Wikies;

class Wiki {

	const __wiki_api__ = "https://en.wikipedia.org/w/api.php?format=json&action=query&continue&";
	const __voyage_api__ = "https://en.wikivoyage.org/w/api.php?format=json&action=query&continue&";

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
							'pageid' => '',
						);
					}
				}
			}
		}

		return false;
	}

	public static function getWiki ( $params = array() ) {
		return static::get( $params, static::__wiki_api__);
	}

	public static function getVoyage ( $params = array() ) {
		return static::get( $params, static::__voyage_api__);
	}

	public static function get ( $params = array(), $url = false ) {
		if ( empty ( $params ) ) {
			return false;
		}

		$wiki_url =  ($url ? $url : static::__wiki_api__) . http_build_query ( $params, '', '&' );
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