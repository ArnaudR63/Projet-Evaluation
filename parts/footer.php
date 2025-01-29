<?php
function get_footer( $page ): string {
	$html = '</main>
<footer>
    <p>©' . date( 'Y' ) . ' - Développ et vous <span>-</span></p>
    <a href="https://www.developp-et-vous.com/mentions-legales">Mentions légales</a>
</footer>
<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/header.js" async defer></script>';

	switch ( $page ) {
		case 'home':
			break;
		case 'shop':
			$html .= '<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/shop.js" async defer></script>';
			break;
		case 'product':
			$html .= '<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/product.js" async defer></script>';
			break;
		case 'contact':
			$html .= '<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/contact.js" async defer></script>';
			break;
		case 'admin':
			$html .= '<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/admin.js" async defer></script>';
			break;
		case 'admin_settings':
			$html .= '<script src="' . $GLOBALS['siteLocation'] . '/assets/scripts/admin.js" async defer></script>';
			break;
	}

	$html .= '</body>
</html>';
	return $html;
}