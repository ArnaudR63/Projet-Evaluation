<?php
$page = 'admin';
require_once( "/realpath/parts/header.php" );
require_once( $GLOBALS['realpathLocation'] . '/sql.php' );
echo get_header( $page );

if ( isset( $_GET['msg'] ) ) {
	$msg = htmlspecialchars( $_GET['msg'] );
	switch ( $msg ) {
		case 'correct':
			echo '<div id="msg"><p>Connexion réussie !</p></div>';
			break;
		case 'incorrect':
			echo '<div id="msg"><p>Impossible de se connecter avec ces identifiants</p></div>';
			break;
		case 'empty':
			echo '<div id="msg"><p>Veuillez entrez vos identifiants</p></div>';
			break;
		case 'added':
			echo '<div id="msg"><p>Item ajoutée avec succès !</p></div>';
			break;
		case 'notAdded':
			echo '<div id="msg"><p>Impossible d\'ajouter l\'item</p></div>';
			break;
		case 'updated':
			echo '<div id="msg"><p>Item modifié avec succès !</p></div>';
			break;
		case 'notUpdated':
			echo '<div id="msg"><p>Impossible de modifier l\'item</p></div>';
			break;
		case 'deleted':
			echo '<div id="msg"><p>Item supprimée avec succès !</p></div>';
			break;
		case 'notDeleted':
			echo '<div id="msg"><p>Impossible de supprimer l\'item</p></div>';
			break;
		case 'noId':
			echo "<div id='msg'><p>L'identifiant n'existe pas</p></div>";
			break;
		case 'disconnected':
			echo "<div id='msg'><p>Vous êtes bien déconnecté(e)</p></div>";
			break;
		case 'noDisconnected':
			echo "<div id='msg'><p>Impossible de se déconnecter, veuillez réessayer ultérieurement</p></div>";
			break;
	}
}

if ( ! isset( $_COOKIE['connection'] ) ) {
	ob_start();
	?>
	<div id="connection">
		<h1>Entrez vos identifiants</h1>
		<form action="<?php echo $siteLocation . '/admin/connection.php'; ?>" method="POST">
			<input type="text" name="user" id="user" placeholder="Votre identifiant ou email">
			<input type="password" name="password" id="password" placeholder="Votre mot de passe">
			<input type="submit" value="Connexion">
		</form>
		<a href="<?php echo $siteLocation . '/inscription.php'; ?>">S'inscrire</a>
	</div>
	<?php
	echo ob_get_clean();
} elseif ( checkConnection() === 9 ) {
	try {
		?>
		<div id="admin_container"></div>
	<?php } catch (Exception $e) {
		echo $e->getMessage();
	}
} elseif ( checkConnection() >= 1 && checkConnection() < 9 ) {
	try {
		?>
		<div id="admin_container"></div>
	<?php } catch (Exception $e) {
		echo $e->getMessage();
	}
} else {
	echo 'Autorisation insuffisante';
}

require_once( "/realpath/parts/footer.php" );
echo get_footer( $page );
?>