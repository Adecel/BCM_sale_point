<?php

include_once 'connectdb.php';
session_start();

include_once "header.php";

if(isset($_POST['btnsave'])){

  $suppliername = $_POST['txtname'];
  $suppliernumber = $_POST['txtphone'];  
  $supplieremail = $_POST['txtemail'];
  $supplieraddress = $_POST['txtaddress'];
  
  if(empty($suppliername)){
  
      $_SESSION['status']="Le nom du fournisseur est vide";
      $_SESSION['status_code']="warning";

  }
  else{

    $insert=$pdo->prepare("insert into tbl_supplier (SupplierName,SupplierNumber,SupplierEmail,SupplierAddress) values(:name,:number,:email,:address)");
    
    $insert->bindParam(':name',$suppliername);
    $insert->bindParam(':number',$suppliernumber);
    $insert->bindParam(':email',$supplieremail);
    $insert->bindParam(':address',$supplieraddress);
    
    if($insert->execute()){
        $_SESSION['status']="Unité ajoutée avec succès";
        $_SESSION['status_code']="success";
    
    }else{
    
        $_SESSION['status']="Échec dajout de unité";
        $_SESSION['status_code']="warning";
    }
    }

}


if(isset($_POST['btnupdate'])){

  $suppliername = $_POST['txtname'];
  $suppliernumber = $_POST['txtphone'];  
  $supplieremail = $_POST['txtemail'];
  $supplieraddress = $_POST['txtaddress'];
  $id = $_POST['txtsupplierid'];

  if(empty($suppliername)){
  
      $_SESSION['status']="Le nom du fournisseur est vide";
      $_SESSION['status_code']="warning";

  }
  else{

    $update=$pdo->prepare("update tbl_supplier set SupplierName=:name, SupplierNumber=:number,
    SupplierEmail=:email, SupplierAddress=:address where SupplierId=".$id);
    
    $update->bindParam(':name',$suppliername);
    $update->bindParam(':number',$suppliernumber);
    $update->bindParam(':email',$supplieremail);
    $update->bindParam(':address',$supplieraddress);
    
    if($update->execute()){
        $_SESSION['status']="Unité mis a jour avec succès";
        $_SESSION['status_code']="success";
    
    }else{
    
        $_SESSION['status']="Échec mis a jour de unité";
        $_SESSION['status_code']="warning";
    }
    }

}

If(isset($_POST['btndelete'])){
   
  $delete=$pdo->prepare("delete from tbl_supplier where SupplierId=".$_POST['btndelete']); 

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
          <h1 class="m-0">Les fournisseurs</h1>
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
              <h5 class="m-0">Formulaire des fournisseurs</h5>
            </div>
            <div class="card-body">

            <form action="" method="post">

<div class="row">

<?php

  if(isset($_POST['btnedit'])){

    $select=$pdo->prepare("select * from tbl_supplier where SupplierId =".$_POST['btnedit']);
    $select->execute();

    if($select){
    $row=$select->fetch(PDO::FETCH_OBJ);

    echo'
    <div class="col-md-4">

                  <div class="form-group">
                    <label for="exampleInputEmail1">Nom du fournisseur</label>
                    <input type="hidden" class="form-control" placeholder="Entrez le nom du fournisseur" value="'.$row->SupplierId.'" name="txtsupplierid">
                    <input type="text" class="form-control" placeholder="Entrez le nom du fournisseur" value="'.$row->SupplierName.'" name="txtname">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Téléphone du fournisseur</label>
                    <input type="text" class="form-control" placeholder="Entrez le Téléphone du fournisseur" value="'.$row->SupplierNumber.'" name="txtphone">
                  </div>


                  <div class="form-group">
                    <label for="exampleInputEmail1">E-mail du fournisseur</label>
                    <input type="email" class="form-control"  placeholder="Entrez le-mail du fournisseur" value="'.$row->SupplierEmail.'" name="txtemail">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">Adresse du fournisseur</label>
                    <input type="text" class="form-control" placeholder="Entrez ladresse du fournisseur" value="'.$row->SupplierAddress.'" name="txtaddress">
                  </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-info" name="btnupdate">mise à jour</button>
                </div>
</div>';

    }


  }
  else{
    echo'
    <div class="col-md-3">

                  <div class="form-group">
                    <label for="exampleInputEmail1">Nom du fournisseur</label>
                    <input type="text" class="form-control" placeholder="Entrez le nom du fournisseur" name="txtname">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Téléphone du fournisseur</label>
                    <input type="text" class="form-control" placeholder="Entrez le Téléphone du fournisseur" name="txtphone">
                  </div>


                  <div class="form-group">
                    <label for="exampleInputEmail1">E-mail du fournisseur</label>
                    <input type="email" class="form-control"  placeholder="Entrez le-mail du fournisseur" name="txtemail">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1">Adresse du fournisseur</label>
                    <input type="text" class="form-control" placeholder="Entrez ladresse du fournisseur" name="txtaddress">
                  </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
                </div>
</div>';
    
  }

?>



<div class="col-md-9">

<table id="table_supplier" class="table table-striped table-hover ">
<thead>
<tr>
 <!-- <td>#</td> -->
 <td>Nom</td>
 <td>Téléphone</td>
 <td>E-mail</td>   
 <td>Adresse</td>   
 <td>Modifier</td>
 <td>Supprimer</td>
</tr>

</thead>


<tbody>

<?php

$select = $pdo->prepare("select * from tbl_supplier order by SupplierId ASC");
$select->execute();

while($row=$select->fetch(PDO::FETCH_OBJ))
{

echo'
<tr>

<td>'.$row->SupplierName.'</td>
<td>'.$row->SupplierNumber.'</td>
<td>'.$row->SupplierEmail.'</td>
<td>'.$row->SupplierAddress.'</td>
<td>
<button type="submit" class="btn btn-primary" value="'.$row->SupplierId.'" name="btnedit">Modifiez</button>
</td>
<td>
<button type="submit" class="btn btn-danger" value="'.$row->SupplierId.'" name="btndelete">Supprimez</button>
</td>


</tr>';

}

?>

</tbody>

<tfoot>
<tr>
 <!-- <td>#</td> -->
 <td>Nom</td>
 <td>Téléphone</td>
 <td>E-mail</td>   
 <td>Adresse</td>   
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
    $('#table_supplier').DataTable();
} );

</script>