<?php
function getProducts( $cat = null ) {
	try {
		$co = connectSql();
		$sql = "";
		$params = [];

		if ( ! empty( $cat ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $cat ), '?' ) );
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Products WHERE Category IN ($placeholders)";
			$params = $cat;
		} else {
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Products";
		}

		$products = executeSql( $co, $sql, $params );
		return $products;
	} catch (PDOException $e) {
		error_log( "Erreur dans getProducts : " . $e->getMessage() . "\n", 3, '/realpath/errors.log' );
		return false;
	}
}

function getProduct( $id ) {
	try {
		$co = connectSql();
		$sql = "";
		$params = [];

		if ( ! empty( $id ) ) {
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Products WHERE ID = :id";
			$params = array( ':id' => $id );
		} else {
			throw new Exception( 'Identifiant de produit non fournis' );
		}

		$products = executeSql( $co, $sql, $params );
		return $products;
	} catch (PDOException $e) {
		error_log( "Erreur dans getProduct : " . $e->getMessage() . "\n", 3, '/realpath/errors.log' );
		return false;
	}
}

function getCategories() {
	try {
		$co = connectSql();
		$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Categories";
		$cat = executeSql( $co, $sql );
		return $cat;
	} catch (PDOException $e) {
		error_log( "Erreur dans getCategories : " . $e->getMessage() . "\n", 3, '/realpath/errors.log' );
		return false;
	}
}

function getCategory( $id ) {
	try {
		$co = connectSql();
		$sql = "";
		$params = [];

		if ( ! empty( $id ) ) {
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Categories WHERE ID = :id";
			$params = array( ':id' => $id );
		} else {
			throw new Exception( 'Identifiant de catégorie non fournis' );
		}

		$products = executeSql( $co, $sql, $params );
		return $products;
	} catch (PDOException $e) {
		error_log( "Erreur dans getCategory : " . $e->getMessage() . "\n", 3, '/realpath/errors.log' );
		return false;
	}
}