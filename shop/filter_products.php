<?php
require_once('../config.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');

$categoriesArrayID = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];
$categories = array();
foreach ($categoriesArrayID as $catID) {
    array_push($categories, (int) $catID);
}
$data = getProducts($categories);
if (!empty($data)) {
    foreach ($data as $product) {
        echo '<div class="product">'
            . '<img src="' . $product['Thumbnail'] . '" alt="' . htmlspecialchars($product['Name']) . '">'
            . '<h2>' . str_replace('_', ' ', ucfirst(htmlspecialchars($product['Name']))) . '</h2>'
            . '<a class="button" href="' . $GLOBALS['siteLocation'] . '/shop/product.php?id=' . $product['ID'] . '" title="Voir le produit : ' . htmlspecialchars($product['Name']) . '">Découvrir</a>'
            . '</div>';
    }
} else {
    echo '<h2>Aucun produit trouvé.</h2>';
}
?>