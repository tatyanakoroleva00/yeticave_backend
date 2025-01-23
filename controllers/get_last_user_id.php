<?php
# Получить айди последнего юзера, поставившего ставку
function getLastUserId($con, $lot_id) {
    $sql = "
        SELECT *, rate.user_id
        FROM rate
        WHERE rate.lot_id = ?
        ORDER BY rate_date DESC;
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $lot_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return mysqli_fetch_assoc($result);
}
