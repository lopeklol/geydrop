<?php require_once 'header.php'; ?>

<div id="cases">
    <h1>INVENTORY</h1>
    <div id="case_panel">
        Search item:
        <form action="" method="POST">
            <input type="text" name="search" id="case_name_inp">
            <select name="sort" id="sort_mode">
                <option value="item_name ASC">Item name - Ascending</option>
                <option value="item_name DESC">Item name - Descending</option>
                <option value="item_value ASC">Item value - Ascending</option>
                <option value="item_value DESC">Item value - Descending</option>
            </select>
            <input type="submit" value="Search" class="button blue_button" style="margin: 0px;">
        </form>
    </div>
    <div id="items_panel" style="display: none;">
        <button id="select_all" class="button green_button" style="margin: 0px;">Select all</button>
        <button id="unselect_all" class="button yellow_button" style="margin: 0px;">Unselect all</button>
        <button id="sell_all" class="button red_button" style="margin: 0px;">Sell selected</button>
        <form id="id_item_form" action="/keydrop/sell" method="POST">
            <input type="hidden" name="id_item" id="id_item_input">
        </form>
    </div>
    <?php
        if (!isset($_SESSION['session_id'])) {
            header("Location: ./login");
        }

        require "./connection.php";

        $sql = "SELECT id_user FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $user_id = $stmt -> get_result() -> fetch_row()[0];

        $search = $_POST['search'] ?? '';
        $sort = $_POST['sort'] ?? 'szucka';
        $sort = in_array($sort, ['item_name ASC', 'item_name DESC', 'item_value ASC', 'item_value DESC']) ? $sort : "item_name ASC";
        $sql = "SELECT * FROM items INNER JOIN item_inventory USING (id_item) WHERE LOWER(item_name) LIKE LOWER(?) AND id_user = $user_id ORDER BY $sort";
        $stmt = $connection -> prepare($sql);
        $search = "%$search%";
        $stmt -> execute([$search]);
        $result = $stmt -> get_result();

        foreach ($result as $item) {
            ?>
                <a href="item/<?php print(rawurlencode($item['id'])); ?>">
                    <div class="case_block">
                        <input type="checkbox" class="item_checkbox" data-id="<?php print($item['id']); ?>">
                        <img class="case_image" src="./<?php print(htmlspecialchars($item['image'])); ?>" alt="">
                        <p>
                            <span class="item_name_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[0])); ?></span><br>
                            <span class="item_skin_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[1])); ?></span><br>
                            <span class="item_value"><?php print(htmlspecialchars($item['item_value'])); ?> USD</span>
                        </p>
                    </div>
                </a><br>
            <?php
        }

        if ($result -> num_rows == 0) {
            ?>
                <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                    Inventory empty.
                </div>
            <?php
        }

        $connection -> close();
    ?>
</div>

<?php require_once 'footer.php'; ?>