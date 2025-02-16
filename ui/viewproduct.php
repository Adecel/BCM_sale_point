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

  include 'barcode/barcode128.php';
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
          <div class="card card-info card-outline">
              <div class="card-header">
                <h5 class="m-0">Le produit</h5>
              </div>
              <div class="card-body">
                <?php
                  $id =$_GET['id'];

                  $select = $pdo->prepare("SELECT * FROM tProduct WHERE ProductId = $id");
                  $select->execute();

                  while($row=$select->fetch(PDO::FETCH_OBJ)){
                    echo'
                      <div class="row">
                        <div class="col-md-6">
                          <ul class="list-group">
                            <center><p class="list-group-item list-group-item-info"><b>DÉTAILS DU PRODUIT</b></p></center>  

                            <li class="list-group-item"><b>Code-barre</b> <span class="badge badge-light float-right">'.bar128($row->Barcode).'</span></li>
                            <li class="list-group-item"><b>Nom du produit</b><span class="badge badge-warning float-right">'.$row->ProductName.'</span></li>
                            <li class="list-group-item"><b>Fournisseur</b> <span class="badge badge-success float-right">'.$row->SupplierId.'</span></li>
                            <li class="list-group-item"><b>Unité</b> <span class="badge badge-success float-right">'.$row->UnitId.'</span></li>
                            <li class="list-group-item"><b>Catégorie</b> <span class="badge badge-success float-right">'.$row->CategoryId.'</span></li>
                            <li class="list-group-item"><b>Description </b><span class="badge badge-primary float-right">'.$row->Description.'</span></li>
                            <li class="list-group-item"><b>Quantité en stock</b> <span class="badge badge-danger float-right">'.$row->Stock.'</span></li>
                            <li class="list-group-item"><b>Prix ​​dachat </b><span class="badge badge-secondary float-right">'.$row->PurchasePrice.'</span></li>
                            <li class="list-group-item"><b>Prix ​​de vente</b> <span class="badge badge-dark float-right">'.$row->SalePrice.'</span></li>
                            <li class="list-group-item"><b>Bénéfice du produit</b> <span class="badge badge-success float-right">'.($row->SalePrice - $row->PurchasePrice).'</span></li>
                          </ul>
                        </div>

                        <div class="col-md-6">
                          <ul class="list-group">
                            <center> <p class="list-group-item list-group-item-info"> <b>IMAGE DU PRODUIT</b> </p> </center>  
                            <img src="productimages/'.$row->Image.'" class="img-thumbnail"/>
                          </ul>
                        </div>
                      </div>

                    ';
                  }
                ?>
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