<?php
require_once('/realpath/config.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');

$user = isset($_POST['user']) ? $_POST['user'] : false;
$password = isset($_POST['password']) ? $_POST['password'] : false;

if ($user && $password) {
    $result = connection($user, $password);
    if ($result) {
        header("Location: $siteLocation/admin?msg=correct");
    } else {
        header("Location: $siteLocation/admin?msg=incorrect");
    }
} else {
    header("Location: $siteLocation/admin?msg=empty");
}