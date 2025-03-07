<?php
require_once( '/realpath/config.php' );
require_once( $GLOBALS['realpathLocation'] . '/sql.php' );
require_once( $GLOBALS['realpathLocation'] . '/functions.php' );

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$data = json_decode( $_POST['data'], true );

	$categoriesArrayID = isset( $data['categories'] ) ? $data['categories'] : [];
	$keywords = isset( $data['keywords'] ) ? $data['keywords'] : [];

	$categories = array_map( 'intval', $categoriesArrayID );
	$search = array_map( 'trim', $keywords );

	$result = getBlog( $categories, $search );

	if ( is_array( $result ) && ! empty( $result ) ) {
		$html = '';

		foreach ( $result as $post ) {
			$title = htmlspecialchars( $post['Title'] );
			$content = description( htmlspecialchars( $post['Content'] ) );
			$html .= '<div class="post">'
				. '<img src="' . $post['Preview'] . '" alt="' . $title . '">'
				. '<h2>' . str_replace( '_', ' ', ucfirst( $title ) ) . '</h2>'
				. "<p>$content</p>"
				. '<a class="button" href="' . $GLOBALS['siteLocation'] . '/blog/post.php?id=' . $post['ID'] . '" title="Voir le produit : ' . $title . '">Découvrir</a>'
				. '</div>';
		}

		echo json_encode( [ 'html' => $html ] );
	} else {
		echo json_encode( [ 'html' => '<p>Aucun résultat trouvé.</p>' ] );
	}
} else {
	echo json_encode( [ 'error' => 'Requête invalide' ] );
}
exit();
