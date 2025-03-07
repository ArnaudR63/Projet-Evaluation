<?php
require_once("/realpath/sql.php");

try {
	$co = connectSql();
	$productsTable = $GLOBALS['table_prefix'] . 'Blog';
	$action = htmlspecialchars($_GET["action"] ?? '');

	if ($action === "create") {
		try {
			$productName = isset($_POST['post_title']) ? str_replace(' ', '_', strtolower(htmlspecialchars($_POST['post_title']))) : null;
			$img = $_FILES['post_image']['tmp_name'] ?? null;
			$desc = isset($_POST['postDesc']) ? htmlspecialchars($_POST['postDesc']) : '';
			$cat = isset($_POST['cat']) && ctype_digit($_POST['cat']) ? (int) $_POST['cat'] : 1;

			if (empty($productName) || strlen($productName) < 3) {
				throw new Exception("Nom du post invalide (au moins 3 caractères requis). ");
			}

			if (!$img || !is_uploaded_file($img)) {
				throw new Exception("Image du post invalide ou non uploadée.");
			}

			if (empty($desc) || strlen($desc) < 10) {
				throw new Exception("Description invalide (au moins 10 caractères requis).");
			}

			if ($cat <= 0) {
				throw new Exception("Catégorie invalide.");
			}

			$filename = "/realpath/assets/images/posts/$productName-" . time() . ".png";
			if (!move_uploaded_file($img, $filename)) {
				throw new Exception("Échec du téléchargement de l'image.");
			}

			$fileUrl = str_replace('/home/develoz/preprod/', 'https://test.com/', $filename);

			$sql = "INSERT INTO $productsTable (Title, Preview, Content, Category) VALUES (:name, :preview, :desc, :cat)";
			$stmt = $co->prepare($sql);
			$stmt->execute([
				':name' => $productName,
				':desc' => $desc,
				':cat' => $cat,
				':preview' => $fileUrl
			]);

			header("Location: $siteLocation/admin?msg=added");
		} catch (Exception $e) {
			header("Location: $siteLocation/admin?msg=notAdded&error=" . urlencode($e->getMessage()));
		}
	} elseif ($action === "delete") {
		try {
			$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : null;
			if (!$id) {
				throw new Exception("ID invalide ou manquant.");
			}

			$sql = "SELECT Preview FROM $productsTable WHERE ID = :id";
			$stmt = $co->prepare($sql);
			$stmt->execute([':id' => $id]);
			$thumb = $stmt->fetchColumn();

			if ($thumb) {
				$filePath = str_replace('https://test.com/', '/home/develoz/preprod/', $thumb);
				@unlink($filePath);
			}

			$sql = "DELETE FROM $productsTable WHERE ID = :id";
			$stmt = $co->prepare($sql);
			$stmt->execute([':id' => $id]);

			header("Location: $siteLocation/admin?msg=deleted");
		} catch (Exception $e) {
			header("Location: $siteLocation/admin?msg=notDeleted&error=" . urlencode($e->getMessage()));
		}
	} elseif ($action === "update") {
		try {
			$id = intval($_POST['id']) ?? null;
			if (!isset($_GET['id']) || !isset($_POST['id']) || $_GET['id'] !== $_POST['id']) {
				throw new Exception("ID invalide ou non correspondant.");
			}

			$sql = "SELECT * FROM $productsTable WHERE ID = :id";
			$stmt = $co->prepare($sql);
			$stmt->execute([':id' => $id]);
			$product = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$product) {
				throw new Exception("Post non trouvé.");
			}

			$productTitle = htmlspecialchars($_POST['postTitle'] ?? '');
			$productDescription = htmlspecialchars($_POST['postDescription'] ?? '');
			$fileUrl = $product['Preview'];

			if (!empty($_POST['postImage']) && strpos($_POST['postImage'], 'data:image') === 0) {
				$base64String = preg_replace('#^data:image/\w+;base64,#i', '', $_POST['postImage']);
				$imageData = base64_decode($base64String);
				if (!$imageData) {
					throw new Exception("L'encodage base64 est invalide.");
				}

				$imageDetails = getimagesizefromstring($imageData);
				if (!$imageDetails) {
					throw new Exception("Le format de l'image est invalide.");
				}

				switch ($imageDetails['mime']) {
					case 'image/webp':
						$extension = 'webp';
						break;
					case 'image/jpg':
						$extension = 'jpg';
						break;
					default:
						$extension = 'png';
				}

				$newFileName = "/realpath/assets/images/posts/{$productTitle}-" . time() . ".{$extension}";

				if (!file_put_contents($newFileName, $imageData)) {
					throw new Exception("Impossible de sauvegarder l'image.");
				}

				if (file_exists(str_replace('https://test.com/', '/home/develoz/preprod/', $product['Preview']))) {
					@unlink(str_replace('https://test.com/', '/home/develoz/preprod/', $product['Preview']));
				}

				$fileUrl = str_replace('/home/develoz/preprod/', 'https://test.com/', $newFileName);
			}

			$catId = intval($_POST['catId']);
			$category = getPostCategory($catId)[0];

			if (!$category || !isset($category['ID']) || intval($category['ID']) !== $catId) {
				error_log("\r\n" . json_encode($category) . "\r\n", 3, '/realpath/errors.log');
				throw new Exception('Catégorie invalide ou inexistante');
			}

			$sql = "UPDATE $productsTable SET Title = :name, Content = :description, Preview = :preview, Category = :catId WHERE ID = :id";
			$stmt = $co->prepare($sql);
			$stmt->execute([
				':name' => $productTitle,
				':description' => $productDescription,
				':preview' => $fileUrl,
				':catId' => $catId,
				':id' => $id
			]);

			header("Location: $siteLocation/admin?msg=updated");
		} catch (Exception $e) {
			header("Location: $siteLocation/admin?msg=notUpdated&error=" . urlencode($e->getMessage()));
		}
	} else {
		header("Location: $siteLocation/admin?msg=noAction");
	}
} catch (Exception $e) {
	echo "Erreur : " . $e->getMessage();
} finally {
	$co = null;
}
