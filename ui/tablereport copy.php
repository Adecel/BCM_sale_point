<?php
error_reporting(0);
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


?>

 <!-- daterange picker -->
 <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Rapport de tableau</h1>
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
        

        <div class="card card-warning card-outline">
            <div class="card-header">
              <h5 class="m-0">DE : <?php echo $_POST['date_1']; ?>  -- À : <?php echo $_POST['date_2']; ?> </h5>
            </div>


<form action="" method="post" name="">

            <div class="card-body">
<div class="row">

<div class="col-md-5">
<div class="form-group">
                  <!-- <label>Date:</label> -->
                    <div class="input-group date" id="date_1" data-target-input="nearest">
                        <input type="text" class="form-control date_1" data-target="#date_1" name="date_1"/>
                        <div class="input-group-append" data-target="#date_1" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>

</div>

<div class="col-md-5">
<div class="form-group">
                  <!-- <label>Date:</label> -->
                    <div class="input-group date" id="date_2" data-target-input="nearest">
                        <input type="text" class="form-control date_2" data-target="#date_2"  name="date_2"/>
                        <div class="input-group-append" data-target="#date_2" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>



</div>

<div class="col-md-2">

<div class="text-center">
                  <button type="submit" class="btn btn-warning" name="btnfilter">Filtrer les données</button></div>
                </div>

</div>


<br>

<?php
$select = $pdo->prepare("select sum(total) as grandtotal , sum(subtotal) as stotal, count(invoice_id) as invoice from tbl_invoice where order_date between :fromdate AND :todate");
        $select->bindParam(':fromdate',$_POST['date_1']);
        $select->bindParam(':todate',$_POST['date_2']);




                  $select->execute();

                  $row = $select->fetch(PDO::FETCH_OBJ);

               $grand_total = $row->grandtotal;  
               $subtotal = $row->stotal; 
               $invoice = $row->invoice;

?>



<div class="row">
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">TOTAL DES FACTURE</span>
                <span class="info-box-number">
                 <h2><?php echo number_format($invoice,2);?></h2>
                 
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">TOTAL</span>
                <span class="info-box-number"> <h2><?php echo number_format($subtotal,2);?></h2></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <!-- <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">TOTAL GENERAL</span>
                <span class="info-box-number"> <h2><?php echo number_format($grand_total,2);?></h2></span>
              </div>
            </div>
          </div> -->

          <!-- /.col -->
         
          <!-- /.col -->
        </div>
        <!-- /.row -->


<br>




<table class="table table-striped table-hover " id="table_report">
                <thead>
                  <tr>
                  
                    <td>Facture no</td>
                    <td>Date de commande</td>
                    <td>Total</td>
                    <td>Rabais</td>
                    <!-- <td>SGST(%)</td>
                    <td>CGST(%)</td> -->
                    <!-- <td>Total</td> -->
                    <td>PAYÉ</td>
                    <td>Dû</td>
                    <td>Type de paiement</td>
                  
                    

                  </tr>

                </thead>


                <tbody>

                  <?php

                  $select = $pdo->prepare("select * from tbl_invoice where order_date between :fromdate AND :todate");
        $select->bindParam(':fromdate',$_POST['date_1']);
        $select->bindParam(':todate',$_POST['date_2']);




                  $select->execute();

                  while ($row = $select->fetch(PDO::FETCH_OBJ)) {

                    echo '
<tr>

<td>' . $row->invoice_id   . '</td>
<td>' . $row->order_date   . '</td>
<td>' . $row->subtotal        . '</td>
<td>' . $row->discount       . '</td>
<td>' . $row->sgst       . '</td>
<td>' . $row->cgst       . '</td>
<td><span class="badge badge-danger">' . $row->total        . '</td></span></td>
<td>' . $row->paid         . '</td>
<td>' . $row->due          . '</td>';




if($row->payment_type=="Cash"){
  echo'<td><span class="badge badge-warning">'.$row->payment_type.'</td></span></td>';
}else if($row->payment_type=="Card"){

  echo'<td><span class="badge badge-success">'.$row->payment_type.'</td></span></td>';
}else{
  echo'<td><span class="badge badge-danger">'.$row->payment_type.'</td></span></td>';

}







                  }
                  ?>
                  </tbody>
                 </table>








</div>


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

<!-- InputMask -->
<script src="../plugins/moment/moment.min.js"></script>

<!-- date-range-picker -->
<script src="../plugins/daterangepicker/daterangepicker.js"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>




<script>

 //Date picker
 $('#date_1').datetimepicker({
        format: 'YYYY-MM-DD'
    });



    //Date picker
 $('#date_2').datetimepicker({
        format: 'YYYY-MM-DD'
    });




</script>

<script>
  $(document).ready(function() {
    $('#table_report').DataTable({

      "order":[[0,"desc"]]
    });
  });
</script>