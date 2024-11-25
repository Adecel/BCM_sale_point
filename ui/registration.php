<?php
include_once 'connectdb.php';
session_start();

// Check if the session 'role' is set and load the appropriate header
if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once 'header.php';
} else {
    // Redirect to the login page or access denied page if role doesn't match
    header('location:../index.php');
    exit();
}

error_reporting(0);

// Handle saving or updating a user
if (isset($_POST['btnsave']) || isset($_POST['btnupdate'])) {
    $username = $_POST['txtname'];
    $useremail = $_POST['txtemail'];
    $userpassword = $_POST['txtpassword'];
    $userrole = $_POST['txtselect_option'];
    $userid = isset($_POST['txtuserid']) ? $_POST['txtuserid'] : null;

    if (empty($username) || empty($useremail) || empty($userpassword) || empty($userrole)) {
        $_SESSION['status'] = "Tous les champs sont obligatoires";
        $_SESSION['status_code'] = "warning";
    } else {
        if ($userid) {
            // Update existing user
            $update = $pdo->prepare("UPDATE tUser SET username=:name, useremail=:email, userpassword=:password, role=:role WHERE userid=:id");
            $update->bindParam(':name', $username);
            $update->bindParam(':email', $useremail);
            $update->bindParam(':password', $userpassword);
            $update->bindParam(':role', $userrole);
            $update->bindParam(':id', $userid);

            if ($update->execute()) {
                $_SESSION['status'] = "Utilisateur mis à jour avec succès";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Erreur lors de la mise à jour de l'utilisateur";
                $_SESSION['status_code'] = "error";
            }
        } else {
            // Insert new user
            $insert = $pdo->prepare("INSERT INTO tUser (username, useremail, userpassword, role) VALUES (:name, :email, :password, :role)");
            $insert->bindParam(':name', $username);
            $insert->bindParam(':email', $useremail);
            $insert->bindParam(':password', $userpassword);
            $insert->bindParam(':role', $userrole);

            if ($insert->execute()) {
                $_SESSION['status'] = "Utilisateur ajouté avec succès";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Erreur lors de l'ajout de l'utilisateur";
                $_SESSION['status_code'] = "error";
            }
        }
    }
}

// Handle deleting a user
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $pdo->prepare("DELETE FROM tUser WHERE userid = :id");
    $delete->bindParam(':id', $id);

    if ($delete->execute()) {
        $_SESSION['status'] = "Compte supprimé avec succès";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Le compte n'est pas supprimé";
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
                    <h1 class="m-0">Gestion des utilisateurs</h1>
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
                    <h5 class="m-0">Formulaire des utilisateurs</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Form -->
                        <div class="col-md-4">
                            <form action="" method="post">
                                <?php
                                if (isset($_POST['btnedit'])) {
                                    $userid = $_POST['btnedit'];
                                    $select = $pdo->prepare("SELECT * FROM tUser WHERE userid = :id");
                                    $select->bindParam(':id', $userid);
                                    $select->execute();
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    if ($row) {
                                        echo '
                                        <input type="hidden" name="txtuserid" value="'.$row->userid.'">
                                        <div class="form-group">
                                            <label for="username">Nom d\'utilisateur</label>
                                            <input type="text" class="form-control" name="txtname" value="'.$row->username.'" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">E-mail</label>
                                            <input type="email" class="form-control" name="txtemail" value="'.$row->useremail.'" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Mot de passe</label>
                                            <input type="password" class="form-control" name="txtpassword" value="'.$row->userpassword.'" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Rôle</label>
                                            <select class="form-control" name="txtselect_option" required>
                                                <option value="" disabled>Sélectionnez un rôle</option>
                                                <option '.($row->role == 'Admin' ? 'selected' : '').'>Admin</option>
                                                <option '.($row->role == 'Utilisateur' ? 'selected' : '').'>Utilisateur</option>
                                                <option '.($row->role == 'Manager' ? 'selected' : '').'>Manager</option>
                                                <option '.($row->role == 'Stockeur' ? 'selected' : '').'>Stockeur</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>';
                                    }
                                } else {
                                    echo '
                                    <div class="form-group">
                                        <label for="username">Nom d\'utilisateur</label>
                                        <input type="text" class="form-control" name="txtname" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="email" class="form-control" name="txtemail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Mot de passe</label>
                                        <input type="password" class="form-control" name="txtpassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Rôle</label>
                                        <select class="form-control" name="txtselect_option" required>
                                            <option value="" disabled selected>Sélectionnez un rôle</option>
                                            <option>Admin</option>
                                            <option>Utilisateur</option>
                                            <option>Manager</option>
                                            <option>Stockeur</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>';
                                }
                                ?>
                            </form>
                        </div>

                        <!-- Users List -->
                        <div class="col-md-8">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <td>Nom d'utilisateur</td>
                                    <td>E-mail</td>
                                    <td>Rôle</td>
                                    <td>Modifier</td>
                                    <td>Supprimer</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Fetch and display all users
                                $select = $pdo->prepare("SELECT * FROM tUser ORDER BY userid ASC");
                                $select->execute();

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    echo '
                                    <tr>
                                        <td>'.$row->username.'</td>
                                        <td>'.$row->useremail.'</td>
                                        <td>'.$row->role.'</td>
                                        <td>
                                            <form method="post" action="">
                                                <button type="submit" name="btnedit" class="btn btn-primary" value="'.$row->userid.'">Modifier</button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="registration.php?id='.$row->userid.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</a>
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
