<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["session_id"])) {
        header("Location: /keydrop/index.php");
    }
    $session_id = $_SESSION["session_id"];

    require "./../connection.php";

    $sql = "SELECT * FROM user_sessions INNER JOIN users USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$session_id]);
    $user = $stmt -> get_result() -> fetch_assoc();

    $token = bin2hex(random_bytes(128));

    $sql = "INSERT INTO ads (ad_token, id_user) VALUES (?, ?)";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$token, $user["id_user"]]);

    echo json_encode(["token" => $token]);
?>
