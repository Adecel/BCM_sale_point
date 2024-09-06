<?php
// Include the database connection and start the session
include_once 'connectdb.php';
session_start();

// Check if the session 'role' is set, and load the appropriate header based on the role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        include_once 'header.php';
    } elseif ($_SESSION['role'] == 'Manager') {
        include_once 'ManagerHeader.php';
    } elseif ($_SESSION['role'] == 'Utilisateur') {
        include_once 'UserHeader.php';
    } else {
        // Redirect to the login page or access denied page if role doesn't match
        header('location:../index.php');
        exit();
    }
} else {
    // If session 'role' is not set, redirect to the login page
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
                    <h1 class="m-0">Liste des commandes</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- Breadcrumbs can be added here if needed -->
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
                            <h5 class="m-0">Les commandes</h5>
                        </div>
                        <div class="card-body">
                            <!-- Table for displaying orders -->
                            <table class="table table-striped table-hover" id="table_orderlist">
                                <thead>
                                <tr>
                                    <td>Facture No</td>
                                    <td>Date de la commande</td>
                                    <td>Total</td>
                                    <td>PAYÉ</td>
                                    <td>Dû</td>
                                    <td>Type de paiement</td>
                                    <td>Actions</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Fetch order data from the database and display it in the table
                                $select = $pdo->prepare("SELECT * FROM tbl_invoice ORDER BY invoice_id ASC");
                                $select->execute();

                                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    echo '
                                        <tr>
                                            <td>' . $row->invoice_id . '</td>
                                            <td>' . $row->order_date . '</td>
                                            <td>' . $row->total . '</td>
                                            <td>' . $row->paid . '</td>
                                            <td>' . $row->due . '</td>';

                                    // Display payment type with corresponding badge
                                    if ($row->payment_type == "Cash") {
                                        echo '<td><span class="badge badge-warning">' . $row->payment_type . '</span></td>';
                                    } elseif ($row->payment_type == "Card") {
                                        echo '<td><span class="badge badge-success">' . $row->payment_type . '</span></td>';
                                    } else {
                                        echo '<td><span class="badge badge-danger">' . $row->payment_type . '</span></td>';
                                    }

                                    echo '
                                            <td>
                                                <div class="btn-group">
                                                    <!-- Print button -->
                                                    <a href="printbill.php?id=' . $row->invoice_id . '" class="btn btn-warning" role="button" target="_blank">
                                                        <span class="fa fa-print" style="color:#ffffff" data-toggle="tooltip" title="Print Bill"></span>
                                                    </a>';

                                    // Conditionally display edit and delete buttons based on the user's role
                                    if ($_SESSION['role'] == "Manager" || $_SESSION['role'] == "Admin") {
                                        echo '
                                                    <a href="editorderpos.php?id=' . $row->invoice_id . '" class="btn btn-info" role="button">
                                                        <span class="fa fa-edit" style="color:#ffffff" data-toggle="tooltip" title="Edit Order"></span>
                                                    </a>';
                                    }
                                    if ($_SESSION['role'] == "Admin") {
                                        echo '
                                                    <button id="' . $row->invoice_id . '" class="btn btn-danger btndelete">
                                                        <span class="fa fa-trash" style="color:#ffffff" data-toggle="tooltip" title="Delete Order"></span>
                                                    </button>';
                                    }

                                    echo '
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

<?php
// Include footer
include_once "footer.php";
?>

<!-- JavaScript for handling tooltips and delete action -->
<script>
    $(document).ready(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle delete button click
        $('.btndelete').click(function () {
            var tdh = $(this);
            var id = $(this).attr("id");

            // Show confirmation dialog with SweetAlert
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
                    // Send AJAX request to delete the order
                    $.ajax({
                        url: 'ordertdelete.php',
                        type: 'post',
                        data: {pidd: id},
                        success: function (data) {
                            tdh.parents('tr').hide();
                        }
                    });

                    // Show success message
                    Swal.fire(
                        'Deleted!',
                        'Your Invoice has been deleted.',
                        'success'
                    )
                }
            })
        });

        // Initialize DataTable with descending order by invoice_id
        $('#table_orderlist').DataTable({
            "order": [[0, "desc"]]
        });
    });
</script>
