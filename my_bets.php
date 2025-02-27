<?php
session_start();

require_once 'models/init.php';
require_once 'controllers/get_last_user_id.php';

# Найти все мои ставки

$user_id = $_SESSION['user']['id'];

// SQL-запрос для получения самых последних ставок по дате по каждому лоту - у определенного юзера.
$sql = "
    SELECT
    t1.*,
    t1.price AS rate_price,
    lot.name AS lot_name,
    lot.id AS lot_id,
    lot_date, img_url,
    lot_message,
    category.name AS category_name,
    last_bid.user_id AS last_bid_user_id
FROM rate t1
JOIN (
    SELECT lot_id, MAX(rate_date) AS max_rate_date
    FROM rate
    WHERE user_id = ?
    GROUP BY lot_id
) t2 ON t1.lot_id = t2.lot_id AND t1.rate_date = t2.max_rate_date
JOIN lot ON t1.lot_id = lot.id
JOIN category ON lot.category_id = category.id
JOIN (
    SELECT r1.lot_id, r1.user_id
    FROM rate r1
    JOIN (
        SELECT lot_id, MAX(rate_date) AS max_rate_date
        FROM rate
        GROUP BY lot_id
    ) r2 ON r1.lot_id = r2.lot_id AND r1.rate_date = r2.max_rate_date
) last_bid ON t1.lot_id = last_bid.lot_id
WHERE t1.user_id = ?
ORDER BY lot.lot_date DESC;
";

$stmt = $con->prepare($sql);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}
$cur_time = time();
echo json_encode(['my_bets' => $rows]);



