<?php
session_start();

if(isset($_SESSION['uploaded_file'])) {
    unset($_SESSION['uploaded_file']);
}
