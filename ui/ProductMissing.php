<?php
include_once 'connectdb.php';
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    } elseif ($_SESSION['role'] == 'Utilisateur') {
        include_once 'UserHeader.php';
        include_once 'UserHeader.php';
    } else {
        // Redirect to the login page or access denied page if role doesn't match
        header('location:../index.php');
        exit();
    }
} else {
    // If session 'role' is not set, redirect to the login page
    header('location:../index.php');
    exit();
}

error_reporting(0);

// Handle saving or updating a product in need
if (isset($_POST['btnsave']) || isset($_POST['btnupdate'])) {
    $productname = $_POST['txtproductname'];
    $userid = $_SESSION['username']; // Assuming username is used for CreatedBy and ModifiedBy
    $productid = isset($_POST['txtproductid']) ? $_POST['txtproductid'] : null;
    $numberoftimes = isset($_POST['txtnumberoftimes']) ? $_POST['txtnumberoftimes'] : 0;

    if (empty($productname)) {
        $_SESSION['status'] = "Le nom du produit est obligatoire";
        $_SESSION['status_code'] = "warning";
    } else {
        $date = date('Y-m-d');

        if ($numberoftimes <= 0) {
            $_SESSION['status'] = "Le nombre de fois recherché doit être supérieur à 0";
            $_SESSION['status_code'] = "warning";
        } else {
            // Check if the product already exists
            $check = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE ProductName = :name AND IsDeleted = 0");
            $check->bindParam(':name', $productname);
            $check->execute();

            if ($check->rowCount() > 0) {
                $row = $check->fetch(PDO::FETCH_OBJ); // Get the existing product details

                // Update the NumberOfTimes by adding the new value to the existing one
                $newNumberOfTimes = $row->NumberOfTimes + $numberoftimes;

                // Update existing product's NumberOfTimes
                $update = $pdo->prepare("UPDATE tbl_ProductInNeed SET ProductName=:name, ModifiedBy=:modifiedby, ModifiedDate=:modifieddate, NumberOfTimes=:numberoftimes WHERE ProductInNeedId=:id");
                $update->bindParam(':name', $productname);
                $update->bindParam(':modifiedby', $userid);
                $update->bindParam(':modifieddate', $date);
                $update->bindParam(':numberoftimes', $newNumberOfTimes);
                $update->bindParam(':id', $row->ProductInNeedId);

                if ($update->execute()) {
                    $_SESSION['status'] = "Le produit existe déjà, le nombre de fois recherché a été mis à jour";
                    $_SESSION['status_code'] = "success";
                } else {
                    $_SESSION['status'] = "Erreur lors de la mise à jour du produit";
                    $_SESSION['status_code'] = "error";
                }
            } else {
                // Insert new product in need
                $insert = $pdo->prepare("INSERT INTO tbl_ProductInNeed (ProductName, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, NumberOfTimes, IsDeleted) VALUES (:name, :createdby, :modifiedby, :createddate, :modifieddate, :numberoftimes, 0)");
                $insert->bindParam(':name', $productname);
                $insert->bindParam(':createdby', $userid);
                $insert->bindParam(':modifiedby', $userid);
                $insert->bindParam(':createddate', $date);
                $insert->bindParam(':modifieddate', $date);
                $insert->bindParam(':numberoftimes', $numberoftimes);

                if ($insert->execute()) {
                    $_SESSION['status'] = "Produit ajouté avec succès";
                    $_SESSION['status_code'] = "success";
                } else {
                    $_SESSION['status'] = "Erreur lors de l'ajout du produit";
                    $_SESSION['status_code'] = "error";
                }
            }
        }
    }
}

// Handle deleting a product in need
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $pdo->prepare("UPDATE tbl_ProductInNeed SET IsDeleted = 1 WHERE ProductInNeedId = :id");
    $delete->bindParam(':id', $id);

    if ($delete->execute()) {
        $_SESSION['status'] = "Produit supprimé avec succès";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Le produit n'est pas supprimé";
        $_SESSION['status_code'] = "warning";
    }
}

// Pagination variables
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page or set default to 1
$start = ($page - 1) * $limit; // Calculate the starting point

// Fetch total number of products
$totalQuery = $pdo->prepare("SELECT COUNT(*) as total FROM tbl_ProductInNeed WHERE IsDeleted = 0");
$totalQuery->execute();
$totalResult = $totalQuery->fetch(PDO::FETCH_OBJ);
$total = $totalResult->total;

// Calculate total pages
$totalPages = ceil($total / $limit);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestion des produits en demande</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="m-0">Formulaire des produits en demande</h5>
                </div>
                <div class="card-body">
                    <!-- Display session messages -->
                    <?php
                    if (isset($_SESSION['status'])) {
                        echo '<div class="alert alert-' . $_SESSION['status_code'] . '">' . $_SESSION['status'] . '</div>';
                        unset($_SESSION['status']); // Clear the message after displaying
                    }
                    ?>
                    <div class="row">
                        <!-- Product Form -->
                        <div class="col-md-4">
                            <form action="" method="post">
                                <?php
                                    if (isset($_POST['btnedit'])) {
                                        $productid = $_POST['btnedit'];
                                        $select = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE ProductInNeedId = :id");
                                        $select->bindParam(':id', $productid);
                                        $select->execute();
                                        $row = $select->fetch(PDO::FETCH_OBJ);

                                        if ($row) {
                                            echo '
                                                <input type="hidden" name="txtproductid" value="' . $row->ProductInNeedId . '">
                                                <div class="form-group">
                                                    <label for="productname">Nom du produit</label>
                                                    <input type="text" class="form-control" id="txtproductname" name="txtproductname" value="' . $row->ProductName . '" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="numberoftimes">Nombre de fois recherché</label>
                                                    <input type="number" class="form-control" name="txtnumberoftimes" value="' . $row->NumberOfTimes . '" required>
                                                </div>
                                                <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>';
                                        }
                                    }
                                    else {
                                        echo '
                                            <div class="form-group">
                                                <label for="productname">Nom du produit</label>
                                                <input type="text" class="form-control" id="txtproductname" name="txtproductname" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="numberoftimes">Nombre de fois recherché</label>
                                                <input type="number" class="form-control" name="txtnumberoftimes" value="0" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>';
                                    }
                                ?>
                            </form>
                            <br>
                            <!-- Container to display AJAX search results -->
                            <div id="search-results"></div>
                        </div>

                        <!-- Products List -->
                        <div class="col-md-8">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <td>Nom du produit</td>
                                        <td>Nombre de fois recherché</td>
                                        <td>Modifier</td>
                                        <td>Supprimer</td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Fetch and display all products in need that are not deleted
                                //$select = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE IsDeleted = 0 ORDER BY ProductInNeedId ASC");
                                //$select->execute();
                                $select = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE IsDeleted = 0 ORDER BY ProductInNeedId DESC LIMIT :start, :limit");
                                $select->bindParam(':start', $start, PDO::PARAM_INT);
                                $select->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $select->execute();

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    echo '
                                    <tr>
                                        <td>'.$row->ProductName.'</td>
                                        <td>'.$row->NumberOfTimes.'</td>
                                        <td>
                                            <form method="post" action="">
                                                <button type="submit" name="btnedit" class="btn btn-primary" value="'.$row->ProductInNeedId.'">Modifier</button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="ProductMissing.php?id='.$row->ProductInNeedId.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                            <!-- Pagination links -->
                            <nav>
                                <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Précédent</a></li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Suivant</a></li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Attach keyup event to the product name input field
        $('#txtproductname').on('keyup', function() {
            var query = $(this).val(); // Get the value from the input field
            if (query.length > 0) { // Only search if the query length is greater than 0
                $.ajax({
                    url: 'searchProduct.php', // PHP script that will handle the search
                    method: 'POST', // Method type
                    data: {query: query}, // Data to send (the search query)
                    success: function(data) {
                        $('#search-results').html(data); // Display the search results in the 'search-results' div
                    }
                });
            } else {
                $('#search-results').html(''); // Clear the search results if the input is empty
            }
        });
    });
</script>


<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        var numberOfTimes = document.querySelector('input[name="txtnumberoftimes"]').value;
        if (numberOfTimes <= 0) {
            alert("Le nombre de fois recherché doit être supérieur à 0.");
            event.preventDefault(); // Prevent the form from being submitted
        }
    });
</script>

<?php
    include_once "footer.php";
?>