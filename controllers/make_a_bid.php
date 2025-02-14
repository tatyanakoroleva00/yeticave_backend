<?php
session_start();
require_once('../models/init.php');

// Порядок действий:
// 1. Проверка ставки - удовлетворяет ли она условиям
// 2. Меняем в таблице "ставки" rate
// 3. Меняем в таблице "lot" - ставку добавляем в массив ставок, меняем текущую цену на ставку пользователя

$_POST = json_decode(file_get_contents('php://input'), true);

$user_id = (int)$_SESSION['user']['id'];
$user_rate = (int)$_POST['user_rate'];
$lot_id = (int)$_POST['lot_id'];
$cur_price = (int)$_POST['cur_price'];
$lot_step = (int)$_POST['lot_step'];

$errors = [];


if (isset($cur_price) && isset($lot_step) && isset($user_rate)) {
    $minimal_possible_rate = $cur_price + $lot_step;
    if ($user_rate > $minimal_possible_rate || $user_rate == $minimal_possible_rate) {

        // QUERY 1. Добавляем ставку в таблицу rate
        $query1 = "INSERT INTO rate (lot_id, price, user_id)
        VALUES (?,?,?);";
        $stmt1 = $con->prepare($query1);

        if (!$stmt1) {
            die ('Ошибка подготовки запроса.');
        }
        $stmt1->bind_param("iii", $lot_id, $user_rate, $user_id);
        $result1 = $stmt1->execute();

        // QUERY 2. Добавляем новую стоимость в таблицу lot
        $query2 = "UPDATE lot SET cur_price = '$user_rate' WHERE id = '$lot_id'";
        $result2 = mysqli_query($con, $query2);

    } else {
        $errors['rate'] = 'Ваша ставка должна представлять собой "текущую стоимость" + "ваша сумма" при учете минимального шага';
        echo json_encode(['errors' => $errors]);
    }
}
