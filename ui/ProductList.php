<?php
include_once 'connectdb.php';
session_start();

// Check if the session 'role' is set and load the appropriate header
if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once 'header.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Manager') {
    include_once 'ManagerHeader.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Stockeur') {
    include_once 'StockerHeader.php';
} else {
    // Redirect to the login page or access denied page if role doesn't match
    header('location:../index.php');
    exit();
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste des produits</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Liste des produits</li> -->
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
                            <h5 class="m-0">Liste des produits</h5>
                        </div>
                        <div class="card-body">

                            <table class="table table-striped table-hover " id="table_product_prediction">
                                <thead>
                                <tr>
                                    <td>Code-barre</td>
                                    <td>Produit</td>
                                    <td>Fournisseur</td>
                                    <td>Unité</td>
                                    <td>Catégorie</td>
                                    <td>Description</td>
                                    <td>Stock</td>
                                    <td>Prix ​​d'achat</td>
                                    <td>Prix ​​de vente</td>
                                    <td>Image</td>
                                    <td>ActionIcons</td>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                $select = $pdo->prepare("SELECT * FROM tProduct WHERE IsDeleted = 0 ORDER BY ProductId DESC");
                                $select->execute();

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    // Determine the row color based on stock level
                                    $rowColor = '';
                                    if ($row->stock == 0) {
                                        $rowColor = 'background-color: red; color: white;';
                                    } elseif ($row->stock < 10) {
                                        $rowColor = 'background-color: yellow;';
                                    }

                                    echo '
                        <tr style="' . $rowColor . '">
                          <td>' . $row->Barcode . '</td>
                          <td>' . $row->ProductName . '</td>
                          <td>' . $row->SupplierId . '</td>
                          <td>' . $row->UnitId . '</td>
                          <td>' . $row->CategoryId . '</td>
                          <td>' . $row->Description . '</td>
                          <td>' . $row->Stock . '</td>
                          <td>' . $row->PurchasePrice . '</td>
                          <td>' . $row->SalePrice . '</td>
                          <td><img src="productimages/' . $row->Image . '" class="img-rounded" width="40px" height="40px" /></td>
                          <td>
                            <div class="btn-group">   
                              <a href="PrintBarcode.php?id=' . $row->ProductId . '" class="btn btn-dark btn-xs" role="button">
                                <span class="fa fa-barcode" style="color:#ffffff" data-toggle="tooltip" title="Print Barcode"></span>
                              </a>
                              <a href="ViewProduct.php?id=' . $row->ProductId . '" class="btn btn-warning btn-xs" role="button">
                                <span class="fa fa-eye" style="color:#ffffff" data-toggle="tooltip" title="View Product"></span>
                              </a>
                              <a href="Editproduct.php?id=' . $row->ProductId . '" class="btn btn-success btn-xs" role="button">
                                <span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Product"></span>
                              </a>
                              <button id="' . $row->ProductId . '" class="btn btn-danger btn-xs btndelete">
                                <span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Product"></span>
                              </button>
                            </div>
                          </td>
                        </tr>';
                                }
                                ?>
                                </tbody>

                            </table>

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

<?php include_once "footer.php"; ?>

<script>
    $(document).ready(function() {
        $('#table_product_prediction').DataTable();
    });
</script>

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    $(document).ready(function() {
        $('.btndelete').click(function() {
            var tdh = $(this);
            var id = $(this).attr("id");
            Swal.fire({
                title: 'Do you want to delete?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'productdelete.php',
                        type: 'post',
                        data: {
                            pidd: id
                        },
                        success: function(data) {
                            tdh.parents('tr').hide();
                        }
                    });

                    Swal.fire(
                        'Deleted!',
                        'Your Product has been deleted.',
                        'success'
                    )
                }
            })

        });

    });
</script>
