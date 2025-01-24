<?php
session_start();
require_once '../models/init.php';

$stmt = $con->query("SELECT * FROM lot");
$lots = $stmt->fetch_all(PDO::FETCH_ASSOC);

echo json_encode($lots);

