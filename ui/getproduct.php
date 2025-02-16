<?php

include_once'connectdb.php';

$productid=$_GET["id"];

$barcode=$_GET["id"];

$select=$pdo->prepare("SELECT * FROM tProduct where ProductId=$productid OR Barcode=$barcode");
$select->execute();

$row=$select->fetch(PDO::FETCH_ASSOC);

$response=$row;

header('Content-Type: application/json');

echo json_encode($response);





?>