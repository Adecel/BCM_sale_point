1- here is category form witch is working fine displaying the categies  that are = 0 

<form action="" method="post">
                    <div class="card-body">
                        <div class="row">
                            <?php
                            // Edit category form
                            if (isset($_POST['btnedit'])) {
                                $select = $pdo->prepare("SELECT * FROM tCategory WHERE CategoryId = :id AND IsDeleted = 0");
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);

                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtcategory">Category</label>
                                            <input type="hidden" class="form-control" name="txtcatid" value="' . $row->CategoryId . '">
                                            <input type="text" class="form-control" name="txtcategory" value="' . $row->CategoryName . '">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-info" name="btnupdate">Mettre à jour</button>
                                        </div>
                                    </div>';
                                }
                            } else {
                                // Add new category form
                                echo '
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtcategory">Category</label>
                                        <input type="text" class="form-control" name="txtcategory" placeholder="Enter Category">
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-warning" name="btnsave">Sauvegarder</button>
                                    </div>
                                </div>';
                            }
                            ?>

                            <!-- Category List with Pagination -->
                            <div class="col-md-8">
                                <table id="table_category" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tCategory WHERE IsDeleted = 0 ORDER BY CategoryId ASC");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <tr>
                                                <td>' . $row->CategoryName . '</td>
                                                <td><button type="submit" class="btn btn-primary" value="' . $row->CategoryId . '" name="btnedit">Modifier</button></td>
                                                <td><button type="submit" class="btn btn-danger" value="' . $row->CategoryId . '" name="btndelete">Supprimer</button></td>
                                            </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Modifier</th>
                                        <th>Supprimer</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>


2- I want the same thing for Unit as well 
<form action="" method="post">
                        <div class="row">
                            <?php
                            if (isset($_POST['btnedit'])) {
                                //$select = $pdo->prepare("SELECT * FROM tUnit WHERE unitid = :id");
                                $select = $pdo->prepare("SELECT * FROM tUnit WHERE IsDeleted = 0 ORDER BY unitid ASC");
                                //$select->execute();
                                $select->bindParam(':id', $_POST['btnedit']);
                                $select->execute();

                                if ($select) {
                                    $row = $select->fetch(PDO::FETCH_OBJ);
                                    echo '
                                        <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Unité</label>
                                            <input type="hidden" class="form-control" placeholder="Entrez l\'unité" value="' . $row->unitid . '" name="txtunitid">
                                            <input type="text" class="form-control" placeholder="Entrez l\'unité" value="' . $row->unitname . '" name="txtunit">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-info" name="btnupdate">Mise à jour</button>
                                        </div>
                                        </div>';
                                                    }
                                                } else {
                                                    echo '
                                    <div class="col-md-4">
                                        <div class="form-group">
                                        <label for="exampleInputEmail1">Unité</label>
                                        <input type="text" class="form-control" placeholder="Entrez l\'unité" name="txtunit">
                                        </div>
                                        <div class="card-footer">
                                        <button type="submit" class="btn btn-primary" name="btnsave">Sauvegarder</button>
                                        </div>
                                    </div>';
                                }
                            ?>
                            <div class="col-md-8">
                                <table id="table_unit" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <td>Unité</td>
                                        <td>Modifier</td>
                                        <td>Supprimer</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tUnit ORDER BY unitid ASC");$select = $pdo->prepare("SELECT * FROM tUnit WHERE IsDeleted = 0 ORDER BY unitid ASC");
                                    // $select->execute();                                    
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                        <tr>
                          <td>' . $row->unitname . '</td>
                          <td>
                            <button type="submit" class="btn btn-primary" value="' . $row->unitid . '" name="btnedit">Modifier</button>
                          </td>
                          <td>
                            <button type="submit" class="btn btn-danger" value="' . $row->unitid . '" name="btndelete">Supprimer</button>
                          </td>
                        </tr>';
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>Unité</td>
                                        <td>Modifier</td>
                                        <td>Supprimer</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>