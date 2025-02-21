<?php
session_start();
require_once '../models/init.php';


if ($_SERVER['REQUEST_METHOD' === $_GET]) {
    echo 'true';
} else echo 'false';
//$category = json_decode($_GET['category']);
//echo json_encode($category);
//exit;


//$stmt = $con->prepare("
//    SELECT *
//    FROM lot
//    JOIN category ON lot.category_id = category.id
//    WHERE category.name = ?;");
//$stmt-> execute([$category]);
//$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
//echo json_encode($lots);
