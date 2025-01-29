<?php
session_start();
require_once 'models/init.php';

$errors = [];
$required = ['lot_name', 'category', 'lot_message', 'img_url', 'cur_price', 'lot_step', 'lot_date'];
$dict = [
    'lot_name' => 'Название лота',
    'category' => 'Категория',
    'lot_message' => 'Описание лота',
    'img_url' => 'Изображение',
    'cur_price' => 'Начальная цена',
    'lot_step' => 'Ставка',
    'lot_date' => 'Дата завершения торгов'];

header('Content-Type: application/json');
//echo json_encode(['data' => $_POST, 'errors' => $errors]);


# Проверка на наличие пустых полей - и где конкретно
foreach ($_POST as $key => $value) {
    if (in_array($key, $required)) {
        if (!$value) {
            $errors[$key] = 'Это поле надо заполнить!';
        } else {
            if ($key === 'cur_price') {
                if (!(filter_var($value, FILTER_VALIDATE_INT) !== false) && !($value > 0)) {
                    $errors[$key] = "Ошибка: пожалуйста, введите целое число.";
                }
            };
            if ($key === 'lot_step') {
                if (!(filter_var($value, FILTER_VALIDATE_INT) !== false) && !(+$value > 0)) {
                    $errors[$key] = "Ошибка: пожалуйста, введите целое число.";
                }
            };
            if ($key === 'lot_date') {

                # Установите нужный часовой пояс
                date_default_timezone_set('Europe/Moscow');

                # Получаем текущую дату.
                $currentDateStr = date('Y-m-d');

                # Вычисляем дату следующего дня после текущей даты.
                $nextDayTimestamp = strtotime($currentDateStr . ' +1 day');

                # Преобразуем строку даты в метку времени
                $timestamp = strtotime($value);

                # Сверяем даты
                if ($timestamp < $nextDayTimestamp) {
                    $errors[$key] = 'Дата должна быть больше текущей даты хотя бы на один день';
                }
            }
        }
    }
}


# Добавление картинки
if (isset($_FILES['img_url']) && $_FILES['img_url']['error'] === UPLOAD_ERR_OK) {
    // Получение MIME-типа файла
    $fileMimeType = mime_content_type($_FILES['img_url']['tmp_name']);
    //Проверка формата загруженного файла
    $allowedMimeTypes = ['image/jpeg','image/png',];


    if(in_array($fileMimeType, $allowedMimeTypes)) {
        $file_name = uniqid() . '-' . $_FILES['img_url']['name'];
        $file_path = __DIR__ . '/img/lots/';
        $relative_file_url = '/img/lots/' . $file_name;
        $uploadFile = $file_path . $file_name;

        if(move_uploaded_file($_FILES['img_url']['tmp_name'], $uploadFile)) {
            $_SESSION['uploaded_file'] = $relative_file_url;
            $_POST['img_url'] = $relative_file_url;
        } else {
            $errors['img_url'] = 'Ошибка загрузки файла.';
        }
    } else {
        $errors['img_url'] = 'Файл не является допустимым изображением.';
    }
} else {
    if(!isset($_SESSION['uploaded_file'])) {
        $errors['img_url'] = "Файл не был загружен.";
    }
}

# Ошибки в форме.

if(count($errors)) {
    echo json_encode(['file' => $_FILES['img_url'], 'data' => $_POST, 'errors' => $errors]);
} else {
    // При изначальном добавлении лота $cur_price = $price у меня
    $formatted_cur_price = formattedPrice($_POST['cur_price']); //Отформатированная цена для публикации на странице
    $formatted_price = formattedPrice($_POST['cur_price']); //Отформатированная цена для публикации на странице

    $name = $_POST['lot_name'];
    $lot_message = $_POST['lot_message'];
    $img_url = $_SESSION['uploaded_file'];
    $lot_step = $_POST['lot_step'];
    $category = $_POST['category'];
    $cur_price = $_POST['cur_price'];
    $price = $_POST['cur_price'];
}


