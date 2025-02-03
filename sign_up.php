<?php
session_start();
require_once 'models/functions.php';
require_once 'controllers/searchUserByEmail.php';
require_once 'models/categories.php';
require_once 'models/init.php';

//header("Access-Control-Allow-Origin: http://localhost:5173");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");

$title = 'Регистрация';
$login_page = 'login.php';

//$form = json_decode(file_get_contents('php://input'), true);

$required = ['email', 'password', 'name', 'message'];
$errors = [];

//Проверка на наличие ошибок при заполнении формы
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        if ($field === 'email') {
            $errors[$field] = 'Введите email';
        }
        if ($field === 'password') {
            $errors[$field] = 'Введите пароль';
        }
        if ($field === 'name') {
            $errors[$field] = 'Введите имя';
        }
        if ($field === 'message') {
            $errors[$field] = 'Напишите как с вами связаться';
        }
        //Проверка на то, что почта неправильно написана (не по формату почты)
    } else {
        if ($field === 'email') {
            if (filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
//                    echo "Адрес электронной почты '$field' является допустимым.";
            } else {
//                    echo "Адрес электронной почты '$field' является недопустимым.";
                $errors['email'] = "Адрес электронной почты '$field' является недопустимым.";
            }
        }
    }
}

//Аутентификация

//Проверка по БД на наличие такого пользователя по адресу email
$query = "SELECT * from `users`";
$result = mysqli_query($con, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Если нет ошибок и пользователь найден в БД по мейлу
if ($user = searchUserByEmail($_POST['email'], $users)) {
    $errors['email'] = 'Такой пользователь уже существует';
}
//Картинка. Если есть картинка и нет ошибок - помещаем в папку.
//if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
//    // Получение MIME-типа файла
//    $fileMimeType = mime_content_type($_FILES['avatar']['tmp_name']);
//    //Проверка формата загруженного файла
//    $allowedMimeTypes = [
//        'image/jpeg',
//        'image/png',
//    ];
//
//    // Проверка, является ли MIME-тип допустимым
//    if (in_array($fileMimeType, $allowedMimeTypes)) {
////            echo "Файл является допустимым изображением.";
//    } else {
////            echo "Файл не является допустимым изображением.";
//        $errors['avatar'] = 'Файл не является допустимым изображением.';
//    }
//
//    // Занимаемся перемещением файла
//    $file_name = $_FILES['avatar']['name'];
//    $file_path = __DIR__ . '/img/avatars/';
//    $file_url = '/img/avatars/' . $file_name;
//    $tmp_name = $_FILES['avatar']['tmp_name'];
//
//    if (!count($errors)) {
//        move_uploaded_file($tmp_name, $file_path . $file_name);
//        $_POST['img_url'] = "$file_url";
//    }
//} else {
//        echo "Файл не был загружен.";
//}

//echo json_encode(['errors' => $errors]);
//var_dump($_FILES);
//exit();


//Если после проверок нет ошибок, то показываем определенную страницу
    if (!count($errors)) {
//        $email = $_POST['email'];
//        $password = $_POST['password'];
//        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
//        $name = $_POST['name'];
//        $message = $_POST['message'];
//        $img_url = '';
//
//        if(isset($_POST['img_url'])) {
//        $img_url = $_POST['img_url'];
//        } else {
//            $img_url = '/img/avatars/avatar.jpg';
//        }
//
//        $sql = "INSERT INTO `users` SET `email` = ?, `password` = ?, `name` = ?, `contacts` = ?, `avatar` = ?";
//        $stmt = $con->prepare($sql);
//        $stmt->bind_param('sssss', $email, $hashed_password, $name, $message, $img_url);
//        $stmt->execute();
//
//        $result = $stmt -> get_result();
//
//        echo json_encode(['status' => 'success', 'message' => '*Теперь вы можете войти, используя свой email и пароль']);

//        $page_content = include_template('login.php', [
//            'login_page' => $login_page,
//            'message' => '*Теперь вы можете войти, используя свой email и пароль',
//        ]);
    } //Если после проверок есть ошибки, то остаемся на текущей странице
    else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
//        $page_content = include_template('sign_up.php', [
//            'login_page' => $login_page,
//            'errors' => $errors,
//        ]);
    }
//} else {
//    $page_content = include_template('sign_up.php', [
//        'login_page' => $login_page,
//    ]);
//}
//
//$layout_content = include_template('layout.php', [
//    'content' => $page_content,
//    'title' => $title,
//    'categories' => $categories,
//]);
//print($layout_content);

