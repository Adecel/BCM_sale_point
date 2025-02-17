<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if(isset($_POST['btnsave'])){

    // Get session username for CreatedBy and ModifiedBy
    $createdBy = $_SESSION['username'];
    $modifiedBy = $createdBy;
    
    // Set current timestamp for CreatedDate and ModifiedDate
    date_default_timezone_set("Africa/Johannesburg");
    $createdDate = date('Y-m-d H:i:s');
    $modifiedDate = $createdDate;
    
    // Set IsDeleted to 0 (indicating it's not deleted)
    $isDeleted = 0;
    
      // Get form data
    $barcode       = $_POST['txtbarcode'];
    $product       = $_POST['txtproductname'];
    $supplier      = $_POST['txtselect_supplier'];
    $unit          = $_POST['txtselect_unit'];
    $category      = $_POST['txtselect_category'];
    $productStatus      = $_POST['txtselect_productStatus'];
    $description   = $_POST['txtdescription'];
    $stock         = $_POST['txtstock'];
    $purchaseprice = $_POST['txtpurchaseprice'];
    $saleprice     = $_POST['txtsaleprice'];

    // $expiryDateForPurchase = $_POST['txtexpiryDate'];

    $expiryDateForPurchase = !empty($_POST['txtexpiryDate']) ? $_POST['txtexpiryDate'] : NULL;

    // Validate the expiry date format
    // $expiryDate = DateTime::createFromFormat('Y-m-d', $expiryDateForPurchase);
    // if (!$expiryDate || $expiryDate->format('Y-m-d') !== $expiryDateForPurchase) {
    //     $_SESSION['status'] = "Date d'expiration invalide";
    //     $_SESSION['status_code'] = "error";
    //     exit();
    // } else {
    //     $expiryDateForPurchase = $expiryDate->format('Y-m-d'); // Use valid date format
    // }

    // Image Code or File Upload Logic
    $f_name        =$_FILES['myfile']['name'];
    $f_tmp         =$_FILES['myfile']['tmp_name'];
    $f_size        =$_FILES['myfile']['size'];
    $f_extension   =explode('.',$f_name);
    $f_extension   =strtolower(end($f_extension));

    $f_newfile     =uniqid().'.'. $f_extension;   
    $store = "productimages/".$f_newfile;

    if($f_extension=='jpg' || $f_extension=='jpeg' || $f_extension=='png' || $f_extension=='gif'){
        if($f_size>=5000000 ){
            $_SESSION['status']="Le fichier maximum doit être de 5 MB";
            $_SESSION['status_code']="warning";
        } else {
            if(move_uploaded_file($f_tmp,$store)){
                $productimage=$f_newfile;

                // Insert without barcode (Barcode will be generated)
                if(empty($barcode)){
                    $insert = $pdo->prepare("INSERT INTO tProduct 
                    (Barcode, ProductName, CategoryId, ProductStatusId, SupplierId, UnitId, Description, Stock, PurchasePrice, SalePrice, Image, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, IsDeleted, ExpiryDate) 
                    VALUES (:barcode, :product, :category, :productStatus, :supplier, :unit, :description, :stock, :pprice, :saleprice, :img, :createdBy, :modifiedBy, :createdDate, :modifiedDate, :isDeleted, :expiryDate)");

                    $insert->bindParam(':barcode', $barcode);
                    $insert->bindParam(':product', $product);
                    $insert->bindParam(':category', $category);
                    $insert->bindParam(':productStatus', $productStatus);
                    $insert->bindParam(':supplier', $supplier);
                    $insert->bindParam(':unit', $unit);
                    $insert->bindParam(':description', $description);
                    $insert->bindParam(':stock', $stock);
                    $insert->bindParam(':pprice', $purchaseprice);
                    $insert->bindParam(':saleprice', $saleprice);
                    $insert->bindParam(':img', $productimage);
                    $insert->bindParam(':createdBy', $createdBy);
                    $insert->bindParam(':modifiedBy', $modifiedBy);
                    $insert->bindParam(':createdDate', $createdDate);
                    $insert->bindParam(':modifiedDate', $modifiedDate);
                    $insert->bindParam(':isDeleted', $isDeleted);
                    // $insert->bindParam(':expiryDate', $expiryDateForPurchase);
                    if ($expiryDateForPurchase !== NULL) {
                        $insert->bindParam(':expiryDate', $expiryDateForPurchase);
                    } else {
                        $insert->bindValue(':expiryDate', NULL, PDO::PARAM_NULL);
                    }

                    $insert->execute();

                    // Get the last inserted ID to generate the barcode
                    $pid = $pdo->lastInsertId();
                    $newbarcode = $pid.date('Hisymd');

                    // Update product with new barcode
                    $update = $pdo->prepare("UPDATE tProduct SET Barcode='$newbarcode' WHERE ProductId='$pid'");
                    // $update->bindParam(':newbarcode', $newbarcode);
                    // $update->bindParam(':pid', $pid);
                    // $update->execute();

                    // Insert into the audit without barcode table tAuditProduct
                    $insertAudit = $pdo->prepare("INSERT INTO tAuditProduct 
                    (ProductId, ProductName, Image, Barcode, SupplierId, CategoryId, UnitId, Stock, SalePrice, PurchasePrice, ProductStatusId, ExpiryDate, CreatedDate, ModifiedDate, CreatedBy, ModifiedBy, IsDeleted)
                    VALUES
                    (:productId, :productName, :image, :barcode, :supplierId, :categoryId, :unitId, :stock, :salePrice, :purchasePrice, :productStatusId, :expiryDate, :createdDate, :modifiedDate, :createdBy, :modifiedBy, :isDeleted)");

                    // Bind parameters for tAuditProduct insert
                    $insertAudit->bindParam(':productId', $pid);
                    $insertAudit->bindParam(':productName', $product);
                    $insertAudit->bindParam(':image', $productimage);
                    $insertAudit->bindParam(':barcode', $newbarcode);
                    $insertAudit->bindParam(':supplierId', $supplier);
                    $insertAudit->bindParam(':categoryId', $category);
                    $insertAudit->bindParam(':unitId', $unit);
                    $insertAudit->bindParam(':stock', $stock);
                    $insertAudit->bindParam(':salePrice', $saleprice);
                    $insertAudit->bindParam(':purchasePrice', $purchaseprice);
                    $insertAudit->bindParam(':productStatusId', $productStatus);
                    // $insertAudit->bindParam(':expiryDate', $expiryDateForPurchase);
                    if ($expiryDateForPurchase !== NULL) {
                      $insertAudit->bindParam(':expiryDate', $expiryDateForPurchase);
                    } else {
                        $insertAudit->bindValue(':expiryDate', NULL, PDO::PARAM_NULL);
                    }
                    $insertAudit->bindParam(':createdDate', $createdDate);
                    $insertAudit->bindParam(':modifiedDate', $modifiedDate);
                    $insertAudit->bindParam(':createdBy', $createdBy);
                    $insertAudit->bindParam(':modifiedBy', $modifiedBy);
                    $insertAudit->bindParam(':isDeleted', $isDeleted);

                    // Execute the audit insert
                    $insertAudit->execute();

                    if($update->execute()){
                        $_SESSION['status'] = "Produit inséré avec succès";
                        $_SESSION['status_code'] = "success";
                    } else {
                        $_SESSION['status'] = "Échec de l'insertion du produit";
                        $_SESSION['status_code'] = "error";
                    }

                // Insert with barcode
                }else {
                  $insert = $pdo->prepare("INSERT INTO tProduct 
                  (Barcode, ProductName, CategoryId, ProductStatusId, SupplierId, UnitId, Description, Stock, PurchasePrice, SalePrice, Image, CreatedBy, ModifiedBy, CreatedDate, ModifiedDate, IsDeleted, ExpiryDate)
                  VALUES (:barcode, :product, :category, :productStatus, :supplier, :unit, :description, :stock, :pprice, :saleprice, :img, :createdBy, :modifiedBy, :createdDate, :modifiedDate, :isDeleted, :expiryDate)");

                  $insert->bindParam(':barcode', $barcode);
                  $insert->bindParam(':product', $product);
                  $insert->bindParam(':category', $category);
                  $insert->bindParam(':productStatus', $productStatus);
                  $insert->bindParam(':supplier', $supplier);
                  $insert->bindParam(':unit', $unit);
                  $insert->bindParam(':description', $description);
                  $insert->bindParam(':stock', $stock);
                  $insert->bindParam(':pprice', $purchaseprice);
                  $insert->bindParam(':saleprice', $saleprice);
                  $insert->bindParam(':img', $productimage);
                  $insert->bindParam(':createdBy', $createdBy);
                  $insert->bindParam(':modifiedBy', $modifiedBy);
                  $insert->bindParam(':createdDate', $createdDate);
                  $insert->bindParam(':modifiedDate', $modifiedDate);
                  $insert->bindParam(':isDeleted', $isDeleted);
                  // $insert->bindParam(':expiryDate', $expiryDateForPurchase);
                  if ($expiryDateForPurchase !== NULL) {
                    $insert->bindParam(':expiryDate', $expiryDateForPurchase);
                  } else {
                      $insert->bindValue(':expiryDate', NULL, PDO::PARAM_NULL);
                  }

                  if ($insert->execute()) {
                    // Get the last inserted product ID
                    $pid = $pdo->lastInsertId();
                
                    // Insert into the audit table tAuditProduct
                    $insertAudit = $pdo->prepare("INSERT INTO tAuditProduct 
                        (ProductId, ProductName, Image, Barcode, SupplierId, CategoryId, UnitId, Stock, SalePrice, PurchasePrice, ProductStatusId, ExpiryDate, CreatedDate, ModifiedDate, CreatedBy, ModifiedBy, IsDeleted)
                        VALUES(:productId, :productName, :image, :barcode, :supplierId, :categoryId, :unitId, :stock, :salePrice, :purchasePrice, :productStatusId, :expiryDate, :createdDate, :modifiedDate, :createdBy, :modifiedBy, :isDeleted)");
                
                    // Bind parameters for tAuditProduct insert
                    $insertAudit->bindParam(':productId', $pid);
                    $insertAudit->bindParam(':productName', $product);
                    $insertAudit->bindParam(':image', $productimage);
                    $insertAudit->bindParam(':barcode', $barcode);
                    $insertAudit->bindParam(':supplierId', $supplier);
                    $insertAudit->bindParam(':categoryId', $category);
                    $insertAudit->bindParam(':unitId', $unit);
                    $insertAudit->bindParam(':stock', $stock);
                    $insertAudit->bindParam(':salePrice', $saleprice);
                    $insertAudit->bindParam(':purchasePrice', $purchaseprice);
                    $insertAudit->bindParam(':productStatusId', $productStatus);
                    // $insertAudit->bindParam(':expiryDate', $expiryDateForPurchase);
                    if ($expiryDateForPurchase !== NULL) {
                      $insertAudit->bindParam(':expiryDate', $expiryDateForPurchase);
                    } else {
                        $insertAudit->bindValue(':expiryDate', NULL, PDO::PARAM_NULL);
                    }
                    $insertAudit->bindParam(':createdDate', $createdDate);
                    $insertAudit->bindParam(':modifiedDate', $modifiedDate);
                    $insertAudit->bindParam(':createdBy', $createdBy);
                    $insertAudit->bindParam(':modifiedBy', $modifiedBy);
                    $insertAudit->bindParam(':isDeleted', $isDeleted);
                
                    $insertAudit->execute();
                
                    $_SESSION['status'] = "Produit inséré avec succès";
                    $_SESSION['status_code'] = "success";
                  }else {
                    $_SESSION['status'] = "Échec de l'insertion du produit";
                    $_SESSION['status_code'] = "error";
                  }
              }
            }
        }
    } else {
        $_SESSION['status'] = "Seuls jpg, jpeg, png et gif peuvent être téléchargés";
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
          <h1 class="m-0">Ajouter les produit2</h1>
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
                            $select=$pdo->prepare("SELECT * FROM tSupplier WHERE IsDeleted = 0 ORDER BY SupplierId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option value="<?php echo $row['SupplierId'];?>"><?php echo $row['SupplierName'];?></option>
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
                            $select=$pdo->prepare("SELECT * FROM tUnit WHERE IsDeleted = 0 ORDER BY UnitId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option value="<?php echo $row['UnitId'];?>"><?php echo $row['UnitName'];?></option>
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
                            $select=$pdo->prepare("SELECT * FROM tCategory WHERE IsDeleted = 0 ORDER BY CategoryId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option value="<?php echo $row['CategoryId'];?>"><?php echo $row['CategoryName'];?></option>
                              <?php
                              }
                          ?>
                        </select>
                    </div>

                    <!-- productStatus -->
                    <div class="form-group">
                      <label>Status du Produit </label>
                      <select class="form-control" name="txtselect_productStatus" required>
                        <option value="" disabled selected>Sélectionnez</option>
                          <?php
                            $select=$pdo->prepare("SELECT * FROM tProductStatus WHERE IsDeleted = 0 ORDER BY ProductStatusId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                                <option value="<?php echo $row['ProductStatusId'];?>"><?php echo $row['Description'];?></option>
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
                      <label>Date d'expiration</label>
                      <input type="Date" min="1" step="any" class="form-control" placeholder="Entrez la quantité estimée" name="txtexpiryDate" >
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