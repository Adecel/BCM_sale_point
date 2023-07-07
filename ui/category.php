<?php

include_once 'connectdb.php';
session_start();



if($_SESSION['useremail']==""  OR $_SESSION['role']=="User"){

  header('location:../index.php');
  
  }


  if($_SESSION['role']=="Admin"){
    include_once'header.php';
  }else{
  
    include_once'headeruser.php';
  }

if(isset($_POST['btnsave'])){

$category = $_POST['txtcategory'];

if(empty($category)){

    $_SESSION['status']="La catégorie est vide";
    $_SESSION['status_code']="warning";



}else{

$insert=$pdo->prepare("insert into tbl_category (category) values(:cat)");

$insert->bindParam(':cat',$category);

if($insert->execute()){
    $_SESSION['status']="Catégorie ajoutée avec succès";
    $_SESSION['status_code']="success";

}else{

    $_SESSION['status']="Échec de lajout de la catégorie";
    $_SESSION['status_code']="warning";
}
}}








if(isset($_POST['btnupdate'])){

  $category = $_POST['txtcategory'];
  $id = $_POST['txtcatid'];
  
  if(empty($category)){
  
      $_SESSION['status']="La catégorie est vide";
      $_SESSION['status_code']="warning";
  
  
  
  }else{
  
  $update=$pdo->prepare("update tbl_category set category=:cat where catid=".$id);
  
  $update->bindParam(':cat',$category);
  
  if($update->execute()){
      $_SESSION['status']="Mise à jour de catégorie réussie";
      $_SESSION['status_code']="success";
  
  }else{
  
      $_SESSION['status']="Échec de la mise à jour de la catégorie";
      $_SESSION['status_code']="warning";
  }
  }}


  If(isset($_POST['btndelete'])){
   
   $delete=$pdo->prepare("delete from tbl_category where catid=".$_POST['btndelete']); 

   if($delete->execute()){
    $_SESSION['status']="Supprimé";
    $_SESSION['status_code']="success";

   }else{

    $_SESSION['status']="Échec de la suppression";
    $_SESSION['status_code']="warning";


   }




  }else{




  }


?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Catégories</h1>
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
    
    <div class="card card-warning card-outline">
            <div class="card-header">
              <h5 class="m-0">Formulaire de catégorie</h5>
            </div>

            
            <form action="" method="post">
            <div class="card-body">


<div class="row">


<?php

if(isset($_POST['btnedit'])){

$select=$pdo->prepare("select * from tbl_category where catid =".$_POST['btnedit']);

$select->execute();

if($select){
$row=$select->fetch(PDO::FETCH_OBJ);

echo'<div class="col-md-4">


               
<div class="form-group">
  <label for="exampleInputEmail1">Category</label>

  <input type="hidden" class="form-control" placeholder="Enter Category"  value="'.$row->catid.'" name="txtcatid" >

  <input type="text" class="form-control" placeholder="Enter Category"  value="'.$row->category.'" name="txtcategory" >
</div>


<div class="card-footer">
<button type="submit" class="btn btn-info" name="btnupdate">mise à jour</button>
</div>



</div>';




}





}else{

echo'<div class="col-md-4">


               
<div class="form-group">
  <label for="exampleInputEmail1">Category</label>
  <input type="text" class="form-control" placeholder="Enter Category"  name="txtcategory" >
</div>


<div class="card-footer">
<button type="submit" class="btn btn-warning" name="btnsave">Sauvegarder</button>
</div>



</div>';




}




?>



















<div class="col-md-8">

<table id="table_category" class="table table-striped table-hover ">
<thead>
<tr>
 <!-- <td>#</td> -->
 <td>Nom</td>
 <td>Modifier</td>
 <td>Supprimer</td>
 
</tr>

</thead>


<tbody>

<?php

$select = $pdo->prepare("select * from tbl_category order by catid ASC");
$select->execute();

while($row=$select->fetch(PDO::FETCH_OBJ))
{

echo'
<tr>
<td>'.$row->category.'</td>

<td>

<button type="submit" class="btn btn-primary" value="'.$row->catid.'" name="btnedit">Modifier</button>

</td>

<td>

<button type="submit" class="btn btn-danger" value="'.$row->catid.'" name="btndelete">Supprimer</button>

</td>

</tr>';

}

?>

</tbody>


<tfoot>
<tr>
 <!-- <td>#</td> -->
 <td>Nom</td>
 <td>Modifier</td>
 <td>Supprimer</td>
 
</tr>



</tfoot>


</table>




</div>


           

       
             
            </div>

         

            </div>
            </form>
          </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php

include_once "footer.php";


?>

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


<script>

$(document).ready( function () {
    $('#table_category').DataTable();
} );

</script>