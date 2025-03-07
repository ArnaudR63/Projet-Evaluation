<?php

try {
    $page = 'home';
    require_once('/realpath/parts/header.php');
    echo get_header($page);
    ?>
    <div id="inscription">
        <h1>Inscrivez-vous !</h1>
        <form method="POST" action="<?= $GLOBALS['siteLocation'] ?>/admin/inscription.php">
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur">
            <input type="password" id="password" name="password" placeholder="Mot de passe">
            <input type="email" id="email" name="email" placeholder="email">
            <input type="checkbox" name="consent" id="consent"><label for="consent">Accepter les conditions
                générales</label>
            <input type="submit" class="button" name="submit" value="S'inscrire">
        </form>
    </div>
    <?php
    require_once($GLOBALS['realpathLocation'] . '/parts/footer.php');
    echo get_footer($page);
} catch (Exception $e) {
    header('Location : ' . $GLOBALS['siteLocation']);
}
?>