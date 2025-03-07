<?php

require_once( '/realpath/config.php' );

function connectSql() {
	try {
		$env = parse_ini_file( $GLOBALS['realpathLocation'] . '/.env' );
		$co = new PDO( "mysql:host=" . $env['HOST'] . ";dbname=" . $env['DATABASE'], $env['USERNAME'], $env['PASSWORD'] );
		$co->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $co;
	} catch (PDOException $e) {
		echo $e->getMessage() . '<br>';
	}
}
function executeSql( $co, $sql, $params = [] ) {
	try {
		$stmt = $co->prepare( $sql );
		if ( ! empty( $params ) ) {
			$isAssociative = array_keys( $params ) !== range( 0, count( $params ) - 1 );

			if ( $isAssociative ) {
				foreach ( $params as $key => $value ) {
					$type = is_int( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR;
					$stmt->bindValue( $key, $value, $type );
				}
			} else {
				foreach ( $params as $index => $value ) {
					$type = is_int( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR;
					$stmt->bindValue( $index + 1, $value, $type );
				}
			}
		}
		$stmt->execute();
		if ( str_starts_with( trim( $sql ), 'SELECT' ) ) {
			return $stmt->fetchAll( PDO::FETCH_ASSOC );
		}
		return $stmt->rowCount();
	} catch (PDOException $e) {
		error_log( "Erreur dans executeSql : " . $e->getMessage() . "\n", 3, '/realpath/errors.log' );
		return [];
	} finally {
		$co = null;
	}
}

require_once($GLOBALS['realpathLocation'] . '/sql/shop.php');
require_once($GLOBALS['realpathLocation'] . '/sql/account.php');
require_once($GLOBALS['realpathLocation'] . '/sql/blog.php');