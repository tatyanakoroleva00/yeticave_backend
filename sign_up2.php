<?php
session_start();
require_once 'controllers/searchUserByEmail.php';
require_once 'models/init.php';

$required = ['email', 'password', 'name', 'message'];
$errors = [];

//Проверка на наличие ошибок при заполнении формы

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        switch ($field) {
            case 'email':
                $errors[$field] = 'Введите email';
                break;
            case 'password':
                $errors[$field] = 'Введите пароль';
            case 'name':
                $errors[$field] = 'Введите имя';
            case 'message':
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


# Добавление картинки
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    // Получение MIME-типа файла
    $fileMimeType = mime_content_type($_FILES['avatar']['tmp_name']);
    //Проверка формата загруженного файла
    $allowedMimeTypes = ['image/jpeg','image/png',];


    if(in_array($fileMimeType, $allowedMimeTypes)) {
        $file_name = uniqid() . '-' . $_FILES['avatar']['name'];
        $file_path = __DIR__ . '/img/newLots/';
        $file_path_thumb = __DIR__ . '/img/thumbs/';
        $relative_file_url = '/img/newLots/' . $file_name;
        $relative_file_url_thumb = '/img/thumbs/' . 'thumb-' . $file_name;
        $uploadFile = $file_path . $file_name;

        if(!count($errors)) {
            # Перемещение файла
            if(move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                $_SESSION['uploaded_file'] = $relative_file_url;
                $_POST['avatar'] = $relative_file_url;

                // Создание миниатюры изображения
                $thumbnailWidth = 350; // Задайте нужную ширину миниатюры
                $thumbnailHeight = 150; // Задайте нужную высоту миниатюры
                $thumbnailPath = $file_path_thumb . 'thumb-' .$file_name ; // Путь для сохранения миниатюры

                $result = createThumbnail($uploadFile, $thumbnailPath, $thumbnailWidth, $thumbnailHeight, $fileMimeType);
                if($result) {
                    $_POST['thumb'] =  $relative_file_url_thumb;
                }

            } else {
                $errors['avatar'] = 'Ошибка загрузки файла.';
            }}
    } else {
        $errors['avatar'] = 'Файл не является допустимым изображением.';
    }
}

# Ошибки в форме.
if(count($errors)) {
    echo json_encode(['file' => $_FILES['avatar'], 'data' => $_POST, 'errors' => $errors]);
} else {
    echo json_encode(['file' => $_FILES['avatar'], 'data' => $_POST, 'errors' => $errors]);
}




////if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
////    // Получение MIME-типа файла
////    $fileMimeType = mime_content_type($_FILES['avatar']['tmp_name']);
////    //Проверка формата загруженного файла
////    $allowedMimeTypes = [
////        'image/jpeg',
////        'image/png',
////    ];
////
////    // Проверка, является ли MIME-тип допустимым
////    if (in_array($fileMimeType, $allowedMimeTypes)) {
//////            echo "Файл является допустимым изображением.";
////    } else {
//////            echo "Файл не является допустимым изображением.";
////        $errors['avatar'] = 'Файл не является допустимым изображением.';
////    }
////
////    // Занимаемся перемещением файла
////    $file_name = $_FILES['avatar']['name'];
////    $file_path = __DIR__ . '/img/avatars/';
////    $file_url = '/img/avatars/' . $file_name;
////    $tmp_name = $_FILES['avatar']['tmp_name'];
////
////    if (!count($errors)) {
////        move_uploaded_file($tmp_name, $file_path . $file_name);
////        $_POST['img_url'] = "$file_url";
////    }
////} else {
////        echo "Файл не был загружен.";
////}
//
////echo json_encode(['errors' => $errors]);
////var_dump($_FILES);
////exit();
//
//
////Если после проверок нет ошибок, то показываем определенную страницу
//if (!count($errors)) {
////        $email = $_POST['email'];
////        $password = $_POST['password'];
////        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
////        $name = $_POST['name'];
////        $message = $_POST['message'];
////        $img_url = '';
////
////        if(isset($_POST['img_url'])) {
////        $img_url = $_POST['img_url'];
////        } else {
////            $img_url = '/img/avatars/avatar.jpg';
////        }
////
////        $sql = "INSERT INTO `users` SET `email` = ?, `password` = ?, `name` = ?, `contacts` = ?, `avatar` = ?";
////        $stmt = $con->prepare($sql);
////        $stmt->bind_param('sssss', $email, $hashed_password, $name, $message, $img_url);
////        $stmt->execute();
////
////        $result = $stmt -> get_result();
////
////        echo json_encode(['status' => 'success', 'message' => '*Теперь вы можете войти, используя свой email и пароль']);
//
////        $page_content = include_template('login.php', [
////            'login_page' => $login_page,
////            'message' => '*Теперь вы можете войти, используя свой email и пароль',
////        ]);
//} //Если после проверок есть ошибки, то остаемся на текущей странице
//else {
////    echo json_encode(['status' => 'error', 'errors' => $errors]);
////        $page_content = include_template('sign_up.php', [
////            'login_page' => $login_page,
////            'errors' => $errors,
////        ]);
//}
////} else {
////    $page_content = include_template('sign_up.php', [
////        'login_page' => $login_page,
////    ]);
////}
////
////$layout_content = include_template('layout.php', [
////    'content' => $page_content,
////    'title' => $title,
////    'categories' => $categories,
////]);
////print($layout_content);
//
