<?php
    function checkBan() {
        if (in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php', 'tos.php', 'banned.php']) || !isset($_SESSION['session_id'])) {
            return;
        }

        require './connection.php';

        $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $user = $stmt -> get_result() -> fetch_assoc();

        $sql = 'SELECT * FROM bans WHERE id_user = ? AND (ban_end > CURRENT_TIMESTAMP() OR ban_date = "1970-01-01 00:00:00") LIMIT 1';
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$user['id_user']]);
        $result = $stmt -> get_result();

        $connection -> close();

        if ($result -> num_rows > 0) {
            header('Location: /keydrop/banned');
        }
    }
    checkBan();
?>