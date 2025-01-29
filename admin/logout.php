<?php
require_once('../config.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');

$unconnect = logout();
if ($unconnect) {
    header("Location: " . $GLOBALS['siteLocation'] . "/admin?msg=disconnected");
} else {
    header("Location: " . $GLOBALS['siteLocation'] . "/admin?msg=noDisconnected");
}