<?php
session_start();
require_once('../models/init.php');

$_POST = json_decode(file_get_contents('php://input'), true);
$lot_id = (int)$_POST['lot_id'];

$sql_count_rates = "SELECT COUNT(*) as count
                   FROM rate
                   WHERE lot_id = ?;";

$stmt3 = $con->prepare($sql_count_rates);
$stmt3->bind_param('i', $lot_id);
$stmt3->execute();

$count_rates = $stmt3->get_result();

if ($count_rates) {
    $count_rows = mysqli_fetch_assoc($count_rates);
    $rates_number = $count_rows['count'];
} else {
    $rates_number = 0;
}
echo $rates_number;






