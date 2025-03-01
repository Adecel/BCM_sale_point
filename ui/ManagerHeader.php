<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BCM - POS | Manager</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <!-- <a href="index3.html" class="nav-link">Home</a> -->
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <!-- <a href="#" class="nav-link">Contact</a> -->
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="Dashboard.php" class="brand-link">
            <img src="../dist/img/BCM.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">BCM System | Manager</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="../dist/img/user01.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo $_SESSION['username']; ?></a>
                </div>
            </div>

            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="ManagerDashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Tableau de bord
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="Supplier.php" class="nav-link">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>
                                Gérer les fournisseurs
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="Category.php" class="nav-link">
                            <i class="nav-icon fas fa-braille"></i>
                            <p>
                                Gérer les catégories
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="Unit.php" class="nav-link">
                            <i class="nav-icon fas fa-weight"></i>
                            <p>
                                Gérer les unités

                            </p>
                        </a>
                    </li>
                    <!--  -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>
                                Gérer les produits
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="ProductAdd.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ajouter les produits</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ProductList.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Liste de produits</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ProductMissing.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Produit manquant</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ProductPredictionReport.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Liste des predictions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!--  -->
                    <!-- <li class="nav-item">
                      <a href="addproduct.php" class="nav-link">
                      <i class="nav-icon fas fa-sitemap"></i>
                        <p>
                        Gérer les produits

                        </p>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="productlist.php" class="nav-link">
                      <i class="nav-icon fas fa-list"></i>
                        <p>
                        Liste de produits

                        </p>
                      </a>
                    </li> -->

                    <li class="nav-item">
                        <a href="pos.php" class="nav-link">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>
                                points de vente

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="OrderList.php" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Liste de commandes

                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Rapport des ventes
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="ReportTable.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapport de tableau</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ReportGraph.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapport graphique</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ReportDaily.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapport journalière</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ReportMonthly.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapport mensuel</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ReportAnnual.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Rapport annuel</p>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <!-- <li class="nav-item">
                      <a href="taxdis.php" class="nav-link">
                      <i class="nav-icon fas fa-calculator"></i>
                        <p>
                          Tax(Impôt)-(SGST-CGST)
                        </p>
                      </a>
                    </li> -->

<!--                    <li class="nav-item">-->
<!--                        <a href="registration.php" class="nav-link">-->
<!--                            <i class="nav-icon far fa-address-card"></i>-->
<!--                            <p>-->
<!--                                Gérer les utilisateurs-->
<!--                            </p>-->
<!--                        </a>-->
<!--                    </li>-->

                    <li class="nav-item">
                        <a href="ChangePassword.php" class="nav-link">
                            <i class="nav-icon fas fa-user-lock"></i>
                            <p>
                                Changer le mot de passe
                            </p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Se déconnecter
                            </p>
                        </a>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
