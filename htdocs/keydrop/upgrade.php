<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    session_start();

    $inputJSON = file_get_contents('php://input');

    $input = json_decode($inputJSON, true);

    if (!isset($input['id_item_old']) || !isset($input['id_item_new'])) {
        http_response_code(403);
        require "error_403.html";
        return;
    }

    if (!isset($_SESSION['session_id'])) {
        die();
    }

    require "./connection.php";

    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user = $stmt -> get_result() -> fetch_assoc();

    $id_user = $user['id_user'];
    $luck = $user['luck'];

    $sql = "SELECT items.* FROM item_inventory INNER JOIN items USING(id_item) INNER JOIN users USING(id_user) WHERE id = ? AND id_user = ? LIMIT 1";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$input['id_item_old'], $id_user]);
    $result = $stmt -> get_result();

    if ($result -> num_rows == 0) {
        require "ban_engine.php";
        ban('ban', '1m', 'Trying to upgrade item that doesn\'t exist or isn\'t yours.');
        exit;
    }

    $old_item = $result -> fetch_assoc();
    
    $sql = "SELECT * FROM items WHERE id_item = ? LIMIT 1";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$input['id_item_new']]);
    $new_item = $stmt -> get_result() -> fetch_assoc();

    $sql = "SELECT * FROM upgrades INNER JOIN users USING(id_user) WHERE id_user = ? AND DATE_ADD(date, INTERVAL 1 SECOND) > CURRENT_TIMESTAMP() ORDER BY date DESC LIMIT 1";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$id_user]);
    $result = $stmt -> get_result();

    if ($result -> num_rows > 0) {
        require "ban_engine.php";
        ban('ban', '10m', 'Upgrading items too fast.');
        exit;
    }

    $chance = $old_item['item_value'] / $new_item['item_value'] * $luck;
    $finish = random_int(0, 35999) / 100;
    $success = $finish <= $chance * 360 / 100;

    if ($success === true) {
        $finish = random_int(0, floor($chance / $luck * 35999)) / 100;
    } else {
        $finish = random_int(ceil($chance / $luck * 35999), 35999) / 100;
    }
    
    try {
        $connection -> begin_transaction();

        if ($success === true) {
            $sql = "UPDATE item_inventory SET id_item = ? WHERE id = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$input['id_item_new'], $input['id_item_old']]);

            $sql = "INSERT INTO upgrades (id_user, id_item_old, id_item_new, date, success) VALUES (?, ?, ?, CURRENT_TIMESTAMP(), 1)";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$id_user, $old_item['id_item'], $input['id_item_new']]);
        } else {
            $sql = "DELETE FROM item_inventory WHERE id = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$input['id_item_old']]);

            $sql = "INSERT INTO upgrades (id_user, id_item_old, id_item_new, date, success) VALUES (?, ?, ?, CURRENT_TIMESTAMP(), 0)";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$id_user, $old_item['id_item'], $input['id_item_new']]);
        }

        $connection -> commit();

    } catch (Exception) {
        $connection -> rollback();
        print("ERROR");
        die();
    }

    $result = [
        'success' => $success,
        'finish' => $finish,
    ];

    $connection -> close();

    print(json_encode(["result" => $result]));
?>