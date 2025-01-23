<?php
session_start();
require_once 'models/init.php';
require_once 'models/functions.php';
require_once 'controllers/email.php';
$title = 'Главная страница';
$i = 0;

# -----------------ВСЕ КАТЕГОРИИ
$query2 = "SELECT * FROM category";
$categories_query = mysqli_query($con, $query2);

$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';
$nextOrder = $order === 'asc' ? 'desc' : 'asc';

$publicationOrder = isset($_GET['publicationOrder']) && $_GET['publicationOrder'] === 'asc' ? 'asc' : 'desc';
$nextPublicationOrder = $publicationOrder === 'desc' ? 'asc' : 'desc';

$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : $min_price;

//-------------ВЫВОД ОСНОВНЫХ ЛОТОВ

# Количество страниц - Открытые и Закрытые лоты
if($_GET['show'] === 'new' || isset($order) || isset($publicationOrder) || isset($min_price) && isset($max_price)) {
    $totalSql= "SELECT COUNT(*)
        FROM lot
        JOIN category ON lot.category_id = category.id
        WHERE lot_date > NOW();";
} else  if ($_GET['show'] === 'old') {
    # Количество страниц
    $totalSql= "SELECT COUNT(*)
        FROM lot
        JOIN category ON lot.category_id = category.id
        WHERE lot_date < NOW();";
} else {
    // Обработка случая, если 'show' имеет некорректное значение
    die('Invalid show parameter');
}

$result= mysqli_query($con, $totalSql);
if(!$result) {
    die('Ошибка выполнения запроса: ' . mysqli_error($con));
}

/////////////////////////ПАГИНАЦИЯ

//1. Установка количества записей на странице
$records_per_page = 12;

//2. Определение текущей страницы
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

//3. Определение смещения для SQL-запроса
$offset = ($page - 1) * $records_per_page;

//Извлечение результата - количество записей
$row = mysqli_fetch_array($result);
$total_records = $row[0];

//5. Вычисление общего количества страниц
$total_pages = ceil($total_records / $records_per_page);
//6. Запрос для получения данных с учетом пагинации

if($_GET['show'] === 'new' || $_GET['order'] === 'asc') {
# Сортировка лотов, начиная с тех, чей срок уже почти истек, до тех, у кого поздний срок.
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date ASC
        LIMIT ?, ?";
} else if($_GET['show'] === 'old') {
    # Закрытые лоты
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` < NOW()
        ORDER BY lot_date ASC
        LIMIT ?, ?";
} else if($_GET['order'] === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date DESC
        LIMIT ?, ?";
} else if($_GET['publicationOrder'] === 'asc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at ASC
        LIMIT ?, ?";
} else if($_GET['publicationOrder'] === 'desc') {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY created_at DESC
        LIMIT ?, ?";
}
else if(isset($_GET['min_price']) && isset($_GET['max_price'])) {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        AND cur_price BETWEEN '$min_price' AND '$max_price'
        LIMIT ?, ?";
}
else {
    $query = "SELECT lot.id, lot.name, lot_message, img_url, lot_rate, lot_date, lot_step, lot.price, cur_price, category.name AS category_name
        FROM `lot`
        JOIN category ON lot.category_id = category.id
        WHERE `lot_date` > NOW()
        ORDER BY lot_date ASC
        LIMIT ?, ?";
}

// Создание подготовленного выражения
$stmt  = $con->prepare($query);

// Связывание переменных с параметрами запроса
$stmt -> bind_param('ii', $offset, $records_per_page);

// Выполнение запроса
$stmt->execute();

$lots_list = $stmt->get_result();

if(!$lots_list) {
    die('Ошибка выполнения запроса: ' . mysqli_error($con));
}

$result2 = $lots_list;

if ($result2 && mysqli_num_rows($result2) > 0) {

    $search_array = mysqli_fetch_all($result2, MYSQLI_ASSOC);
    $page_content = include_template('index.php', [
        'categories_query' => $categories_query,
        'con' => $con,
        'page' => $page,
        'lots_list' => $lots_list,
        'totalPages' => $total_pages,
        'type_of_lots_to_show' => $_GET['show'],
        'search_array' => $search_array,
        'nextOrder' => $nextOrder,
        'order' => $order,
        'publicationOrder' => $publicationOrder,
        'nextPublicationOrder' => $nextPublicationOrder,
    ]);

} else {
//    echo "Ничего не найдено по вашему запросу!";
    $page_content = '<h1>Ничего не найдено по вашему запросу!</h1>';
}

$layout_content = include_template('layout.php', [
    'title' => $title,
    'content' => $page_content,
    'categories_query' => $categories_query,
    'con' => $con,
]);

print_r($layout_content);
