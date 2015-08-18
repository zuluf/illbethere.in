<?php

namespace Api\Resource;

class Pages {

	public static function get ( $query = false ) {
		$locations = array();

		if ( !empty($query) ) {
			return \App\Models\Pages::get( array('name' => 'like:%' . $query . '%' ) );
		}

		return $locations;
	}
}