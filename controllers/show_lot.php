<?php
session_start();
require_once '../models/init.php';
// Отправляется с фронта id лота. И получаем id.
$lot_id = json_decode(file_get_contents('php://input'), true);
$lot_id = $lot_id['id'];

# ЗАПРОС К БД. ВЫВОД ЛОТА.
$query = "SELECT lot.id, lot.name AS lot_name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price,
       category.name AS category_name, user_id, users.email, users.name, users.id AS users_id
       FROM `lot`
       JOIN category ON lot.category_id = category.id
       JOIN users ON lot.user_id = users.id
       WHERE lot.id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param('i', $lot_id);
$stmt->execute();
$chosen_lot = $stmt->get_result();
if ($chosen_lot && mysqli_num_rows($chosen_lot) > 0) {
    $search_array = mysqli_fetch_assoc($chosen_lot);
    echo json_encode($search_array);
}
