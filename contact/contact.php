<?php
function sendFormMail() {
	$to = 'contact@developp-et-vous.com';
	$identity = isset( $_POST['identity'] ) ? htmlspecialchars( $_POST['identity'] ) : null;
	$email = isset( $_POST['email'] ) ? filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) : null;
	$phone = isset( $_POST['phone'] ) ? htmlspecialchars( $_POST['phone'] ) : null;
	$message = isset( $_POST['message'] ) ? htmlspecialchars( $_POST['message'] ) : null;
	$files = [];
	$subject = null;

	if ( ! $identity || ! $email || ! $message ) {
		redirectWithMessage( 'error', 'Tous les champs obligatoires ne sont pas remplis.' );
		return false;
	}

	switch ( $_POST['subject'] ) {
		case 'devis-buy':
		case 'devis-shop':
			$subject = 'Je souhaite un devis pour un ou des ' . ( $_POST['subject'] === 'devis-buy' ? 'Produit(s)' : 'Achat(s) de gros' );
			if ( ! empty( $_POST['product-name'] ) ) {
				$subject .= ' - ' . htmlspecialchars( $_POST['product-name'] );
			}
			break;
		case 'candidature':
			$subject = 'Je souhaite faire partie de votre entreprise';
			$files = verifiedFiles();
			if ( ! $files ) {
				redirectWithMessage( 'error', 'Fichiers requis pour la candidature manquants ou invalides.' );
				return false;
			}
			break;
		case 'autre':
			$subject = ! empty( $_POST['other'] ) ? htmlspecialchars( $_POST['other'] ) : null;
			break;
		default:
			redirectWithMessage( 'error', 'Sujet invalide.' );
			return false;
	}

	if ( ! $subject ) {
		redirectWithMessage( 'error', 'Sujet manquant.' );
		return false;
	}

	$boundary = md5( uniqid( microtime(), true ) );
	$headers = 'From: ' . $identity . ' <' . $to . '>' . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

	$body = "--$boundary\r\n";
	$body .= "Content-Type: text/plain; charset=utf-8\r\n";
	$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$body .= "Email : $email\nTéléphone : $phone\n\nMessage :\n $message\n\n";

	foreach ( $files as $file ) {
		$fileContent = file_get_contents( $file['tmp_name'] );
		$encodedContent = chunk_split( base64_encode( $fileContent ) );
		$body .= "--$boundary\r\n";
		$body .= "Content-Type: {$file['type']}; name=\"{$file['name']}\"\r\n";
		$body .= "Content-Transfer-Encoding: base64\r\n";
		$body .= "Content-Disposition: attachment; filename=\"{$file['name']}\"\r\n\r\n";
		$body .= $encodedContent . "\r\n";
	}

	$body .= "--$boundary--\r\n";

	if ( mail( $to, $subject, $body, $headers ) ) {
		redirectWithMessage( 'success', 'E-mail envoyé avec succès.' );
		return true;
	} else {
		redirectWithMessage( 'error', 'Échec de l\'envoi de l\'e-mail.' );
		return false;
	}
}

function verifiedFiles() {
	$allowedTypes = [ 
		'application/pdf',
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'image/jpeg',
		'image/png',
		'image/svg+xml',
	];
	$allowedExtensions = [ 'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'svg' ];

	$files = [];

	foreach ( [ 'cv', 'motivation' ] as $key ) {
		if ( isset( $_FILES[ $key ] ) && $_FILES[ $key ]['error'] === UPLOAD_ERR_OK ) {
			$extension = strtolower( pathinfo( $_FILES[ $key ]['name'], PATHINFO_EXTENSION ) );
			$type = mime_content_type( $_FILES[ $key ]['tmp_name'] );

			if ( in_array( $extension, $allowedExtensions ) && in_array( $type, $allowedTypes ) ) {
				$files[] = $_FILES[ $key ];
			} else {
				return false;
			}
		}
	}

	return ! empty( $files ) ? $files : false;
}

function redirectWithMessage( $status, $message ) {
	$encodedMessage = urlencode( $message );
	header( "Location: ./index.php?status=$status&message=$encodedMessage" );
	exit;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	sendFormMail();
}