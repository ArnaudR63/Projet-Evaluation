<?php
$page = 'product';
require_once( '../parts/header.php' );
require_once( $GLOBALS['realpathLocation'] . '/sql.php' );
$data;
if ( isset( $_GET['id'] ) ) {
	$data = getProduct( $_GET['id'] )[0];
} else {
	header( 'Location: ' . $GLOBALS['siteLocation'] . '/shop' );
}
if ( empty( $data ) ) {
	header( 'Location: ' . $GLOBALS['siteLocation'] . '/shop' );
}

echo get_header( $page );
ob_start();
?>
<div class="flex">
	<div class="column">
		<img src="<?= $data['Thumbnail'] ?>" alt="<?= $data['Name'] ?>">
		<h1><?= $data['Name'] ?></h1>
	</div>
	<div class="column">
		<p><?= $data['Description'] ?></p>
		<a class="button" title="Pré-commander ce produit"
			href="<?php echo $GLOBALS['siteLocation'] . '/contact?pName=' . $data['Name']; ?>">Pré-commander</a>
	</div>
</div>
<?php
echo ob_get_clean();
require_once( '../parts/footer.php' );
echo get_footer( $page );