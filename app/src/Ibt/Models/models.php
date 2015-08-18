<?php

namespace Ibt;

use \App\Includes\Dbase;
use \Ibt\Errors;
use \Ibt\Data;

class Models {

	protected static $_db;

	protected static $_compare_property;

	private static function query ( $query = "" ) {

		$data = Data::query( $query );

		if ( isset( $data->error ) && ! empty( $data->error ) ) {
			Errors::set ( 'database', $data->error, true );
			return null;
		}

		return $data;
	}

	public static function fetch ( $query = '', $object = false ) {
		$data = array();

		if ( ! empty($query) && is_string( $query ) ) {

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

	public static function get ( $where = array(), $object = false ) {
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

		if ( !empty( $object ) && count( $data ) === 1 ) {
			return array_pop( $data );
		}

		return $data;
	}

	public static function insert ( $data = array () ) {

		if ( ! empty ( $data ) ) {

			$columns = implode('`, `', array_keys( $data ) );
			$repeat = str_repeat ( ', ?', count($columns) );
			$values = array();

			foreach ($data as $column => $value) {
				$values[] = is_string( $value ) ? "'" . self::escape( $value ) . "'" : self::escape( $value );
			}

			$values = implode(', ', $values);

			$query = "INSERT INTO `" . static::$_table . "` (`" . static::$_primary . "`, `{$columns}`) VALUES (null, {$values})";

			static::query( $query );

			return Data::insertId();
		}

		return false;
	}

	public static function where( $where = array (), $_and = ' AND ', $match = false){
		$_where = "";
		$_order = "";
		$_group = "";
		$_limit = "";

		if ( isset($where['order']) && is_array($where['order']) && !empty($where['order']) ) {
			if ( isset($where['order']['DESC'] ) ) {
				$_order = "DESC";
			} else if ( isset($where['order']['ASC']) ) {
				$_order = "ASC";
			}

			if ( $_order ) {
				if ( ! empty($where['order'][$_order]) ) {
					if ( is_array($where['order'][$_order]) ) {
						$_order = " ORDER BY `" . implode( "`, `", array_map( 'self::escape', $where['order'][$_order] ) ) . "` {$_order}";
					} else {
						$_order = " ORDER BY " . self::escape( $where['order'][$_order] ) . " {$_order}";
					}

					unset($where['order']);
				}
			}
		}

		if ( isset($where['group']) && !empty($where['group']) ) {
			$_group = " GROUP BY `" . implode('`, `', $where['group']) . "`";
			unset($where['group']);
		}


		if ( isset($where['limit']) && !empty($where['limit']) ) {
			$_limit = (int) $where['limit'];
			if ( $_limit ) {
				$_limit = " LIMIT {$_limit}";
				unset($where['limit']);
			}
		}


		if ( is_array( $where ) && !empty( $where ) ) {
			$operator = "=";

			foreach ( $where as $key => $value ) {
				if ( is_string($value) && strpos($value, ':') ) {
					$value = explode(':', $value);
					$operator = $value[0];
					$value = $value[1];
				}

				if ( !is_array( $value ) ) {
					$key = self::escape($key);
					$value = self::escape($value);
					$where[$key] = "`{$key}` {$operator} '{$value}'";
				} else {
					array_walk($value, 'self::escape');
					$where[$key] = "`{$key}` in ('". implode("','", $value)."')";
				}
			}

			$_and = ! in_array($_and, array(' AND ', ' OR ') ) ? ' AND ' : $_and;

			$_where = implode($_and, array_values($where) );
		}

		if ( !empty($_where) ) {
			$_where = "WHERE {$_where}";
		}

		return $_where . $_group . $_order . $_limit;
	}

	public static function escape ( $value = false, $match = false ) {

		if ( empty( $value ) ) {
			return "";
		}

		$value = (string) $value;
		$value = strip_tags( $value );
		$value = str_replace( "'%s'", '%s', $value ); // in case someone mistakenly already singlequoted it
		$value = str_replace( '"%s"', '%s', $value ); // doublequote unquoting
		$value = preg_replace( '|(?<!%)%f|' , '%F', $value ); // Force floats to be locale unaware
		$value = preg_replace( '|(?<!%)%s|', "'%s'", $value ); // quote the strings, avoiding escaped strings like %%s

		return Data::escape( $value );
	}

	public static function urlencode ( $query = '' ) {
		if ( ! empty( $query ) ) {
			return self::escape( urldecode( $query ) );
		}

		return "";
	}
}