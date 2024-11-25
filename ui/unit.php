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

    if (empty($unit)) {
        $_SESSION['status'] = "Unité est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $insert = $pdo->prepare("INSERT INTO tUnit (unitname) VALUES (:cat)");
        $insert->bindParam(':cat', $unit);

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
    $id = $_POST['txtunitid'];

    if (empty($unit)) {
        $_SESSION['status'] = "Unité est vide";
        $_SESSION['status_code'] = "warning";
    } else {
        $update = $pdo->prepare("UPDATE tUnit SET unitname = :cat WHERE unitid = :id");
        $update->bindParam(':cat', $unit);
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

    $delete = $pdo->prepare("DELETE FROM tUnit WHERE unitid = :id");
    $delete->bindParam(':id', $id);

    if ($delete->execute()) {
        $_SESSION['status'] = "Supprimé";
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
                            if (isset($_POST['btnedit'])) {
                                $select = $pdo->prepare("SELECT * FROM tUnit WHERE unitid = :id");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);
                                    echo '
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Unité</label>
                        <input type="hidden" class="form-control" placeholder="Entrez l\'unité" value="' . $row->unitid . '" name="txtunitid">
                        <input type="text" class="form-control" placeholder="Entrez l\'unité" value="' . $row->unitname . '" name="txtunit">
                      </div>
                      <div class="card-footer">
                        <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>
                      </div>
                    </div>';
                                }
                            } else {
                                echo '
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Unité</label>
                      <input type="text" class="form-control" placeholder="Entrez l\'unité" name="txtunit">
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
                    </div>
                  </div>';
                            }
                            ?>
                            <div class="col-md-8">
                                <table id="table_unit" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <td>Unité</td>
                                        <td>Modifier</td>
                                        <td>Supprimer</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tUnit ORDER BY unitid ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                        <tr>
                          <td>' . $row->unitname . '</td>
                          <td>
                            <button type="submit" class="btn btn-primary" value="' . $row->unitid . '" name="btnedit">Modifier</button>
                          </td>
                          <td>
                            <button type="submit" class="btn btn-danger" value="' . $row->unitid . '" name="btndelete">Supprimer</button>
                          </td>
                        </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>Unité</td>
                                        <td>Modifier</td>
                                        <td>Supprimer</td>
                                    </tr>
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