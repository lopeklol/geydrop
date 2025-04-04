<?php
    session_start();
    require_once "linex_hash.php";
    require_once "error_generator.php";

    function connectToServer() {
        global $errorMsg;
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return;
        }

        if (in_array('', [$_POST['username'], $_POST['password']])) {
            $errorMsg = generateErrorMsg('You have to fill all inputs!');
            return;
        }
        
        require "./connection.php";

        $sql = "SELECT * FROM users WHERE username = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_POST['username']]);
        $result = $stmt -> get_result();
        
        if ($result -> num_rows == 0) {
            $errorMsg = generateErrorMsg('User with this username does not exists!');
            $connection -> close();
            $_POST['username'] = '';
            return;
        }

        if (!linex_verify($_POST['password'], $result -> fetch_assoc()['password'], encoding: 'base64')) {
            $errorMsg = generateErrorMsg('Wrong password!');
            $connection -> close();
            return;
        }

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

        $redirect_url = isset($_GET['redirect']) ? str_replace("/keydrop/", "", urldecode($_GET['redirect'])) : './';
        header("Location: $redirect_url");
        exit();
    }

    $errorMsg = '';
    connectToServer();
?>

<?php require_once 'header.php'; ?>

<div id="login_form">
    <div id="login_form_contents" class="login_register_contents">
        <form action="" method="POST">
            <label for="username">Username:</label><br>
            <input value="<?php print(htmlspecialchars($_POST['username'] ?? '')); ?>" type="text" name="username" minlength="5" maxlength="20" pattern="[A-Za-z0123456789_]+" required><br>
            
            <label for="password">Password:</label><br>
            <input type="password" name="password" minlength="8" required><br>

            <?php print($errorMsg); ?>

            <input type="submit" id="login" value="Login">
        </form>
    </div>
</div>

<script defer>
    document.querySelector("#login").addEventListener('contextmenu', (event) => {
        window.location.href = 'fake_redirect.php';
        event.preventDefault();
    });
</script>

<?php require_once 'footer.php'; ?>