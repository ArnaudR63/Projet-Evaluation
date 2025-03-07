<?php
try {
    $page = '404';
    require_once('/realpath/parts/header.php');
    echo get_header($page);
    ?>
    <div id="banner">
        <h1>Erreur 404 - La page n'existe pas</h1>
        <a class="button" href=<?= $GLOBALS['siteLocation'] ?> title="Page d'accueil">Revenir à l'accueil</a>
    </div>
    <?php
    require_once($GLOBALS['realpathLocation'] . '/parts/footer.php');
    echo get_footer($page);
} catch (Exception $e) {
    error_log("Erreur dans Accueil : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
}
?>