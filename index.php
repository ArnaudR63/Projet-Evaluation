<?php
try {
	$page = 'home';
	require_once('/realpath/parts/header.php');
	echo get_header($page);
	?>
	<div id="banner">
		<div class="element">
			<h1 class="title">Découvrez tous nos produits dérivés</h1>
			<p class="text">
				Plongez dans l'univers de nos produits dérivés et exprimez votre passion au quotidien ! Nous avons
				sélectionné
				avec soin une gamme d'accessoires uniques qui allient qualité et originalité. Que vous soyez un amateur de
				mugs pour savourer votre café du matin, ou que vous recherchiez un travel mug pour vos déplacements,
				nos articles sauront répondre à vos attentes. Profitez d'un design soigné, de matériaux résistants et d'une
				touche de style qui ne passera pas inaperçue. Découvrez sans plus attendre notre collection et trouvez
				le produit qui correspond à votre personnalité !
			</p>
			<a href="<?php echo $GLOBALS['siteLocation']; ?>/shop?category=10" class="button">Visiter la boutique</a>
			<div class="overlay">
				<img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/products/mug-view-2-5.png"
					alt="Produits dérivés, mug et travel mug">
			</div>
		</div>
		<div class="element">
			<h2 class="title">Découvrez tous nos t-shirts</h2>
			<p class="text">
				Affirmez votre style avec notre collection de t-shirts et polos conçus pour allier confort et esthétique.
				Fabriqués à partir de matières de qualité, nos vêtements sont doux au toucher, agréables à porter et
				résistants
				aux lavages répétés. Nos designs exclusifs ont été pensés pour convenir à toutes les occasions, que ce soit
				pour
				une sortie entre amis, une journée détente ou même pour un look plus sophistiqué. En choisissant nos
				t-shirts,
				vous optez pour un style moderne et intemporel, tout en profitant d'un excellent rapport qualité-prix.
				Faites
				votre choix parmi nos différentes gammes et trouvez la tenue qui vous correspond parfaitement !
			</p>
			<a href="<?php echo $GLOBALS['siteLocation']; ?>/shop?category=8" class="button">Découvrir toute la gamme</a>
			<div class="overlay">
				<img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/products/polo-logo.png"
					alt="T-shirts et Polo">
			</div>
		</div>
		<div class="element">
			<h2 class="title">Rejoignez nos réseaux</h2>
			<p class="text">
				Ne manquez plus aucune actualité et entrez dans les coulisses de notre marque en nous rejoignant sur les
				réseaux
				sociaux ! Découvrez en avant-première nos nouvelles collections, profitez d'offres exclusives et échangez
				avec
				notre communauté passionnée. Nous partageons régulièrement des inspirations, des conseils et des contenus
				exclusifs
				pour vous tenir informé de toutes nos nouveautés. Rejoignez-nous dès maintenant et soyez au cœur de notre
				aventure !
				Commentez, partagez et interagissez avec nous pour faire partie d'une communauté dynamique et engagée.
				C'est ensemble que nous écrivons l'histoire !
			</p>
			<a href="<?php echo $GLOBALS['siteLocation']; ?>/networks" class="button">Rejoignez-nous !</a>
			<div class="overlay">
				<img src="<?php echo $GLOBALS['siteLocation']; ?>/assets/images/networks.png" alt="Liens sociaux, réseaux">
			</div>
		</div>
	</div>
	<?php
	require_once($GLOBALS['realpathLocation'] . '/parts/footer.php');
	echo get_footer($page);
} catch (Exception $e) {
	error_log("Erreur dans Accueil : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
}
?>