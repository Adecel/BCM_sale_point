<?php
include_once 'connectdb.php';
session_start();

if (isset($_POST['query'])) {
    $searchText = $_POST['query'];

    // Prepare the SQL statement with a LIKE clause to search for matching product names
    $stmt = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE ProductName LIKE :search AND IsDeleted = 0 ORDER BY ProductInNeedId ASC");
    $searchParam = "%" . $searchText . "%";
    $stmt->bindParam(':search', $searchParam);
    $stmt->execute();

    // Fetch the results and generate HTML to display them
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="alert alert-info">';
            echo '<strong>' . htmlspecialchars($row['ProductName']) . '</strong> - ' . $row['NumberOfTimes'] . ' fois recherché';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Aucun produit correspondant trouvé.</div>';
    }
}
?>
