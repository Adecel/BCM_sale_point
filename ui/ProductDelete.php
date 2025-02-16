<?php
include_once'connectdb.php';

// $id=$_POST['pidd'];
// $sql="delete from tbl_product where pid =$id";
// $delete=$pdo->prepare($sql);

// if($delete->execute()){

// }else{
//     echo"Error in deleting product";
// }

if ($_POST['pidd']) {
    $productId = $_POST['pidd'];

    // Soft delete by updating the IsDeleted field to 1
    $sql = "UPDATE tProduct SET IsDeleted = 1 WHERE ProductId = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $productId]);

    // You can return some response if needed
    echo 'Product soft deleted successfully.';
}
else{
    echo"Error in deleting product";
}

?>