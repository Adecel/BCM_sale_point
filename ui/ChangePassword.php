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
} else{
    include_once "UserHeader.php";
}

// 1 Step) When user click on updatepassword button then we get out values from user into php variables.

if(isset($_POST['btnupdate'])){

$oldpassword_txt=$_POST['txt_oldpassword'];
$newpassword_txt=$_POST['txt_newpassword'];
$rnewpassword_txt=$_POST['txt_rnewpassword'];

//echo $oldpassword_txt."-".$newpassword_txt."-".$rnewpassword_txt;

// 2 Step) Using of select Query we will get out database records according to useremail.

$email = $_SESSION['useremail'];

$select =$pdo->prepare("select * from tbl_user where useremail='$email'");

$select->execute();
$row=$select->fetch(PDO:: FETCH_ASSOC);

 $useremail_db=$row['useremail'];
 $password_db= $row['userpassword'];



// 3 Step) We will compare the user inputs values to database values.

if($oldpassword_txt== $password_db){

if($newpassword_txt==$rnewpassword_txt){

// 4 Step) If values will match then we will run update Query. 


$update=$pdo->prepare("update tbl_user set userpassword=:pass where useremail=:email");

$update->bindParam(':pass',$rnewpassword_txt);
$update->bindParam(':email',$email);

if($update->execute()){

$_SESSION['status']="Mot de passe mis à jour avec succès";
$_SESSION['status_code']="success";

}else{

$_SESSION['status']="Le mot de passe na pas été mis à jour avec succès";
$_SESSION['status_code']="error";

}





  // $_SESSION['status']="New Password Matched";
  // $_SESSION['status_code']="success";

}else{
  $_SESSION['status']="Le nouveau mot de passe ne correspond pas";
  $_SESSION['status_code']="error";
}

}else{
  $_SESSION['status']="Le mot de passe ne correspond pas";
  $_SESSION['status_code']="error";
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
          <h1 class="m-0">Changer le mot de passe</h1>
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
      <div class="row">
        <div class="col-lg-12">
        

          <!-- Horizontal Form -->
          <div class="card card-info ">
              <div class="card-header">
                <h3 class="card-title">Changer le mot de passe</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" action="" method="post">
                <div class="card-body">
                 
                <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Ancien mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder=" Ancien mot de passe" name="txt_oldpassword">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Nouveau mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="Nouveau mot de passe" name="txt_newpassword">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Répété le nouveau mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="Répété le nouveau mot de passe" name="txt_rnewpassword">
                    </div>
                  </div>
                 
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info" name="btnupdate">Mettre à jour le mot de passe</button>
                </div>
                <!-- /.card-footer -->
              </form>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-6 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php
include_once "footer.php";

?>

<?php
  if(isset($_SESSION['status']) && $_SESSION['status']!='')
 
  {

?>
<script>
     Swal.fire({
        icon: '<?php echo $_SESSION['status_code'];?>',
        title: '<?php echo $_SESSION['status'];?>'
      });

</script>
<?php
unset($_SESSION['status']);
  }
  ?>
