<?php
session_start();
require_once('../models/init.php');

$_POST = json_decode(file_get_contents('php://input'), true);
$lot_id = (int)$_POST['lot_id'];

# История торгов
$query5 = "SELECT rate.rate_date, rate.lot_id, rate.price, users.name AS users_name, lot.name AS lot_name
                FROM rate
                INNER JOIN users ON rate.user_id = users.id
                JOIN lot ON rate.lot_id = lot.id
                WHERE rate.lot_id = ?
                ORDER BY rate.rate_date DESC;";

$stmt5 = $con->prepare($query5);
$stmt5->bind_param('i', $lot_id);
$stmt5->execute();

$result5 = $stmt5->get_result();

if ($result5 && mysqli_num_rows($result5) > 0) {
    $rows = $result5 -> fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
} else {
    echo json_encode([]);
}
