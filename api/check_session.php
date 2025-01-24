<?php
session_start();
require_once '../models/init.php';
echo json_encode($_SESSION['user']);
