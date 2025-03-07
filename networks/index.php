<?php $page = 'networks';
require_once( '../parts/header.php' );
echo get_header( $page ); ?>
<h1>Rejoignez-nous sur nos différents réseaux !</h1>
<div id="networks">
	<a href="" class="network" title="Lien vers LinkedIn" target="_blank">
		<img src="../assets/images/networks/linkedin.jpg" alt="Bannière LinkedIn">
		<div class="network_button">
			Rejoindre la page LinkedIn
		</div>
	</a>
	<a href="" class="network" title="Lien vers Facebook" target="_blank">
		<img src="../assets/images/networks/facebook.jpg" alt="Bannière Facebook">
		<div class="network_button">
			Rejoindre la page Facebook
		</div>
	</a>
	<a href="" class="network" title="Lien vers Instagram" target="_blank">
		<img src="../assets/images/networks/instagram.jpg" alt="Bannière Instagram">
		<div class="network_button">
			Rejoindre le compte Instagram
		</div>
	</a>
</div>

<?php require_once( '../parts/footer.php' );
echo get_footer( $page ); ?>