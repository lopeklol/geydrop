<?php
    session_start();
    if (!isset($_SESSION['session_id'])) {
        header("Location: /login");
    }

    require "./connection.php";

    $item_id = $_POST['id_item'] ?? '0';

    $sql = "SELECT id_user FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user_id = $stmt -> get_result() -> fetch_row()[0];

    $sql = "SELECT * FROM items INNER JOIN item_rarities USING(id_item_rarity) INNER JOIN item_inventory USING(id_item) WHERE id_user = ? AND id = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$user_id, $item_id]);
    $result = $stmt -> get_result();

    if ($result -> num_rows == 0) {
        http_response_code(400);
        header("Location: ./inventory");
    } else {
        $item_to_upgrade = $result -> fetch_assoc();
        $color = $item_to_upgrade['rarity_color'];
    }

    $connection -> close();
?>

<?php require_once 'header.php'; ?>

<input type="hidden" id="item_to_upgrade" value="<?php print($item_to_upgrade['id']); ?>">
<input type="hidden" id="item_to_upgrade_value" value="<?php print($item_to_upgrade['item_value']); ?>">
<div id="profile_manage_container">
    <div id="item_name" style="margin: 0px;">
        <a href="/keydrop/item/<?php print($item_to_upgrade['id']); ?>"><button style="height: 66%; top: 16.5%;" class="back_button">< Go back</button></a>
        <span style="font-size: 1.5em;">UPGRADER</span>
    </div>
    <div id="upgrader_contents">
        <div id="upgrader_old_item">
            <div class="case_block" style="border-radius: 10px; padding: 10px; background: linear-gradient(0deg, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.3) 0%, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.01) 80%);">
                <img class="case_image" src="./<?php print(htmlspecialchars($item_to_upgrade['image'])); ?>" alt="">
                <p>
                    <span class="item_name_inv"><?php print(htmlspecialchars(explode(" | ", $item_to_upgrade['item_name'])[0])); ?></span><br>
                    <span class="item_skin_inv"><?php print(htmlspecialchars(explode(" | ", $item_to_upgrade['item_name'])[1])); ?></span><br>
                    <span class="item_value"><?php print(htmlspecialchars($item_to_upgrade['item_value'])); ?> USD</span>
                </p>
            </div>
        </div>
        <div id="upgrader_circle_container">
            <div id="upgrader_circle">
                <div id="upgrader_win_pointer"></div>
                <div id="upgrader_circle_inner">
                    <div id="upgrader_win_circle"></div>
                    <div id="upgrader_circle_chance">
                        <strong><div id="upgrader_win_chance">50.00%</div></strong><br>
                        <div id="upgrader_chance_text">Upgrade chance</div>
                    </div>
                </div>
            </div>
            <button style="margin: 0px;" class="button green_button" id="upgrader_button" disabled>
                UPGRADE
            </button>
        </div>
        <div id="upgrader_new_item">
            No item selected.
        </div>
    </div>
    <div id="upgrader_items">
        <div id="upgrader_inventory">
            <h2 style="width: 100%; text-align: center; color: white;">Choose item to win:</h2>
            <?php
                require "./connection.php";

                $sql = "SELECT * FROM items INNER JOIN item_rarities USING(id_item_rarity) WHERE item_value > ? ORDER BY id_item_rarity DESC, item_value DESC, item_name ASC";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$item_to_upgrade['item_value']]);
                $result = $stmt -> get_result();
    
                foreach ($result as $item) {
                    $color = $item['rarity_color'];
                    ?>
                        <div id="chooseUpgrade<?php print($item['id_item']); ?>" onclick="chooseItem(<?php print($item['id_item']); ?>)" class="upgrader_item_block" style="background: linear-gradient(0deg, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.3) 0%, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.01) 80%);">
                            <img class="case_image" src="./<?php print(htmlspecialchars($item['image'])); ?>" alt="">
                            <p>
                                <span class="item_name_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[0])); ?></span><br>
                                <span class="item_skin_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[1])); ?></span><br>
                                <span class="item_value"><?php print(htmlspecialchars($item['item_value'])); ?> USD</span>
                            </p>
                        </div>
                    <?php
                }
    
                if ($result -> num_rows == 0) {
                    ?>
                        <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                            There is no item with higher value.
                        </div>
                    <?php
                }
    
                $connection -> close();
            ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>