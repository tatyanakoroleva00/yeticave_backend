<?php
session_start();
require_once 'models/functions.php';

$layout_content = include_template('react.php');
print_r($layout_content);
