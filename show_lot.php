<?php
session_start();
require_once 'models/functions.php';
require_once 'models/categories.php';
require_once 'models/init.php';
require_once 'vendor/autoload.php';

if (isset($_GET['id'])) {
    $lot_id = $_GET['id'];

#1 --ДОБАВЛЕНИЕ ЛОТОВ В FAVOURITES ДЛЯ ПОКАЗА НА СТРАНИЦЕ HISTORY.PHP.

#Проверяем, существует ли массив избранных лотов в сессии
    if (!isset($_SESSION['favourite_lots'])) {
        $_SESSION['favourite_lots'] = [];
    }

#Добавляем текущий лот в массив избранных, если его там еще нет
    if (!in_array($lot_id, $_SESSION['favourite_lots'])) {
        $_SESSION['favourite_lots'][] = $lot_id;
    }

#2 -- ВЫВОД ЛОТА НА СТРАНИЦЕ

#Делаем запрос в БД, ищем лот по id. Соединяем две таблицы вместе по id.
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
       FROM `lot`
       JOIN category ON lot.category_id = category.id
       WHERE lot.id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $lot_id);
    $stmt->execute();

    $chosen_lot = $stmt->get_result();

    if ($chosen_lot && mysqli_num_rows($chosen_lot) > 0) {
        foreach ($chosen_lot as $row => $elem) {
            $lot_name = $elem['name'];
            $lot_message = $elem['lot_message'];
            $img_url = $elem['img_url'];
            $lot_rate = $elem['lot_rate'];
            $lot_date = $elem['lot_date'];
            $lot_step = $elem['lot_step'];
            $price = $elem['price'];
            $cur_price = $elem['cur_price'];
            $category_name = $elem['category_name'];
        }

# Делаем запрос по айди лота к БД, находим лот, его стоимость и мин.ставку. Меняем цену на лот
        if (isset($_POST['lot_rate'])) {

            $lot_rate = $_POST['lot_rate'];

            $errors = [];

            $minimal_possible_rate = $cur_price + $lot_step;
            if ($lot_rate > $minimal_possible_rate || $lot_rate == $minimal_possible_rate) {
                $query = "SELECT lot_rate, cur_price from lot WHERE id = '$lot_id'";
                $result = mysqli_query($con, $query);
                $cur_price = $lot_rate;

                $user_id = $_SESSION['user']['id'];

                if ($result && mysqli_num_rows($result) > 0 && ($lot_rate > $lot_step || $lot_rate == $lot_step)) {
                    $row = mysqli_fetch_assoc($result);
                    $data = json_decode($row['lot_rate'], true);

# Добавление нового значения
                    if (!is_array($data)) {
                        $data = [];
                    }
                    $data[] = $lot_rate;

# Обновление данных по лоту в БД
                    $json_data = mysqli_real_escape_string($con, json_encode($data));
                    $query2 = "UPDATE lot SET lot_rate = '$json_data', cur_price = '$cur_price' WHERE id = '$lot_id'";
                    $result2 = mysqli_query($con, $query2);

# Добавление данных в таблицу rate
                    $query3 = "INSERT INTO rate (lot_id, price, user_id) VALUES ('$lot_id', '$lot_rate', '$user_id')";

                    if (mysqli_query($con, $query3)) {
                        header("Location: " . $_SERVER['REQUEST_URI']);
                        exit;
                    } else {
//                        echo "Ошибка обновления: " . mysqli_error($con);
                    }
                }
            } else {
                $errors['rate'] = 'Ваша ставка должна представлять собой "текущую стоимость" + "ваша сумма" при учете минимального шага';
            }
        }

# Ищем контактные данные разместившего лот
        if (isset($_GET)) {
            $current_id = $_GET['id'];

            $query_search_user = "SELECT lot.id, user_id, users.id, users.name, users.contacts
           FROM lot
           JOIN users ON lot.user_id = users.id
           WHERE lot.id = ? ";

            $stmt2 = $con->prepare($query_search_user);
            $stmt2->bind_param('i', $current_id);
            $stmt2->execute();

            $result = $stmt2->get_result();
            $row = mysqli_fetch_assoc($result);
        }

# Количество ставок, сделанных по лоту
        $sql_count_rates = "SELECT COUNT(*) as count
                   FROM rate
                   WHERE lot_id = ?;";

        $stmt3 = $con->prepare($sql_count_rates);
        $stmt3->bind_param('i', $lot_id);
        $stmt3->execute();

        $count_rates = $stmt3->get_result();

        if ($count_rates) {
            $count_rows = mysqli_fetch_assoc($count_rates);
            $rates_number = $count_rows['count'];
        } else {
            $rates_number = 0;
        }

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


        $page_content = include_template('lot.php', [
            'chosen_lot' => $chosen_lot,
            'lot_name' => $lot_name,
            'lot_message' => $lot_message,
            'img_url' => $img_url,
            'lot_date' => $lot_date,
            'lot_rate' => $_POST['lot_rate'],
            'lot_step' => $lot_step,
            'price' => $price,
            'cur_price' => $cur_price,
            'category_name' => $category_name,
            'lot_id' => $lot_id,
            'con' => $con,
            'humanReadableTimeDifference' => $humanReadableTimeDifference,
            'user_name' => $row['name'],
            'contacts' => $row['contacts'],
            'user_id' => $row['user_id'],
            'rates_number' => $rates_number,
            'errors' => $errors['rate'],
            'result5' => $result5,
        ]);

    } # Ошибка добавления лота
    else {
        http_response_code(404);
        $page_content = '<h1>Ошибка 404: Страница не найдена</h1>';
    }
} else {
    $page_content = '<h1>Вы не выбрали лот.</h1>';
}
$layout_content = include_template('layout.php', [
    'title' => 'Лот',
    'content' => $page_content,
    'categories' => $categories,
]);

print_r($layout_content);


