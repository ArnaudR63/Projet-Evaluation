<div class="carousel_element">
	<h2>Liste des catégories et Posts</h2>
	<div id="categories-list">
		<?php
		$categories = getPostCategories();
		foreach ( $categories as $category ) : ?>
			<div class="category">
				<h3><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?> - ID:
					<?= $category['ID'] ?>
				</h3>
				<img src="<?= htmlspecialchars( $category['Thumbnail'] ) ?>"
					alt="<?= htmlspecialchars( $category['Name'] ) ?>">

				<div class="buttons">
					<?= $category['ID'] === 1 ? '' : '<button class="button delete" data-id="' . $category["ID"] . '">Supprimer</button>' ?>
					<button class="button update" data-id="<?= $category['ID'] ?>">Modifier</button>
				</div>
				<?php $post = getBlog( [ $category['ID'] ] ); ?>
				<?php if ( ! empty( $post ) ) : ?>
					<a href="#" data-action="openPopup" class="button post button_popup">Voir les posts associés</a>
					<div class="background-blur"></div>
					<div class="popup">
						<div class="flex">
							<h2><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $category['Name'] ) ) ) ?></h2><img
								class="close" src="../assets/images/close.svg" alt="Close">
						</div>
						<div class="content">
							<?php foreach ( $post as $product ) : ?>
								<div class="post item" data-list=<?= json_encode( implode( ',', array_column( $categories, 'ID' ) ) ) ?> data-listName=<?= implode( ',', array_column( $categories, 'Name' ) ) ?>
									data-cat="<?= $category['ID'] ?>">
									<img src="<?= htmlspecialchars( $product['Preview'] ) ?>"
										alt="<?= htmlspecialchars( $product['Title'] ) ?>">
									<div>
										<h3><?= str_replace( '_', ' ', ucfirst( htmlspecialchars( $product['Title'] ) ) ) ?></h3>
										<p><?= description( htmlspecialchars( $product['Content'] ) ) . '...' ?></p>
									</div>
									<div class="buttons">
										<button class="button deletePost" data-action="deletePost"
											data-id="<?= $product['ID'] ?>">Supprimer</button>
										<button class="button updatePost" data-action="updatePost" data-id="<?= $product['ID'] ?>"
											data-desc="<?= $product['Content'] ?>">Modifier</button>
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
					action="<?php echo $GLOBALS['siteLocation'] ?>/admin/postCategory.php?action=create" method="POST">
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
			<div class="title">Ajouter un article</div>
			<div class="content">
				<form enctype="multipart/form-data" id="add-category"
					action="<?php echo $GLOBALS['siteLocation'] ?>/admin/post.php?action=create" method="POST">
					<label for="post_title" class="cat_name_label">Titre
						<input type="text" maxlength="255" required name="post_title" id="post_title">
					</label>
					<label for="cat" class="cat_name_label">Catégorie associé
						<select name="cat" id="cat">
							<?php
							foreach ( $categories as $category ) {
								echo '<option value="' . $category['ID'] . '">' . $category['Name'] . '</option>';
							}
							?>
						</select>
					</label>
					<label for="postDesc" class="cat_name_label">Contenu
						<textarea maxlength="1000" required name="postDesc" id="postDesc"></textarea>
					</label>
					<label for="post_image" class="drop-container" id="dropcontainer">
						<span class="drop-title">Choisir la miniature</span>
						or
						<input type="file" name="post_image" id="post_image" accept="image/*" required>
					</label>

					<input type="submit" value="Ajouter">
				</form>
			</div>
		</div>
	</div>
</div>