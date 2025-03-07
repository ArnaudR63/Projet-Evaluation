<?php
$page = 'blog';
require_once('../parts/header.php');
require_once($GLOBALS['realpathLocation'] . '/sql.php');
$data;
if (isset($_GET['category'])) {
	$data = getBlog(array($_GET['category']));
} else {
	$data = getBlog();
}
$categories = getPostCategories();
echo get_header($page);
ob_start();
?>
<h1>Découvrez nos actualités !</h1>
<div id="item_list">
	<div id="filters_list">
		<h2>Filtrer le blog</h2>
		<div id="filters_content">
			<div class="toggled">
				<div class="flex">
					<input type="search" name="keyword" id="keyword">
					<button class="button" id="search"><img src="../assets/images/loupe.svg" alt="Chercher"></button>
				</div>
				<button id="reset">Réinitialiser les filtres</button>
			</div>
			<ul id="filters_cat">
				<?php
				foreach ($categories as $cat) {
					echo '<li class="filter">
				<input type="checkbox" ' . (isset($_GET[' category']) && strval($_GET['category']) === strval(
							$cat['ID']
						) ? 'checked' : '') . ' name="' . htmlspecialchars($cat['ID']) . '" 
				id="' . htmlspecialchars($cat['Name']) . '">
				<label for="' . htmlspecialchars($cat['Name']) . '">
					<span class="checkmark"></span> ' . str_replace('_', ' ', ucfirst(htmlspecialchars($cat['Name']))) . '
				</label>
			  </li>';
				}
				?>
			</ul>
		</div>
	</div>
	<div id="posts_container">
		<?php
		if (!empty($data)) {
			foreach ($data as $post) {
				echo '<div class="post">'
					. '<img src="' . $post['Preview'] . '" alt="' . htmlspecialchars($post['Title']) . '">'
					. '<h2>' . str_replace('_', ' ', ucfirst(htmlspecialchars($post['Title']))) . '</h2>'
					. '<p>' . description(htmlspecialchars($post['Content'])) . '</p>'
					. '<a class="button" href="' . $GLOBALS['siteLocation'] . '/blog/post.php?id=' . $post['ID'] . '" title="Voir le produit : ' . htmlspecialchars($post['Title']) . '">Découvrir</a>'
					. '</div>';
			}
		} else {
			echo '<h2>Aucun post trouvé !</h2>';
		}
		?>
	</div>
</div>
<?php
echo ob_get_clean();
require_once('../parts/footer.php');
echo get_footer($page);