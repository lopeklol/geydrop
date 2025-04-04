<?php
    session_start();

    if (!isset($_SESSION['session_id'])) {
        header("Location: ./login");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(403);
        require "error_403.html";
        return;
    }

    if (!isset($_POST['id_item'])) {
        header("Location: ./inventory");
        exit();
    }

    $id_items = explode(",", $_POST['id_item']);

    require "./connection.php";

    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user = $stmt -> get_result() -> fetch_assoc();

    try {
        $connection -> begin_transaction();
        
        foreach ($id_items as $id_item) {
            $sql = "SELECT * FROM item_inventory INNER JOIN items USING(id_item) WHERE id = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$id_item]);
            $result = $stmt -> get_result();

            if ($result -> num_rows == 0) {
                throw new Exception('Item not found!');
            }

            $item = $result -> fetch_assoc();

            if ($item['id_user'] != $user['id_user']) {
                throw new Exception('Item not yours!');
            }

            $sql = "UPDATE users SET money = money + ? WHERE id_user = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$item['item_value'], $user['id_user']]);

            $sql = "DELETE FROM item_inventory WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id_item]);
        }

        $connection->commit();
    } catch (Exception) {
        $connection -> rollback();
        $connection -> close();
        header("Location: ./inventory");
        exit();
    }

    $connection->close();
    header("Location: ./inventory");
    exit();
?>