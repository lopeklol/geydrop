<?php
    require_once './load_env.php';

    $host = getenv('DB_HOST'); 
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    $database = getenv('DB_NAME');

    $connection = new mysqli($host, $username, $password, $database);
?>