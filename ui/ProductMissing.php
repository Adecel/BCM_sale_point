<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'connectdb.php';
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    } elseif ($_SESSION['role'] == 'Utilisateur') {
        include_once 'UserHeader.php';
    } else {
        header('location:../index.php');
        exit();
    }
} else {
    header('location:../index.php');
    exit();
}

// Fetch distinct product names for the dropdown
$productNamesQuery = $pdo->prepare("SELECT DISTINCT ProductInNeedName FROM tProductInNeed WHERE IsDeleted = 0");
$productNamesQuery->execute();
$productNames = $productNamesQuery->fetchAll(PDO::FETCH_OBJ);

// Function to determine ProductInNeedStatusId
function getProductInNeedStatusId($numberoftimes) {
    if ($numberoftimes < 10) {
        return 1;
    } elseif ($numberoftimes >= 10 && $numberoftimes <= 20) {
        return 2;
    } else {
        return 3;
    }
}

// Handle saving or updating a product in need
if (isset($_POST['btnsave']) || isset($_POST['btnupdate'])) {
    $productname = $_POST['txtproductname'];
    $userid = $_SESSION['username'];
    $productid = isset($_POST['txtproductid']) ? $_POST['txtproductid'] : null;
    $numberoftimes = isset($_POST['txtnumberoftimes']) ? $_POST['txtnumberoftimes'] : 0;

    // Determine ProductInNeedStatusId based on NumberOfTimes
    $statusId = getProductInNeedStatusId($numberoftimes);

    // Check if EstimatePrice is set and is a valid number, otherwise set it to NULL
    $estimateprice = isset($_POST['txtestimateprice']) && $_POST['txtestimateprice'] !== '' ? $_POST['txtestimateprice'] : null;

    if (empty($productname)) {
        $_SESSION['status'] = "Le nom du produit est obligatoire";
        $_SESSION['status_code'] = "warning";
    } else {
        $date = date('Y-m-d');
        if ($numberoftimes <= 0) {
            $_SESSION['status'] = "Le nombre de fois recherché doit être supérieur à 0";
            $_SESSION['status_code'] = "warning";
        } else {
            $check = $pdo->prepare("SELECT * FROM tProductInNeed WHERE ProductInNeedName = :name AND IsDeleted = 0");
            $check->bindParam(':name', $productname);
            $check->execute();

            if ($check->rowCount() > 0) {
                // If product already exists, update it
                $row = $check->fetch(PDO::FETCH_OBJ);
                $newNumberOfTimes = $row->NumberOfTimes + $numberoftimes;
                $newStatusId = getProductInNeedStatusId($newNumberOfTimes);

                $update = $pdo->prepare("UPDATE tProductInNeed 
                                         SET ProductInNeedName=:name, ModifiedBy=:modifiedby, ModifiedDate=:modifieddate, 
                                             NumberOfTimes=:numberoftimes, EstimatePrice=:estimateprice, 
                                             ProductInNeedStatusId=:statusId 
                                         WHERE ProductInNeedId=:id");
                $update->bindParam(':name', $productname);
                $update->bindParam(':modifiedby', $userid);
                $update->bindParam(':modifieddate', $date);
                $update->bindParam(':numberoftimes', $newNumberOfTimes);
                $update->bindParam(':statusId', $newStatusId);
                $update->bindParam(':id', $row->ProductInNeedId);
                
                // Bind the EstimatePrice only if it has a value; otherwise, keep the previous value
                if ($estimateprice !== null) {
                    $update->bindParam(':estimateprice', $estimateprice, PDO::PARAM_STR);
                } else {
                    $update->bindParam(':estimateprice', $row->EstimatePrice, PDO::PARAM_STR);
                }

                if ($update->execute()) {
                    $_SESSION['status'] = "Le produit existe déjà, le nombre de fois recherché a été mis à jour";
                    $_SESSION['status_code'] = "success";
                } else {
                    $_SESSION['status'] = "Erreur lors de la mise à jour du produit";
                    $_SESSION['status_code'] = "error";
                }
            } else {
                // Insert a new product
                $insert = $pdo->prepare("INSERT INTO tProductInNeed 
                                         (ProductInNeedName, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, 
                                          NumberOfTimes, EstimatePrice, ProductInNeedStatusId, IsDeleted) 
                                         VALUES (:name, :createdby, :modifiedby, :createddate, :modifieddate, 
                                                 :numberoftimes, :estimateprice, :statusId, 0)");
                $insert->bindParam(':name', $productname);
                $insert->bindParam(':createdby', $userid);
                $insert->bindParam(':modifiedby', $userid);
                $insert->bindParam(':createddate', $date);
                $insert->bindParam(':modifieddate', $date);
                $insert->bindParam(':numberoftimes', $numberoftimes);
                $insert->bindParam(':statusId', $statusId);
                
                // Bind the EstimatePrice, set as NULL if it's empty
                if ($estimateprice !== null) {
                    $insert->bindParam(':estimateprice', $estimateprice, PDO::PARAM_STR);
                } else {
                    $insert->bindValue(':estimateprice', null, PDO::PARAM_NULL);
                }

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
    $delete = $pdo->prepare("UPDATE tProductInNeed SET IsDeleted = 1 WHERE ProductInNeedId = :id");
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
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$totalQuery = $pdo->prepare("SELECT COUNT(*) as total FROM tProductInNeed WHERE IsDeleted = 0");
$totalQuery->execute();
$totalResult = $totalQuery->fetch(PDO::FETCH_OBJ);
$total = $totalResult->total;
$totalPages = ceil($total / $limit);

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestion des produits en demande</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="m-0">Formulaire des produits en demande</h5>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($_SESSION['status'])) {
                        echo '<div class="alert alert-' . $_SESSION['status_code'] . '">' . $_SESSION['status'] . '</div>';
                        unset($_SESSION['status']);
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <form action="" method="post">
                                <?php
                                    if (isset($_POST['btnedit'])) {
                                        $productid = $_POST['btnedit'];
                                        $select = $pdo->prepare("SELECT * FROM tProductInNeed WHERE ProductInNeedId = :id");
                                        $select->bindParam(':id', $productid);
                                        $select->execute();
                                        $row = $select->fetch(PDO::FETCH_OBJ);

                                        if ($row) {
                                            echo '
                                                <input type="hidden" name="txtproductid" value="' . $row->ProductInNeedId . '">
                                                <div class="form-group">
                                                    <label for="productname">Nom du produit</label>
                                                    <select class="form-control" id="txtproductname" name="txtproductname">
                                                        <option value="' . $row->ProductInNeedName . '" selected>' . $row->ProductInNeedName . '</option>';
                                            foreach ($productNames as $product) {
                                                echo '<option value="' . $product->ProductInNeedName . '">' . $product->ProductInNeedName . '</option>';
                                            }
                                            echo '</select></div>
                                                <div class="form-group">
                                                    <label for="numberoftimes">Nombre de fois recherché</label>
                                                    <input type="number" class="form-control" name="txtnumberoftimes" value="' . $row->NumberOfTimes . '" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="estimateprice">Prix estimé</label>
                                                    <input type="number" class="form-control" step="0.01" name="txtestimateprice" value="' . ($row->EstimatePrice ? $row->EstimatePrice : '') . '">
                                                </div>
                                                <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>';
                                        }
                                    }
                                    else {
                                        echo '
                                            <div class="form-group">
                                                <label for="productname">Nom du produit</label>
                                                <select class="form-control" id="txtproductname" name="txtproductname">
                                                    <option value="">Sélectionner un produit</option>';
                                        foreach ($productNames as $product) {
                                            echo '<option value="' . $product->ProductInNeedName . '">' . $product->ProductInNeedName . '</option>';
                                        }
                                        echo '</select></div>
                                            <div class="form-group">
                                                <label for="numberoftimes">Nombre de fois recherché</label>
                                                <input type="number" class="form-control" name="txtnumberoftimes" value="0" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="estimateprice">Prix estimé</label>
                                                <input type="number" class="form-control" step="0.01" name="txtestimateprice">
                                            </div>
                                            <button type="submit" class="btn btn-success" name="btnsave">Enregistrer</button>';
                                    }
                                ?>
                            </form>
                        </div>

                        <div class="col-md-8">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom du produit</th>
                                        <th>Nombre de fois recherché</th>
                                        <th>Prix estimé</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list">
                                    <?php
                                    //$select = $pdo->prepare("SELECT * FROM tProductInNeed WHERE IsDeleted = 0 LIMIT $start, $limit");
                                    $select = $pdo->prepare("SELECT * FROM tProductInNeed WHERE IsDeleted = 0 ORDER BY ModifiedDate DESC LIMIT $start, $limit");
                                    $select->execute();
                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <tr>
                                                <td>' . $row->ProductInNeedName . '</td>
                                                <td>' . $row->NumberOfTimes . '</td>
                                                <td>' . ($row->EstimatePrice ? number_format($row->EstimatePrice, 2) : 'N/A') . '</td>
                                                <td>
                                                    <form method="post" action="">
                                                        <button type="submit" name="btnedit" class="btn btn-primary" value="' . $row->ProductInNeedId . '">Modifier</button>
                                                    </form>
                                                    <a href="ProductMissing.php?id=' . $row->ProductInNeedId . '" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</a>
                                                </td>
                                            </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JQuery and Select2 JS to handle searchable dropdown -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
<?php

// Assuming the rest of the code remains unchanged above
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 with an option to allow user input
    $('#txtproductname').select2({
        placeholder: 'Sélectionner ou taper pour ajouter un produit',
        allowClear: true,
        tags: true,  // Allow tags (custom input)
        createTag: function(params) {
            var term = params.term;
            return {
                id: term,
                text: term,
                newOption: true  // Mark this option as a new custom entry
            };
        }
    });

    // Detect if a user typed in a new product name and show the confirmation popup
    $('#txtproductname').on('select2:select', function (e) {
        var data = e.params.data;
        
        if (data.newOption) {
            var newProductName = data.text; // Get the typed product name

            // Show confirmation to add the new product
            if (confirm('Voulez-vous ajouter ce produit: ' + newProductName + '?')) {
                // Set the typed product name in the "Nom du produit" field
                $('#txtproductname').val(newProductName).trigger('change');
                
                // Optionally, focus on the next input (Nombre de fois recherché)
                $('#txtnumberoftimes').focus();
            }
        }
    });
});

</script>

<?php
// Handle inserting a new product if it's typed in the dropdown
if (isset($_POST['newproduct'])) {
    $newProductName = $_POST['newproduct'];
    $userid = $_SESSION['username'];
    $date = date('Y-m-d');
    
    // Insert the new product into the database
    $insertNewProduct = $pdo->prepare("INSERT INTO tProductInNeed (ProductInNeedName, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, NumberOfTimes, EstimatePrice, IsDeleted) 
                                      VALUES (:name, :createdby, :modifiedby, :createddate, :modifieddate, 0, NULL, 0)");
    $insertNewProduct->bindParam(':name', $newProductName);
    $insertNewProduct->bindParam(':createdby', $userid);
    $insertNewProduct->bindParam(':modifiedby', $userid);
    $insertNewProduct->bindParam(':createddate', $date);
    $insertNewProduct->bindParam(':modifieddate', $date);

    if ($insertNewProduct->execute()) {
        $_SESSION['status'] = "Produit ajouté avec succès!";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Erreur lors de l'ajout du produit!";
        $_SESSION['status_code'] = "error";
    }
}
?>

<?php include_once 'footer.php'; ?>

