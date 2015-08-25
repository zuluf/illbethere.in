<?php

namespace Ibt\Models;

use \Closure;
use Ibt\Models;

/**
 *	Class Ibt\Models\Settings
 *
 */
class Settings extends Models {

	/**
	 * Settings model table name
	 *
	 * @var string
	 */
	protected static $_table = 'ibt_settings';

	/**
	 * Settings model table primary key
	 *
	 * @var string
	 */
	protected static $_primary = 'setting_id';

	/**
	 * Static instance property for holding the static model instance
	 *
	 * @var \Ibt\Models\Settings
	 */
	protected static $_instance;

	/**
	 * Returns the given setting by name; null if the setting doesn't exists
	 *
	 * @param  string  $name
	 * @return bool|null
	 */
	public static function get ( $name = array(), $unique = false ) {
		if ( empty( $name ) || ! is_string( $name ) ) {
			return null;
		}

		$setting = parent::get( array ( 'name' => $name ), true );

		if ( isset ( $setting->value ) ) {
			return static::decode( $setting->value );
		}

		return null;
	}

	/**
	 * Insert's new setting value to the table
	 * If the setting already exists function will perform an update
	 *
	 * @param  string  $name
	 * @param  mixed  $value
	 * @return bool|null
	 */
	public static function add ( $name = null, $value = null ) {
		if ( empty( $name ) || ! is_string( $name ) ) {
			return null;
		}

		$setting = parent::get( array ( 'name' => $name ), true );
		$value = static::encode( $value );

		if ( isset ( $setting->setting_id ) && ! empty( $setting->setting_id ) ) {
			return parent::update( array( 'value' => $value ), array( 'setting_id' => $setting->setting_id, 'name' => $name ) );
		} else {
			return parent::insert( array( 'name' => $name, 'value' => $value ) );
		}
	}

	/**
	 * Updates the model instance with the new set of data
	 *
	 * @param  array   $data
	 * @return return \Ibt\Models\Settings|bool
	 */
	public function save ( array $data = array() ) {

		if( isset( $data[ 'value' ] ) ) {
			$data[ 'value' ] = static::encode( $data[ 'value' ] );
		}

		return static::update( $data, array ( static::$_primary => $this->{ static::$_primary } ) );
	}
}