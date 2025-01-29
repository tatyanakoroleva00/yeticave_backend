<?php
session_start();
require_once '../models/init.php';

$data = json_decode(file_get_contents('php://input'), true);
$param = $data['show'];
$order = $data['order'];
$publicationOrder = $data['publicationOrder'];
$min_price = (float)$data['min_price'];
$max_price = (float)$data['max_price'];


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
    # Открытые лоты, сортировка по дате истечения срока лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date DESC";
} else if($param === 'old' && $order === 'desc') {
    # Закрытые лоты, сортировка по дате истечения срока лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY lot_date DESC";
} else if($param === 'new' && $publicationOrder === 'asc') {
    # Открытые лоты, сортировка по дате размещения лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at ASC";
} else if($param === 'new' && $publicationOrder === 'desc') {
    # Открытые лоты, сортировка по дате размещения лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at DESC";
}else if($param === 'old' && $publicationOrder === 'asc') {
    # Закрытые лоты, сортировка по дате размещения лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY created_at ASC";
} else if($param === 'old' && $publicationOrder === 'desc') {
    # Закрытые лоты, сортировка по дате размещения лота
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY created_at DESC";
} else if(isset($min_price) && isset($max_price) && $param === 'new') {
    # Открытые лоты, фильтрация по цене
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        AND cur_price BETWEEN '$min_price' AND '$max_price'
        ORDER BY cur_price ASC";
}
else if(isset($min_price) && isset($max_price) && $param === 'old') {
    # Закрытые лоты, фильтрация по цене
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        AND cur_price BETWEEN '$min_price' AND '$max_price'
        ORDER BY cur_price ASC";
}
$result = mysqli_query($con, $query);
$rows = mysqli_fetch_all($result);
echo json_encode($rows);



