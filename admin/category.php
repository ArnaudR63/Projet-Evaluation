<?php
require_once( "../sql.php" );

try {
	if ( checkConnection() === 9 ) {
		$co = connectSql();
		$catTable = $GLOBALS['table_prefix'] . 'Categories';

		$action = htmlspecialchars( $_GET["action"] );
		if ( $action === "create" ) {
			try {
				$catName = str_replace( ' ', '_', strtolower( htmlspecialchars( $_POST['category_name'] ) ) );
				$img = $_FILES['cat_image']['tmp_name'];
				$filename = "../assets/images/cat/$catName-" . time() . ".png";
				$fileExist = move_uploaded_file( $img, $filename );

				$fileUrl = "";

				if ( $fileExist ) {
					$linkExtracted = str_replace( '../', '', $filename );

					$fileUrl = str_starts_with( $filename, '../assets/images/cat/' )
						&& str_ends_with( $filename, '.png' )
						? "https://preprod.developp-et-vous.com/$linkExtracted" : throw new Exception( "Le lien n'est pas valide" );
				}
				$sql = "INSERT INTO $catTable (Name, Thumbnail) VALUES (:name, :thumbnail)";
				$stmt = $co->prepare( $sql );
				$stmt->execute( [ ':name' => $catName, ':thumbnail' => $fileUrl ] );

				header( "Location: $siteLocation/admin?msg=added" );
			} catch (Exception $e) {
				header( "Location: $siteLocation/admin?msg=notAdded" );
			}
		} elseif ( $action === "delete" ) {
			if ( isset( $_GET['id'] ) ) {
				try {
					$id = (int) $_GET['id'];

					$sql = "SELECT Thumbnail FROM $catTable WHERE ID = $id";
					$thumb = executeSql( $co, $sql )[0]['Thumbnail'];
					@unlink( str_replace( 'https://preprod.developp-et-vous.com/', '../', $thumb ) );

					$sql = "DELETE FROM $catTable WHERE ID = $id";
					executeSql( $co, $sql );
					header( "Location: $siteLocation/admin?msg=deleted" );
				} catch (Exception $e) {
					header( "Location: $siteLocation/admin?msg=notDeleted" );
				}
			} else {
				header( "Location: $siteLocation/admin?msg=noId&id=" . $_GET['id'] );
			}
		} elseif ( $action === "update" ) {
			if ( isset( $_GET['id'] ) && isset( $_POST['id'] ) && $_GET['id'] === $_POST['id'] ) {
				try {
                    //Nom
					$cat = getCategory( $_POST['id'] );
					$change = false;
					$oldCatName = str_replace( '_', ' ', ucfirst( htmlspecialchars( $cat['Name'] ) ) );
					$catName = $oldCatName;
					if ( $oldCatName !== htmlspecialchars( $_POST['catName'] ) ) {
						$change = true;
						$catName = str_replace( ' ', '_', strtolower( htmlspecialchars( $_POST['catName'] ) ) );
					}

                    //Image
					$oldThumbUrl = str_replace( 'https://preprod.developp-et-vous.com/', '../', $oldThumbUrl );
					$oldThumb = "";
					if ( file_exists( $oldThumbUrl ) ) {
						$imageData = base64_encode( file_get_contents( $oldThumbUrl ) );
						$mimeType = mime_content_type( $oldThumbUrl );
						$oldThumb = "data:$mimeType;base64,$imageData";
					}
					if ( $oldThumb !== $_POST['catImage'] ) {
						$change = true;
						$base64String = $_POST['catImage'];
						$matches = [];
						if ( preg_match( '/^data:image\/(\w+);base64,/', $base64String, $matches ) ) {
							$imageType = $matches[1];
							$base64String = substr( $base64String, strpos( $base64String, ',' ) + 1 );
							$base64String = base64_decode( $base64String );
							if ( $base64String === false ) {
								throw new Exception( "L'encodage base64 est invalide." );
							}
							$newFileName = "../assets/images/cat/{$catName}-" . time() . ".{$imageType}";
							if ( ! file_put_contents( $newFileName, $base64String ) ) {
								throw new Exception( "Impossible de sauvegarder l'image." );
							}
							$fileUrl = str_replace( '../', 'https://preprod.developp-et-vous.com/', $newFileName );
							if ( file_exists( $oldThumbUrl ) ) {
								@unlink( $oldThumbUrl );
							}
						} else {
							throw new Exception( "Le format base64 est invalide." );
						}
					} else {
						$fileUrl = htmlspecialchars( $cat['Thumbnail'] );
					}

                    //MàJ BDD
					if ( $change ) {
						$sql = "UPDATE $catTable SET Name = :name, Thumbnail = :thumbnail WHERE ID = :id";
						$stmt = $co->prepare( $sql );
						$stmt->execute( [ 
							':name' => $catName,
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
	} else {
		header( "Location: $siteLocation/admin?msg=notAdmin" );
	}
} catch (Exception $e) {
	echo $e->getMessage();
} finally {
	$co = null;
}