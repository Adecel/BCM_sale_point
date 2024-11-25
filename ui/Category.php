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

// Save new category
if (isset($_POST['btnsave'])) {
    $category = $_POST['txtcategory'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($category)) {
        $_SESSION['status'] = "La catégorie est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $insert = $pdo->prepare("INSERT INTO tCategory (CategoryName, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, IsDeleted) 
                                 VALUES (:cat, :createdBy, :modifiedBy, :createdDate, :modifiedDate, 0)");
        $insert->bindParam(':cat', $category);
        $insert->bindParam(':createdBy', $username);
        $insert->bindParam(':modifiedBy', $username);
        $insert->bindParam(':createdDate', $currentDate);
        $insert->bindParam(':modifiedDate', $currentDate);

        if ($insert->execute()) {
            $_SESSION['status'] = "Catégorie ajoutée avec succès";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec de l'ajout de la catégorie";
            $_SESSION['status_code'] = "warning";
        }
    }
}

// Update existing category
if (isset($_POST['btnupdate'])) {
    $category = $_POST['txtcategory'];
    $id = $_POST['txtcatid'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($category)) {
        $_SESSION['status'] = "La catégorie est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $update = $pdo->prepare("UPDATE tCategory 
                                 SET CategoryName = :cat, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                                 WHERE CategoryId = :id");
        $update->bindParam(':cat', $category);
        $update->bindParam(':modifiedBy', $username);
        $update->bindParam(':modifiedDate', $currentDate);
        $update->bindParam(':id', $id);

        if ($update->execute()) {
            $_SESSION['status'] = "Mise à jour de la catégorie réussie";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec de la mise à jour de la catégorie";
            $_SESSION['status_code'] = "warning";
        }
    }
}

// Delete category (soft delete)
if (isset($_POST['btndelete'])) {
    $id = $_POST['btndelete'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    $delete = $pdo->prepare("UPDATE tCategory 
                             SET IsDeleted = 1, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                             WHERE CategoryId = :id");
    $delete->bindParam(':id', $id);
    $delete->bindParam(':modifiedBy', $username);
    $delete->bindParam(':modifiedDate', $currentDate);

    if ($delete->execute()) {
        $_SESSION['status'] = "Catégorie supprimée avec succès";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Échec de la suppression de la catégorie";
        $_SESSION['status_code'] = "warning";
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Catégories</h1>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h5 class="m-0">Formulaire de catégorie</h5>
                </div>

                <form action="" method="post">
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Edit category form
                            if (isset($_POST['btnedit'])) {
                                $select = $pdo->prepare("SELECT * FROM tCategory WHERE CategoryId = :id AND IsDeleted = 0");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtcategory">Category</label>
                                            <input type="hidden" class="form-control" name="txtcatid" value="' . $row->CategoryId . '">
                                            <input type="text" class="form-control" name="txtcategory" value="' . $row->CategoryName . '">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-info" name="btnupdate">Mettre à jour</button>
                                        </div>
                                    </div>';
                                }
                            } else {
                                // Add new category form
                                echo '
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtcategory">Category</label>
                                        <input type="text" class="form-control" name="txtcategory" placeholder="Enter Category">
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning" name="btnsave">Sauvegarder</button>
                                    </div>
                                </div>';
                            }
                            ?>

                            <!-- Category List with Pagination -->
                            <div class="col-md-8">
                                <table id="table_category" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tCategory WHERE IsDeleted = 0 ORDER BY CategoryId ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <tr>
                                                <td>' . $row->CategoryName . '</td>
                                                <td><button type="submit" class="btn btn-primary" value="' . $row->CategoryId . '" name="btnedit">Modifier</button></td>
                                                <td><button type="submit" class="btn btn-danger" value="' . $row->CategoryId . '" name="btndelete">Supprimer</button></td>
                                            </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nom</th>
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
        $('#table_category').DataTable({
            "paging": true,
            "pageLength": 10
        });
    });
</script>
