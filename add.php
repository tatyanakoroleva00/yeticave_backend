<?php
session_start();
require_once 'models/init.php';
require_once 'models/functions.php';
require_once 'models/categories.php';
require_once 'vendor/autoload.php';
require_once 'controllers/remove_file.php';
$title = 'Добавить лот';
$errors = [];

/*1*/
# ЕСЛИ ПОЛЬЗОВАТЕЛЬ НЕ АВТОРИЗОВАН, запретить доступ к странице с добавление лота
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Доступ запрещен";
}
else {
    /*2*/
    # ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН и отправка нового лота не совершена
    if (empty($_POST)) {
        $page_content = include_template('add.php', []);
    } else {
        $required = ['lot_name', 'category', 'lot_message', 'img_url', 'cur_price', 'lot_step', 'lot_date'];
        $dict = [
            'lot_name' => 'Название лота',
            'category' => 'Категория',
            'lot_message' => 'Описание лота',
            'img_url' => 'Изображение',
            'cur_price' => 'Начальная цена',
            'lot_step' => 'Ставка',
            'lot_date' => 'Дата завершения торгов'];

        /*3*/
        # Проверка на наличие пустых полей - и где конкретно.
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
//                        $errors[$key] = 'stop';
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
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Получение MIME-типа файла
            $fileMimeType = mime_content_type($_FILES['image']['tmp_name']);
            //Проверка формата загруженного файла
            $allowedMimeTypes = ['image/jpeg','image/png',];


            if(in_array($fileMimeType, $allowedMimeTypes)) {
                $file_name = uniqid() . '-' . $_FILES['image']['name'];
                $file_path = __DIR__ . '/img/lots/';
                $relative_file_url = '/img/lots/' . $file_name;
                $uploadFile = $file_path . $file_name;

                if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $_SESSION['uploaded_file'] = $relative_file_url;
                    $_POST['img_url'] = $relative_file_url;
                } else {
                    $errors['image'] = 'Ошибка загрузки файла.';
                }
            } else {
                $errors['image'] = 'Файл не является допустимым изображением.';
            }
        } else {
            if(!isset($_SESSION['uploaded_file'])) {
                $errors['image'] = "Файл не был загружен.";
            }
        }

        # Ошибки в форме.
        if (count($errors)) {
            $page_content = include_template('add.php', [
                'errors' => $errors,
                'lot' => $_POST,
            ]);
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


            // Преобразование даты из формата 11.11.2024 в формат 2024-11-11 для БД
            $originalDate = $_POST['lot_date'];
            $lot_date = $_POST['lot_date'];

            // Поиск айди пользователя
            $user_id = $_SESSION['user']['id'];

            // Поиск по категории номера из таблицы "category", чтобы подставить в новый lot в БД.
            //$query1 = "SELECT id FROM category WHERE LOWER(category.name) = LOWER('$category')";

            $query1 = "SELECT id FROM category WHERE LOWER(category.name) = LOWER(?)";

            $stmt  = $con->prepare($query1);
            $stmt-> bind_param('s', $category);
            $stmt->execute();
            $result1 = $stmt->get_result();

            if ($result1 && mysqli_num_rows($result1) > 0) {
                $row = mysqli_fetch_assoc($result1);
                $category_id = $row['id'];

                $query2 = "INSERT into `lot` SET `name` = '$name', `lot_message` = '$lot_message', `img_url` = '$img_url',
                `lot_step` = '$lot_step', `category_id` = '$category_id', `price` = '$price', `lot_date` = '$lot_date', `lot_rate` = 0, `cur_price` = '$price',
                `user_id` = '$user_id', `notified` =  NULL";

                // SQL-запрос для получения последнего добавленного товара
                $sql = "SELECT id FROM lot ORDER BY id DESC LIMIT 1"; // Предполагается, что таблица называется 'products' и у нее есть поле 'id'

                if (mysqli_query($con, $query2)) {
                    echo 'Лот успешно добавлен!';
                    // Получение ID последней вставленной записи
                    $last_id = mysqli_insert_id($con);
                    $name = $name . ' #' . $last_id;

                    $query3 = "UPDATE lot
                    SET name = ?
                    WHERE lot.id = ?;";

                    $stmt3 = $con->prepare($query3);
                    $stmt3->bind_param('si', $name, $last_id);
                    $stmt3->execute();

                    // Переадресация на страницу с созданным лотом
                    header("Location: show_lot.php?id=" . $last_id);
                    unset($_SESSION['uploaded_file']);
                    exit();
                } else {
                    echo "Ошибка добавления лота: " . mysqli_error($con);
                }
            }
        }
        // Обработка успешной формы
        // Удалите временный файл из сессии, если больше не нужен

    }

    $layout_content = include_template('layout.php', [
        'title' => $title,
        'content' => $page_content,
        'categories' => $categories
    ]);

    print_r($layout_content);
}
