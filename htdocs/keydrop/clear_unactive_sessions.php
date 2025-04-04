<?php

require $_SERVER['DOCUMENT_ROOT'] . "/keydrop/connection.php";

$sql = "SELECT created_at FROM logs WHERE log_type = 'clear_sessions' ORDER BY created_at DESC LIMIT 1";
$stmt = $connection -> prepare($sql);
$stmt -> execute();
$last_clear = $stmt -> get_result() -> fetch_row()[0];

if (!$last_clear || strtotime($last_clear) < time() - 60) {
    $sql = "DELETE FROM user_sessions WHERE last_activity < NOW() - INTERVAL 30 MINUTE";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute();
    
    $sql = "INSERT INTO logs (log_type) VALUES ('clear_sessions')";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute();
}

$connection -> close();

?>