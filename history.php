<?php
session_start();
require_once 'models/functions.php';
require_once 'models/categories.php';
require_once 'models/init.php';

$title = 'Избранное';
$page_content = '';

if(isset($_SESSION['favourite_lots'])) {
# Создаем String с ID-шниками лотов для SQL-запроса
    $lot_ids = implode(',', array_map('intval', $_SESSION['favourite_lots']));

    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE lot.id IN ($lot_ids)";

    $lots_list = mysqli_query($con, $query);

    $page_content = include_template('history.php', [
        'lots_list' => $lots_list
    ]);
} else {
    $page_content = '<h1>Нет просмотренных лотов</h1>';
}
    $layout_content = include_template('layout.php', [
        'title' => $title,
        'content' => $page_content,
        'categories' => $categories
    ]);

    print_r($layout_content);





