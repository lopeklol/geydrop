<?php
    session_start();

    if (isset($_SESSION['session_id'])) {
        require "./connection.php";

        $stmt = $connection -> prepare("SELECT id_user FROM user_sessions WHERE id_session = ?");
        $stmt -> execute([$_SESSION['session_id']]);
        $user_id = $stmt -> get_result() -> fetch_row()[0];
         
        $stmt = $connection -> prepare("DELETE FROM user_sessions WHERE id_user = ?");
        $stmt -> execute([$user_id]);
        
        session_unset();
        session_destroy();
        $connection -> close();
    }
    header('Location: ./login');
?>