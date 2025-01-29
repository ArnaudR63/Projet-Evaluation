<?php
$page = 'contact';
require_once( '../parts/header.php' );
echo get_header( $page );

if ( isset( $_GET['status'] ) && isset( $_GET['message'] ) ) {
	$status = htmlspecialchars( $_GET['status'] );
	$message = htmlspecialchars( $_GET['message'] );
	echo "<div class='alert alert-$status'>$message</div>";
}
?>

<main>
	<div id="contact_container">
		<h1>Contactez Développ et vous !</h1>
		<form method="POST" action="./contact.php" id="contact_form" enctype="multipart/form-data">
			<div class="form_input">
				<label for="identity">Prénom et Nom <span class="required">*</span></label>
				<input type="text" name="identity" id="identity" placeholder="Jean Dupont" required>
			</div>
			<div class="form_input">
				<label for="email">E-mail <span class="required">*</span></label>
				<input type="email" name="email" id="email" placeholder="jean.dupont@email.com" required>
			</div>
			<div class="form_input">
				<label for="phone">Téléphone</label>
				<input type="tel" name="phone" id="phone" placeholder="00 00 00 00 00">
			</div>
			<div class="form_input">
				<label for="subject">Sujet de votre demande <span class="required">*</span></label>
				<select name="subject" id="subject" required>
					<option value="">Pourquoi me contactez-vous ?</option>
					<option value="devis-buy" <?= isset( $_GET['pName'] ) ? 'selected' : '' ?>>Je souhaite commander un
						produit</option>
					<option value="devis-shop">Je souhaite un devis pour un achat en gros</option>
					<option value="candidature">Je souhaite vous rejoindre</option>
					<option value="autre">Ma demande n'est pas dans la liste</option>
				</select>
			</div>
			<?= isset( $_GET['pName'] ) ? '<div id="select_input" class="form_input">
<label for="product-name">Nom du / des produit(s) concernés</label><input type="text" name="product-name" id="product-name" value="'
				. str_replace( '_', ' ', ucfirst( strip_tags( $_GET['pName'] ) ) )
				. '"></input></div>' : '<div id="select_input" class="form_input"></div>' ?>
			<div class="form_input">
				<label for="message">Votre message <span class="required">*</span></label>
				<textarea name="message" id="message" cols="50" rows="5" required></textarea>
			</div>
			<input type="submit" class="button_main" value="Envoyer">
		</form>
	</div>
</main>

<?php
require_once( $GLOBALS['realpathLocation'] . '/parts/footer.php' );
echo get_footer( $page );
?>