<?php
include_once 'connectdb.php';

if (isset($_POST['query'])) {
    $searchText = $_POST['query'];

    // Prepare the SQL statement with a LIKE clause to search for matching product names
    $stmt = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE ProductName LIKE :search AND IsDeleted = 0 ORDER BY ProductInNeedId ASC");
    $searchParam = "%" . $searchText . "%";
    $stmt->bindParam(':search', $searchParam);
    $stmt->execute();

    // Fetch the results and generate HTML to display them
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row->ProductName) . '</td>';
            echo '<td>' . $row->NumberOfTimes . '</td>';
            echo '<td>
                    <form method="post" action="">
                        <button type="submit" name="btnedit" class="btn btn-primary" value="' . $row->ProductInNeedId . '">Modifier</button>
                    </form>
                  </td>';
            echo '<td>
                    <a href="ProductMissing.php?id=' . $row->ProductInNeedId . '" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</a>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4" class="text-center">Aucun produit correspondant trouvé.</td></tr>';
    }
}
?>
