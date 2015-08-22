<?php

namespace Ibt;

use \App\Includes\Dbase;
use \Ibt\Errors;
use \Ibt\Data;

/**
 *	Class Ibt\Models
 *
 */
class Models {

	/**
	 * Executes a query on a database
	 *
	 * @param  string $query
	 * @return mixed
	 */
	private static function query ( $query = "" ) {

		$data = Data::query( $query );

		if ( isset( $data->error ) && ! empty( $data->error ) ) {
			Errors::set ( 'database', $data->error, true );
			return null;
		}

		return $data;
	}

	/**
	 * Executes the given query
	 *
	 * @param  string $query
	 * @param  bool   $object   If true, converts the resultset to array of objects
	 * @return mixed
	 */
	public static function fetch ( $query = '', $object = false ) {
		$data = array();

		if ( ! empty( $query ) && is_string( $query ) ) {

			$_data = static::query( $query );

			if (! empty( $_data ) ) {

				if ( $object ) {
					foreach ( $_data as $item ) {
						if ( !empty( $item ) && $item->{static::$_primary} ) {

							foreach ( (array) $item as $column => $value ) {
								$item->{$column} = is_string( $value ) ? stripslashes( $value ) : $value;
							}

							$data[$item->{static::$_primary}] = $item;
						}
					}
					return $data;
				}
				return $_data;
			}
		}

		return $data;
	}

	/**
	 * Executes SELECT query for the current table instance
	 *
	 * @param  array  $where    Array of column => value to add to WHERE section
	 * @param  bool   $unique   If true returns the first row from the result-set
	 * @return array|object
	 */
	public static function get ( $where = array(), $unique = false ) {
		$data = array();

		$where = static::where ( $where );

		$_data = static::query( "SELECT * FROM `" . static::$_table . "` {$where};" );

		if ( ! empty( $_data ) ) {
			foreach ( $_data as $item ) {
				if ( !empty( $item ) && $item->{static::$_primary} ) {

					foreach ( (array) $item as $column => $value ) {
						$item->{$column} = is_string( $value ) ? stripslashes( $value ) : $value;
					}

					$data[$item->{static::$_primary}] = $item;
				}
			}
		}

		if ( ! empty( $unique ) && ! empty ( $data ) ) {
			return array_pop( $data );
		}

		return $data;
	}

	/**
	 * Prepares the query and binds the given parameter data to the mysqli statement
	 *
	 * @param  string  $query
	 * @param  array   $data
	 * @return mysqli_stmt|bool
	 */
	public static function prepare ( $query = "", $data = array () ) {
		return Data::prepare( $query, $data );
	}

	/**
	 * Executes INSERT query for the current table instance
	 *
	 * @param  array  $data    Array of column => value properties for the new data row
	 * @return bool|int 	   Returns false on failure, or the table primary_id value for the inserted row
	 */
	public static function insert ( $data = array () ) {

		if ( ! empty ( $data ) ) {

			$columns = implode('`, `', array_keys( $data ) );
			$repeat = str_repeat ( ', ?', count( $data ) );

			$query = "INSERT INTO `" . static::$_table . "` (`" . static::$_primary . "`, `{$columns}`) VALUES (null {$repeat})";

			$statement = static::prepare($query, $data);

			if ( empty ( $statement ) ) {
				Errors::set ( 'database', 'Insert: ' . Data::errorMessage() , true );
				return null;
			}

			$statement->execute();

			Errors::log($statement);
			return Data::insertId();
		}

		return false;
	}

	/**
	 * Converts given array ( column => value ) to string prepared|escaped for the query WHERE section
	 * Adding an "order" array ( 'DESC'||'ASC' => (column1, column2, ...)) will add ORDER BY to the result string
	 * Adding an "group" array ( column1, column2, ...) will add GROUP BY to the result string
	 * Adding an "limit" int will add LIMIT to the result string
	 *
	 * @param  array   $where      Array of column => value properties for the WHERE query section
	 * @param  string  $operator   Operator to be used for where column conditions
	 * @return string
	 */
	public static function where( $where = array (), $operator = ' AND ' ){
		$_where = "";
		$_order = "";
		$_group = "";
		$_limit = "";

		if ( isset( $where[ 'order' ] ) && is_array( $where[ 'order' ] ) && !empty( $where[ 'order' ] ) ) {
			if ( isset( $where[ 'order' ][ 'DESC' ] ) ) {
				$_order = "DESC";
			} else if ( isset( $where[ 'order'][ 'ASC' ] ) ) {
				$_order = "ASC";
			}

			if ( $_order ) {
				if ( ! empty( $where[ 'order' ][ $_order ] ) ) {
					if ( is_array( $where[ 'order' ][ $_order ] ) ) {
						$_order = " ORDER BY `" . implode( "`, `", array_map( 'self::escape', $where[ 'order' ][ $_order ] ) ) . "` {$_order}";
					} else {
						$_order = " ORDER BY " . self::escape( $where[ 'order' ][ $_order ] ) . " {$_order}";
					}

					unset( $where[ 'order' ] );
				}
			}
		}

		if ( isset( $where[ 'group' ] ) && !empty( $where[ 'group' ] ) ) {

			$_group = " GROUP BY `" . implode('`, `', $where[ 'group' ]) . "`";
			unset( $where[ 'group' ] );
		}


		if ( isset( $where[ 'limit' ] ) && !empty( $where[ 'limit' ] ) ) {

			$_limit = (int) $where[ 'limit' ];

			if ( $_limit ) {
				$_limit = " LIMIT {$_limit}";
				unset( $where[ 'limit' ] );
			}
		}


		if ( is_array( $where ) && !empty( $where ) ) {
			$compare = " = ";

			foreach ( $where as $key => $value ) {
				if ( is_string( $value ) && strpos( $value, ':' ) ) {
					$value = explode( ':', $value );
					$compare = $value[0];
					$value = $value[1];
				}

				if ( ! is_array( $value ) ) {
					$key = self::escape( $key );
					$value = self::escape( $value );
					$compare = self::escape( $compare );
					$where[$key] = "`{$key}` {$compare} '{$value}'";
				} else {
					array_walk( $value, 'self::escape' );
					$where[$key] = "`{$key}` in ('". implode("','", $value)."')";
				}
			}

			$operator = ! in_array( $operator, array( 'AND', 'OR' ) ) ? ' AND ' : ' ' . $operator . ' ';

			$_where = implode( $operator, array_values( $where ) );
		}

		if ( ! empty( $_where ) ) {
			$_where = "WHERE {$_where}";
		}

		return $_where . $_group . $_order . $_limit;
	}

	/**
	 * Escapes the given string value
	 *
	 * @param  string   $value
	 * @return string
	 */
	public static function escape ( $value = false, $match = false ) {
		return Data::escape( $value );
	}

	/**
	 * Escapes the urlencoded string
	 *
	 * @param  string   $value
	 * @return string
	 */
	public static function urlencode ( $value = '' ) {
		if ( ! empty( $value ) ) {
			return self::escape( urldecode( $value ) );
		}

		return "";
	}
}