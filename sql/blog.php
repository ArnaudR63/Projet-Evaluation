<?php
function getBlog($cat = null, $search = null)
{
    try {
        $co = connectSql();
        $sql = "SELECT Blog.ID, Blog.Title, Blog.Preview, Blog.Content, PostsCategories.Name AS CategoryName 
                FROM " . $GLOBALS['table_prefix'] . "Blog AS Blog
                INNER JOIN " . $GLOBALS['table_prefix'] . "PostsCategories AS PostsCategories
                ON Blog.Category = PostsCategories.ID";

        $params = [];
        $conditions = [];

        if (!empty($cat) && is_array($cat)) {
            $placeholders = implode(',', array_fill(0, count($cat), '?'));
            $conditions[] = "Blog.Category IN ($placeholders)";
            $params = array_merge($params, $cat);
        }

        if (!empty($search) && is_array($search)) {
            $searchConditions = [];

            foreach ($search as $keyword) {
                $cleanKeyword = '%' . $keyword . '%';
                $searchConditions[] = "(Blog.Title LIKE ? OR Blog.Content LIKE ?)";
                $params[] = $cleanKeyword;
                $params[] = $cleanKeyword;
            }

            $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $result = executeSql($co, $sql, $params);

        if ($result === false) {
            throw new Exception("Échec de l'exécution de la requête SQL.");
        }

        return $result;
    } catch (Exception $e) {
        error_log("Erreur dans getBlog : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
        return false;
    }
}

function getPost($id)
{
	try {
		$co = connectSql();
		$sql = "";
		$params = [];

		if (!empty($id)) {
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "Blog WHERE ID = :id";
			$params = array(':id' => $id);
		} else {
			throw new Exception('Identifiant de Post non fournis');
		}

		$products = executeSql($co, $sql, $params);
		return $products;
	} catch (PDOException $e) {
		error_log("Erreur dans getProduct : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
		return false;
	}
}

function getPostCategories()
{
	try {
		$co = connectSql();
		$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "PostsCategories";


		$cat = executeSql($co, $sql);
		return $cat;
	} catch (PDOException $e) {
		error_log("Erreur dans getCategories : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
		return false;
	}
}

function getPostCategory($id)
{
	try {
		$co = connectSql();
		$sql = "";
		$params = [];

		if (!empty($id)) {
			$sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "PostsCategories WHERE ID = :id";
			$params = array(':id' => $id);
		} else {
			throw new Exception('Identifiant de Catégorie de Post non fournis');
		}

		$products = executeSql($co, $sql, $params);
		return $products;
	} catch (PDOException $e) {
		error_log("Erreur dans getCategory : " . $e->getMessage() . "\n", 3, '/realpath/errors.log');
		return false;
	}
}