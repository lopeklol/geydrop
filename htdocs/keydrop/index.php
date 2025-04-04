<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/header.php'; ?>

<div id="main_index">
    <div id="live_drop_contents">
        <?php
            require './connection.php';
    
            $stmt = $connection -> prepare('SELECT COUNT(*) as online FROM user_sessions;');
            $stmt -> execute();
            $online = $stmt -> get_result() -> fetch_row()[0];

            $connection -> close();
        ?>
        <div id="live_drop_info">
            <h2>LIVEDROP</h2><br>
            <?php print($online); ?> online
        </div>
        <?php
            require './connection.php';
    
            $stmt = $connection -> prepare('SELECT * FROM drops INNER JOIN items USING (id_item) INNER JOIN item_rarities USING (id_item_rarity) ORDER BY drop_date DESC LIMIT 30');
            $stmt -> execute();
            $result = $stmt -> get_result();
            
            foreach ($result as $item) {
                $image = $item['image'];
                $color = $item['rarity_color'];
                $name = explode(" | ", $item['item_name'])[0];
                $skin = explode(" | ", $item['item_name'])[1];
                ?>
                    <div class="live_drop_item" style="background: linear-gradient(0deg, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.5) 0%, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.01) 60%);">
                        <div class="case_item_image_container">
                            <img class="case_item_image" src="<?php print(htmlspecialchars($image)); ?>">
                        </div>
                        <div class="case_item_name"><?php print(htmlspecialchars($name)); ?></div>
                        <div class="case_item_skin"><?php print(htmlspecialchars($skin)); ?></div>
                        <hr class="rarity_line" style="color: <?php print(htmlspecialchars($color)); ?>;">
                    </div>
                <?php
            }

            $connection -> close();
        ?>
    </div>
    <p style="color: white; display: flex; justify-content: center; width: 100%;">
    <a href="./cases"><button class="button green_button">CASES</button></a>
    <a href="./inventory"><button class="button yellow_button">INVENTORY</button></a>
    </p>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/footer.php'; ?>