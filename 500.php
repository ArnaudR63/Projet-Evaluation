<?php
try {
	$page = '500';
	require_once('/realpath/parts/header.php');
	echo get_header($page);
	?>
	<div id="banner">
		<h1>Erreur 500 - Erreur serveur</h1>
		<p>Veuillez réessayer plus tard</p>
	</div>
	<?php
	require_once($GLOBALS['realpathLocation'] . '/parts/footer.php');
	echo get_footer($page);
} catch (Exception $e) {
	error_log("Erreur dans Accueil : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
}
?>