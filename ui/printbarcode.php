<?php
  include_once 'connectdb.php';
  
  session_start();

  if($_SESSION['useremail']==""  OR $_SESSION['role']=="User"){
    header('location:../index.php');
  }

  if($_SESSION['role']=="Admin"){
    include_once'header.php';
  }else{  
    include_once 'UserHeader.php';
  }
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
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Generate Barcode Stickers:</h5>
            </div>
            <div class="card-body">
            <form class="form-horizontal" method="post" action="barcode/barcode.php" target="_blank">
              <?php
                $id=$_GET['id'];
                $select=$pdo->prepare("SELECT * FROM tProduct WHERE ProductId =$id AND IsDeleted = 0 ");
                $select->execute();

                while($row=$select->fetch(PDO::FETCH_OBJ)){
                  echo'
                  <div class="row">
                    <div class="col-md-6">
                      <ul class="list-group">
                        <center><p class="list-group-item list-group-item-info"><b>Print Barcode</b></p></center>  
                        <div class="form-group">
                          <label class="control-label col-sm-2" for="product">Product:</label>
                          <div class="col-sm-10">
                            <input autocomplete="OFF" type="text" class="form-control" value="'.$row->ProductName.'" id="product" name="product" readonly>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-sm-2" for="product_id">Barcode:</label>
                          <div class="col-sm-10">
                            <input autocomplete="OFF" type="text" class="form-control" value="'.$row->Barcode.'" id="barcode" name="barcode" readonly>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-sm-2" for="rate">Price</label>
                          <div class="col-sm-10">          
                            <input autocomplete="OFF" type="text" class="form-control" value="'.$row->SalePrice.'" id="rate"  name="rate" readonly>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-sm-2" for="rate">Stock QTY</label>
                          <div class="col-sm-10">          
                            <input autocomplete="OFF" type="text" class="form-control" value="'.$row->Stock.'" id="stock"  name="stock" readonly>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-sm-2" for="print_qty">Barcode Quantity</label>
                          <div class="col-sm-10">          
                            <input autocomplete="OFF" type="print_qty" class="form-control" id="print_qty"  name="print_qty" required>
                          </div>
                        </div>
                        <div class="form-group">        
                          <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Generate Barcode</button>
                          </div>
                        </div>
                      </ul>
                    </div>
                    <div class="col-md-6">
                      <ul class="list-group">
                        <center><p class="list-group-item list-group-item-info"><b>PRODUCT IMAGE</b></p></center>  
                        <img src="productimages/'.$row->Image.'" class="img-thumbnail"/>
                      </ul>
                    </div>
                  </div>';
                }
              ?>      
            </form>
          </div>
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