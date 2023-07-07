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
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>



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
          <h1 class="m-0">Rapport graphique</h1>
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
        
<form method="post" action="" name="">
        <div class="card card-primary card-outline">
            <div class="card-header">
            <h5 class="m-0">DE : <?php echo $_POST['date_1']; ?>  -- À : <?php echo $_POST['date_2']; ?> </h5>
            </div>
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
                  <button type="submit" class="btn btn-warning" name="btnfilter">filtrer les données</button></div>
                </div>

</div>


<?php

$select = $pdo->prepare("select order_date , sum(total) as grandtotal from tbl_invoice where order_date between :fromdate AND :todate group by order_date");
$select->bindParam(':fromdate',$_POST['date_1']);
$select->bindParam(':todate',$_POST['date_2']);




          $select->execute();


          $total=[];
          $date=[];

          while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
extract($row);

$total[]=$grandtotal;
$date[]=$order_date;




          }

// echo json_encode($total);


?>






            <div>
  <canvas id="myChart" style="height:250px"></canvas>
</div>




            </div>

            <script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($date);?>,
      datasets: [{
        label: 'gain Total',
       backgroundColor:'rgb(255,99,132)',
       borderColor:'rgb(255,99,132)',
        data: <?php echo json_encode($total);?>,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>





<?php

$select = $pdo->prepare("select product_name , sum(qty) as q from tbl_invoice_details where order_date between :fromdate AND :todate group by product_id");
$select->bindParam(':fromdate',$_POST['date_1']);
$select->bindParam(':todate',$_POST['date_2']);




          $select->execute();


          $pname=[];
          $qty=[];

          while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
extract($row);

$pname[]=$product_name;
$qty[]=$q;




          }

// echo json_encode($total);


?>






            <div>
  <canvas id="bestsellingproduct" style="height:250px"></canvas>
</div>




            </div>

            <script>
  const ctx1 = document.getElementById('bestsellingproduct');

  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($pname);?>,
      datasets: [{
        label: 'La quantité de produit',
       backgroundColor:'rgb(102,255,102)',
       borderColor:'rgb(0,102,0)',
        data: <?php echo json_encode($qty);?>,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>













         
          </div>
          </form>

       
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





