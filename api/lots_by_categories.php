<?php
session_start();
require_once '../models/init.php';

$categoryData = json_decode(file_get_contents('php://input'), true);
$cat = $categoryData['category'];

$query = "
    SELECT lot.name AS name, category.name AS category_name, lot.id AS id, category.id AS category_id, cur_price, img_url, lot_message, lot_date, price
    FROM lot
    JOIN category ON lot.category_id = category.id
    WHERE category.name_eng = ?;";

$stmt = $con->prepare($query);
if ($stmt) {
    $stmt->bind_param('s', $cat);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($rows);
    } else {
        echo json_encode([]);
    }
    $stmt->close();
} else {
    // Handle prepare() error
    echo json_encode(['error' => 'Database query failed']);
}

$con->close();
?>
