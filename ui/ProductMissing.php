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
        if ($productid) {
            // Update existing product in need
            $update = $pdo->prepare("UPDATE tbl_ProductInNeed SET ProductName=:name, ModifiedBy=:modifiedby, ModifiedDate=:modifieddate, NumberOfTimes=:numberoftimes WHERE ProductInNeedId=:id");
            $update->bindParam(':name', $productname);
            $update->bindParam(':modifiedby', $userid);
            $update->bindParam(':modifieddate', $date);
            $update->bindParam(':numberoftimes', $numberoftimes);
            $update->bindParam(':id', $productid);

            if ($update->execute()) {
                $_SESSION['status'] = "Produit mis à jour avec succès";
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
                                        <input type="hidden" name="txtproductid" value="'.$row->ProductInNeedId.'">
                                        <div class="form-group">
                                            <label for="productname">Nom du produit</label>
                                            <input type="text" class="form-control" name="txtproductname" value="'.$row->ProductName.'" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="numberoftimes">Nombre de fois recherché</label>
                                            <input type="number" class="form-control" name="txtnumberoftimes" value="'.$row->NumberOfTimes.'" required>
                                        </div>
                                        <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>';
                                    }
                                } else {
                                    echo '
                                    <div class="form-group">
                                        <label for="productname">Nom du produit</label>
                                        <input type="text" class="form-control" name="txtproductname" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="numberoftimes">Nombre de fois recherché</label>
                                        <input type="number" class="form-control" name="txtnumberoftimes" value="0" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>';
                                }
                                ?>
                            </form>
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
                                $select = $pdo->prepare("SELECT * FROM tbl_ProductInNeed WHERE IsDeleted = 0 ORDER BY ProductInNeedId ASC");
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
                                            <a href="ProductMissing01.php?id='.$row->ProductInNeedId.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php
    include_once "footer.php";
?>