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
    // Redirect to the login page or access denied page if role doesn't match
    header('location:../index.php');
    exit();
}

// Save new category
if (isset($_POST['btnsave'])) {
    $category = $_POST['txtcategory'];

    if (empty($category)) {
        $_SESSION['status'] = "La catégorie est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $insert = $pdo->prepare("INSERT INTO tbl_category (category) VALUES (:cat)");
        $insert->bindParam(':cat', $category);

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

    if (empty($category)) {
        $_SESSION['status'] = "La catégorie est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $update = $pdo->prepare("UPDATE tbl_category SET category = :cat WHERE catid = :id");
        $update->bindParam(':cat', $category);
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

// Delete category
if (isset($_POST['btndelete'])) {
    $delete = $pdo->prepare("DELETE FROM tbl_category WHERE catid = :id");
    $delete->bindParam(':id', $_POST['btndelete']);

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
                                $select = $pdo->prepare("SELECT * FROM tbl_category WHERE catid = :id");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtcategory">Category</label>
                                            <input type="hidden" class="form-control" name="txtcatid" value="' . $row->catid . '">
                                            <input type="text" class="form-control" name="txtcategory" value="' . $row->category . '">
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

                            <!-- Category List -->
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
                                    $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <tr>
                                                <td>' . $row->category . '</td>
                                                <td><button type="submit" class="btn btn-primary" value="' . $row->catid . '" name="btnedit">Modifier</button></td>
                                                <td><button type="submit" class="btn btn-danger" value="' . $row->catid . '" name="btndelete">Supprimer</button></td>
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
        $('#table_category').DataTable();
    });
</script>