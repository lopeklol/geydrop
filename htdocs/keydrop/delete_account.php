<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    session_start();
    require_once "linex_hash.php";
    require_once "error_generator.php";

    $show_form = isset($_SESSION['session_id']) ? true : false;

    if (!isset($_SESSION['session_id']) && !isset($_GET['token'])) {
        header('Location: login/%2Fkeydrop%2Fdelete_account');
    }

    function connectToServer() {
        global $errorMsg, $show_form;
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            if (isset($_GET['token'])) {
                require "./connection.php";

                $show_form = false;
                
                $sql = "SELECT id_user FROM delete_requests WHERE id_request = ?;";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$_GET['token']]);
                $result = $stmt -> get_result();

                if ($result -> num_rows === 0) {
                    $errorMsg = "<p style='text-align: center; color: red; font-size: 24px;'>
                                    Incorrect delete token!
                                <p>";
                    return;
                }

                $sql = "DELETE FROM users WHERE id_user = ?;";
                $stmt = $connection -> prepare($sql);
                $stmt -> execute([$result -> fetch_row()[0]]);

                $errorMsg = "<p style='text-align: center; color: green; font-size: 24px;'>
                                Your account has beed deleted successfully!
                            <p>";
            }
            return;
        }

        if (in_array('', [$_POST['password']])) {
            $errorMsg = generateErrorMsg('You have to enter your password to continue!');
            return;
        }
        
        require "./connection.php";

        $sql = "SELECT id_user, email, password FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?;";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$_SESSION['session_id']]);
        $user = $stmt -> get_result() -> fetch_assoc();

        if (!linex_verify($_POST['password'], $user['password'], encoding: 'base64')) {
            $errorMsg = generateErrorMsg('Wrong password!');
            $connection -> close();
            return;
        }

        $sql = "SELECT * FROM delete_requests INNER JOIN users USING (id_user) WHERE id_user = ? AND DATE_ADD(request_date, INTERVAL 10 MINUTE) > CURRENT_TIMESTAMP()";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$user['id_user']]);
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('You can send request once every 10 minutes.');
            return;
        }

        $requestid = bin2hex(random_bytes(64));

        require_once 'email_sender.php';

        send_email($user['email'], "Delete account on Geydrop", "<h1>Account deletion</h1><br>You are about to delete your account on Geydrop.<br>Remember that if you delete your account there is NO GOING BACK! Your every item and whole money will be LOST!<br>If you are ABSOLUTELY SURE you want to continue, click this link: <a href='https://lopeklol.fanth.pl/keydrop/delete_account?token=$requestid'>https://lopeklol.fanth.pl/keydrop/delete_account?token=$requestid</a>.<br><br>If the link doesn't work, copy and paste it into your browser.");

        $sql = "INSERT INTO delete_requests (id_request, id_user) VALUES (?, ?)";
        $stmt = $connection -> prepare($sql);
        $stmt -> execute([$requestid, $user['id_user']]);

        $connection -> close();

        $errorMsg = "<p style='text-align: center; color: green;'>
                        Request created successfully! Now log in to your email and click the link to finish the process.
                    <p>";

    }

    $errorMsg = '';
    connectToServer();
?>

<?php require_once 'header.php'; ?>

<div id="login_form">
    <div id="login_form_contents" class="login_register_contents">
        <form action="" method="POST">
            <?php if ($show_form) { ?>
                <h2 style="text-align: center; color: darkred; margin-top: 0px;">
                    WARNING:
                </h2>
                <p style="text-align: center; color: red;">
                    If you delete your account, your every item and money on this account will be lost! We are <strong>NOT RESPONSIBLE</strong> for any lost items/money.
                    By continuing, you confirm that you read and agree to these terms.
                </p>
                <p style="text-align: center; color: red;">
                    Enter your password below to continue:
                </p>

                <label for="password">Password:</label><br>
                <input type="password" name="password" minlength="8" required style="margin-bottom: 20px;"><br>

                <input type="submit" id="del_acc_button" value="Delete account" style="margin-bottom: 20px;">
            <?php } ?>

            <?php print($errorMsg); ?>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>