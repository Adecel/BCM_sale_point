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
    <title>Annual Inventory Report</title>
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
</head>
<body>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Annual Inventory Report</h1>
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
                            <h5 class="m-0">Year: <?php echo date('Y'); ?></h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover" id="table_report">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Barcode</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Sales</th>
                                    <th>Sale Year</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $stmt = $pdo->prepare("
                      SELECT 
                          p.ProductName AS ProductName,
                          p.Barcode AS Barcode,
                          p.CategoryId AS Category,
                          p.Stock AS Stock,
                          SUM(id.qty) AS QuantitySold,
                          SUM(id.qty * id.saleprice) AS TotalSales,
                          DATE_FORMAT(i.order_date, '%Y') AS SaleYear
                      FROM 
                          tProduct p
                      LEFT JOIN 
                          tbl_invoice_details id ON p.pid = id.product_id
                      LEFT JOIN 
                          tbl_invoice i ON id.invoice_id = i.invoice_id
                      WHERE
                          DATE_FORMAT(i.order_date, '%Y') = DATE_FORMAT(CURDATE(), '%Y')
                      GROUP BY 
                          p.product, p.barcode, p.category, p.stock, DATE_FORMAT(i.order_date, '%Y')
                      ORDER BY 
                          SaleYear
                    ");
                                $stmt->execute();
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                    echo '<tr>
                              <td>' . $row['ProductName'] . '</td>
                              <td>' . $row['Barcode'] . '</td>
                              <td>' . $row['Category'] . '</td>
                              <td>' . $row['Stock'] . '</td>
                              <td>' . $row['QuantitySold'] . '</td>
                              <td>' . $row['TotalSales'] . '</td>
                              <td>' . $row['SaleYear'] . '</td>
                            </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
<script>
    $(document).ready(function() {
        $('#table_report').DataTable({
            "order": [[0, "desc"]],
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#table_report_wrapper .col-md-6:eq(0)');
    });
</script>
</body>
</html>

<?php
include_once "footer.php";
?>
