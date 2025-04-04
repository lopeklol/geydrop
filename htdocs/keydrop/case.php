<?php require_once 'header.php'; ?>

<div id="case_page" style="color: white;">
    <?php
        $case_name = urldecode($_GET['case']);

        require "./connection.php";

        $sql = "SELECT * FROM cases WHERE case_name = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$case_name]);
        $result = $stmt -> get_result();
        
        if ($result -> num_rows == 0) {
            ?>
                <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                    This case does not exist.
                </div>
            <?php
        } else {
            $case = $result -> fetch_assoc();
            ?>
                <div id="case_name">
                    <a href="/keydrop/cases"><button class="back_button">< Go back</button></a>
                    <?php print(htmlspecialchars($case['case_name'])); ?>
                </div><br>
                <div id="case_contents">
                    <?php
                        function getCaseContents() {
                            global $connection, $case;
                            $sql = "SELECT id_item FROM case_contents WHERE id_case = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> execute([$case['id_case']]);
                            $result = $stmt -> get_result();

                            $array = [];
                            foreach ($result as $item_id) {
                                $sql = "SELECT * FROM items WHERE id_item = ?";
                                $stmt = $connection -> prepare($sql);
                                $stmt -> execute([$item_id['id_item']]);
                                $item = $stmt -> get_result() -> fetch_assoc();

                                $sql = "SELECT rarity_color FROM item_rarities WHERE id_item_rarity = ?";
                                $stmt = $connection -> prepare($sql);
                                $stmt -> execute([$item['id_item_rarity']]);
                                $result3 = $stmt -> get_result();
                                
                                $item['rarity_color'] = $result3 -> fetch_row()[0];
                                $array[] = $item;
                            }

                            return $array;
                        }

                        function loadCaseContents() {
                            global $connection, $case, $end_item_gradient;
                            $sql = "SELECT money FROM user_sessions INNER JOIN users USING(id_user) WHERE id_session = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> execute([$_SESSION['session_id']]);
                            $money = $stmt -> get_result() -> fetch_row()[0];

                            $sql = "SELECT id_item, IF(SUM(chance) = 100, 1, 0) AS 'CONFIGURED' FROM case_contents WHERE id_case = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> execute([$case['id_case']]);
                            $result = $stmt -> get_result();

                            $contents = getCaseContents();

                            if (!$result -> fetch_assoc()['CONFIGURED']) {
                                ?>
                                    <div id="case_configure_error">
                                        This case is not configured correctly!
                                    </div>
                                <?php
                                return;
                            }

                            ?>
                                <div id="marker">
                                    <div id="triangletop" class="triangle"></div>
                                    <div id="line"></div>
                                    <div id="trianglebottom" class="triangle"></div>
                                </div>
                            <?php

                            $case_count = $_POST['case_count'] ?? 1;
                            for ($_ = 0; $_ < $case_count; $_++) {
                                ?>
                                <div class="case_row">
                                <?php
                                    for ($i = 0; $i < 250; $i++) {
                                        $item = $contents[array_rand($contents)];
                                        $image = $item['image'];
                                        $color = $item['rarity_color'];
                                        $name = explode(" | ", $item['item_name'])[0];
                                        $skin = explode(" | ", $item['item_name'])[1];
                                        ?>
                                            <div class="case_item" data-item-idx="<?php print($i); ?>">
                                                <div class="case_item_image_container">
                                                    <img class="case_item_image" src="/keydrop/<?php print(htmlspecialchars($image)); ?>">
                                                </div>
                                                <hr class="rarity_line" style="color: <?php print(htmlspecialchars($color)); ?>; box-shadow: 0px 0px 10px 5px <?php print(htmlspecialchars($color)); ?>;">
                                                <div class="case_item_name"><?php print(htmlspecialchars($name)); ?></div>
                                                <div class="case_item_skin"><?php print(htmlspecialchars($skin)); ?></div>
                                            </div>
                                        <?php
                                    }
                                ?>
                                </div><br>
                                <?php
                            }
                    ?>
                </div>
                <div id="case_options">
                    <div id="case_count_container">
                        <div id="case_count_desc">
                            Number of cases to open:
                        </div>
                        <div id="case_count_buttons">
                            <form id="case_count_form" method="POST">
                                <input type="hidden" name="case_count" id="case_count_input" value="<?php print(htmlspecialchars($case_count)); ?>">
                            </form>
                            <button class="case_count_button" data-count="1">1</button>
                            <button class="case_count_button" data-count="2">2</button>
                            <button class="case_count_button" data-count="3">3</button>
                            <button class="case_count_button" data-count="4">4</button>
                            <button class="case_count_button" data-count="5">5</button>
                        </div>
                    </div>
                    <div id="case_open_buttons">
                        <?php if ($case['case_value'] > 0) { ?>
                            <button id="case_open" class="button green_button" <?php print(htmlspecialchars($money >= $case['case_value'] * $case_count ? '' : 'disabled')); ?>>Open for <?php print(htmlspecialchars($case['case_value'] * $case_count)); ?> USD</button>
                        <?php } ?>
                        <?php if ($case['for_ad'] == 1) { ?>
                            <button id="case_open_ab" class="button blue_button">Open for Ad</button>
                        <?php } ?>
                    </div>
                </div>
                <div id="case_contents_items">
                    <div style="width: 100%;"><h2>Contents of <?php print($case["case_name"]); ?>:</h2></div>
                    <?php
                        $case_name = urldecode($_GET['case']);
                        $sql = "SELECT item_name, item_value, items.image, chance, id_item_rarity, rarity_color FROM cases INNER JOIN case_contents USING(id_case) INNER JOIN items USING(id_item) INNER JOIN item_rarities USING(id_item_rarity)  WHERE case_name = ? ORDER BY chance DESC, id_item_rarity DESC, item_value DESC, item_name ASC";
                        $stmt = $connection -> prepare($sql);
                        $stmt -> execute([$case_name]);
                        $result = $stmt -> get_result();

                        foreach ($result as $item) {
                            $color = $item['rarity_color'];
                            ?>
                                <div class="item_block" style="background: linear-gradient(0deg, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, <?php echo $end_item_gradient ?? 0.3 ?>) 0%, <?php print(htmlspecialchars(str_replace(')', '', str_replace('rgb(', 'rgba(', $color)))); ?>, 0.01) 80%);">
                                    <div class="item_chance"><?php print(htmlspecialchars($item['chance'])); ?>%</div>
                                    <img class="case_image" src="/keydrop/<?php print(htmlspecialchars($item['image'])); ?>" alt="">
                                    <p>
                                        <span class="item_name_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[0])); ?></span><br>
                                        <span class="item_skin_inv"><?php print(htmlspecialchars(explode(" | ", $item['item_name'])[1])); ?></span><br>
                                        <span class="item_value"><strong><?php print(htmlspecialchars($item['item_value'])); ?> USD</strong></span>
                                    </p>
                                </div><br>
                            <?php
                        }
                    ?>
                </div>
            <?php
            }

            loadCaseContents();
        }

        $connection -> close();
    ?>
</div>

<?php require_once 'footer.php'; ?>