<?php
session_start();
require_once('../models/init.php');

// Проверка на наличие избранных лотов
if (!empty($_SESSION['favourite_lots']) && is_array($_SESSION['favourite_lots'])) {
    // Преобразование массивов в строку с ID
    $lot_ids = implode(',', array_map('intval', $_SESSION['favourite_lots']));

    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE lot.id IN ($lot_ids)";

    $lots_list = mysqli_query($con, $query);

    if ($lots_list) {
        $lots_data = [];
        while ($row = mysqli_fetch_assoc($lots_list)) {
            $lots_data[] = $row;
        }
        // Кодирование данных в JSON
        echo json_encode($lots_data);
    } else {
        // Обработка ошибки запроса
        echo json_encode(['error' => 'Ошибка выполнения запроса: ' . mysqli_error($con)]);
    }
} else {
    // Если список избранных лотов пуст или не массив
    echo json_encode([]);
}



