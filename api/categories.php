<?php
session_start();
require_once '../models/init.php';

$stmt = $con->query("SELECT * FROM category");
$categories = $stmt->fetch_all(PDO::FETCH_ASSOC);

echo json_encode($categories);
