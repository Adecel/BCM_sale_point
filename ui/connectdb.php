<?php
try{
    $pdo = new PDO('mysql:host=localhost;dbname=bcm_sale_point_db','root','');
}catch(PDOException $e  ){
    echo $e->getMessage();
}
//echo'connection success';
?>