<?php
session_start();
require_once '../models/init.php';

$stmt = $con->query("SELECT name FROM category");
$categories = $stmt->fetch_all(PDO::FETCH_ASSOC);

// Возвращаем данные в формате JSON
echo json_encode($categories);
