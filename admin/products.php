<?php
require_once( "/realpath/sql.php" );

try {
	$co = connectSql();
	$productsTable = $GLOBALS['table_prefix'] . 'Products';

	$action = htmlspecialchars( $_GET["action"] ?? '' );

	if ( $action === "create" ) {
		try {
			$productName = isset( $_POST['product_name'] ) ? str_replace( ' ', '_', strtolower( htmlspecialchars( $_POST['product_name'] ) ) ) : null;
			$img = $_FILES['product_image']['tmp_name'] ?? null;
			$desc = isset( $_POST['desc'] ) && is_string( $_POST['desc'] ) ? htmlspecialchars( $_POST['desc'] ) : '';
			$cat = isset( $_POST['cat'] ) && ctype_digit( $_POST['cat'] ) ? (int) $_POST['cat'] : 0;

			if ( empty( $productName ) || strlen( $productName ) < 3 ) {
				throw new Exception( "Nom du produit invalide (au moins 3 caractères requis)." );
			}

			if ( ! $img || ! is_uploaded_file( $img ) ) {
				throw new Exception( "Image du produit invalide ou non uploadée." );
			}

			if ( empty( $desc ) || strlen( $desc ) < 10 ) {
				throw new Exception( "Description invalide (au moins 10 caractères requis)." );
			}

			if ( $cat <= 0 ) {
				throw new Exception( "Catégorie invalide." );
			}

			$filename = "/realpath/assets/images/products/$productName-" . time() . ".png";
			if ( ! move_uploaded_file( $img, $filename ) ) {
				throw new Exception( "Échec du téléchargement de l'image." );
			}

			$linkExtracted = str_replace( '/home/develoz/preprod/', '', $filename );
			$fileUrl = "https://test.com/$linkExtracted";

			$sql = "INSERT INTO $productsTable (Name, Description, Category, Thumbnail) VALUES (:name, :desc, :cat, :thumbnail)";
			$stmt = $co->prepare( $sql );
			$stmt->execute( [ 
				':name' => $productName,
				':desc' => $desc,
				':cat' => $cat,
				':thumbnail' => $fileUrl
			] );

			header( "Location: $siteLocation/admin?msg=added" );
		} catch (Exception $e) {
			header( "Location: $siteLocation/admin?msg=notAdded&error=" . urlencode( $e->getMessage() ) );
		}
	} elseif ( $action === "delete" ) {
		try {
			$id = isset( $_GET['id'] ) && ctype_digit( $_GET['id'] ) ? (int) $_GET['id'] : null;
			if ( ! $id ) {
				throw new Exception( "ID invalide ou manquant." );
			}

			$sql = "SELECT Thumbnail FROM $productsTable WHERE ID = :id";
			$stmt = $co->prepare( $sql );
			$stmt->execute( [ ':id' => $id ] );
			$thumb = $stmt->fetchColumn();

			if ( $thumb ) {
				$filePath = str_replace( 'https://test.com/', '/home/develoz/preprod/', $thumb );
				@unlink( $filePath );
			}

			$sql = "DELETE FROM $productsTable WHERE ID = :id";
			$stmt = $co->prepare( $sql );
			$stmt->execute( [ ':id' => $id ] );

			header( "Location: $siteLocation/admin?msg=deleted" );
		} catch (Exception $e) {
			header( "Location: $siteLocation/admin?msg=notDeleted" );
		}
	} elseif ( $action === "update" ) {
		if ( isset( $_GET['id'] ) && isset( $_POST['id'] ) && $_GET['id'] === $_POST['id'] ) {
			try {
				$product = getProduct( $_POST['id'] )[0];
				$change = false;
				$oldProductName = str_replace( '_', ' ', ucfirst( htmlspecialchars( $product['Name'] ) ) );
				$productName = $oldProductName;
				if ( $oldProductName !== htmlspecialchars( $_POST['productName'] ) ) {
					$change = true;
					$productName = str_replace( ' ', '_', strtolower( htmlspecialchars( $_POST['productName'] ) ) );
				}

				$productDescription = htmlspecialchars( $_POST['productDescription'] );
				if ( $product['Description'] !== $productDescription ) {
					$change = true;
				}

				$oldThumbUrl = str_replace( 'https://test.com/', '/home/develoz/preprod/', $product['Thumbnail'] );
				$oldThumb = "";
				if ( file_exists( $oldThumbUrl ) ) {
					$imageData = base64_encode( file_get_contents( $oldThumbUrl ) );
					$mimeType = mime_content_type( $oldThumbUrl );
					$oldThumb = "data:$mimeType;base64,$imageData";
				}
				if ( $oldThumb !== $_POST['productImage'] ) {
					$change = true;
					$base64String = $_POST['productImage'];
					$matches = [];
					if ( preg_match( '/^data:image\/(\w+);base64,/', $base64String, $matches ) ) {
						$imageType = $matches[1];
						$base64String = substr( $base64String, strpos( $base64String, ',' ) + 1 );
						$base64String = base64_decode( $base64String );
						if ( $base64String === false ) {
							throw new Exception( "L'encodage base64 est invalide." );
						}
						$newFileName = "/realpath/assets/images/products/{$productName}-" . time() . ".{$imageType}";
						if ( ! file_put_contents( $newFileName, $base64String ) ) {
							throw new Exception( "Impossible de sauvegarder l'image." );
						}
						$fileUrl = str_replace( '/home/develoz/preprod/', 'https://test.com/', $newFileName );
						if ( file_exists( $oldThumbUrl ) ) {
							@unlink( $oldThumbUrl );
						}
					} else {
						throw new Exception( "Le format base64 est invalide." );
					}
				} else {
					$fileUrl = htmlspecialchars( $product['Thumbnail'] );
				}

				if ( $change ) {
					$sql = "UPDATE $productsTable SET Name = :name, Description = :description, Thumbnail = :thumbnail WHERE ID = :id";
					$stmt = $co->prepare( $sql );
					$stmt->execute( [ 
						':name' => $productName,
						':description' => $productDescription,
						':thumbnail' => $fileUrl,
						':id' => $_POST['id']
					] );
				}
				header( "Location: $siteLocation/admin?msg=updated" );
			} catch (Exception $e) {
				header( "Location: $siteLocation/admin?msg=notUpdated" );
			}
		} else {
			header( "Location: $siteLocation/admin?msg=noId" );
		}
	} else {
		header( "Location: $siteLocation/admin?msg=noAction" );
	}
} catch (Exception $e) {
	echo "Erreur : " . $e->getMessage();
} finally {
	$co = null;
}
