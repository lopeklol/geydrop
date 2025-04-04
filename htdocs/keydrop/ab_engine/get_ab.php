<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["session_id"])) {
        header("Location: /keydrop/index.php");
        return;
    }

    require_once './../connection.php';

    $sql = "SELECT dedicated_ad FROM user_sessions INNER JOIN users USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION["session_id"]]);
    $ad_id = $stmt -> get_result() -> fetch_row()[0];
    $ad_id = is_null($ad_id) ? '*' : $ad_id;

    $connection -> close();

    $dir = "abs/";
    $files = str_replace('\\', '', glob($dir . "ab$ad_id.mp4"));

    if ($files) {
        $randomFile = $files[array_rand($files)];
        echo json_encode(["file" => $randomFile]);
    } else {
        echo json_encode(["error" => "Brak dostępnych reklam"]);
    }
?>
