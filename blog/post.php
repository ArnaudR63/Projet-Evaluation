<?php
$page = 'post';
require_once('../parts/header.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');
$data;
if (isset($_GET['id'])) {
    $data = getPost($_GET['id'])[0];
} else {
    header('Location: ' . $GLOBALS['siteLocation'] . '/blog');
}
if (empty($data)) {
    header('Location: ' . $GLOBALS['siteLocation'] . '/blog');
}

echo get_header($page);
ob_start();
?>
<div class="flex">
    <div class="column">
        <img src="<?= $data['Preview'] ?>" alt="<?= $data['Title'] ?>">
        <h1><?= str_replace('_', ' ', ucfirst(htmlspecialchars($data['Title']))) ?></h1>
    </div>
    <div class="column">
        <p><?= $data['Content'] ?></p>
    </div>
</div>
<?php
echo ob_get_clean();
require_once('../parts/footer.php');
echo get_footer($page);