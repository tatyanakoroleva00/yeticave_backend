<?php
require_once 'models/init.php';
require_once 'models/functions.php';

// Получение текущего времени
$current_time = date('Y-m-d H:i:s');

// Сортировка по самой поздней дате ставки, группировка по лоту, и где победитель лота еще не оповещен
$query = "
SELECT  rate.rate_date, lot.id AS lot_id, lot.user_id, users.email, lot_date, notified, rate.user_id
FROM lot
JOIN rate ON lot.id = rate.lot_id
JOIN users ON rate.user_id = users.id
WHERE rate.rate_date = (
    SELECT MAX(r.rate_date)
    FROM rate r
    WHERE r.lot_id = lot.id
    )
AND notified IS NULL AND lot_date <= ?
ORDER BY rate.rate_date DESC
;";


// Подготовка и выполнение подготовленного запроса
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $current_time);
mysqli_stmt_execute($stmt);

// Получение результата
$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($result)) {
    $to = $row['email'];
    $subject = 'Поздравления!';
    $message = "Вы победили. Выйгравший лот " . $row['lot_id'];

    if(mail($to, $subject, $message)) {
        // Обновление статуса уведомления в БД
        $update_query = "UPDATE lot SET lot.notified = 1 WHERE id = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, 'i', $row['lot_id']);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
    }
}

mysqli_stmt_close($stmt);
