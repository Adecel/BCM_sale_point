<?php
include_once 'connectdb.php';
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    include_once 'header.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Manager') {
    include_once 'ManagerHeader.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'Stockeur') {
    include_once 'StockerHeader.php';
} else {
    header('location:../index.php');
    exit();
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Liste des produits</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div>
    </div>

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

                            <table class="table table-striped table-hover" id="table_product_prediction">
                                <thead>
                                    <tr>
                                        <td>Code-barre</td>
                                        <td>Produit</td>
                                        <td>Fournisseur</td>
                                        <td>Unité</td>
                                        <td>Catégorie</td>
                                        <td>Stock Restant</td>
                                        <td>Statut</td>
                                        <td>Date d'expiration</td>
                                        <td>Image</td>
                                        <td>Actions</td>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php
                                $select = $pdo->prepare("
                                    SELECT 
                                        P.*, 
                                        C.CategoryName, 
                                        PS.Description AS ProductStatus,
                                        U.UnitName,
                                        S.SupplierName
                                    FROM tProduct P
                                        LEFT JOIN tCategory C           ON P.CategoryId = C.CategoryId                  AND C.IsDeleted = 0
                                        LEFT JOIN tProductStatus PS     ON P.ProductStatusId = PS.ProductStatusId       AND PS.IsDeleted = 0
                                        LEFT JOIN tSupplier S           ON P.SupplierId = S.SupplierId                  AND S.IsDeleted = 0
                                        LEFT JOIN tUnit U               ON P.UnitId = U.UnitId                          AND U.IsDeleted = 0
                                    WHERE P.IsDeleted = 0
                                        ORDER BY P.ProductId DESC
                                ");
                                $select->execute();

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    $rowColor = '';
                                    if ($row->Stock == 0) {
                                        $rowColor = 'background-color: red; color: white;';
                                    } elseif ($row->Stock < 10) {
                                        $rowColor = 'background-color: yellow;';
                                    }

                                    echo "
                                    <tr style='$rowColor'>
                                        <td>{$row->Barcode}</td>
                                        <td>{$row->ProductName}</td>
                                        <td>{$row->SupplierName}</td>
                                        <td>{$row->UnitName}</td>
                                        <td>{$row->CategoryName}</td>
                                        <td>{$row->Stock}</td>
                                        <td>{$row->ProductStatus}</td>
                                        <td>{$row->ExpiryDate}</td>
                                        <td><img src='productimages/{$row->Image}' class='img-rounded' width='40px' height='40px' /></td>
                                        <td>
                                            <div class='btn-group'>   
                                                <a href='PrintBarcode.php?id={$row->ProductId}' class='btn btn-dark btn-xs'>
                                                    <span class='fa fa-barcode' style='color:#ffffff' data-toggle='tooltip' title='Print Barcode'></span>
                                                </a>
                                                <a href='ViewProduct.php?id={$row->ProductId}' class='btn btn-warning btn-xs'>
                                                    <span class='fa fa-eye' style='color:#ffffff' data-toggle='tooltip' title='View Product'></span>
                                                </a>
                                                <a href='Editproduct.php?id={$row->ProductId}' class='btn btn-success btn-xs'>
                                                    <span class='fa fa-edit' style='color:#ffffff' data-toggle='tooltip' title='Edit Product'></span>
                                                </a>
                                                <button id='{$row->ProductId}' class='btn btn-danger btn-xs btndelete'>
                                                    <span class='fa fa-trash' style='color:#ffffff' data-toggle='tooltip' title='Delete Product'></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>";
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

<?php include_once "footer.php"; ?>

<script>
    $(document).ready(function() {
        $('#table_product_prediction').DataTable();
        $('[data-toggle="tooltip"]').tooltip();
        
        $('.btndelete').click(function() {
            var tdh = $(this);
            var id = $(this).attr("id");

            Swal.fire({
                title: 'Voulez-vous supprimer ce produit?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'productdelete.php',
                        type: 'post',
                        data: { pidd: id },
                        success: function(data) {
                            tdh.parents('tr').hide();
                        }
                    });

                    Swal.fire(
                        'Supprimé!',
                        'Le produit a été supprimé.',
                        'success'
                    )
                }
            })
        });
    });
</script>
