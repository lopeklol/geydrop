<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    session_start();

    require "./connection.php";

    $search = $_POST['search'] ?? '';

    $sql = "SELECT * FROM cases WHERE LOWER(case_name) = LOWER(?) AND hidden = 1 LIMIT 1";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$search]);
    $result = $stmt -> get_result();

    
    if ($result -> num_rows > 0) {
        $code = rawurlencode($result -> fetch_assoc()['case_name']);
        header("Location: /keydrop/case/$code");
    }
?>

<?php require_once 'header.php'; ?>

<div id="cases">
    <h1>CASES</h1>
    <div id="case_panel">
        Search case:
        <form action="" method="POST">
            <input type="text" name="search" id="case_name_inp">
            <select name="sort" id="sort_mode">
                <option value="case_name ASC">Case name - Ascending</option>
                <option value="case_name DESC">Case name - Descending</option>
                <option value="case_value ASC">Case value - Ascending</option>
                <option value="case_value DESC">Case value - Descending</option>
            </select>
            <input type="submit" value="Search" class="button blue_button" style="margin: 0px;">
        </form>
    </div>
    <?php
        $sort = $_POST['sort'] ?? 'szucka';
        $sort = in_array($sort, ['case_name ASC', 'case_name DESC', 'case_value ASC', 'case_value DESC']) ? $sort : "case_name ASC";
        $sql = "SELECT *, IF(SUM(chance) = 100, 1, 0) AS 'CONFIGURED' FROM cases LEFT JOIN case_contents USING(id_case) WHERE LOWER(case_name) LIKE LOWER(?) AND hidden = 0 GROUP BY cases.id_case ORDER BY $sort";
        $stmt = $connection -> prepare($sql);
        $search = "%$search%";
        $stmt -> execute([$search]);
        $result = $stmt -> get_result();

        foreach ($result as $case) {
            ?>
                <a href="case/<?php print(htmlspecialchars(urlencode($case['case_name']))); ?>">
                    <div class="case_block">
                        <img class="case_image" src="./case_images/<?php print(htmlspecialchars($case['image'])); ?>" alt="">
                        <p>
                            <span class="case_name"><?php print(htmlspecialchars($case['case_name'])); ?></span><br>
                            <span class="case_value" <?php print($case['CONFIGURED'] === 0 ? 'style="color: red;"' : ''); ?>>
                                <?php
                                    $price = $case['CONFIGURED'] === 1 ? htmlspecialchars($case['case_value'] . ' USD') : 'UNAVAILABLE';
                                    print($price === '0.00 USD' ? '<span style="color: blue;">FOR AN AD</span>' : $price);
                                ?>
                            </span>
                        </p>
                    </div>
                </a><br>
            <?php
        }

        if ($result -> num_rows == 0) {
            ?>
                <div style="color: gray; display: flex; justify-content: center; width: 100%; font-size: 30px;">
                    No results found.
                </div>
            <?php
        }

        $connection -> close();
    ?>
</div>

<?php require_once 'footer.php'; ?>