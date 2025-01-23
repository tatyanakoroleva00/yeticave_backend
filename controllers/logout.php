<?php
session_start();

$_SESSION = [];
session_destroy();
setcookie('email', '', time() - 3600, '/');  // Устанавливаем в прошлое для удаления
header('Location: /');
