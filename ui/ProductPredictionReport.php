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
    } else {
        // Redirect to the login page or access denied page if role doesn't match
        header('location:../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Report</title>
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
                    <h1 class="m-0">Stock Report</h1>
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
                            <h5 class="m-0">Stock Status and Products in Need</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover" id="table_stock_report">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Barcode</th>
                                    <th>Category</th>
                                    <th>Initial Stock</th>
                                    <th>Stock Restant</th>
                                    <th>Status</th>
                                    <th>Number of Times Needed</th>
                                    <th>Amount</th>
                                    <th>Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $totalPrice = 0; // Initialize total price variable

                                // Fetch products with low stock
                                $stmt = $pdo->prepare("
                                    (SELECT 
                                        P.ProductName,
                                        P.Barcode,
                                        C.CategoryName AS Category,
                                        AP.Stock AS StockIntial,
                                        P.Stock,
                                        'Stock Status' AS Source,
                                        NULL AS NumberOfTimes,
                                        CASE 
                                            WHEN P.Stock = 0 THEN 'Rupture de stock' 
                                            WHEN P.Stock <= 10 THEN 'Presque fini' 
                                        END AS Status,
                                        PS.Description AS ProductStatus,
                                        P.PurchasePrice AS Amount,
                                        (P.PurchasePrice * AP.Stock) AS TotalPrice
                                    FROM 
                                        tProduct P
                                        LEFT JOIN tAuditProduct AP ON P.ProductId = AP.ProductId  AND AP.IsDeleted = 0
                                        LEFT JOIN tCategory C ON P.CategoryId = C.CategoryId AND C.IsDeleted = 0
                                        LEFT JOIN tProductStatus PS ON P.ProductStatusId = PS.ProductStatusId AND PS.IsDeleted = 0
                                    WHERE 
                                        P.Stock <= 10
                                    ORDER BY 
                                        FIELD(PS.Description, 'Rupture de stock', 'Meilleure vente', 'Promotion', 'En attente de livraison', 'En stock', 'Obsolète')
                                    )
                                    UNION ALL
                                    (SELECT
                                        PIN.ProductInNeedName AS ProductName,
                                        NULL AS Barcode,
                                        NULL AS Category,
                                        NULL AS StockIntial,
                                        NULL AS Stock,
                                        'Products in Need' AS Source,
                                        PIN.NumberOfTimes,
                                        CASE 
                                            WHEN PIN.ProductInNeedStatusId = 3 THEN 'Haut Demande'
                                            WHEN PIN.ProductInNeedStatusId = 1 THEN 'Faible'
                                            ELSE 'Normal'
                                        END AS Status,
                                        NULL AS ProductStatus,
                                        PIN.EstimatePrice AS Amount,
                                        (PIN.EstimatePrice * PIN.NumberOfTimes) AS TotalPrice
                                    FROM 
                                        tProductInNeed PIN
                                    WHERE 
                                        PIN.IsDeleted = 0
                                    ORDER BY 
                                        PIN.NumberOfTimes DESC, 
                                        FIELD(PIN.ProductInNeedStatusId, 1, 2, 3)
                                    )");
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $totalPrice += $row['TotalPrice']; // Accumulate total price
                                    echo '<tr>
                                        <td>' . ($row['ProductName'] ? $row['ProductName'] : '') . '</td>
                                        <td>' . ($row['Barcode'] ? $row['Barcode'] : '') . '</td>
                                        <td>' . ($row['Category'] ? $row['Category'] : '') . '</td>
                                        <td>' . ($row['StockIntial'] ? $row['StockIntial'] : '') . '</td>
                                        <td>' . ($row['Stock'] ? $row['Stock'] : '') . '</td>
                                        <td>' . $row['Status'] . '</td>
                                        <td>' . ($row['NumberOfTimes'] ? $row['NumberOfTimes'] : '') . '</td>
                                        <td>' . ($row['Amount'] ? number_format($row['Amount'], 2) : '') . '</td>
                                        <td>' . ($row['TotalPrice'] ? number_format($row['TotalPrice'], 2) : '') . '</td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8" style="text-align: right;">Total:</td>
                                    <td><?php echo number_format($totalPrice, 2); ?></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the necessary scripts -->
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
        // Initialize DataTable with button export functionality
        var table = $('#table_stock_report').DataTable({
            "order": [[0, "asc"]],
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [
                {
                    extend: 'copy',
                    footer: true,  // Export footer with totals
                    exportOptions: {
                        modifier: {
                            page: 'all' // Export all pages
                        },
                        // Custom function to append footer row to export data
                        format: {
                            body: function(data, row, column, node) {
                                return data; // No modification in the body
                            },
                            footer: function (data, columnIdx) {
                                // Get the footer data to append it to export
                                return $('#table_stock_report tfoot tr').children().eq(columnIdx).text();
                            }
                        }
                    }
                },
                {
                    extend: 'csv',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            },
                            footer: function (data, columnIdx) {
                                return $('#table_stock_report tfoot tr').children().eq(columnIdx).text();
                            }
                        }
                    }
                },
                {
                    extend: 'excel',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            },
                            footer: function (data, columnIdx) {
                                return $('#table_stock_report tfoot tr').children().eq(columnIdx).text();
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            },
                            footer: function (data, columnIdx) {
                                return $('#table_stock_report tfoot tr').children().eq(columnIdx).text();
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    footer: true,
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        format: {
                            body: function(data, row, column, node) {
                                return data;
                            },
                            footer: function (data, columnIdx) {
                                return $('#table_stock_report tfoot tr').children().eq(columnIdx).text();
                            }
                        }
                    }
                },
                'colvis'
            ]
        });

        // Attach buttons to the container
        table.buttons().container().appendTo('#table_stock_report_wrapper .col-md-6:eq(0)');
    });
</script>

</body>
</html>

<?php
include_once "footer.php";
?>