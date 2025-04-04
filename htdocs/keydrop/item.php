<?php require_once 'header.php'; ?>

<div id="case_page" style="color: white;">
    <?php
        $item_id = rawurldecode($_GET['id']);
        echo is_null($_GET['id']);

        require "./connection.php";

        $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $user = $stmt -> get_result() -> fetch_assoc();

        $sql = "SELECT * FROM item_inventory INNER JOIN items USING(id_item) WHERE id = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$item_id]);
        $result = $stmt -> get_result();
        
        if ($result -> num_rows == 0) {
            ?>
                <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                    This item does not exist.
                </div>
            <?php
        } else {
            $sql = "SELECT * FROM item_inventory INNER JOIN items USING(id_item) WHERE id = ? AND id_user = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$item_id, $user['id_user']]);
            $result = $stmt -> get_result();

            if ($result -> num_rows == 0) {
                ?>
                    <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                        This item is not yours.
                    </div>
                <?php
            } else {
                $item = $result -> fetch_assoc();
                ?>
                    <div id="item_name">
                        <a href="/keydrop/inventory"><button class="back_button">< Go back</button></a>
                        <?php print(htmlspecialchars($item['item_name'])); ?>
                    </div>
                    <div id="item_data">
                        <img id="item_image" src="/keydrop/<?php print(htmlspecialchars($item['image'])); ?>" alt="">
                        <div id="item_name_only"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[0])); ?></div>
                        <div id="item_skin"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[1])); ?></div>
                        <div id="item_value"><?php print(htmlspecialchars($item['item_value'])); ?> USD</div>
                    </div>
                    <div id="item_options">
                        <form id="sell_php_form" action="/keydrop/sell" method="POST">
                            <input name="id_item" type="hidden" value="<?php print($item_id); ?>">
                            <input id="item_sell" class="item_button button red_button" type="submit" value="Sell for <?php print(htmlspecialchars($item['item_value'])); ?> USD">
                        </form>
                        <form id="upgrade_php_form" action="/keydrop/upgrader" method="POST">
                            <input name="id_item" type="hidden" value="<?php print($item_id); ?>">
                            <input id="item_upgrade" class="item_button button yellow_button" type="submit" value="Upgrade">
                        </form>
                    </div>
                </div>
                <?php
            }
        }

        $connection -> close();
    ?>
</div>

<?php require_once 'footer.php'; ?>