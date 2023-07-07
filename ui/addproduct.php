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

if(isset($_POST['btnsave'])){

$barcode       =$_POST['txtbarcode'];
$product       =$_POST['txtproductname'];
$supplier      =$_POST['txtselect_supplier'];
$unit          =$_POST['txtselect_unit'];
$category      =$_POST['txtselect_category'];
$description   =$_POST['txtdescription'];
$stock         =$_POST['txtstock'];
$purchaseprice =$_POST['txtpurchaseprice'];
$saleprice     =$_POST['txtsaleprice'];

//Image Code or File Code Start Here..
$f_name        =$_FILES['myfile']['name'];
$f_tmp         =$_FILES['myfile']['tmp_name'];
$f_size        =$_FILES['myfile']['size'];
$f_extension   =explode('.',$f_name);
$f_extension   =strtolower(end($f_extension));

$f_newfile     =uniqid().'.'. $f_extension;   
$store = "productimages/".$f_newfile;
    
if($f_extension=='jpg' || $f_extension=='jpeg' ||   $f_extension=='png' || $f_extension=='gif'){
 
  if($f_size>=5000000 ){
      
  $_SESSION['status']="Le fichier maximum doit être de 5 MB";
  $_SESSION['status_code']="warning";
        
  }
  else{
    if(move_uploaded_file($f_tmp,$store)){
        $productimage=$f_newfile;

        if(empty($barcode)){
          $insert=$pdo->prepare("insert into tbl_product ( barcode,product,category,Supplier,unit,description,stock, purchaseprice, saleprice,image) 
          values(:barcode,:product,:category,:supplier,:unit,:description,:stock,:pprice,:saleprice,:img)");

        $insert->bindParam(':barcode',$barcode);

          $insert->bindParam(':product',$product);
          $insert->bindParam(':category',$category);
          $insert->bindParam(':supplier',$supplier);
          $insert->bindParam(':unit',$unit);
          $insert->bindParam(':description',$description);
          $insert->bindParam(':stock',$stock);
          $insert->bindParam(':pprice',$purchaseprice);
          $insert->bindParam(':saleprice',$saleprice);
          $insert->bindParam(':img',$productimage);
  
          $insert->execute();

          $pid=$pdo->lastInsertId();

          date_default_timezone_set("Africa/Johannesburg");
          $newbarcode=$pid.date('Hisymd');

          $update=$pdo->prepare("update tbl_product SET barcode='$newbarcode' where pid='".$pid."'");

          if($update->execute()){

            $_SESSION['status']="Produit inséré avec succès";
            $_SESSION['status_code']="success";
          }
          else{
            $_SESSION['status']="Échec de linsertion du produit";
            $_SESSION['status_code']="error";
          }

        }
        else{
          $insert=$pdo->prepare("insert into tbl_product (barcode, product,category,Supplier,unit,description,stock, purchaseprice, saleprice,image) 
          values(:barcode,:product,:category,:supplier,:unit,:description,:stock,:pprice,:saleprice,:img)");

          $insert->bindParam(':barcode',$barcode);
          $insert->bindParam(':product',$product);
          $insert->bindParam(':category',$category);
          $insert->bindParam(':supplier',$supplier);
          $insert->bindParam(':unit',$unit);
          $insert->bindParam(':description',$description);
          $insert->bindParam(':stock',$stock);
          $insert->bindParam(':pprice',$purchaseprice);
          $insert->bindParam(':saleprice',$saleprice);
          $insert->bindParam(':img',$productimage);

          if($insert->execute()){

            $_SESSION['status']="Produit inséré avec succès";
            $_SESSION['status_code']="success";
          }
          else{
            $_SESSION['status']="Échec de l'insertion du produit";
            $_SESSION['status_code']="error";
          }
        }        
    }         
  }   
        
  }else{
      $_SESSION['status']="seuls jpg, jpeg, png et gif peuvent être téléchargés";
      $_SESSION['status_code']="warning";
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
          <h1 class="m-0">Ajouter les produit</h1>
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
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Product</h5>
            </div>
           
            <form action="" method="post" enctype="multipart/form-data">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">

                    <div class="form-group">
                      <label >Code-barre</label>
                      <input type="text" class="form-control" placeholder="Entrez le code-barres" name="txtbarcode" autocomplete="off">
                    </div>

                    <div class="form-group">
                      <label>Nom du produit</label>
                      <input type="text" class="form-control" placeholder="Entrez le now du produit" name="txtproductname" autocomplete="off" required>
                    </div>

                    <!-- supplier -->
                    <div class="form-group">
                      <label>Fournisseur</label>
                      <select class="form-control" name="txtselect_supplier" required>
                        <option value="" disabled selected>Sélectionnez</option>
                          <?php
                            $select=$pdo->prepare("select * from tbl_supplier order by SupplierId desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option><?php echo $row['SupplierName'];?></option>
                              <?php

                            }
                          ?>
                        </select>
                    </div>
                    <!-- unit -->
                    <div class="form-group">
                      <label>Unité</label>
                      <select class="form-control" name="txtselect_unit" required>
                        <option value="" disabled selected>Sélectionnez</option>
                          <?php
                            $select=$pdo->prepare("select * from tbl_unit order by unitid desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option><?php echo $row['unitname'];?></option>
                              <?php
                            }
                          ?>
                        </select>
                    </div>
                          <!-- category -->
                    <div class="form-group">
                      <label>Catégorie</label>
                      <select class="form-control" name="txtselect_category" required>
                        <option value="" disabled selected>Sélectionnez</option>
                          <?php
                            $select=$pdo->prepare("select * from tbl_category order by catid desc");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option><?php echo $row['category'];?></option>
                              <?php
                              }
                          ?>
                        </select>
                    </div>
                
                  </div>

                  <div class="col-md-6">

                    <div class="form-group">
                      <label >Quantité en stock</label>
                      <input type="number" min="1" step="any" class="form-control" placeholder="Entrer le stock" name="txtstock" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label >Prix ​​d'achat</label>
                      <input type="number" min="1" step="any" class="form-control" placeholder="Entrer le Prix ​​d'achat" name="txtpurchaseprice" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label >Prix ​​de vente</label>
                      <input type="number" min="1" step="any" class="form-control" placeholder="Entrer le Prix ​​de vente" name="txtsaleprice" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                      <label>Description</label>
                      <textarea class="form-control" placeholder="Entrer la Description" name="txtdescription" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                      <label >Image du produit</label>
                      <input type="file" class="input-group"  name="myfile">
                      <p>Télécharger une image</p>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card-footer">
                <div class="text-center">
                  <button type="submit" class="btn btn-primary" name="btnsave">Enregistrer le produit</button>
                </div>
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

<?php include_once "footer.php"; ?>

<?php
  if(isset($_SESSION['status']) && $_SESSION['status']!='') {
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