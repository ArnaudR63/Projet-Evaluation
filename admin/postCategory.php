<?php
require_once("/realpath/sql.php");

try {
	if (checkConnection() === 9) {
		$co = connectSql();
		if (!$co) {
			exit;
		}
		$catTable = $GLOBALS['table_prefix'] . 'PostsCategories';
		$action = isset($_GET["action"]) ? htmlspecialchars($_GET["action"]) : '';

		if ($action === "create" && isset($_POST['category_name'], $_FILES['cat_image'])) {
			try {
				$catName = str_replace(' ', '_', strtolower(htmlspecialchars($_POST['category_name'])));
				$img = $_FILES['cat_image']['tmp_name'];
				$extension = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
				$filename = "/realpath/assets/images/cat/{$catName}-" . time() . ".{$extension}";

				if ($_FILES['cat_image']['error'] !== UPLOAD_ERR_OK) {
					error_log("Erreur upload : " . $_FILES['cat_image']['error']);
					throw new Exception("Erreur lors du téléchargement de l'image.");
				}

				$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
				if (!in_array($extension, $allowedTypes)) {
					throw new Exception("Type de fichier non autorisé. Veuillez télécharger une image.");
				}

				if (move_uploaded_file($img, $filename)) {
					$fileUrl = str_replace('/home/develoz/preprod/', 'https://test.com/', $filename);
					$sql = "INSERT INTO $catTable (Name, Thumbnail) VALUES (:name, :thumbnail)";
					$stmt = $co->prepare($sql);
					$stmt->execute([':name' => $catName, ':thumbnail' => $fileUrl]);
					header("Location: $siteLocation/admin?msg=added");
					exit;
				}
				throw new Exception("Le fichier n'a pas pu être déplacé.");
			} catch (Exception $e) {
				error_log("Erreur dans la création de la catégorie : " . $e->getMessage());
				header("Location: $siteLocation/admin?msg=notAdded");
				exit;
			}
		} elseif ($action === "delete" && isset($_GET['id'])) {
			try {
				$id = (int) $_GET['id'];
				$sql = "SELECT Thumbnail FROM $catTable WHERE ID = :id";
				$stmt = $co->prepare($sql);
				$stmt->execute([':id' => $id]);
				$thumb = $stmt->fetchColumn();

				if ($thumb) {
					$filePath = str_replace('https://test.com/', '/home/develoz/preprod/', $thumb);
					@unlink($filePath);
				}

				$sql = "DELETE FROM $catTable WHERE ID = :id";
				$stmt = $co->prepare($sql);
				$stmt->execute([':id' => $id]);
				header("Location: $siteLocation/admin?msg=deleted");
				exit;
			} catch (Exception $e) {
				error_log("Erreur dans la suppression de la catégorie : " . $e->getMessage());
				header("Location: $siteLocation/admin?msg=notDeleted");
				exit;
			}
		} elseif ($action === "update" && isset($_GET['id'], $_POST['id']) && $_GET['id'] === $_POST['id']) {
			try {
				$id = (int) $_POST['id'];
				$sql = "SELECT Name, Thumbnail FROM $catTable WHERE ID = :id";
				$stmt = $co->prepare($sql);
				$stmt->execute([':id' => $id]);
				$category = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$category) {
					throw new Exception("Catégorie introuvable.");
				}

				$catName = str_replace(' ', '_', strtolower(htmlspecialchars($_POST['catName'])));
				$fileUrl = $category['Thumbnail'];

				if (!empty($_POST['catImage']) && preg_match('/^data:image\/([a-zA-Z]+);base64,/', $_POST['catImage'], $matches)) {
					$imageType = $matches[1];
					$base64Data = substr($_POST['catImage'], strpos($_POST['catImage'], ',') + 1);
					$decodedData = base64_decode($base64Data);

					if ($decodedData === false) {
						throw new Exception("L'encodage base64 est invalide.");
					}

					$newFileName = "/realpath/assets/images/cat/{$catName}-" . time() . ".{$imageType}";
					if (file_put_contents($newFileName, $decodedData)) {
						@unlink(str_replace('https://test.com/', '/home/develoz/preprod/', $category['Thumbnail']));
						$fileUrl = str_replace('/home/develoz/preprod/', 'https://test.com/', $newFileName);
					} else {
						throw new Exception("Impossible de sauvegarder l'image.");
					}
				}

				$sql = "UPDATE $catTable SET Name = :name, Thumbnail = :thumbnail WHERE ID = :id";
				$stmt = $co->prepare($sql);
				$stmt->execute([':name' => $catName, ':thumbnail' => $fileUrl, ':id' => $id]);

				header("Location: $siteLocation/admin?msg=updated");
				exit;
			} catch (Exception $e) {
				error_log("Erreur dans la mise à jour de la catégorie : " . $e->getMessage());
				header("Location: $siteLocation/admin?msg=notUpdated");
				exit;
			}
		} else {
			header("Location: $siteLocation/admin?msg=noAction");
			exit;
		}
	} else {
		header("Location: $siteLocation/admin?msg=notAdmin");
		exit;
	}
} catch (Exception $e) {
	error_log("Erreur générale : " . $e->getMessage());
	echo "Erreur : " . $e->getMessage();
} finally {
	$co = null;
}