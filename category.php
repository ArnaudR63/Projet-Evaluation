<?php
require_once("../sql.php");

try {
    $co = connectSql();
    $catTable = $GLOBALS['table_prefix'] . 'Categories';

    $action = htmlspecialchars($_GET["action"]);
    if ($action === "create") {
        try {
            $catName = str_replace(' ', '_', strtolower(htmlspecialchars($_POST['category_name'])));
            $img = $_FILES['cat_image']['tmp_name'];
            $filename = "../assets/images/cat/$catName-" . time() . ".png";
            $fileExist = move_uploaded_file($img, $filename);

            $fileUrl = "";

            if ($fileExist) {
                $linkExtracted = str_replace('../', '', $filename);

                $fileUrl = str_starts_with($filename, '../assets/images/cat/')
                    && str_ends_with($filename, '.png')
                    ? "https://preprod.developp-et-vous.com/$linkExtracted" : throw new Exception("Le lien n'est pas valide");
            }
            $sql = "INSERT INTO $catTable (Name, Thumbnail) VALUES (:name, :thumbnail)";
            $stmt = $co->prepare($sql);
            $stmt->execute([':name' => $catName, ':thumbnail' => $fileUrl]);

            header("Location: $siteLocation/admin?msg=added");
        } catch (Exception $e) {
            header("Location: $siteLocation/admin?msg=notAdded");
        }
    } elseif ($action === "delete") {
        if (isset($_GET['id'])) {
            try {
                $id = (int) $_GET['id'];

                $sql = "SELECT Thumbnail FROM $catTable WHERE ID = $id";
                $thumb = executeSql($co, $sql)[0]['Thumbnail'];
                @unlink(str_replace('https://preprod.developp-et-vous.com/', '../', $thumb));

                $sql = "DELETE FROM $catTable WHERE ID = $id";
                executeSql($co, $sql);
                header("Location: $siteLocation/admin?msg=deleted");
            } catch (Exception $e) {
                header("Location: $siteLocation/admin?msg=notDeleted");
            }
        } else {
            header("Location: $siteLocation/admin?msg=noId&id=" . $_GET['id']);
        }
    } elseif ($action === "update") {

    } else {
        header("Location: $siteLocation/admin?msg=noAction");
    }
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    $co = null;
}