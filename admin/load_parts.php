<?php
require_once( "/realpath/sql.php" );
require_once( "/realpath/functions.php" );

if ( ! isset( $_COOKIE['connection'] ) ) {
	echo "Accès refusé";
	exit;
}

$role = checkConnection();

if ( $role === 9 ) {
	echo '<div class="carousel-prev"></div><div id="carousel_container">';
	require_once( "./parts/shop.php" );
	require_once( "./parts/blog.php" );
	echo '</div><div class="carousel-next"></div>';
} else {
	echo "Autorisation insuffisante";
}