<?php
  include_once 'connectdb.php';
  session_start();

  if($_SESSION['useremail']==""  OR $_SESSION['role']=="User"){
    header('location:../index.php');
    }

    if($_SESSION['role']=="Admin"){
      include_once'header.php';
    }
    else{
      include_once'headeruser.php';
    }

  $id = $_GET['id'];

  $select = $pdo->prepare("select * from tbl_product where pid=$id");
  $select->execute();

  $row=$select->fetch(PDO::FETCH_ASSOC);

  $id_db=$row['pid'];

  $barcode_db=$row['barcode'];
  $product_db=$row['product'];
  $supplier_db=$row['Supplier'];
  $unit_db=$row['unit'];
  $category_db=$row['category'];
  $description_db=$row['description'];
  $stock_db=$row['stock'];
  $purchaseprice_db=$row['purchaseprice'];
  $saleprice_db=$row['saleprice'];
  $image_db=$row['image'];



  if(isset($_POST['btneditproduct'])){

    // $barcode_txt       =$_POST['txtbarcode'];
    $product_txt       =$_POST['txtproductname'];
    $supplier_txt      =$_POST['txtselect_supplier'];
    $unit_txt      =$_POST['txtselect_unit'];
    $category_txt      =$_POST['txtselect_option'];
    $description_txt   =$_POST['txtdescription'];
    $stock_txt         =$_POST['txtstock'];
    $purchaseprice_txt =$_POST['txtpurchaseprice'];
    $saleprice_txt     =$_POST['txtsaleprice'];
    
    //Image Code or File Code Start Here..
    $f_name        =$_FILES['myfile']['name'];

  if(!empty($f_name)){

    $f_tmp         =$_FILES['myfile']['tmp_name'];
    $f_size        =$_FILES['myfile']['size'];
    $f_extension   =explode('.',$f_name);
    $f_extension   =strtolower(end($f_extension));
    $f_newfile     =uniqid().'.'. $f_extension;   
      
    $store = "productimages/".$f_newfile;
        
    if($f_extension=='jpg' || $f_extension=='jpeg' ||   $f_extension=='png' || $f_extension=='gif'){
    
      if($f_size>=1000000 ){
          
   
  
  
    $_SESSION['status']="Le fichier maximum doit être de 1 Mb";
    $_SESSION['status_code']="warning";
          
    }else{
        
    if(move_uploaded_file($f_tmp,$store)){
  
$f_newfile;
  

  $update = $pdo->prepare("update tbl_product set product=:product , Supplier=:supplier, unit=:unit, category=:category , 
  description=:description , stock=:stock , purchaseprice=:pprice , saleprice=:sprice , image=:image where pid=$id");

  $update->bindParam(':product',$product_txt);
  $update->bindParam(':Supplier',$supplier_txt);
  $update->bindParam(':unit',$unit_txt);
  $update->bindParam(':category',$category_txt);
  $update->bindParam(':description',$description_txt);
  $update->bindParam(':stock',$stock_txt);
  $update->bindParam(':pprice',$purchaseprice_txt);
  $update->bindParam(':sprice',$saleprice_txt);
  $update->bindParam(':image',$f_newfile);
  
  
  if($update->execute()){
  
  
    $_SESSION['status']="Produit mis à jour avec succès avec une nouvelle image";
    $_SESSION['status_code']="success";
  }else{
    $_SESSION['status']="Échec de la mise à jour du produit";
    $_SESSION['status_code']="error";
  
  }

    } } }

 }else{

$update = $pdo->prepare("update tbl_product set product=:product ,Supplier=:supplier, unit=:unit, category=:category , 
description=:description , stock=:stock , purchaseprice=:pprice , saleprice=:sprice , image=:image where pid=$id");

$update->bindParam(':product',$product_txt);
$update->bindParam(':Supplier',$supplier_txt);
$update->bindParam(':unit',$unit_txt);
$update->bindParam(':category',$category_txt);
$update->bindParam(':description',$description_txt);
$update->bindParam(':stock',$stock_txt);
$update->bindParam(':pprice',$purchaseprice_txt);
$update->bindParam(':sprice',$saleprice_txt);
$update->bindParam(':image',$image_db);


if($update->execute()){


  $_SESSION['status']="Produit mis à jour avec succès";
  $_SESSION['status_code']="success";
}else{
  $_SESSION['status']="Échec de la mise à jour du produit";
  $_SESSION['status_code']="error";

}

 }

}


$select = $pdo->prepare("select * from tbl_product where pid=$id");
$select->execute();

$row=$select->fetch(PDO::FETCH_ASSOC);

$id_db=$row['pid'];

$barcode_db=$row['barcode'];
$product_db=$row['product'];
$category_db=$row['Supplier'];
$category_db=$row['unit'];
$category_db=$row['category'];
$description_db=$row['description'];
$stock_db=$row['stock'];
$purchaseprice_db=$row['purchaseprice'];
$saleprice_db=$row['saleprice'];
$image_db=$row['image'];

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1 class="m-0">Admin Dashboard</h1> -->
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
        

          <div class="card card-success card-outline">
            <div class="card-header">
              <h5 class="m-0">Modifier le produit</h5>
            </div>
           
            <form action="" method="post" name="formeditproduct" enctype="multipart/form-data">
              <div class="card-body">

                <div class="row">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label >Code-barre</label>
                      <input type="text" class="form-control" value="<?php echo $barcode_db;?>" placeholder="Entrez le Code-barre" name="txtbarcode" autocomplete="off" disabled>
                    </div>

                    <div class="form-group">
                      <label>Nom du produit</label>
                      <input type="text" class="form-control" value="<?php echo $product_db;?>" placeholder="Entrez le Nom du produit" name="txtproductname" autocomplete="off" required>
                    </div>


                    <div class="form-group">
                      <label>Fournisseur</label>
                      <select class="form-control" name="txtselect_supplier" required>
                        <option value="" disabled selected>Sélectionnez</option>
                         
                          <?php
                            $select=$pdo->prepare("select * from tbl_supplier order by catid desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option 
                                  <?php 
                                    if($row['Supplier']==$supplier_db  ){ 
                                      ?>
                                      selected="selected"
                                      <?php 
                                    }
                                    ?>  
                                    >
                                    <?php 
                                      echo $row['Supplier'];
                                      ?>
                                </option>

                              <?php

                            }
                            
                            ?>
            
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Unité</label>
                      <select class="form-control" name="txtselect_unit" required>
                        <option value="" disabled selected>Sélectionnez</option>
                         
                          <?php
                            $select=$pdo->prepare("select * from tbl_unit order by catid desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option 
                                  <?php 
                                    if($row['Unit']==$unit_db  ){ 
                                      ?>
                                      selected="selected"
                                      <?php 
                                    }
                                    ?>  
                                    >
                                    <?php 
                                      echo $row['Unit'];
                                      ?>
                                </option>

                              <?php

                            }
                            
                            ?>
            
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Catégorie</label>
                      <select class="form-control" name="txtselect_option" required>
                        <option value="" disabled selected>Sélectionnez</option>
                         
                          <?php
                            $select=$pdo->prepare("select * from tbl_category order by catid desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option 
                                  <?php 
                                    if($row['category']==$category_db  ){ 
                                      ?>
                                      selected="selected"
                                      <?php 
                                    }
                                    ?>  
                                    >
                                    <?php 
                                      echo $row['category'];
                                      ?>
                                </option>

                              <?php

                            }
                            
                            ?>
            
                      </select>
                    </div>


                      <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" placeholder="Entrez la Description" name="txtdescription" rows="4" required>
                          <?php echo $description_db;?> 
                        </textarea>
                      </div>

                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label >Quantité en stock</label>
                      <input type="number" min="1" step="any" class="form-control" value="<?php echo $stock_db;?>" placeholder="Entrez le Stock" name="txtstock" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label >Prix ​​d'achat</label>
                      <input type="number" min="1" step="any" class="form-control" value="<?php echo $purchaseprice_db;?>" placeholder="Entrez le Prix ​​d'achat" name="txtpurchaseprice" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label >Prix ​​de vente</label>
                      <input type="number" min="1" step="any" class="form-control" value="<?php echo $saleprice_db;?>" placeholder="Entrez le Prix ​​de vente" name="txtsaleprice" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label >Image du produit</label><br />
                      <image src="productimages/<?php echo $image_db;?>" class="img-rounded" width="50px" height="50px/">
                      <input type="file" class="input-group"  name="myfile">
                      <p>Télécharger une image</p>
                    </div>
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <div class="text-center">
                <button type="submit" class="btn btn-success" name="btneditproduct">Mettre à jour le produit</button></div>
              </div>
            
            </form>

          </div>
     
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