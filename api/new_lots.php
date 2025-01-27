<?php
session_start();
require_once '../models/init.php';

//var_dump($_POST);
$data = json_decode(file_get_contents('php://input'), true);
$param = $data['show'];
$order = $data['order'];
$min_price = $data['min_price'];
$publicationOrder = $data['publicationOrder'];
if($param === 'new' && $order === 'asc') {
# Сортировка лотов, начиная с тех, чей срок уже почти истек, до тех, у кого поздний срок.
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date ASC";
} else if($param === 'old' && $order === 'asc') {
    # Закрытые лоты
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY lot_date ASC";
} else if($param === 'new' && $order === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date DESC";
} else if($param === 'old' && $order === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY lot_date DESC";
} else if($param === 'new' && $publicationOrder === 'asc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at ASC";
} else if($param === 'new' && $publicationOrder === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at DESC";
}else if($param === 'old' && $publicationOrder === 'asc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY created_at ASC";
} else if($param === 'old' && $publicationOrder === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY created_at DESC";
} else if(isset($min_price) && isset($max_price)) {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        AND cur_price BETWEEN '$min_price' AND '$max_price'";
} else {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date ASC";
}

$result = mysqli_query($con, $query);
$rows = mysqli_fetch_all($result);
echo json_encode($rows);
