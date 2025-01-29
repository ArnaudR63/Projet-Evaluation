<?php
$page = 'admin';
require_once( "../parts/header.php" );
require_once( $GLOBALS['realpathLocation'] . '/sql.php' );
echo get_header( $page );

if ( isset( $_GET['msg'] ) ) {
	$msg = htmlspecialchars( $_GET['msg'] );
	switch ( $msg ) {
		case 'correct':
			echo '<div id="msg"><p>Connexion réussie !</p></div>';
			break;
		case 'incorrect':
			echo '<div id="msg"><p>Impossible de se connecter avec ces identifiants</p></div>';
			break;
		case 'empty':
			echo '<div id="msg"><p>Veuillez entrez vos identifiants</p></div>';
			break;
		case 'added':
			echo '<div id="msg"><p>Item ajoutée avec succès !</p></div>';
			break;
		case 'notAdded':
			echo '<div id="msg"><p>Impossible d\'ajouter l\'item</p></div>';
			break;
		case 'updated':
			echo '<div id="msg"><p>Item modifié avec succès !</p></div>';
			break;
		case 'notUpdated':
			echo '<div id="msg"><p>Impossible de modifier l\'item</p></div>';
			break;
		case 'deleted':
			echo '<div id="msg"><p>Item supprimée avec succès !</p></div>';
			break;
		case 'notDeleted':
			echo '<div id="msg"><p>Impossible de supprimer l\'item</p></div>';
			break;
		case 'noId':
			echo "<div id='msg'><p>L'identifiant n'existe pas</p></div>";
			break;
		case 'disconnected':
			echo "<div id='msg'><p>Vous êtes bien déconnecté(e)</p></div>";
			break;
		case 'noDisconnected':
			echo "<div id='msg'><p>Impossible de se déconnecter, veuillez réessayer ultérieurement</p></div>";
			break;
	}
}

if ( ! isset( $_COOKIE['connection'] ) ) {
	ob_start();
	?>
	<div id="connection">
		<h1>Entrez vos identifiants</h1>
		<form action="<?php echo $siteLocation . '/admin/connection.php'; ?>" method="POST">
			<input type="text" name="user" id="user" placeholder="Votre identifiant ou email">
			<input type="password" name="password" id="password" placeholder="Votre mot de passe">
			<input type="submit" value="Connexion">
		</form>
		<a href="<?php echo $siteLocation . '/inscription.php'; ?>">S'inscrire</a>
	</div>
	<?php
	echo ob_get_clean();
} elseif ( checkConnection() === 9 ) {
	try {
		?>
		<h1>Liste des catégories et produits</h1>
		<div id="categories-list">
			<?php
			$categories = getCategories();
			foreach ( $categories as $category ) : ?>
				<div class="category">
					<h2><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?> - ID:
						<?= $category['ID'] ?>
					</h2>
					<img src="<?= htmlspecialchars( $category['Thumbnail'] ) ?>" alt="<?= htmlspecialchars( $category['Name'] ) ?>">

					<div class="buttons">
						<button class="button delete" data-id="<?= $category['ID'] ?>">Supprimer</button>
						<button class="button update" data-id="<?= $category['ID'] ?>">Modifier</button>
					</div>

					<?php $products = getProducts( [ $category['ID'] ] ); ?>
					<?php if ( ! empty( $products ) ) : ?>
						<a href="#" class="button button_popup">Voir les produits associés</a>
						<div class="background-blur"></div>
						<div class="popup">
							<div class="flex">
								<h2><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?></h2><img
									class="close" src="../assets/images/close.svg" alt="Close">
							</div>
							<div class="content">
								<?php foreach ( $products as $product ) : ?>
									<div class="product">
										<img src="<?= htmlspecialchars( $product['Thumbnail'] ) ?>"
											alt="<?= htmlspecialchars( $product['Name'] ) ?>">
										<div>
											<h3><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $product['Name'] ) ) ) ?></h3>
											<p><?= str_split( htmlspecialchars( $product['Description'] ), 252 )[0] . '...' ?></p>
										</div>
										<div class="buttons">
											<button class="button deleteP" data-id="<?= $product['ID'] ?>">Supprimer</button>
											<button class="button updateP" data-id="<?= $product['ID'] ?>"
												data-desc="<?= $product['Description'] ?>">Modifier</button>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div id="add_cat">
			<div id="cross"><img src="../assets/images/close.svg" alt="+"></div>
			<div class="collapse">
				<div class="title">Ajouter une catégorie</div>
				<div class="content">
					<form enctype="multipart/form-data" id="add-category"
						action="<?php echo $GLOBALS['siteLocation'] ?>/admin/category.php?action=create" method="POST">
						<label for="cat_name" class="cat_name_label">Nom de la catégorie
							<input type="text" maxlength="255" required name="category_name" id="cat_name">
						</label>
						<label for="cat_image" class="drop-container" id="dropcontainer">
							<span class="drop-title">Drop files here</span>
							or
							<input type="file" name="cat_image" id="cat_image" accept="image/*" required>
						</label>

						<input type="submit" value="Ajouter">
					</form>
				</div>
			</div>
			<div class="collapse">
				<div class="title">Ajouter un produit</div>
				<div class="content">
					<form enctype="multipart/form-data" id="add-category"
						action="<?php echo $GLOBALS['siteLocation'] ?>/admin/products.php?action=create" method="POST">
						<label for="product_name" class="cat_name_label">Nom du produit
							<input type="text" maxlength="255" required name="product_name" id="product_name">
						</label>
						<label for="cat" class="cat_name_label">Catégorie associé
							<select name="cat" id="cat">
								<?php
								$categories = getCategories();
								foreach ( $categories as $category ) {
									echo '<option value="' . $category['ID'] . '">' . $category['Name'] . '</option>';
								}
								?>
							</select>
						</label>
						<label for="desc" class="cat_name_label">Description
							<textarea maxlength="1000" required name="desc" id="desc"></textarea>
						</label>
						<label for="cat_image" class="drop-container" id="dropcontainer">
							<span class="drop-title">Drop files here</span>
							or
							<input type="file" name="product_image" id="product_image" accept="image/*" required>
						</label>

						<input type="submit" value="Ajouter">
					</form>
				</div>
			</div>
		</div>
	<?php } catch (Exception $e) {
		echo $e->getMessage();
	}
} else {
	echo 'Autorisation insuffisante';
}

require_once( "../parts/footer.php" );
echo get_footer( $page );
?>