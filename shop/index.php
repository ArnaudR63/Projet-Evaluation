<?php
$page = 'shop';
require_once('../parts/header.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');
$data;
if (isset($_GET['category'])) {
	$data = getProducts(array($_GET['category']));
} else {
	$data = getProducts();
}
echo get_header($page);
ob_start();
?>
<h1>Découvrez tous nos produits</h1>
<div id="item_list">
	<div id="filters_list">
		<h2>Liste des filtres disponibles</h2>
		<?php
		$categories = getCategories();
		echo '<div id="filters_content"><ul>';
		foreach ($categories as $cat) {
			echo '<li class="filter">
                <input type="checkbox" ' . (isset($_GET['category']) && strval($_GET['category']) === strval($cat['ID']) ? 'checked' : '') .
				' name="' . htmlspecialchars($cat['ID']) . '" 
                id="' . htmlspecialchars($cat['Name']) . '">
                <label for="' . htmlspecialchars($cat['Name']) . '">
                    <span class="checkmark"></span> ' . str_replace('_', ' ', ucfirst(htmlspecialchars($cat['Name']))) . '
                </label>
              </li>';
		}
		echo '</ul><button class="button" id="reset">Réinitialiser les filtres</button></div>';
		?>
	</div>
	<div id="products_container">
		<?php
		foreach ($data as $product) {
			echo '<div class="product">'
				. '<img src="' . $product['Thumbnail'] . '" alt="' . htmlspecialchars($product['Name']) . '">'
				. '<h2>' . str_replace('_', ' ', ucfirst(htmlspecialchars($product['Name']))) . '</h2>'
				. '<a class="button" href="' . $GLOBALS['siteLocation'] . '/shop/product.php?id=' . $product['ID'] . '" title="Voir le produit : ' . htmlspecialchars($product['Name']) . '">Découvrir</a>'
				. '</div>';
		}
		?>
	</div>
</div>
<?php
echo ob_get_clean();
require_once('../parts/footer.php');
echo get_footer($page);