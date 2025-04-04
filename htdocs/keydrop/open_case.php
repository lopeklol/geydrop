<?php
    function getWeaponIndex($force_inp) {

        $offset = 0;

        $force = $force_inp;

        while ($force > 0) {

            $offset += $force;

            $force = floor($force * 0.99 * 100) / 100;

            if ($force < 0.1) {

                $force = 0;

            }

        }

        return [
            'index' => floor($offset / 110),
            'offset' => $offset,
        ];

    }

    session_start();

    $inputJSON = file_get_contents('php://input');

    $input = json_decode($inputJSON, true);

    if (!isset($input['case_count'])) {
        http_response_code(403);
        require "error_403.html";
        return;
    }



    if (!in_array($input['case_count'] ?? 1, [1, 2, 3, 4, 5])) {

        require "ban_engine.php";

        ban('ban', '1h', 'Intentionally sending to many packets.');

        exit;

    }



    if (!in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php', 'index.php']) &&

        !isset($_SESSION['session_id'])) {

        die();

    }



    require "./connection.php";



    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";

    $stmt = $connection -> prepare($sql);

    $stmt -> execute([$_SESSION['session_id']]);

    $user = $stmt -> get_result() -> fetch_assoc();


    $sql = "SELECT * FROM drops INNER JOIN users USING(id_user) WHERE id_user = ? AND DATE_ADD(drop_date, INTERVAL 1 SECOND) > CURRENT_TIMESTAMP() ORDER BY drop_date DESC LIMIT 1";

    $stmt = $connection -> prepare($sql);

    $stmt -> execute([$user['id_user']]);

    $result = $stmt -> get_result();



    if ($result -> num_rows > 0) {
        require "ban_engine.php";
        ban('ban', '10m', 'Opening cases too fast.');
        exit;
    }



    $id_user = $user['id_user'];

    $money = $user['money'];

    $luck = $user['luck'];



    $sql = "SELECT * FROM cases WHERE case_name = ?";

    $stmt = $connection -> prepare($sql);

    $stmt -> execute([urldecode($input['case_name'])]);

    $case = $stmt -> get_result() -> fetch_assoc();



    if (($money < $case['case_value'] || $case['case_value'] == 0) && !$input['ad']) {

        $connection -> close();

        print('NOT ENOUCH MONEY!');

        die();

    }



    $sql = "SELECT * FROM case_contents INNER JOIN items USING(id_item) WHERE id_case = ? ORDER BY item_value DESC;";

    $stmt = $connection -> prepare($sql);

    $stmt -> execute([$case['id_case']]);

    $items_mysqli = $stmt -> get_result();



    $items = [];

    foreach ($items_mysqli as $item_row) {

        $item_id = $item_row['id_item'];

        $chance = $item_row['chance'];

        $items[] = ['id_item' => $item_id, 'chance' => $chance];

    }



    $items_rand = [];

    $item_idx = 0;

    while (count($items_rand) < 10000) {

        $item = $items[$item_idx];

        $entries = intval($item['chance'] * $luck) == 0 && $item_idx + 1 == count($items) ? 10000 : intval($item['chance'] * $luck);

        for ($_ = 0; $_ < $entries; $_++) {

            $items_rand[] = $item['id_item'];

            if (count($items_rand) == 10000) {

                break;

            }

        }

        if ($item_idx + 1 < count($items)) {

            $item_idx++;

        }

    }



    try {

        $connection -> begin_transaction();



        if (!$input['ad']) {

            $sql = "UPDATE users SET money = ? - ? WHERE id_user = ?";

            $stmt = $connection -> prepare($sql);

            $stmt -> execute([$money, $case['case_value'] * $input['case_count'], $id_user]);

        } else {
            require './ab_engine/verify_ab.php';

            if (!isset($input['ad_token'])) {
                $input['ad_token'] = '';
            }

            if (!verify_ad($input['ad_token'])) {
                require "ban_engine.php";
                http_response_code(123);
                ban('ban', '1m', 'Skipping an ad.');
                exit;
            }
        }



        $randomized = [];

        for ($i = 0; $i < $input['case_count']; $i++) {

            $rand_idx = array_rand($items_rand);



            $sql = "SELECT * FROM items INNER JOIN item_rarities USING(id_item_rarity) INNER JOIN item_types USING(id_item_type) INNER JOIN item_wears USING(id_item_wear) WHERE id_item = ?;";

            $stmt = $connection -> prepare($sql);

            $stmt -> execute([$items_rand[$rand_idx]]);

            $rand_item = $stmt -> get_result() -> fetch_assoc();



            $randomized['items'][] = [

                'id_item' => $rand_item['id_item'],

                'item_name' => $rand_item['item_name'],

                'type_name' => $rand_item['type_name'],

                'rarity_name' => $rand_item['rarity_name'],

                'rarity_color' => $rand_item['rarity_color'],

                'wear_name' => $rand_item['wear_name'],

                'item_value' => $rand_item['item_value'],

                'image' => '/keydrop/'.$rand_item['image'],

            ];



            $sql = "INSERT INTO drops (id_user, id_item, id_case) VALUES (?, ?, ?);";

            $stmt = $connection -> prepare($sql);

            $stmt -> execute([$id_user, $rand_item['id_item'], $case['id_case']]);



            $sql = "INSERT INTO item_inventory (id_user, id_item) VALUES (?, ?);";

            $stmt = $connection -> prepare($sql);

            $stmt -> execute([$id_user, $rand_item['id_item']]);

        }

        $connection -> commit();

    } catch (Exception) {

        $connection -> rollback();

        print("ERROR");

        die();

    }

    $weapon = getWeaponIndex(random_int(100, 200));

    $randomized['force'] = $weapon['offset'];
    $randomized['index'] = $weapon['index'];
    $randomized['case_value'] = $case['case_value'];

    $result = $randomized;

    $connection -> close();

    print(json_encode(["result" => $result]));

?>