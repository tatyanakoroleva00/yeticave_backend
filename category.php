<?php
session_start();
require_once 'models/functions.php';
require_once 'models/categories.php';
require_once 'models/init.php';
require_once 'vendor/autoload.php';

if(isset($_GET['category'])) {
    $category = $_GET['category']; #English

    $sql = "SELECT name FROM category WHERE name_eng = ?;";
    $stmt = $con->prepare($sql);
    $stmt ->bind_param('s', $category);
    $stmt->execute();
    $query = $stmt->get_result();

    if (mysqli_num_rows($query) > 0) {
        $category_name = mysqli_fetch_assoc($query);
        $category_name = $category_name['name'];
        $title = "Все лоты в категории " . '"' . "$category_name" . '"';
}

    //ПАГИНАЦИЯ

    //1. Установка количества записей на странице
    $records_per_page = 9;

    //2. Определение текущей страницы
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    //3. Определение смещения для SQL-запроса
    $offset = ($page - 1) * $records_per_page;

    //4. Получение общего количества записей
    $total_sql = "
        SELECT COUNT(*)
        FROM lot
        JOIN category ON lot.category_id = category.id
        WHERE category.name = ?";

    $stmt2 = $con->prepare($total_sql);
    $stmt2->bind_param('s', $category_name);
    $stmt2->execute();
    $result = $stmt2->get_result();

    $row = mysqli_fetch_array($result);
    $total_records = $row[0];

    //5. Вычисление общего количества страниц
    $total_pages = ceil($total_records / $records_per_page);

    //6. Запрос для получения данных с учетом пагинации

    $lots = "SELECT *, lot.name AS lot_name, lot.id AS lot_id, lot.cur_price AS lot_cur_price
    FROM lot
    JOIN category ON lot.category_id = category.id
    WHERE category.name = ?
    ORDER BY lot.created_at DESC
    LIMIT ?, ?;";

    $stmt3 = $con->prepare($lots);
    $stmt3 -> bind_param('sii', $category_name, $offset, $records_per_page);
    $stmt3->execute();
    $query2 = $stmt3->get_result();

    if ($query2 && mysqli_num_rows($query2) > 0) {

        $search_array = mysqli_fetch_all($query2, MYSQLI_ASSOC);

        $page_content = include_template('category.php', [
            'title' => $title,
            'lots_list' => $query2,
            'search_array' => $search_array,
            'page' => $page,
            'totalPages' => $total_pages,
            'category' => $category,
            'con' => $con,
        ]);
    }
    else {
        $page_content = '<h1>Нет товаров в данной категории</h1>';
    }
} else {
    $page_content = '<h1>Категория не выбрана</h1>';
}
$layout_content = include_template('layout.php', [
    'title' => $title,
    'content' => $page_content,
    'categories' => $categories,
]);

print_r($layout_content);


