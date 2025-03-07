<?php
require_once('/realpath/config.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');

$unconnect = logout();
if ($unconnect) {
    header("Location: " . $GLOBALS['siteLocation'] . "/admin?msg=disconnected");
} else {
    header("Location: " . $GLOBALS['siteLocation'] . "/admin?msg=noDisconnected");
}