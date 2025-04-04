<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
    $success = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'connection.php';
        
        $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $user = $stmt -> get_result() -> fetch_assoc();

        $success = true;

        if (isset($_POST['username'])) {
            try {
                $connection -> begin_transaction();
        
                $username = $_POST['username'];
                $email = $user['email'];
                $token = bin2hex(random_bytes(128));

                $sql = "SELECT * FROM changes INNER JOIN users USING (id_user) WHERE id_user = ? AND DATE_ADD(change_date, INTERVAL 10 MINUTE) > CURRENT_TIMESTAMP() AND old_username IS NOT NULL";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$user['id_user']]);
                if ($stmt -> get_result() -> num_rows > 0) {
                    $username_result = 'You can change your username once every 10 minutes.';
                    throw new Exception();
                }

                $sql = "SELECT id_user FROM users WHERE username = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$username]);
                if ($stmt -> get_result() -> num_rows > 0) {
                    $username_result = 'User with this username already exists.';
                    throw new Exception();
                }
        
                $sql = "UPDATE users SET username = ? WHERE id_user = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$username, $user['id_user']]);
        
                $sql = "INSERT INTO changes (id_change, id_user, old_username) VALUES (?, ?, ?)";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$token, $user['id_user'], $user['username']]);
        
                require_once 'email_sender.php';
        
                send_email($email, 'Geydrop - Username change', "<h1>Username change</h1><br>Your username in Geydrop has been changed to $username.<br>If it was you, ignore this email.<br>If it wasn't you, click this link: <a href='https://lopeklol.fanth.pl/keydrop/changes/$token'>https://lopeklol.fanth.pl/keydrop/changes/$token</a> to undo changes and logout everywhere, then immediately change your password.<br><br>If the link doesn't work, copy and paste it into your browser.");
                $username_result = 'Username changed successfully!';
                $connection -> commit();
            } catch (Exception) {
                $connection -> rollback();
                $success = false;
            }
        } else if (isset($_POST['email'])) {
            try {
                $connection -> begin_transaction();
        
                $email = $_POST['email'];
                $old_email = $user['email'];
                $token = bin2hex(random_bytes(128));

                $sql = "SELECT * FROM changes INNER JOIN users USING (id_user) WHERE id_user = ? AND DATE_ADD(change_date, INTERVAL 10 MINUTE) > CURRENT_TIMESTAMP() AND old_email IS NOT NULL";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$user['id_user']]);
                if ($stmt -> get_result() -> num_rows > 0) {
                    $email_result = 'You can change your email address once every 10 minutes.';
                    throw new Exception();
                }
                
                $sql = "SELECT id_user FROM users WHERE email = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$email]);
                if ($stmt -> get_result() -> num_rows > 0) {
                    $email_result = 'User with this email already exists.';
                    throw new Exception();
                }
        
                $sql = "UPDATE users SET email = ? INNER JOIN user_sessions USING(id_user) WHERE id_user = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$email, $user['id_user']]);
        
                $sql = "INSERT INTO changes (id_change, id_user, old_email) VALUES (?, ?, ?)";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$token, $user['id_user'], $old_email]);
        
                require_once 'email_sender.php';
        
                send_email($old_email, 'Geydrop - Email change', "<h1>Email change</h1><br>Your email address in Geydrop has been changed to $email.<br>If it was you, ignore this email.<br>If it wasn't you, click this link: <a href='https://lopeklol.fanth.pl/keydrop/changes/$token'>https://lopeklol.fanth.pl/keydrop/changes/$token</a> to undo changes and logout everywhere, then immediately change your password.<br><br>If the link doesn't work, copy and paste it into your browser.");
                $email_result = 'Email address changed successfully!';
                $connection -> commit();
            } catch (Exception) {
                $connection -> rollback();
                $success = false;
            }
        } else if (isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['repeat_password'])) {
            try {
                $connection -> begin_transaction();
        
                $old_password = $_POST['old_password'];
                $password = $_POST['password'];
                $repeat_password = $_POST['repeat_password'];

                $email = $user['email'];
                $token = bin2hex(random_bytes(128));

                $sql = "SELECT * FROM changes INNER JOIN users USING (id_user) WHERE id_user = ? AND DATE_ADD(change_date, INTERVAL 5 MINUTE) > CURRENT_TIMESTAMP() AND old_password IS NOT NULL";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$user['id_user']]);
                if ($stmt -> get_result() -> num_rows > 0) {
                    throw new Exception();
                }

                require_once './linex_hash.php';

                if (!linex_verify($old_password, $user['password'], 'base64')) {
                    throw new Exception();
                }
        
                if ($password !== $repeat_password) {
                    throw new Exception();
                }

                require_once 'linex_hash.php';
        
                $sql = "UPDATE users SET password = ? WHERE id_user = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([linex_hash($password, null, 'base64'), $user['id_user']]);
        
                $sql = "INSERT INTO changes (id_change, id_user, old_password) VALUES (?, ?, ?)";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$token, $user['id_user'], $old_password]);
        
                require_once 'email_sender.php';
        
                send_email($user['email'], 'Geydrop - Password change', "<h1>Password change</h1><br>Your password in Geydrop has been changed.<br>If it was you, ignore this email.<br>If it wasn't you, click this link: <a href='https://lopeklol.fanth.pl/keydrop/changes/$token'>https://lopeklol.fanth.pl/keydrop/changes/$token</a> to undo changes and logout everywhere, then immediately change your password.<br><br>If the link doesn't work, copy and paste it into your browser.");
                $password_result = 'Password changed successfully!';
                $connection -> commit();
            } catch (Exception) {
                $connection -> rollback();
                $password_result = 'Old password is incorrect, new passwords do not match or you are changing passwords too fast (5 minutes).';
                $success = false;
            }
        }
        
        $connection -> close();
    }
?>

<div id="profile_manage_contents">
    <div id="change_user_form" class="profile_form">
        <h3>Change username:</h3>
        <form action="./profile" method="POST">
            <label for="repeat_password">New username:</label><br>
            <input type="text" name="username" minlength="5" maxlength="20" pattern="[A-Za-z0123456789_]+" required><br>
            <input type="submit" value="Change username" class="yellow_button">
            <p style="color: <?php print($success ? 'green' : 'red'); ?>; text-align: center;"><?php print($username_result ?? ''); ?></p>
        </form>
    </div>

    <div id="change_email_form" class="profile_form">
        <h3>Change email address:</h3>
        <form action="./profile" method="POST">
            <label for="email">New email:</label><br>
            <input type="email" name="email" required><br>
            <input type="submit" value="Change email address" class="yellow_button">
            <p style="color: <?php print($success ? 'green' : 'red'); ?>; text-align: center;"><?php print($email_result ?? ''); ?></p>
        </form>
    </div>

    <div id="change_pass_form" class="profile_form">
        <h3>Change password:</h3>
        <form action="./profile" method="POST">
            <label for="old_password">Old password:</label><br>
            <input type="password" name="old_password" minlength="8" required><br>
            <label for="password">New password:</label><br>
            <input type="password" name="password" minlength="8" required><br>
            <label for="repeat_password">Repeat password:</label><br>
            <input type="password" name="repeat_password" minlength="8" required><br>
            <input type="submit" value="Change password" class="yellow_button">
            <p style="color: <?php print($success ? 'green' : 'red'); ?>; text-align: center;"><?php print($password_result ?? ''); ?></p>
        </form>
    </div>

    <div id="profile_buttons">
        <a href="./logout"><button class="profile_button red_button button">LOGOUT</button></a>
        <a href="./logout_everywhere"><button class="profile_button red_button button">LOGOUT EVERYWHERE</button></a>
        <a href="./delete_account"><button class="profile_button dark_red_button button">DELETE ACCOUNT</button></a>
    </div>
</div>