<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Credentials: true');
header("Content-Type: application/json");

session_start();

//var_dump([$_COOKIE, $_SESSION, $_SERVER]);

if(isset($_SESSION['user'])) {
    echo json_encode($_SESSION['user']);
} else {
    echo json_encode(['user' => false]);
}
//403 http code
