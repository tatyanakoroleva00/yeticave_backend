<?php
session_start();
require_once 'models/functions.php';
require_once 'controllers/searchUserByEmail.php';
require_once 'models/categories.php';
require_once 'models/init.php';
require_once 'vendor/autoload.php';
$title = 'Вход';

//Проверка на получение данных из формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];
    $errors = [];

    //Аутентификация

    $query = "SELECT * from `users`";
    $result = mysqli_query($con, $query);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Проверка на пустые значения
    if (empty($email)) {
        $errors['email'] = "Пожалуйста, заполните поле email.";
        $errors['message'] = 'Пожалуйста, исправьте ошибки в форме.';
    }

    if (empty($password)) {
        $errors['password'] = "Пожалуйста, заполните поле пароль.";
        $errors['message'] = 'Пожалуйста, исправьте ошибки в форме.';
    }

    if(!empty($email) && !empty($password)) {
        //Если нет ошибок и пользователь найден в БД по мейлу
        if ($user = searchUserByEmail($form['email'], $users)) {

            //Если пароль совпадает с паролем в БД
            $passwordFromDB = $user['password'];

            if (password_verify($form['password'], $passwordFromDB)) {
                $_SESSION['user'] = $user;

            } //Если пароль не совпадает с паролем в БД
            else {
                $errors['password'] = 'Неверный пароль';
                $errors['message'] = 'Вы ввели неверный email/пароль';
            }
        } //Если такого пользователя нет или есть ошибки
        else {
            $errors['email'] = 'Такой пользователь не найден';
            $errors['message'] = 'Пожалуйста, исправьте ошибки в форме.';
        }
    }
        //Если есть ошибки
        if (count($errors)) {
            $page_content = include_template('login.php', [
                'errors' => $errors,
                'form' => $form,
            ]);
        } //Если нет ошибок, переадресация
        else {
            if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                setcookie('email', $email, time() + (30 * 24 * 60 * 60), "/"); // cookie хранится 30 дней
            }
            header("Location: /index.php");
            exit();
        }

} // Если нет запроса POST
else {
    $page_content = include_template('login.php', []);
}

//Показываем страницу
$layout_content = include_template('layout.php', [
    'title' => $title,
    'content' => $page_content,
    'categories' => $categories,
]);

print_r($layout_content);
