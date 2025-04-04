<?php
    function checkSession() {
        if (isset($_POST['username'])) {
            return;
        }

        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['session_id'])) {
            session_unset();
            session_destroy();
            return;
        }

        require $_SERVER['DOCUMENT_ROOT'] . "/keydrop/connection.php";
    
        $sql = "SELECT * FROM user_sessions WHERE id_session = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $result = $stmt -> get_result() -> num_rows;
    
        if ($result == 0) {
            session_unset();
            session_destroy();
            $connection -> close();
            return;
        }

        $sql = "UPDATE user_sessions SET last_activity = CURRENT_TIMESTAMP() WHERE id_session = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);

        $connection -> close();
    }
    checkSession();
?>