<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    session_start();
    require './connection.php';

    $stmt = $connection -> prepare('SELECT users.* FROM user_sessions INNER JOIN users USING(id_user) WHERE id_session = ?;');
    $stmt -> execute([$_SESSION['session_id']]);
    $id_admin = $stmt -> get_result() -> fetch_row()[0];

    $connection -> close();

    if ($id_admin === 0) {
        http_response_code(403);
        require_once './error_403.php';
        return;
    }

    require './connection.php';

    $stmt = $connection -> prepare('SELECT * FROM users WHERE id_user = ? LIMIT 1;');
    $stmt -> execute([$_GET['id']]);
    $result = $stmt -> get_result();

    if ($result -> num_rows === 0) {
        header('Location: /keydrop/profile.php');
        return;
    }

    $user = $result -> fetch_assoc();

    $errorMsg = '';
    $errorMsgBan = '';
    $errorMsgOther = '';

    function connectToServer() {
        global $errorMsg, $errorMsgBan, $user;
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return;
        }

        require_once "./load_env.php";

        $secret = getenv('RECAPTCHA_SECRET_KEY');
        $captcha_site_key = getenv('RECAPTCHA_SITE_KEY');
        $response = $_POST['g-recaptcha-response'];
        $remoteip = $_SERVER['REMOTE_ADDR'];

        $data = [
            'secret' => $secret,
            'response' => $response,
            'remoteip' => $remoteip
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context);
        $captcha_success = json_decode($verify);

        require_once './error_generator.php';

        if (false) { // !($captcha_success -> success) - disabled, because captcha doesn't work on localhost
            $errorMsg = generateErrorMsg('reCaptcha verification failed!');
            $errorMsgBan = generateErrorMsg('reCaptcha verification failed!');
            $errorMsgOther = generateErrorMsg('reCaptcha verification failed!');
            return;
        }

        if (isset($_POST['ban_time'])) {
            $id_to_ban = $user['id_user'];
            require './ban_engine.php';
            try {
                ban(isset($_POST['terminate_button']) ? 'terminate' : 'ban', $_POST['ban_time'], $_POST['ban_reason'] === '' ? null : $_POST['ban_reason'], $id_to_ban);        
                header("Location: /keydrop/profile");
                return;
            } catch (Exception) {
                $errorMsgBan = generateErrorMsg('Invalid ban time!');
                return;
            }
        }

        require './connection.php';

        if (isset($_POST['unban_button'])) {
            $stmt = $connection -> prepare('DELETE FROM bans WHERE id_user = ?;');
            $stmt -> execute([$user['id_user']]);
            return;
        }
        
        if (isset($_POST['delete_button'])) {
            $stmt = $connection -> prepare('DELETE FROM users WHERE id_user = ?;');
            $stmt -> execute([$user['id_user']]);
            return;
        }

        if (in_array('', [$_POST['firstName'], $_POST['lastName'], $_POST['username'], $_POST['money'], $_POST['email'], $_POST['luck'], $_POST['id_role']])) {
            $errorMsg = generateErrorMsg('You have to fill all inputs!');
            return;
        }

        if ($_POST['password'] !== $_POST['password_repeat']) {
            $errorMsg = generateErrorMsg('Passwords doesn\'t match!');
            return;
        }

        $stmt = $connection -> prepare('SELECT id_role FROM roles WHERE id_role = ?;');
        $stmt -> execute([$_POST['id_role']]);
        if ($stmt -> get_result() -> num_rows === 0) {
            $errorMsg = generateErrorMsg('Role with this ID, does not exist.');
            return;
        }

        $stmt = $connection -> prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
        $stmt -> execute([$_POST['username'], $user['id_user']]);
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('User with this username already exists.');
            return;
        }

        $stmt = $connection -> prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
        $stmt -> execute([$_POST['email'], $user['id_user']]);
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('User with this email already exists.');
            return;
        }

        if ($_POST['password'] === '') {
            $stmt = $connection -> prepare('UPDATE users SET firstName = ?, lastName = ?, username = ?, email = ?, money = ?, luck = ?, dedicated_ad = ?, id_role = ? WHERE id_user = ?');
            $stmt -> execute([$_POST['firstName'], $_POST['lastName'], $_POST['username'], $_POST['email'], $_POST['money'], $_POST['luck'], $_POST['dedicated_ad'] === '' ? NULL : $_POST['dedicated_ad'], $_POST['id_role'], $user['id_user']]);
        } else {
            require_once './linex_hash.php';
            $stmt = $connection -> prepare('UPDATE users SET firstName = ?, lastName = ?, username = ?, email = ?, money = ?, luck = ?, dedicated_ad = ?, id_role = ? , password = ? WHERE id_user = ?');
            $stmt -> execute([$_POST['firstName'], $_POST['lastName'], $_POST['username'], $_POST['email'], $_POST['money'], $_POST['luck'], $_POST['dedicated_ad'] === '' ? NULL : $_POST['dedicated_ad'], $_POST['id_role'], linex_hash($_POST['password'], null, 'base64'), $user['id_user']]);
        }
        $connection -> close();
        
        header("Location: /keydrop/profile");
        return;
    }

    connectToServer();
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/header.php'; ?>

<div id="manage_container" class="login_register_contents">
    <form action="" method="POST" class="manage_form">
        <label for="firstName">First name:</label><br>
        <input value="<?php print(htmlspecialchars($_POST['firstName'] ?? $user['firstName'])); ?>" type="text" name="firstName" minlength="3" maxlength="25" pattern="[A-Za-zęółśążźćńĘÓŁŚĄŻŹĆŃ]+" required><br>

        <label for="lastName">Last name:</label><br>
        <input value="<?php print(htmlspecialchars($_POST['lastName'] ?? $user['lastName'])); ?>" type="text" name="lastName" minlength="3" maxlength="50" pattern="[A-Za-zęółśążźćńĘÓŁŚĄŻŹĆŃ]+" required><br>

        <label for="username">Username:</label><br>
        <span style="font-size: 11px;">(allowed signs in username: letters, numbers and underscore)</span><br>
        <input value="<?php print(htmlspecialchars($_POST['username'] ?? $user['username'])); ?>" type="text" name="username" minlength="5" maxlength="20" pattern="[A-Za-z0123456789_]+" required><br>
        
        <label for="email">E-mail:</label><br>
        <input value="<?php print(htmlspecialchars($_POST['email'] ?? $user['email'])); ?>" type="email" name="email" required><br>

        <label for="password">Password:</label><br>
        <span style="font-size: 11px;">(leave blank to don't change)</span><br>
        <input type="password" name="password" minlength="8"><br>

        <label for="password_repeat">Repeat password:</label><br>
        <input type="password" name="password_repeat" minlength="8"><br>
        
        <label for="money">Money:</label><br>
        <input value="<?php print(htmlspecialchars($_POST['money'] ?? $user['money'])); ?>" type="number" name="money" min="0" max="9999999999" step="0.01" required><br>

        <label for="luck">Luck:</label><br>
        <span style="font-size: 11px;">(allowed range: from 0 to 99999, default for user: 100)</span><br>
        <input value="<?php print(htmlspecialchars($_POST['luck'] ?? $user['luck'])); ?>" type="number" name="luck" min="0" max="99999" step="0.01" required><br>
        
        <label for="dedicated_ad">Dedicated ad ID:</label><br>
        <span style="font-size: 11px;">(leave blank for NULL - no dedicated ad)</span><br>
        <input value="<?php print(htmlspecialchars($_POST['dedicated_ad'] ?? $user['dedicated_ad'] ?? '')); ?>" type="number" min="1" max="5" name="dedicated_ad"><br>

        <label for="id_role">Role:</label><br>
        <select id="form_select" name="id_role">
            <option value="" disabled>Select role</option>
            <?php
                $stmt = $connection -> prepare('SELECT id_role, role_name FROM roles;');
                $stmt -> execute();
                $roles = $stmt -> get_result();
                
                foreach ($roles as $role) {
                    ?>
                        <option value="<?php echo $role['id_role'] ?>" <?php echo $role['id_role'] === $user['id_role'] ? 'selected' : '' ?>><?php echo $role['role_name'] ?></option>
                    <?php
                }
            ?>
        </select>

        <div class="g-recaptcha" data-sitekey="<?php echo $captcha_site_key; ?>"></div><br>
        
        <?php print($errorMsg); ?>
        
        <input type="submit" id="manage_submit" class="button green_button" value="Save">
    </form>
    <form action="" method="POST" class="manage_form">
        <label for="ban_time">Ban time:</label><br>
        <span style="font-size: 11px;">(examples: 1h = 1 hour, 3mo = 3 months, ignored if terminating)</span><br>
        <input type="text" minlength="2" name="ban_time"><br>

        <label for="ban_reason">Reason:</label><br>
        <span style="font-size: 11px;">(leave blank to give no reason)</span><br>
        <input type="text" name="ban_reason"><br>

        <div class="g-recaptcha" data-sitekey="<?php echo $captcha_site_key; ?>"></div><br>
        
        <?php print($errorMsgBan); ?>
        
        <input type="submit" id="manage_submit" class="button yellow_button" name="ban_button" value="Ban" style="margin-bottom: 20px;">
        <input type="submit" id="manage_submit" class="button red_button" name="terminate_button" value="Terminate">
    </form>
    <form action="" method="POST" class="manage_form">
        <label style="display: block; margin: 0px 0px 10px 0px;">More options:</label>
        <div class="g-recaptcha" data-sitekey="<?php echo $captcha_site_key; ?>"></div><br>

        <?php print($errorMsgOther); ?>
        
        <input type="submit" id="manage_submit" class="button blue_button" name="unban_button" value="Unban" style="margin-bottom: 20px;">
        <input type="submit" id="manage_submit" class="button dark_red_button" name="delete_button" value="Delete account">
    </form>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/footer.php'; ?>