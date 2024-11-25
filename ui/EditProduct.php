<?php
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

$select = $pdo->prepare("select * from tbl_product where pid=$id");
$select->execute();

$row=$select->fetch(PDO::FETCH_ASSOC);

$id_db=$row['pid'];

$barcode_db=$row['barcode'];
$product_db=$row['product'];
$category_db=$row['category'];
$unit_db=$row['unit'];
$supplier_db=$row['Supplier'];
$description_db=$row['description'];
$stock_db=$row['stock'];
$purchaseprice_db=$row['purchaseprice'];
$saleprice_db=$row['saleprice'];
$image_db=$row['image'];



if(isset($_POST['btneditproduct'])){

  // $barcode_txt       =$_POST['txtbarcode'];
  $product_txt       =$_POST['txtproductname'];
  $category_txt      =$_POST['txtselect_option'];
  $unit_txt      =$_POST['txtselect_unit'];
  $supplier_txt      =$_POST['txtselect_supplier'];
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
          
   
  
  
    $_SESSION['status']="Le fichier maximum doit être de 1 Mo";
    $_SESSION['status_code']="warning";
          
    }else{
        
    if(move_uploaded_file($f_tmp,$store)){
  
$f_newfile;
  

  $update = $pdo->prepare("update tbl_product set product=:product , category=:category , unit=:unit , supplier=:supplier ,
    description=:description , stock=:stock , purchaseprice=:pprice , saleprice=:sprice , image=:image where pid=$id");

  $update->bindParam(':product',$product_txt);
  $update->bindParam(':category',$category_txt);
  $update->bindParam(':unit',$unit_txt);
  $update->bindParam(':supplier',$supplier_txt);
  $update->bindParam(':description',$description_txt);
  $update->bindParam(':stock',$stock_txt);
  $update->bindParam(':pprice',$purchaseprice_txt);
  $update->bindParam(':sprice',$saleprice_txt);
  $update->bindParam(':image',$f_newfile);
  
  
  if($update->execute()){
  
  
    $_SESSION['status']="Produit mis à jour avec succès avec une nouvelle image";
    $_SESSION['status_code']="success";
  }else{
    $_SESSION['status']="Product Update Failed";
    $_SESSION['status_code']="error";
  
  }

    } } }



 }else{

$update = $pdo->prepare("update tbl_product set product=:product , category=:category , unit=:unit , supplier=:supplier ,
description=:description , stock=:stock , purchaseprice=:pprice , saleprice=:sprice , image=:image where pid=$id");

$update->bindParam(':product',$product_txt);
$update->bindParam(':category',$category_txt);
$update->bindParam(':unit',$unit_txt);
$update->bindParam(':supplier',$supplier_txt);
$update->bindParam(':description',$description_txt);
$update->bindParam(':stock',$stock_txt);
$update->bindParam(':pprice',$purchaseprice_txt);
$update->bindParam(':sprice',$saleprice_txt);
$update->bindParam(':image',$image_db);


if($update->execute()){


  $_SESSION['status']="Produit mis à jour avec succès";
  $_SESSION['status_code']="success";
}else{
  $_SESSION['status']="Product Update Failed";
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
$category_db=$row['category'];
$unit_db=$row['unit'];
$supplier_db=$row['Supplier'];
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
$select=$pdo->prepare("select * from tCategory order by catid desc");
$select->execute();

while($row=$select->fetch(PDO::FETCH_ASSOC))
{
extract($row);

?>
  <option <?php if($row['category']==$category_db  ){ ?>  
    selected="selected"
    <?php }?>  ><?php echo $row['category'];?></option>
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
$select=$pdo->prepare("select * from tUnit order by unitid desc");
$select->execute();

while($row=$select->fetch(PDO::FETCH_ASSOC))
{
extract($row);

?>
  <option <?php if($row['unitname']==$unit_db  ){ ?>  
    selected="selected"
    <?php }?>  ><?php echo $row['unitname'];?></option>
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
$select=$pdo->prepare("select * from tSupplier order by SupplierId desc");
$select->execute();

while($row=$select->fetch(PDO::FETCH_ASSOC))
{
extract($row);

?>
  <option <?php if($row['SupplierName']==$supplier_db  ){ ?>  
    selected="selected"
    <?php }?>  ><?php echo $row['SupplierName'];?></option>
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