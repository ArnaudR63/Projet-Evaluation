<?php
$page = 'admin_settings';
require_once("../parts/header.php");
require_once($GLOBALS['realpathLocation'] . '/sql.php');
echo get_header($page);

if (!isset($_COOKIE['connection'])) {
    header('Location: ' . $GLOBALS['siteLocation'] . '/admin');
} elseif (checkConnection() >= 0) {
    try {
        ?>
        <h1>Paramètres de votre compte</h1>
        <div class="flex">
            <form action="" method="post">
                <fieldset>
                    <legend>Modifier votre email</legend>
                    <input type="text" name="newEmail" id="newEmail" placeholder="Nouvel email">
                    <input type="text" name="newEmail_verify" id="newEmail_verify" placeholder="Confirmer le nouvel email">
                    <input type="hidden" name="action" value="modifyEmail">
                    <input type="submit" value="Modifier">
                </fieldset>
            </form>
            <form action="" method="post">
                <fieldset>
                    <legend>Modifier votre mot de passe</legend>
                    <input type="text" name="oldPassword" id="oldPassword" placeholder="Mot de passe actuel">
                    <input type="text" name="newPassword" id="newPassword" placeholder="Nouveau mot de passe">
                    <input type="text" name="newPassword_verify" id="newPassword_verify"
                        placeholder="Confirmer le nouveau mot de passe">
                    <input type="hidden" name="action" value="modifyPwd">
                    <input type="submit" value="Modifier le mot de passe">
                </fieldset>
            </form>
        </div>
        <form action="" method="POST">
        <fieldset>
                    <legend>Supprimer votre compte (définitif)</legend>
                    <input type="text" name="password" id="password" placeholder="Mot de passe actuel">
                    <input type="text" name="password_verify" id="password_verify"
                        placeholder="Confirmer le mot de passe">
                    <input type="hidden" name="action" value="deleteAccount">
                    <input type="submit" value="Supprimer">
                </fieldset>
        </form>
    <?php } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    echo 'Impossible de vous authentifier';
}

require_once("../parts/footer.php");
echo get_footer($page);

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'modifyEmail') {
        $result = modifyEmail();
        if ($result) {
            echo '<div id="msg"><p>Email modifié avec succès !</p></div>';
        } else {
            echo "<div id='msg'><p>Impossible de modifier l'email</p></div>";
        }
    } elseif ($_POST['action'] === 'modifyPwd') {
        $result = modifyPwd();
        if ($result) {
            echo '<div id="msg"><p>Mot de passe modifié avec succès !</p></div>';
        } else {
            echo "<div id='msg'><p>Impossible de modifier le mot de passe</p></div>";
        }
    } elseif($_POST['action'] === 'deleteAccount') {
        deleteAccount();
    }
}
?>