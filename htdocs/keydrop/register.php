<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
    session_start();
    require_once "linex_hash.php";
    require_once "error_generator.php";
    
    if (isset($_SESSION['session_id'])) {
        header("Location: ./");
    }

    function connectToServer() {
        global $errorMsg;
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

        if (false) { // !($captcha_success -> success) - disabled, because captcha doesn't work on localhost
            $errorMsg = generateErrorMsg('reCaptcha verification failed!');
            return;
        }
        
        if (in_array('', [$_POST['firstName'], $_POST['lastName'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['password_repeat']])) {
            $errorMsg = generateErrorMsg('You have to fill all inputs!');
            return;
        }
        if (!isset($_POST['regulations'])) {
            $errorMsg = generateErrorMsg('You have to accept our terms of service!');
            return;
        }
        if (!isset($_POST['age_verify'])) {
            $errorMsg = generateErrorMsg('You have to be over 18 to use our service!');
            return;
        }

        require './bad_words.php';
        $is_bad_word = false;
        foreach ($bad_words as $word) {
            if (str_contains(strtolower($_POST['username']), strtolower($word))) {
                $is_bad_word = true;
                break;
            }
        }
        if ($is_bad_word) {
            $errorMsg = generateErrorMsg('Your username contains inappropriate words!');
            $_POST['username'] = '';
            return;
        }
        if ($_POST['password'] != $_POST['password_repeat']) {
            $errorMsg = generateErrorMsg('Passwords do not match!');
            return;
        }
        
        require "./connection.php";
        
        $sql = "SELECT * FROM users WHERE username = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_POST['username']]);
        
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('User with the same username already exists!');
            $connection -> close();
            $_POST['username'] = '';
            return;
        }
        
        $sql = "SELECT * FROM users WHERE email = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_POST['email']]);
        
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('User with the same email adress already exists!');
            $connection -> close();
            $_POST['email'] = '';
            return;
        }
        
        $sql = "INSERT INTO users (username, password, firstName, lastName, email) VALUES (?, ?, ?, ?, ?);";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_POST['username'], linex_hash($_POST['password'], encoding: 'base64'), $_POST['firstName'], $_POST['lastName'], $_POST['email']]);
        
        $sql = "SELECT id_user FROM users WHERE username = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_POST['username']]);
        $userid = $stmt -> get_result() -> fetch_row()[0];
        
        $sessionid = bin2hex(random_bytes(64));
        
        $sql = "INSERT INTO user_sessions (id_session, id_user) VALUES (?, ?)";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$sessionid, $userid]);
        
        $connection -> close();
        
        $_SESSION['session_id'] = $sessionid;
        
        header("Location: ./");
    }
    
    $errorMsg = '';
    connectToServer();
    ?>

<?php require_once 'header.php'; ?>

<div id="register_form">
    <div id="register_form_contents" class="login_register_contents">
        <form action="" method="POST">
            <label for="firstName">First name:</label><br>
            <input value="<?php print(htmlspecialchars($_POST['firstName'] ?? '')); ?>" type="text" name="firstName" minlength="3" maxlength="25" pattern="[A-Za-zęółśążźćńĘÓŁŚĄŻŹĆŃ]+" required><br>

            <label for="lastName">Last name:</label><br>
            <input value="<?php print(htmlspecialchars($_POST['lastName'] ?? '')); ?>" type="text" name="lastName" minlength="3" maxlength="50" pattern="[A-Za-zęółśążźćńĘÓŁŚĄŻŹĆŃ]+" required><br>

            <label for="username">Username:</label><br>
            <span style="font-size: 11px;">(allowed signs in username: letters, numbers and underscore)</span><br>
            <input value="<?php print(htmlspecialchars($_POST['username'] ?? '')); ?>" type="text" name="username" minlength="5" maxlength="20" pattern="[A-Za-z0123456789_]+" required><br>
            
            <label for="email">E-mail:</label><br>
            <input value="<?php print(htmlspecialchars($_POST['email'] ?? '')); ?>" type="email" name="email" required><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" minlength="8" required><br>

            <label for="password_repeat">Repeat password:</label><br>
            <input type="password" name="password_repeat" minlength="8" required><br>

            <label for="regulations">I accept <a href="./tos" target="_blank">terms of service</a>:</label>
            <input <?php print(isset($_POST['regulations']) ? 'checked' : ''); ?> type="checkbox" name="regulations" required><br>
            
            <label for="age_verify">I am over 18 years old:</label>
            <input <?php print(isset($_POST['age_verify']) ? 'checked' : ''); ?> type="checkbox" name="age_verify" required><br>

            <div class="g-recaptcha" data-sitekey="<?php echo $captcha_site_key; ?>"></div><br>

            <?php print($errorMsg); ?>
            
            <input type="submit" id="register" value="Register">
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>