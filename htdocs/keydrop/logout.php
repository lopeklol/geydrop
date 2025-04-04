<?php
    session_start();
    if (isset($_SESSION['session_id'])) {
        require "./connection.php";
        
        $stmt = $connection -> prepare("DELETE FROM user_sessions WHERE id_session = ?");
        $stmt -> execute([$_SESSION['session_id']]);
        
        session_unset();
        session_destroy();
        $connection -> close();
    }
    header('Location: ./login');
?>