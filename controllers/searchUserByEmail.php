<?php
//require_once 'vendor/autoload.php';
session_start();

function searchUserByEmail($email, $users) {
    $result = null;
    foreach($users as $user) {
        if($user['email'] == $email) {
            $result = $user;
            break;
        }
    }
    return $result;
}
