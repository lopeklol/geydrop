<?php
    function verify_ad($adToken) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["session_id"])) {
            header("Location: /keydrop/index.php");
        }

        $session_id = $_SESSION["session_id"];

        require "./../connection.php";

        $sql = "SELECT ad_token FROM ads INNER JOIN users USING(id_user) INNER JOIN user_sessions USING(id_user) WHERE id_session = ? AND DATE_ADD(ad_start, INTERVAL 35 SECOND) >= CURRENT_TIMESTAMP() AND DATE_ADD(ad_start, INTERVAL 24 SECOND) <= CURRENT_TIMESTAMP()";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$session_id]);
        $valid_tokens = $stmt -> get_result();

        foreach ($valid_tokens as $valid_token) {
            if ($valid_token["ad_token"] == $adToken) {
                $sql = "DELETE FROM ads WHERE ad_token = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$valid_token["ad_token"]]);
                
                return true;
            }
        }

        return false;
    }
?>