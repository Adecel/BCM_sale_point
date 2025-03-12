<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  include_once 'connectdb.php';
  session_start();

  // Check if the session 'role' is set, and load the appropriate header based on the role
  if (isset($_SESSION['role'])) {
      if ($_SESSION['role'] == 'Admin') {
          include_once 'header.php';
      } elseif ($_SESSION['role'] == 'Manager') {
          include_once 'ManagerHeader.php';
      } else {
          // Redirect to the login page or access denied page if role doesn't match
          header('location:../index.php');
          exit();
      }
  } else {
      // If session 'role' is not set, redirect to the login page
      header('location:../index.php');
      exit();
  }

  $id = $_GET['id'];

  $select = $pdo->prepare("SELECT * FROM tProduct WHERE ProductId=$id AND IsDeleted = 0 ");
  $select->execute();

  $row=$select->fetch(PDO::FETCH_ASSOC);

  $id_db=$row['ProductId'];

  $barcode_db=$row['Barcode'];
  $product_db=$row['ProductName'];
  $category_db=$row['CategoryId'];
  $unit_db=$row['UnitId'];
  $supplier_db=$row['SupplierId'];
  $description_db=$row['Description'];
  $stock_db=$row['Stock'];
  $purchaseprice_db=$row['PurchasePrice'];
  $saleprice_db=$row['SalePrice'];
  $image_db=$row['Image'];

  if(isset($_POST['btneditproduct'])){

    
    $product_txt        =$_POST['txtproductname'];
    $category_txt       =$_POST['txtselect_option'];
    $unit_txt           =$_POST['txtselect_unit'];
    $supplier_txt       =$_POST['txtselect_supplier'];
    $description_txt    =$_POST['txtdescription'];
    $stock_txt          =$_POST['txtstock'];
    $purchaseprice_txt  =$_POST['txtpurchaseprice'];
    $saleprice_txt      =$_POST['txtsaleprice'];

    // Image upload logic
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
                $_SESSION['status']="Le fichier maximum doit être de 1 Mo";
                $_SESSION['status_code']="warning";      
            } else { 
                if(move_uploaded_file($f_tmp,$store)){
                    // Update tProduct table
                    $update = $pdo->prepare("UPDATE tProduct SET ProductName = :ProductName, CategoryId = :CategoryId, UnitId = :UnitId, 
                    SupplierId = :SupplierId, Description = :Description, Stock = :Stock, PurchasePrice = :PurchasePrice, 
                    SalePrice = :SalePrice, Image = :Image WHERE ProductId = :ProductId");

                    $update->bindParam(':ProductName', $product_txt);
                    $update->bindParam(':CategoryId', $category_txt);
                    $update->bindParam(':UnitId', $unit_txt);
                    $update->bindParam(':SupplierId', $supplier_txt);
                    $update->bindParam(':Description', $description_txt);
                    $update->bindParam(':Stock', $stock_txt);
                    $update->bindParam(':PurchasePrice', $purchaseprice_txt);
                    $update->bindParam(':SalePrice', $saleprice_txt);
                    $update->bindParam(':Image', $f_newfile);
                    $update->bindParam(':ProductId', $id, PDO::PARAM_INT);

                    if($update->execute()){
                        // Insert into tAuditProduct table
                        $insertAudit = $pdo->prepare("INSERT INTO tAuditProduct 
                            (ProductId, ProductName, Image, Barcode, SupplierId, CategoryId, UnitId, Stock, SalePrice, PurchasePrice, ProductStatusId, ExpiryDate, CreatedDate, ModifiedDate, CreatedBy, ModifiedBy, IsDeleted)
                            VALUES (:productId, :productName, :image, :barcode, :supplierId, :categoryId, :unitId, :stock, :salePrice, :purchasePrice, :productStatusId, :expiryDate, :createdDate, :modifiedDate, :createdBy, :modifiedBy, :isDeleted)");

                        // Bind parameters for tAuditProduct insert
                        $insertAudit->bindParam(':productId', $id);
                        $insertAudit->bindParam(':productName', $product_txt);
                        $insertAudit->bindParam(':image', $f_newfile);
                        $insertAudit->bindParam(':barcode', $barcode_db);  // Keep the same barcode
                        $insertAudit->bindParam(':supplierId', $supplier_txt);
                        $insertAudit->bindParam(':categoryId', $category_txt);
                        $insertAudit->bindParam(':unitId', $unit_txt);
                        $insertAudit->bindParam(':stock', $stock_txt);
                        $insertAudit->bindParam(':salePrice', $saleprice_txt);
                        $insertAudit->bindParam(':purchasePrice', $purchaseprice_txt);
                        $insertAudit->bindParam(':productStatusId', $productStatus_db);  // You can keep the same or adjust based on logic
                        $insertAudit->bindParam(':expiryDate', $expiryDate_db);  // You can keep the same expiry date or adjust based on your requirements
                        $insertAudit->bindParam(':createdDate', $createdDate);
                        $insertAudit->bindParam(':modifiedDate', $modifiedDate);
                        $insertAudit->bindParam(':createdBy', $createdBy);
                        $insertAudit->bindParam(':modifiedBy', $modifiedBy);
                        $insertAudit->bindParam(':isDeleted', $isDeleted);

                        // Execute audit insert
                        $insertAudit->execute();

                        $_SESSION['status']="Produit mis à jour avec succès avec une nouvelle image";
                        $_SESSION['status_code']="success";
                    } else {
                        $_SESSION['status']="Product Update Failed";
                        $_SESSION['status_code']="error";
                    }
                } 
            } 
        }
    }
    else {
        // Update tProduct table without image
        $update = $pdo->prepare("UPDATE tProduct SET ProductName = :ProductName, CategoryId = :CategoryId, UnitId = :UnitId, 
        SupplierId = :SupplierId, Description = :Description, Stock = :Stock, PurchasePrice = :PurchasePrice, 
        SalePrice = :SalePrice, Image = :Image WHERE ProductId = :ProductId");

        $update->bindParam(':ProductName', $product_txt);
        $update->bindParam(':CategoryId', $category_txt);
        $update->bindParam(':UnitId', $unit_txt);
        $update->bindParam(':SupplierId', $supplier_txt);
        $update->bindParam(':Description', $description_txt);
        $update->bindParam(':Stock', $stock_txt);
        $update->bindParam(':PurchasePrice', $purchaseprice_txt);
        $update->bindParam(':SalePrice', $saleprice_txt);
        $update->bindParam(':Image', $image_db);  // Keep the existing image
        $update->bindParam(':ProductId', $id, PDO::PARAM_INT);

        if($update->execute()){
            // Insert into tAuditProduct table without image change
            $insertAudit = $pdo->prepare("INSERT INTO tAuditProduct 
                (ProductId, ProductName, Image, Barcode, SupplierId, CategoryId, UnitId, Stock, SalePrice, PurchasePrice, ProductStatusId, ExpiryDate, CreatedDate, ModifiedDate, CreatedBy, ModifiedBy, IsDeleted)
                VALUES (:productId, :productName, :image, :barcode, :supplierId, :categoryId, :unitId, :stock, :salePrice, :purchasePrice, :productStatusId, :expiryDate, :createdDate, :modifiedDate, :createdBy, :modifiedBy, :isDeleted)");

            // Bind parameters for tAuditProduct insert
            $insertAudit->bindParam(':productId', $id);
            $insertAudit->bindParam(':productName', $product_txt);
            $insertAudit->bindParam(':image', $image_db);  // Keep the existing image
            $insertAudit->bindParam(':barcode', $barcode_db);  // Keep the same barcode
            $insertAudit->bindParam(':supplierId', $supplier_txt);
            $insertAudit->bindParam(':categoryId', $category_txt);
            $insertAudit->bindParam(':unitId', $unit_txt);
            $insertAudit->bindParam(':stock', $stock_txt);
            $insertAudit->bindParam(':salePrice', $saleprice_txt);
            $insertAudit->bindParam(':purchasePrice', $purchaseprice_txt);
            $insertAudit->bindParam(':productStatusId', $productStatus_db);  // You can keep the same or adjust based on logic
            $insertAudit->bindParam(':expiryDate', $expiryDate_db);  // You can keep the same expiry date or adjust based on your requirements
            $insertAudit->bindParam(':createdDate', $createdDate);
            $insertAudit->bindParam(':modifiedDate', $modifiedDate);
            $insertAudit->bindParam(':createdBy', $createdBy);
            $insertAudit->bindParam(':modifiedBy', $modifiedBy);
            $insertAudit->bindParam(':isDeleted', $isDeleted);

            // Execute audit insert
            $insertAudit->execute();

            $_SESSION['status']="Produit mis à jour avec succès";
            $_SESSION['status_code']="success";
        } else {
            $_SESSION['status']="Product Update Failed";
            $_SESSION['status_code']="error";
        }
    }
}

  $select = $pdo->prepare("SELECT * FROM tProduct WHERE ProductId = :ProductId AND IsDeleted = 0");
  $select->bindParam(':ProductId', $id, PDO::PARAM_INT);
  $select->execute();

  $row=$select->fetch(PDO::FETCH_ASSOC);

  $id_db=$row['ProductId'];

  $barcode_db=$row['Barcode'];
  $product_db=$row['ProductName'];
  $category_db=$row['CategoryId'];
  $unit_db=$row['UnitId'];
  $supplier_db=$row['SupplierId'];
  $description_db=$row['Description'];
  $stock_db=$row['Stock'];
  $purchaseprice_db=$row['PurchasePrice'];
  $saleprice_db=$row['SalePrice'];
  $image_db=$row['Image'];
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
              <h5 class="m-0">Edit Product</h5>
            </div>
            <form action="" method="post" name="formeditproduct" enctype="multipart/form-data">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label >Barcode</label>
                      <input type="text" class="form-control" value="<?php echo $barcode_db;?>" placeholder="Enter Barcode" name="txtbarcode" autocomplete="off" disabled>
                    </div>
                    <div class="form-group">
                      <label >Product Name</label>
                      <input type="text" class="form-control" value="<?php echo $product_db;?>" placeholder="Enter Name" name="txtproductname" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                      <label>Category</label>
                      <select class="form-control" name="txtselect_option" required>
                        <option value="" disabled selected>Select Category</option>
                        <?php
                          $select=$pdo->prepare("SELECT * FROM tCategory WHERE IsDeleted = 0 ORDER BY CategoryId DESC ");
                          $select->execute();
                          while($row=$select->fetch(PDO::FETCH_ASSOC)){
                            extract($row);
                            ?>
                            <option <?php if($row['CategoryName']==$category_db  ){ ?>  selected="selected"<?php }?>>
                              <?php echo $row['CategoryName'];?>
                            </option>
                            <?php
                          }
                        ?>
                      </select>
                    </div>
                    <!-- --------------------Unité----------------------- -->
                    <div class="form-group">
                      <label>Unité</label>
                        <select class="form-control" name="txtselect_unit" required>
                          <option value="" disabled selected>Select Category</option>
                          <?php
                            $select=$pdo->prepare("SELECT * FROM tUnit WHERE IsDeleted = 0 ORDER BY UnitId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                              <option <?php if($row['UnitName']==$unit_db){?> selected="selected"<?php }?>>
                                <?php echo $row['UnitName'];?>
                              </option>
                              <?php
                            }
                            ?>
                        </select>
                      </div>
                      <!-- --------------------Fournisseur------------------------------- -->
                      <div class="form-group">
                        <label>Fournisseur</label>
                        <select class="form-control" name="txtselect_supplier" required>
                          <option value="" disabled selected>Select Category</option>
                          <?php
                            $select=$pdo->prepare("SELECT * FROM tSupplier WHERE IsDeleted = 0 ORDER BY SupplierId DESC");
                            $select->execute();

                            while($row=$select->fetch(PDO::FETCH_ASSOC)){
                              extract($row);
                              ?>
                              <option <?php if($row['SupplierName']==$supplier_db  ){ ?> selected="selected" <?php }?> >
                                <?php echo $row['SupplierName'];?>
                              </option>
                              <?php
                              }
                            ?>
                        </select>
                      </div>
                      <!-- --------------------------------------------------------- -->
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label >Stock Quantity</label>
                        <input type="number" min="1" step="any" class="form-control" value="<?php echo $stock_db;?>" placeholder="Enter Stock" name="txtstock" autocomplete="off" required>
                      </div>
                      <div class="form-group">
                        <label >Purchase Price</label>
                        <input type="number" min="1" step="any" class="form-control" value="<?php echo $purchaseprice_db;?>" placeholder="Enter Stock" name="txtpurchaseprice" autocomplete="off" required>
                      </div>
                      <div class="form-group">
                        <label >Sale Price</label>
                        <input type="number" min="1" step="any" class="form-control" value="<?php echo $saleprice_db;?>" placeholder="Enter Stock" name="txtsaleprice" autocomplete="off" required>
                      </div>
                      <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" placeholder="Enter Description" name="txtdescription" rows="4" required><?php echo $description_db;?> </textarea>
                      </div>
                      <div class="form-group">
                        <label >Product image</label><br />
                        <image src="productimages/<?php echo $image_db;?>" class="img-rounded" width="50px" height="50px/">
                        <input type="file" class="input-group"  name="myfile">
                        <p>Upload image</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-center">
                    <button type="submit" class="btn btn-success" name="btneditproduct">Update Product</button></div>
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
  if(isset($_SESSION['status']) && $_SESSION['status']!=''){
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

<!--  
1- Error : 
Fatal error: Uncaught PDOException: SQLSTATE[22007]: Invalid datetime format: 1366 Incorrect integer value: 'Librerie' for column `bcm_sale_point_db`.`tproduct`.`CategoryId` at row 1 in /Applications/XAMPP/xamppfiles/htdocs/www/ui/Editproduct.php:150 Stack trace: #0 /Applications/XAMPP/xamppfiles/htdocs/www/ui/Editproduct.php(150): PDOStatement->execute() #1 {main} thrown in /Applications/XAMPP/xamppfiles/htdocs/www/ui/Editproduct.php on line 150

2- Fix this by joining this tables

tCategory
1	CategoryId Primary	int(11)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
	2	CategoryName	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	3	CreatedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	4	ModifiedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	5	CreatedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	6	ModifiedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	7	IsDeleted	tinyint(1)			Yes	NULL			Change Change	Drop Drop	

tSupplier
  1	SupplierId Primary	int(20)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
	2	SupplierName	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	3	SupplierNumber	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	4	SupplierEmail	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	5	SupplierAddress	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	6	CreatedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	7	ModifiedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	8	CreatedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	9	ModifiedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	10	IsDeleted	tinyint(1)			Yes	NULL			Change Change	Drop Drop	

tUnit
  1	UnitId Primary	int(20)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
	2	UnitName	varchar(200)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	3	CreatedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	4	ModifiedBy	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	5	CreatedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	6	ModifiedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	7	IsDeleted	tinyint(1)			Yes	NULL			Change Change	Drop Drop	

tProductStatus
  1	ProductStatusId Primary	int(11)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
	2	Code	varchar(10)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	3	Description	varchar(255)	utf8mb4_general_ci		No	None			Change Change	Drop Drop	
	4	ModifiedBy	varchar(100)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	5	CreatedBy	varchar(100)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	6	ModifiedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	7	CreatedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	8	IsDeleted	tinyint(4)			No	None			Change Change	Drop Drop	

tProduct
  1	ProductId Primary	int(20)			No	None		AUTO_INCREMENT	Change Change	Drop Drop	
	2	Barcode	varchar(255)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	3	ProductName	varchar(500)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	4	CategoryId	int(20)			Yes	NULL			Change Change	Drop Drop	
	5	Description	varchar(200)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	6	Stock	decimal(10,0)			Yes	NULL			Change Change	Drop Drop	
	7	PurchasePrice	decimal(10,0)			Yes	NULL			Change Change	Drop Drop	
	8	SalePrice	decimal(10,0)			Yes	NULL			Change Change	Drop Drop	
	9	Image	varchar(200)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	10	SupplierId	int(20)			Yes	NULL			Change Change	Drop Drop	
	11	UnitId	int(20)			Yes	NULL			Change Change	Drop Drop	
	12	ExpiryDate	date			Yes	NULL			Change Change	Drop Drop	
	13	CreatedBy	varchar(200)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	14	ModifiedBy	varchar(200)	utf8mb4_general_ci		Yes	NULL			Change Change	Drop Drop	
	15	CreatedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	16	ModifiedDate	datetime			Yes	NULL			Change Change	Drop Drop	
	17	IsDeleted	tinyint(1)			Yes	NULL			Change Change	Drop Drop	
	18	ProductTypeId	int(20)			Yes	NULL			Change Change	Drop Drop	
	19	ProductStatusId	int(11)			Yes	NULL			Change Change	Drop Drop	
-->