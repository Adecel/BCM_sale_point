<?php
include_once 'connectdb.php';
session_start();

// Check if the session 'role' is set and load the appropriate header
if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once 'header.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Manager') {
    include_once 'ManagerHeader.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Stockeur') {
    include_once 'StockerHeader.php';
} else {
    header('location:../index.php');
    exit();
}

// Save new product status
if (isset($_POST['btnsave'])) {
    $code = $_POST['txtcode'];
    $description = $_POST['txtdescription'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($code) || empty($description)) {
        $_SESSION['status'] = "Le code ou la description est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $insert = $pdo->prepare("INSERT INTO tProductStatus (Code, Description, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, IsDeleted) 
                                 VALUES (:code, :description, :createdBy, :modifiedBy, :createdDate, :modifiedDate, 0)");
        $insert->bindParam(':code', $code);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':createdBy', $username);
        $insert->bindParam(':modifiedBy', $username);
        $insert->bindParam(':createdDate', $currentDate);
        $insert->bindParam(':modifiedDate', $currentDate);

        if ($insert->execute()) {
            $_SESSION['status'] = "Statut produit ajouté avec succès";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec de l'ajout du statut produit";
            $_SESSION['status_code'] = "warning";
        }
    }
}

// Update existing product status
if (isset($_POST['btnupdate'])) {
    $code = $_POST['txtcode'];
    $description = $_POST['txtdescription'];
    $id = $_POST['txtstatusid'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($code) || empty($description)) {
        $_SESSION['status'] = "Le code ou la description est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $update = $pdo->prepare("UPDATE tProductStatus 
                                 SET Code = :code, Description = :description, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                                 WHERE ProductStatusId = :id");
        $update->bindParam(':code', $code);
        $update->bindParam(':description', $description);
        $update->bindParam(':modifiedBy', $username);
        $update->bindParam(':modifiedDate', $currentDate);
        $update->bindParam(':id', $id);

        if ($update->execute()) {
            $_SESSION['status'] = "Mise à jour du statut produit réussie";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec de la mise à jour du statut produit";
            $_SESSION['status_code'] = "warning";
        }
    }
}

// Delete product status (soft delete)
if (isset($_POST['btndelete'])) {
    $id = $_POST['btndelete'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    $delete = $pdo->prepare("UPDATE tProductStatus 
                             SET IsDeleted = 1, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                             WHERE ProductStatusId = :id");
    $delete->bindParam(':id', $id);
    $delete->bindParam(':modifiedBy', $username);
    $delete->bindParam(':modifiedDate', $currentDate);

    if ($delete->execute()) {
        $_SESSION['status'] = "Statut produit supprimé avec succès";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Échec de la suppression du statut produit";
        $_SESSION['status_code'] = "warning";
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Statuts Produit</h1>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h5 class="m-0">Formulaire de statut produit</h5>
                </div>

                <form action="" method="post">
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Edit product status form
                            if (isset($_POST['btnedit'])) {
                                $select = $pdo->prepare("SELECT * FROM tProductStatus WHERE ProductStatusId = :id AND IsDeleted = 0");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtcode">Code</label>
                                            <input type="hidden" class="form-control" name="txtstatusid" value="' . $row->ProductStatusId . '">
                                            <input type="text" class="form-control" name="txtcode" value="' . $row->Code . '">
                                        </div>
                                        <div class="form-group">
                                            <label for="txtdescription">Description</label>
                                            <input type="text" class="form-control" name="txtdescription" value="' . $row->Description . '">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-info" name="btnupdate">Mettre à jour</button>
                                        </div>
                                    </div>';
                                }
                            } else {
                                // Add new product status form
                                echo '
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtcode">Code</label>
                                        <input type="text" class="form-control" name="txtcode" placeholder="Enter Code">
                                    </div>
                                    <div class="form-group">
                                        <label for="txtdescription">Description</label>
                                        <input type="text" class="form-control" name="txtdescription" placeholder="Enter Description">
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning" name="btnsave">Sauvegarder</button>
                                    </div>
                                </div>';
                            }
                            ?>

                            <!-- Product Status List with Pagination -->
                            <div class="col-md-8">
                                <table id="table_productstatus" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tProductStatus WHERE IsDeleted = 0 ORDER BY ProductStatusId ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <tr>
                                                <td>' . $row->Code . '</td>
                                                <td>' . $row->Description . '</td>
                                                <td><button type="submit" class="btn btn-primary" value="' . $row->ProductStatusId . '" name="btnedit">Modifier</button></td>
                                                <td><button type="submit" class="btn btn-danger" value="' . $row->ProductStatusId . '" name="btndelete">Supprimer</button></td>
                                            </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>

<?php
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    echo "
    <script>
        Swal.fire({
            icon: '" . $_SESSION['status_code'] . "',
            title: '" . $_SESSION['status'] . "'
        });
    </script>";
    unset($_SESSION['status']);
}
?>

<script>
    $(document).ready(function() {
        $('#table_productstatus').DataTable({
            "paging": true,
            "pageLength": 10
        });
    });
</script>
