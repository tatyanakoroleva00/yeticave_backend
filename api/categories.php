<?php
session_start();
require_once '../models/init.php';

//$stmt1 = $con->query("SELECT name_eng FROM category");
//$categories_eng = $stmt1->fetch_all(PDO::FETCH_ASSOC);
//
//$stmt2 = $con->query("SELECT name FROM category");
//$categories = $stmt2->fetch_all(PDO::FETCH_ASSOC);

$stmt = $con->query("SELECT * FROM category");
$categories = $stmt->fetch_all(PDO::FETCH_ASSOC);

// Возвращаем данные в формате JSON
//echo json_encode(['names' => $categories, 'names_eng' => $categories_eng]);

echo json_encode($categories);
