<?php

include_once 'connectdb.php';
session_start();


if($_SESSION['useremail']=="" OR $_SESSION['role']=="User"){

  header('location:../index.php');
  
  }


  if($_SESSION['role']=="Admin" ){

    include_once "header.php";

    
  }else{

    include_once "headeruser.php";

  }

error_reporting(0);

$id=$_GET['id'];

if(isset($id)){

$delete=$pdo->prepare("delete from tbl_user where userid =".$id);

if($delete->execute()){

  $_SESSION['status']="Compte supprimé avec succès";
  $_SESSION['status_code']="success";

}else{

$_SESSION['status']="Le compte n'est pas supprimé";
$_SESSION['status_code']="warning";
     }





}


if(isset($_POST['btnsave'])){

$username = $_POST['txtname'];
$useremail = $_POST['txtemail'];
$userpassword= $_POST['txtpassword'];
$userrole= $_POST['txtselect_option'];

if(isset($_POST['txtemail'])){

$select=$pdo->prepare("select useremail from tbl_user where useremail='$useremail'");

$select->execute();


if($select->rowCount()>0){



$_SESSION['status']="L'email existe déjà. Créer un compte à partir d'un nouvel e-mail";
  $_SESSION['status_code']="warning";

}else{

  $insert=$pdo->prepare("insert into tbl_user (username,useremail,userpassword,role) values(:name,:email,:password,:role)");

  $insert->bindParam(':name',$username);
  $insert->bindParam(':email',$useremail);
  $insert->bindParam(':password',$userpassword);
  $insert->bindParam(':role',$userrole);
  
  if($insert->execute()){
  
  
  
  $_SESSION['status']="L'utilisateur inséré avec succès dans la base de données";
  $_SESSION['status_code']="success";
  
  }else{
  
  
  
  $_SESSION['status']="Erreur lors de l'insertion de l'utilisateur dans la base de données";
  $_SESSION['status_code']="error";
  
  }



}







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
          <h1 class="m-0">Enregistrement</h1>
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
              <h5 class="m-0">Formulaire d'Inscription</h5>
            </div>
            <div class="card-body">

<div class="row">

<div class="col-md-4">

<form action="" method="post">
               
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nom d'utilisateur</label>
                    <input type="text" class="form-control" placeholder="Entrez le nom" name="txtname" required>
                  </div>


                  <div class="form-group">
                    <label for="exampleInputEmail1">E-mail</label>
                    <input type="email" class="form-control"  placeholder="Entrez l'e-mail" name="txtemail" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Mot de passe</label>
                    <input type="password" class="form-control"  placeholder="Entrez le mot de passe" name="txtpassword" required>
                  </div>
                 
                  <div class="form-group">
                        <label>Rôle</label>
                        <select class="form-control" name="txtselect_option" required>
                          <option value="" disabled selected>Sélectionnez un rôle</option>
                          <option>Admin</option>
                          <option>Utilisateur</option>
                         
                        </select>
                      </div>
               

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
                </div>
              </form>


</div>









<div class="col-md-8">

<table class="table table-striped table-hover ">
<thead>
<tr>
 <!-- <td>#</td> -->
 <td>nom d'utilisateur</td>
 <td>E-mail</td>
 <td>Mot de passe</td>
 <td>Rôle</td> 
 <td>Supprimer</td>   
</tr>

</thead>


<tbody>

<?php

$select = $pdo->prepare("select * from tbl_user order by userid ASC");
$select->execute();

while($row=$select->fetch(PDO::FETCH_OBJ))
{

echo'
<tr>

<td>'.$row->username.'</td>
<td>'.$row->useremail.'</td>
<td>'.$row->userpassword.'</td>
<td>'.$row->role.'</td>
<td>

<a href="registration.php?id='.$row->userid.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i></a>
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
     
<!--  -->
       
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