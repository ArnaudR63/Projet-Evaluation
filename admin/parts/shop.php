<div class="carousel_element">
	<h2>Liste des catégories et produits</h2>
	<div id="categories-list">
		<?php
		$categories = getCategories();
		foreach ( $categories as $category ) : ?>
			<div class="category">
				<h3><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?> - ID:
					<?= $category['ID'] ?>
				</h3>
				<img src="<?= htmlspecialchars( $category['Thumbnail'] ) ?>"
					alt="<?= htmlspecialchars( $category['Name'] ) ?>">

				<div class="buttons">
					<button class="button delete" data-id="<?= $category['ID'] ?>">Supprimer</button>
					<button class="button update" data-id="<?= $category['ID'] ?>">Modifier</button>
				</div>

				<?php $products = getProducts( [ $category['ID'] ] ); ?>
				<?php if ( ! empty( $products ) ) : ?>
					<a href="#" data-action="openPopup" class="button product button_popup">Voir les produits associés</a>
					<div class="background-blur"></div>
					<div class="popup">
						<div class="flex">
							<h2><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?></h2><img
								class="close" src="../assets/images/close.svg" alt="Close">
						</div>
						<div class="content">
							<?php foreach ( $products as $product ) : ?>
								<div class="product item" data-list=<?= json_encode( implode( ',', array_column( $categories, 'ID' ) ) ) ?> data-listName=<?= implode( ',', array_column( $categories, 'Name' ) ) ?>
									data-cat="<?= $product['Category'] ?>">
									<img src="<?= htmlspecialchars( $product['Thumbnail'] ) ?>"
										alt="<?= htmlspecialchars( $product['Name'] ) ?>">
									<div>
										<h3><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $product['Name'] ) ) ) ?></h3>
										<p><?= description( htmlspecialchars( $product['Description'] ) ) . '...' ?></p>
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
					<label for="product_image" class="drop-container" id="dropcontainer">
						<span class="drop-title">Drop files here</span>
						or
						<input type="file" name="product_image" id="product_image" accept="image/*" required>
					</label>

					<input type="submit" value="Ajouter">
				</form>
			</div>
		</div>
	</div>
</div>