<?php
error_reporting(0);

include_once 'connectdb.php';
session_start();
// Check if the session 'role' is set, and load the appropriate header based on the role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    }else {
        // Redirect to the login page or access denied page if role doesn't match
        header('location:../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rapport de tableau</title>
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
</head>
<body>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Rapport de tableau</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div>
    </div>

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
                                            <div class="input-group date" id="date_2" data-target-input="nearest">
                                                <input type="text" class="form-control date_2" data-target="#date_2" name="date_2"/>
                                                <div class="input-group-append" data-target="#date_2" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-warning" name="btnfilter">Filtrer les données</button>
                                        </div>
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
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="info-box mb-3">
                                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">TOTAL</span>
                                                <span class="info-box-number">
                                                    <h2><?php echo number_format($subtotal,2);?></h2>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-striped table-hover" id="table_report">
                                    <thead>
                                    <tr>
                                        <td>Facture no</td>
                                        <td>Date de la commande</td>
                                        <td>Total</td>
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
                                                <td>' . $row->paid         . '</td>
                                                <td>' . $row->due          . '</td>
                                                <td>' . $row->payment_type . '</td>
                                            </tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "footer.php";
?>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
    // Date pickers initialization
    $('#date_1').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#date_2').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    // DataTable initialization with buttons
    $(document).ready(function() {
        $('#table_report').DataTable({
            "order": [[0, "desc"]],
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#table_report_wrapper .col-md-6:eq(0)');
    });
</script>
</body>
</html>
