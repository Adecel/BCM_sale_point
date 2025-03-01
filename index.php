<?php
include_once "ui/connectdb.php";
session_start();

if (isset($_POST['btn_login'])) {
    $username = $_POST['txt_username'];
    $password = $_POST['txt_password'];

    // Prepare and execute query
    $select = $pdo->prepare("SELECT * FROM tUser WHERE username = :username AND userpassword = :password");
    $select->bindParam(':username', $username);
    $select->bindParam(':password', $password);
    $select->execute();

    $row = $select->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        // Check for Admin role
        if ($row['username'] == $username && $row['userpassword'] == $password && $row['role'] == "Admin") {
            $_SESSION['status'] = "Connexion réussie par administrateur";
            $_SESSION['status_code'] = "success";

            // Set session variables
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['useremail'] = $row['useremail'];
            $_SESSION['role'] = $row['role'];

            header('refresh: 1;ui/Dashboard.php');
        }
        // Check for Utilisateur role
        else if ($row['username'] == $username && $row['userpassword'] == $password && $row['role'] == "Utilisateur") {
            $_SESSION['status'] = "Connexion réussie par utilisateur";
            $_SESSION['status_code'] = "success";

            // Set session variables
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['useremail'] = $row['useremail'];
            $_SESSION['role'] = $row['role'];

            header('refresh: 3;ui/UserDashboard.php');
        }
        // Check for Manager role
        else if ($row['username'] == $username && $row['userpassword'] == $password && $row['role'] == "Manager") {
            $_SESSION['status'] = "Connexion réussie par Manager";
            $_SESSION['status_code'] = "success";

            // Set session variables
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['useremail'] = $row['useremail'];
            $_SESSION['role'] = $row['role'];

            header('refresh: 4;ui/ManagerDashboard.php');
        }
        // Check for Stoker role
        else if ($row['username'] == $username && $row['userpassword'] == $password && $row['role'] == "Stockeur") {
            $_SESSION['status'] = "Connexion réussie par Stockeur";
            $_SESSION['status_code'] = "success";

            // Set session variables
            $_SESSION['userid'] = $row['userid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['useremail'] = $row['useremail'];
            $_SESSION['role'] = $row['role'];

            header('refresh: 2;ui/StockerDashboard.php');
        }
        }else {
            // Wrong username or password
            $_SESSION['status'] = "Nom utilisateur ou mot de passe erroné";
            $_SESSION['status_code'] = "error";

            header('refresh: 1;ui/UserDashboard.php');
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BCM | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="../../index2.html" class="h1"><b>BCM </b>- LOGIN</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Connectez-vous pour démarrer votre session</p>

            <form action="" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="nom d'utilisateur" name="txt_username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Mot de passe" name="txt_password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <a href="forgot-password.html">j'ai oublié mon mot de passe</a>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block" name="btn_login">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<?php
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
    <script>
        $(function () {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 5000
            });

            Toast.fire({
                icon: '<?php echo $_SESSION['status_code']; ?>',
                title: '<?php echo $_SESSION['status']; ?>'
            });
        });
    </script>
    <?php
    unset($_SESSION['status']);
}
?>
</body>

</html>