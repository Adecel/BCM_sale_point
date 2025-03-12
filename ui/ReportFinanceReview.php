<?php
error_reporting(0);
include_once 'connectdb.php';
session_start();

// Check user role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    } else {
        header('location:../index.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Financial Review Report</title>
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
                    <h1 class="m-0">Financial Review Report</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h5 class="m-0">From: <?php echo $_POST['date_1']; ?> - To: <?php echo $_POST['date_2']; ?></h5>
                        </div>
                        <form action="" method="post">
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
                                            <button type="submit" class="btn btn-success" name="btnfilter">Filter Data</button>
                                        </div>
                                    </div>
                                </div>

                                <br>
                                <?php
                                if (isset($_POST['btnfilter'])) {
                                    $from_date = $_POST['date_1'];
                                    $to_date = $_POST['date_2'];

                                    // Capital (Total Purchase Price)
                                    $query = $pdo->prepare("SELECT SUM(PurchasePrice * Stock) AS capital FROM tAuditProduct WHERE IsDeleted = 0 AND CreatedDate BETWEEN :fromdate AND :todate");
                                    $query->bindParam(':fromdate', $from_date);
                                    $query->bindParam(':todate', $to_date);
                                    $query->execute();
                                    $capital = $query->fetch(PDO::FETCH_OBJ)->capital;

                                    // Amount in Shop (Total Sale Price)
                                    $query = $pdo->prepare("SELECT SUM(SalePrice * Stock) AS amount_in_shop FROM tAuditProduct WHERE IsDeleted = 0 AND CreatedDate BETWEEN :fromdate AND :todate");
                                    $query->bindParam(':fromdate', $from_date);
                                    $query->bindParam(':todate', $to_date);
                                    $query->execute();
                                    $amount_in_shop = $query->fetch(PDO::FETCH_OBJ)->amount_in_shop;

                                    // Expected Profit (Sale Price - Purchase Price)
                                    $expected_profit = $amount_in_shop - $capital;

                                    // Money in Hand (Total Sales from Invoices)
                                    $query = $pdo->prepare("SELECT SUM(total) AS money_in_hand FROM tbl_invoice WHERE order_date BETWEEN :fromdate AND :todate");
                                    $query->bindParam(':fromdate', $from_date);
                                    $query->bindParam(':todate', $to_date);
                                    $query->execute();
                                    $money_in_hand = $query->fetch(PDO::FETCH_OBJ)->money_in_hand;
                                }
                                ?>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-coins"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Capital</span>
                                                <span class="info-box-number"><?php echo number_format($capital, 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-store"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Amount in Shop</span>
                                                <span class="info-box-number"><?php echo number_format($amount_in_shop, 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-dollar-sign"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Expected Profit</span>
                                                <span class="info-box-number"><?php echo number_format($expected_profit, 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Money in Hand</span>
                                                <span class="info-box-number"><?php echo number_format($money_in_hand, 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "footer.php"; ?>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
    $('#date_1, #date_2').datetimepicker({ format: 'YYYY-MM-DD' });
</script>
</body>
</html>
