<?php
session_start();
require_once 'models/init.php';

//$searchQuery = $_GET['search'] ?? '';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data !== null && isset($data['query'])) {
    $query = $data['query'];
} else {
    echo json_encode(['error' => 'Invalid input']);
}


if (isset($query)) {
    $searchQuery = $query;
    // Обрабатываем строку для безопасного вывода
    $searchQuery = htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');
    $searchQuery = trim($searchQuery);
    // Удаляем все пробелы из строки
    $searchQuery = str_replace(' ', '', $searchQuery);
    $searchTerm = '%' . $searchQuery . '%';

    //ПАГИНАЦИЯ

    //1. Установка количества записей на странице
    $records_per_page = 9;

    //2. Определение текущей страницы
    $page = isset($data['currentPage']) ? (int)$data['currentPage'] : 1;

    //3. Определение смещения для SQL-запроса
    $offset = ($page - 1) * $records_per_page;

    //4. Получение общего количества записей
    $total_sql = "
        SELECT COUNT(*)
        FROM lot
        WHERE (name LIKE ? OR lot_message LIKE ?);";

    $stmt = $con->prepare($total_sql);
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = mysqli_fetch_array($result);
    $total_records = $row[0];

    //5. Вычисление общего количества страниц
    $total_pages = ceil($total_records / $records_per_page);

    //6. Запрос для получения данных с учетом пагинации

    $sql = "SELECT *
            FROM lot
            WHERE (name LIKE ? OR lot_message LIKE ?)
            ORDER BY lot_date ASC
            LIMIT ?, ?;";

    $stmt2 = $con->prepare($sql);
    $stmt2->bind_param('ssii', $searchTerm, $searchTerm, $offset, $records_per_page);
    $stmt2->execute();

    $result2 = $stmt2->get_result();

    if ($result2 && mysqli_num_rows($result2) > 0) {

        $search_array = mysqli_fetch_all($result2, MYSQLI_ASSOC);

        echo json_encode(['search_array' => $search_array, 'page' => $page, 'totalPages' => $total_pages]);

    } else {
        echo json_encode(['message' => 'Ничего не найдено по вашему запросу!']);
        }

}
