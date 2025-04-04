<?php
    if (!isset($_GET['token'])) {
        header('Location: /keydrop/index.php');
    }

    $token = $_GET['token'];
    
    require './connection.php';
    
    $sql = "SELECT * FROM changes WHERE token = ? LIMIT 1";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$token]);

    if ($stmt -> num_rows === 0) {
        header('Location: /keydrop/index.php');
    }

    $change = $stmt -> fetch_assoc();

    $id_user = $change['id_user'];
    $old_username = $change['old_username'];
    $old_email = $change['old_email'];
    $old_password = $change['old_password'];
    
    $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id_user = ?";
    $stmt = $connection -> prepare($sql);
    if ($old_username !== null) {
        $stmt -> execute([$old_username, null, null, $id_user]);
    }
    if ($old_email !== null) {
        $stmt -> execute([null, $old_email, null, $id_user]);
    }
    if ($old_password !== null) {
        $stmt -> execute([null, null, $old_password, $id_user]);
    }

    $sql = "DELETE FROM changes WHERE token = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$token]);
    
    $sql = "DELETE FROM user_sessions WHERE id_user = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$id_user]);

    header('Location: /keydrop/index.php');
?>