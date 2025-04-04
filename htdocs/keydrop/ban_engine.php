<?php
    require './connection.php';
    
    if (!isset($_SESSION['session_id'])) {
        exit;
    }

    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user = $stmt -> get_result() -> fetch_assoc();

    function ban(string $type, string|null $time, string|null $reason, int|null $user_id) {
        global $connection, $user;
        $user_id = $user_id ?? $user['id_user'];
        
        if ($type == 'terminate') {
            $sql = "INSERT INTO bans (id_user, ban_date, ban_end, reason) VALUES (?, '1970-01-01 00:00:00', CURRENT_TIMESTAMP(), ?)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$user_id, $reason]);
        } else {
            $pattern = "/^(\d+)([smhdwy]|mo)$/";;
            if (preg_match($pattern, $time, $matches)) {
                $amount = (int)$matches[1];
                $unit = $matches[2];
    
                switch ($unit) {
                    case 's': $interval = "SECOND"; break;
                    case 'm': $interval = "MINUTE"; break;
                    case 'h': $interval = "HOUR"; break;
                    case 'd': $interval = "DAY"; break;
                    case 'w': $interval = "WEEK"; break;
                    case 'mo': $interval = "MONTH"; break;
                    case 'y': $interval = "YEAR"; break;
                    default: return;
                }
    
                $sql = "INSERT INTO bans (id_user, ban_date, ban_end, reason) VALUES (?, CURRENT_TIMESTAMP(), DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ? $interval), ?)";
                $stmt = $connection->prepare($sql);
                $stmt->execute([$user_id, $amount, $reason]);
    
                $connection->close();
                header('Location: /keydrop/');
            } else {
                $connection->close();
                throw new Exception("Nieprawidłowy czas bana!");
            }
        }
    }
?>