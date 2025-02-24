<?php
session_start();
require_once 'models/functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$layout_content = include_template('react.php');
print_r($layout_content);
