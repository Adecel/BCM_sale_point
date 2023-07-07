<?php

include_once 'connectdb.php';
session_start();

include_once "header.php";

if(isset($_POST['btnsave'])){

  $unit = $_POST['txtunit'];
  
  if(empty($unit)){
  
      $_SESSION['status']="Unité est vide";
      $_SESSION['status_code']="warning";

  }
  else{

    $insert=$pdo->prepare("insert into tbl_unit (unitname) values(:cat)");
    
    $insert->bindParam(':cat',$unit);
    
    if($insert->execute()){
        $_SESSION['status']="Unité ajoutée avec succès";
        $_SESSION['status_code']="success";
    
    }else{
    
        $_SESSION['status']="Échec dajout de unité";
        $_SESSION['status_code']="warning";
    }
    }}


    if(isset($_POST['btnupdate'])){

      $unit = $_POST['txtunit'];
      $id = $_POST['txtunitid'];
      
      if(empty($unit)){
      
          $_SESSION['status']="Unité est vide";
          $_SESSION['status_code']="warning";
      
      }else{
      
      $update=$pdo->prepare("update tbl_unit set unitname=:cat where unitid=".$id);
      
      $update->bindParam(':cat',$unit);
      
      if($update->execute()){
          $_SESSION['status']="Unité mis a jour avec succès";
          $_SESSION['status_code']="success";
      
      }else{
      
          $_SESSION['status']="Échec de la mis a jour unité";
          $_SESSION['status_code']="warning";
      }
      }}

      If(isset($_POST['btndelete'])){
   
        $delete=$pdo->prepare("delete from tbl_unit where unitid=".$_POST['btndelete']); 
     
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

  if(isset($_POST['btnedit'])){

    $select=$pdo->prepare("select * from tbl_unit where unitid =".$_POST['btnedit']);
    $select->execute();

    if($select){
    $row=$select->fetch(PDO::FETCH_OBJ);

    echo'
    <div class="col-md-4">   
          <div class="form-group">
            <label for="exampleInputEmail1">Unité</label>
            <input type="hidden" class="form-control" placeholder="Entrez lunité" value="'.$row->unitid.'" name="txtunitid">
            <input type="text" class="form-control" placeholder="Entrez lunité" value="'.$row->unitname.'" name="txtunit">
          </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-info" name="btnupdate">mise à jour</button>
        </div>
            
    </div>';


    }
  }
  else{
    echo'
    <div class="col-md-4">   
          <div class="form-group">
            <label for="exampleInputEmail1">Unité</label>
            <input type="text" class="form-control" placeholder="Entrez lunité " name="txtunit">
          </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
        </div>
            
</div>';

  }

?>









<div class="col-md-8">

<table id="table_unit" class="table table-striped table-hover ">
<thead>
<tr>
 <!-- <td>#</td> -->
 <td>Unité</td>
 <td>Modifier</td>
 <td>Supprimer</td>   
</tr>

</thead>


<tbody>

<?php
  $select = $pdo->prepare("select * from tbl_unit order by unitid ASC");
  $select->execute();

  while($row=$select->fetch(PDO::FETCH_OBJ)) {
    echo'
      <tr>

        <td>'.$row->unitname.'</td>
        <td>
          <button type="submit" class="btn btn-primary" value="'.$row->unitid.'" name="btnedit">Modifiez</button>
        </td>
        <td>
          <button type="submit" class="btn btn-danger" value="'.$row->unitid.'" name="btndelete">Supprimez</button>
        </td>

      </tr>';
  }
?>

</tbody>

<tfoot>
<tr>
 <!-- <td>#</td> -->
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
    $('#table_unit').DataTable();
} );

</script>