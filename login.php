<?php
session_start();
//header("Access-Control-Allow-Origin: http://localhost:5173");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'models/init.php';
require_once 'models/functions.php';
require_once 'controllers/searchUserByEmail.php';


# Получение данных
$form = json_decode(file_get_contents('php://input'), true);
$required = ['email', 'password'];
$errors = [];

# Аутентификация
$query = "SELECT * from `users`";
$result = mysqli_query($con, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

$email = htmlspecialchars(trim($form['email'])) ?? '';
$password = htmlspecialchars(trim($form['password'])) ?? '';

# Проверка на пустоту
if(empty($email)) {
    $errors['email'] = "Пожалуйста, заполните поле email.";
}
if (empty($password)) {
    $errors['password'] = "Пожалуйста, заполните поле пароль.";
}
if(!empty($email) && !empty($password)) {
# Если нет ошибок и пользователь найден в БД по мейлу
    if ($user = searchUserByEmail($form['email'], $users)) {

        # Если пароль совпадает с паролем в БД
        $passwordFromDB = $user['password'];

        if (password_verify($form['password'], $passwordFromDB)) {
            $_SESSION['user'] = $user;
//            var_dump($_SESSION['user']);
        } else { # Если пароль не совпадает с паролем в БД
            $errors['password'] = 'Неверный пароль';
            $errors['message'] = 'Вы ввели неверный email/пароль';
        }
    } else { # Если такого пользователя нет или есть ошибки
        $errors['email'] = 'Такой пользователь не найден';
        $errors['message'] = 'Пожалуйста, исправьте ошибки в форме.';
    }
}
# Если есть ошибки
if (count($errors)) {
    $errors['message'] = 'Пожалуйста, исправьте ошибки в форме.';
    echo json_encode(['status' => 'error', 'errors' => $errors]);
}  else {
    if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
        setcookie('email', $email, time() + (30 * 24 * 60 * 60), "/"); # cookie хранится 30 дней
    }
    echo json_encode(['status' => 'success', 'user' => $_SESSION['user']]);
}
