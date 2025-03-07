<?php
require_once('/realpath/config.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');

if (isset($_POST['consent']) && $_POST['consent'] === 'on') {
    $username = isset($_POST['username']) ? strip_tags($_POST['username']) : false;
    $password = isset($_POST['password']) ? $_POST['password'] : false;
    $mail = filter_var($_POST['email']) ? $_POST['email'] : false;

    if ($username && $password && $mail) {
        echo addUser($username, $password, $mail);
    } else {
        echo 'non';
    }
} else {
    echo 'non';
}