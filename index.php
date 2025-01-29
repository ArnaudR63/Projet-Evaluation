<?php
try {
    $page = 'home';
    require_once('../parts/header.php');
    echo get_header($page);
    ?>
    <div id="banner" role="slider">
        <div class="element">
            <h1 class="title">Découvrez tous nos produits dérivés</h1>
            <a href="<?php echo $GLOBALS['siteLocation']; ?>/shop?category=goodies" class="button">Visiter la boutique</a>
            <div class="overlay">
                <img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/products/mug-view-2-5.png" alt="Produits dérivés, mug et travel mug">
            </div>
        </div>
        <div class="element">
            <h2 class="title">Découvrez tous nos t-shirts</h2>
            <a href="<?php echo $GLOBALS['siteLocation']; ?>/shop?category=tshirts" class="button">Découvrir toute les
                gammes</a>
            <div class="overlay">
                <img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/products/polo-logo.png" alt="T-shirts et Polo">
            </div>
        </div>
        <div class="element">
            <h2 class="title">Rejoignez nos réseaux</h2>
            <a href="<?php echo $GLOBALS['siteLocation']; ?>/networks" class="button">Rejoignez-nous !</a>
            <div class="overlay">
                <img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/networks.png" alt="Liens sociaux, réseaux">
            </div>
        </div>
    </div>
    <?php
    require_once($GLOBALS['realpathLocation'] . '/parts/footer.php');
    echo get_footer($page);
} catch (Exception $e) {
    echo $e->getMessage() . '<br>';
    echo $e->getFile() . '<br>';
    echo $e->getLine();
}
?>