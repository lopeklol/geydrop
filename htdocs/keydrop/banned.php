<?php
    session_start();
    if (!isset($_SESSION['session_id'])) {
        header('Location: ./');
    }

    require './connection.php';

    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user = $stmt -> get_result() -> fetch_assoc();

    $sql = 'SELECT * FROM bans WHERE id_user = ? AND (ban_end > CURRENT_TIMESTAMP() OR ban_date = "1970-01-01 00:00:00") LIMIT 1';
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$user['id_user']]);
    $result = $stmt -> get_result();

    $connection -> close();

    session_abort();

    if ($result -> num_rows > 0) {
        $ban_data = $result -> fetch_assoc();
        if ($ban_data['ban_date'] == '1970-01-01 00:00:00') {
            ?>
                <?php require_once 'header.php'; ?>
                <div id="ban_page">
                    <h1>Your account has been pernamently terminated!</h1>
                    <p>
                        REASON: <?php print(!is_null($ban_data['reason']) ? htmlspecialchars($ban_data['reason']) : "<i>No reason given.</i>"); ?>
                    </p>
                    <a href="./logout"><button class="button red_button" style="width: 200px; height: 70px;">Log out</button></a>
                </div>
                <?php require_once 'footer.php'; ?>
            <?php
            exit;
        } else {
            ?>
                <?php require_once 'header.php'; ?>
                <div id="ban_page">
                    <h1>Your account has been temporarly banned!</h1>
                    <p>
                        <b>Ban date:</b> <?php print(htmlspecialchars($ban_data['ban_date'])); ?><br>
                        <b>Ban expires:</b> <?php print(htmlspecialchars($ban_data['ban_end'])); ?><br>
                    </p>
                    <p>
                        <b>REASON:</b> <?php print(!is_null($ban_data['reason']) ? htmlspecialchars($ban_data['reason']) : "<i>No reason given.</i>"); ?>
                    </p>
                    <a href="./logout"><button class="button red_button" style="width: 200px; height: 70px;">Log out</button></a>
                </div>
                <?php require_once 'footer.php'; ?>
            <?php
            exit;
        }
    }

    header('Location: ./');
?>