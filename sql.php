<?php

require_once( '../config.php' );

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
		error_log( "Erreur dans executeSql : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return [];
	} finally {
		$co = null;
	}
}

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
		error_log( "Erreur dans getProducts : " . $e->getMessage() . "\n", 3, '../errors.log' );
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
		error_log( "Erreur dans getProduct : " . $e->getMessage() . "\n", 3, '../errors.log' );
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
		error_log( "Erreur dans getCategories : " . $e->getMessage() . "\n", 3, '../errors.log' );
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
			throw new Exception( 'Identifiant de produit non fournis' );
		}

		$products = executeSql( $co, $sql, $params );
		return $products;
	} catch (PDOException $e) {
		error_log( "Erreur dans getCategory : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return false;
	}
}

function addUser( $username, $password, $mail ) {
	if ( empty( $username ) || empty( $password ) || empty( $mail ) ) {
		return "Une erreur est survenue. Veuillez compléter correctement tous les champs.";
	}

	if ( ! filter_var( $mail, FILTER_VALIDATE_EMAIL ) ) {
		return "Une erreur est survenue. Veuillez compléter correctement tous les champs.";
	}

	$pwd = password_hash( $password, PASSWORD_BCRYPT );
	if ( ! $pwd ) {
		error_log( "Échec du hachage du mot de passe pour l'utilisateur : $username" . "\n", 3, '../errors.log' );
		return "Une erreur est survenue. Veuillez réessayer plus tard.";
	}

	try {
		$co = connectSql();
		$sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "Users (Username, Pwd, Mail) VALUES (:username, :password, :email)";

		$params = [ 
			':username' => $username,
			':password' => $pwd,
			':email' => $mail
		];

		$result = executeSql( $co, $sql, $params );

		if ( ! empty( $result ) ) {
			return "Utilisateur ajouté avec succès.";
		} else {
			throw new PDOException( "Erreur SQL lors de l'insertion de l'utilisateur : $username" );
		}
	} catch (PDOException $e) {
		error_log( "Erreur dans addUser : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return "Une erreur est survenue. Veuillez réessayer plus tard.";
	}
}

function connection( $username, $password ) {
	try {
		$co = connectSql();

		$sql = filter_var( $username, FILTER_VALIDATE_EMAIL )
			? "SELECT * FROM " . $GLOBALS['table_prefix'] . "Users WHERE BINARY mail = :username"
			: "SELECT * FROM " . $GLOBALS['table_prefix'] . "Users WHERE BINARY username = :username";

		$params = [ ':username' => $username ];
		$result = executeSql( $co, $sql, $params );

		if ( empty( $result ) ) {
			echo 'inscription';
			return false;
		}

		$user = $result[0];

		if ( password_verify( $password, $user['Pwd'] ) ) {
			$token = base64_encode( hash( 'sha256', uniqid() . $user['ID'] . $user['Mail'], true ) );
			$sqlUpdate = "UPDATE " . $GLOBALS['table_prefix'] . "Users SET Token = :token WHERE ID = :id";
			$paramsUpdate = [ 
				':token' => $token,
				':id' => $user['ID']
			];
			$stmt = $co->prepare( $sqlUpdate );
			$stmt->execute( $paramsUpdate );

			setcookie(
				'connection',
				$token,
				time() + 43200, // 12 heures de validité
				'/',
				'',
				true,
				true
			);

			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		error_log( "Erreur lors de la connexion : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return false;
	}
}

function checkConnection() {
	if ( ! isset( $_COOKIE['connection'] ) ) {
		return false;
	}

	$token = $_COOKIE['connection'];

	try {
		$co = connectSql();

		$sql = "SELECT role FROM " . $GLOBALS['table_prefix'] . "Users WHERE token = :token";
		$params = array( ':token' => $token );

		$result = executeSql( $co, $sql, $params );

		if ( empty( $result ) ) {
			return false;
		}

		return $result[0]['role'];
	} catch (PDOException $e) {
		error_log( "Erreur lors de la vérification de la connexion : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return false;
	}
}

function getUserID() {
	if ( ! isset( $_COOKIE['connection'] ) ) {
		return false;
	}

	$token = $_COOKIE['connection'];

	try {
		$co = connectSql();

		$sql = "SELECT ID FROM " . $GLOBALS['table_prefix'] . "Users WHERE token = :token";
		$params = array( ':token' => $token );

		$result = executeSql( $co, $sql, $params );

		if ( empty( $result ) ) {
			return false;
		}

		return $result[0]['ID'];
	} catch (PDOException $e) {
		error_log( "Erreur lors de la récupération de l'ID : " . $e->getMessage() . "\n", 3, '../errors.log' );
		return false;
	}
}

function logout() {
	if ( isset( $_COOKIE['connection'] ) ) {
		$cookie = $_COOKIE['connection'];

		try {
			$co = connectSql();

			$sql = "UPDATE " . $GLOBALS['table_prefix'] . "Users SET token = NULL WHERE token = :token";
			$stmt = $co->prepare( $sql );
			$stmt->bindParam( ':token', $cookie, PDO::PARAM_STR );
			$stmt->execute();

			setcookie(
				'connection',
				'',
				time() - 3600,
				'/',
				'',
				true,
				true
			);

			return true;
		} catch (PDOException $e) {
			error_log( "Erreur lors de la déconnexion : " . $e->getMessage() . "\n", 3, '../errors.log' );
			return false;
		}
	} else {
		header( "Location: " . $GLOBALS['siteLocation'] . "/admin" );
		exit;
	}
}

function modifyEmail() {
	$id = getUserID();
	$co = connectSql();
	$sql = 'SELECT Mail FROM ' . $GLOBALS['table_prefix'] . 'Users WHERE ID = :id';
	$result = executeSql( $co, $sql, array( ':id' => $id ) );
	if ( empty( $result ) ) {
		return false;
	}
	$oldMail = $result[0]['Mail'];
	$headers = array(
		'From' => 'contact@developp-et-vous.com',
		'X-Mailer' => 'PHP/' . phpversion()
	);
	if ( filter_var_array( array( $_POST['newEmail'], $_POST['newEmail_verify'] ), FILTER_VALIDATE_EMAIL ) ) {
		if ( $_POST['newEmail'] === $_POST['newEmail_verify'] && $_POST['newEmail'] !== $oldMail && ! empty( $_POST['newEmail'] ) ) {
			$sqlUpdate = 'UPDATE ' . $GLOBALS['table_prefix'] . 'Users SET Mail = :mail WHERE ID = :id';
			$result = executeSql( $co, $sqlUpdate, array(
				':mail' => $_POST['newEmail'],
				':id' => $id
			) );
			mail(
				$oldMail,
				'Modification de votre Email sur le site Développ et vous',
				"Votre mail a été modifié avec succès ! \r\n\r\nSi vous n'êtes pas à l'origine de cette modification, contactez-nous par mail : \r\ncontact@developp-et-vous.com",
				$headers
			);
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function modifyPwd() {
	$id = getUserID();
	$co = connectSql();
	$sql = 'SELECT Pwd, Mail FROM ' . $GLOBALS['table_prefix'] . 'Users WHERE ID = :id';
	$result = executeSql( $co, $sql, array( ':id' => $id ) );
	if ( empty( $result ) ) {
		return false;
	}

	$oldPwd = $result[0]['Pwd'];
	$mail = $result[0]['Mail'];
	$headers = array(
		'From' => 'contact@developp-et-vous.com',
		'X-Mailer' => 'PHP/' . phpversion()
	);

	$newPwd = $_POST['newPassword'];
	$newPwdVerify = $_POST['newPassword_verify'];
	if ( ! password_verify( $_POST['oldPassword'], $oldPwd ) ) {
		return false;
	}

	if ( $newPwd === $newPwdVerify ) {
		$hashedPwd = password_hash( $newPwd, PASSWORD_BCRYPT );
		$sqlUpdate = 'UPDATE ' . $GLOBALS['table_prefix'] . 'Users SET Pwd = :pwd WHERE ID = :id';
		$updateResult = executeSql( $co, $sqlUpdate, array( ':pwd' => $hashedPwd, ':id' => $id ) );
		if ( $updateResult ) {
			mail(
				$mail,
				'Modification de votre mot de passe sur le site Développ et vous',
				"Votre mot de passe a été modifié avec succès ! \r\n\r\nSi vous n'êtes pas à l'origine de cette modification, contactez-nous par mail : \r\ncontact@developp-et-vous.com",
				$headers
			);
			return true;
		} else {
			return false;
		}
	}
	return false;
}

function deleteAccount() {
	if ( $_POST['password'] === $_POST['password_verify'] ) {
		$id = getUserID();
		$co = connectSql();
		$sql = 'SELECT Pwd, Mail FROM ' . $GLOBALS['table_prefix'] . 'Users WHERE ID = :id';
		$result = executeSql( $co, $sql, array( ':id' => $id ) );
		if ( empty( $result ) ) {
			return false;
		}
		$pwd = $result[0]['Pwd'];
		if ( password_verify( $_POST['password'], $pwd ) ) {
			$sql = 'DELETE FROM ' . $GLOBALS['table_prefix'] . 'Users WHERE ID = :id';
			$result = executeSql( $co, $sql, array( ':id' => $id ) );

			if ( $result ) {
				setcookie(
					'connection',
					'',
					time() - 3600,
					'/',
					'',
					true,
					true
				);
				echo "<script>alert('Votre compte a bien été supprimé');window.location.assign('" . $GLOBALS['siteLocation'] . "');</script>";
			} else {
				echo "<script>alert('Votre compte n\'a pas pu être supprimé, veuillez réessayer ultérieurement.')</script>";
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
}