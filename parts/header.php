<?php
require_once('/realpath/config.php');
require_once('/realpath/functions.php');

function isConnected()
{
	if (isset($_COOKIE['connection'])) {
		return true;
	} else {
		return false;
	}
}

function get_header($page): string
{
	global $page;

	$pagename = preg_filter('/(\/projet-evaluation\/)/', '', $_SERVER['REQUEST_URI']);

	switch ($pagename) {
		case '':
			$page = 'home';
			break;
		case '404':
			$page = 'error';
			break;
		case '500':
			$page = 'error';
			break;
		case 'networks/':
			$page = 'networks';
			break;
		case 'shop/':
			$page = 'shop';
			break;
		case 'shop/product.php':
			$page = 'product';
			break;
		case 'blog/':
			$page = 'blog';
			break;
		case 'blog/post.php':
			$page = 'post';
			break;
		case 'contact/':
			$page = 'contact';
			break;
		case 'admin/':
			$page = 'admin';
			break;
	}

	$html = '<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/style.css">';

	switch ($page) {
		case 'home':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/home.css">'
				. '<title>Accueil du site Développ et vous</title>';
			break;
		case 'networks':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/networks.css">'
				. '<title>Réseaux sociaux de Développ et vous</title>';
			break;
		case 'shop':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/shop.css">'
				. '<title>Boutique de Développ et vous</title>';
			break;
		case 'product':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/item.css">'
				. '<title>' . 'Produit' . '</title>';
			break;
		case 'blog':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/blog.css">'
				. '<title>Blog de Développ et vous</title>';
			break;
		case 'post':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/item.css">'
				. '<title>' . 'Post ' . '</title>';
			break;
		case 'contact':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/contact.css">'
				. '<title>Contacter Développ et vous</title>';
			break;
		case 'admin':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/admin/assets/styles/admin.css">'
				. '<title>Page administrateur</title>';
			break;
		case 'admin_settings':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/admin/assets/styles/admin.css">'
				. '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/admin/assets/styles/admin_settings.css">'
				. '<title>Réglages du compte</title>';
			break;
		case '404':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/error.css">'
				. '<title>Erreur 404</title>';
			break;
		case '500':
			$html .= '<link rel="stylesheet" href="' . $GLOBALS['siteLocation'] . '/assets/styles/error.css">'
				. '<title>Erreur 500</title>';
			break;
	}

	$html .= '</head>
	<body> 
		<header>
			<nav>
				<a href="' . $GLOBALS['siteLocation'] . '" title="Revenir à l\'accueil" id="home">
					<img src="' . $GLOBALS['siteLocation'] . '/assets/images/logo.png" alt="Logo Boutique Développ et vous">
				</a>
				<ul id="menu">
					<li class="item"><a href="' . $GLOBALS['siteLocation'] . '/networks">Réseaux</a></li>
					<li class="item"><a href="' . $GLOBALS['siteLocation'] . '/shop">Boutique</a></li>
					<li class="item"><a href="' . $GLOBALS['siteLocation'] . '/blog">Blog</a></li>
					<li class="item"><a href="' . $GLOBALS['siteLocation'] . '/contact">Contact</a></li>
				</ul>
				<div id="links">
					<div id="account">';

	if (isConnected()) {
		$html .= "<a href='#' title=\"Ouvrir le menu d'utilisateur\" onclick='toggleAdmin()'>
						<img src='" . $GLOBALS['siteLocation'] . "/assets/images/user.svg' alt='Icône utilisateur'>
					</a>
					<ul>
					<li><a href='" . $GLOBALS['siteLocation'] . "/admin/'>Accéder au panneau d'administration</a></li>
					<li><a href='" . $GLOBALS['siteLocation'] . "/admin/settings.php'>Paramètres de mon compte</a></li>
					<li><a href='" . $GLOBALS['siteLocation'] . "/admin/logout.php'>Se déconnecter</a></li>
					</ul>";
	} else {
		$html .= "<a href='" . $GLOBALS['siteLocation'] . "/admin/' title=\"Ouvrir le menu d'utilisateur\">
						<img src='" . $GLOBALS['siteLocation'] . "/assets/images/user.svg' alt='Icône utilisateur'>
					</a>";
	}

	$html .= '</div>
	<div id="toggle-menu">
						<svg width="64" height="64" viewBox="0 0 64 64" fill="none" onclick="toggleMenu()"
							xmlns="http://www.w3.org/2000/svg">
							<rect x="1" y="1" width="62" height="62" rx="31" stroke="#D06027" stroke-width="2" />
							<g id="lines">
								<path id="line1" d="M12 23L52 23" stroke="#D06027" stroke-width="4" stroke-linecap="round" />
								<path id="line2" d="M12 40L52 40" stroke="#D06027" stroke-width="4" stroke-linecap="round" />
							</g>
						</svg>
					</div>';



	$html .= '</div>
			</nav>
		</header>
	<main>';
	return $html;
}