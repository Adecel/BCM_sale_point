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

// Handle form submissions
if (isset($_POST['btnsave'])) {
    $unit = $_POST['txtunit'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($unit)) {
        $_SESSION['status'] = "Unité est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $insert = $pdo->prepare("INSERT INTO tUnit (UnitName, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, IsDeleted) 
                                 VALUES (:unit, :createdBy, :modifiedBy, :createdDate, :modifiedDate, 0)");
        $insert->bindParam(':unit', $unit);
        $insert->bindParam(':createdBy', $username);
        $insert->bindParam(':modifiedBy', $username);
        $insert->bindParam(':createdDate', $currentDate);
        $insert->bindParam(':modifiedDate', $currentDate);

        if ($insert->execute()) {
            $_SESSION['status'] = "Unité ajoutée avec succès";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec d'ajout de l'unité";
            $_SESSION['status_code'] = "warning";
        }
    }
}


if (isset($_POST['btnupdate'])) {
    $unit = $_POST['txtunit'];
    $id = $_POST['txtUnitId'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    if (empty($unit)) {
        $_SESSION['status'] = "Unité est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $update = $pdo->prepare("UPDATE tUnit 
                                 SET UnitName = :unit, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                                 WHERE UnitId = :id");
        $update->bindParam(':unit', $unit);
        $update->bindParam(':modifiedBy', $username);
        $update->bindParam(':modifiedDate', $currentDate);
        $update->bindParam(':id', $id);

        if ($update->execute()) {
            $_SESSION['status'] = "Unité mise à jour avec succès";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Échec de la mise à jour de l'unité";
            $_SESSION['status_code'] = "warning";
        }
    }
}


if (isset($_POST['btndelete'])) {
    $id = $_POST['btndelete'];
    $username = $_SESSION['username'];
    $currentDate = date('Y-m-d H:i:s');

    $delete = $pdo->prepare("UPDATE tUnit 
                             SET IsDeleted = 1, ModifiedBy = :modifiedBy, ModifiedDate = :modifiedDate 
                             WHERE UnitId = :id");
    $delete->bindParam(':id', $id);
    $delete->bindParam(':modifiedBy', $username);
    $delete->bindParam(':modifiedDate', $currentDate);

    if ($delete->execute()) {
        $_SESSION['status'] = "Unité supprimée avec succès";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Échec de la suppression";
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
                    <h1 class="m-0">Les Unités</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Starter Page</li> -->
                    </ol>
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
                    <h5 class="m-0">Formulaire des Unités</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="row">
                            <?php
                            // Edit unit form
                            if (isset($_POST['btnedit'])) {
                                $select = $pdo->prepare("SELECT * FROM tUnit WHERE UnitId = :id AND IsDeleted = 0");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtunit">Unité</label>
                                            <input type="hidden" class="form-control" name="txtUnitId" value="' . $row->UnitId . '">
                                            <input type="text" class="form-control" name="txtunit" value="' . $row->UnitName . '">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-info" name="btnupdate">Mettre à jour</button>
                                        </div>
                                    </div>';
                                }
                                } else {
                                    // Add new unit form
                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtunit">Unité</label>
                                            <input type="text" class="form-control" name="txtunit" placeholder="Entrez l\'unité">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
                                        </div>
                                    </div>';
                                }
                            ?>

                            <!-- Unit List with Pagination -->
                            <div class="col-md-8">
                                <table id="table_unit" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Unité</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    // Fetch units where IsDeleted is 0 (active units)
                                    $select = $pdo->prepare("SELECT * FROM tUnit WHERE IsDeleted = 0 ORDER BY UnitId ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                        <tr>
                                            <td>' . $row->UnitName . '</td>
                                            <td><button type="submit" class="btn btn-primary" value="' . $row->UnitId . '" name="btnedit">Modifier</button></td>
                                            <td><button type="submit" class="btn btn-danger" value="' . $row->UnitId . '" name="btndelete">Supprimer</button></td>
                                        </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <!-- <tr>
                                        <th>Unité</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr> -->
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include_once "footer.php";

if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['status_code']; ?>',
            title: '<?php echo $_SESSION['status']; ?>'
        });
    </script>
    <?php
    unset($_SESSION['status']);
}
?>

<script>
    $(document).ready(function () {
        $('#table_unit').DataTable();
    });
</script>

</body>
</html>