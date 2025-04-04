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

    $stmt = $connection -> prepare('SELECT * FROM cases WHERE id_case = ? LIMIT 1;');
    $stmt -> execute([$_GET['id']]);
    $result = $stmt -> get_result();

    if ($result -> num_rows === 0) {
        header('Location: /keydrop/profile.php');
        return;
    }

    $case = $result -> fetch_assoc();

    $errorMsg = '';

    function connectToServer() {
        global $errorMsg, $case;
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
            return;
        }

        require './connection.php';
        if (isset($_POST['delete'])) {
            $stmt = $connection -> prepare('DELETE FROM cases WHERE id_case = ?');
            $stmt -> execute([$_GET['id']]);
            $connection -> close();
            header("Location: /keydrop/profile");
            return;
        }

        if (!isset($_POST['case_name']) || !isset($_POST['case_value']) || in_array('', [$_POST['case_name'], $_POST['case_value']])) {
            $errorMsg = generateErrorMsg('You have to fill all inputs!');
            return;
        }

        $stmt = $connection -> prepare("SELECT case_name FROM cases WHERE case_name = ? AND id_case != ?");
        $stmt -> execute([$_POST['case_name'], $case['id_case']]);
        if ($stmt -> get_result() -> num_rows > 0) {
            $errorMsg = generateErrorMsg('Case with this name already exists.');
            return;
        }

        if (!isset($_FILES["case_image"]) || $_FILES["case_image"]["error"] == UPLOAD_ERR_NO_FILE) {
            $image_name = $case['image'];
        } else {
            $upload_dir = "./case_images/";
            $file_temp = $_FILES["case_image"]["tmp_name"];
            $file_name = $_FILES["case_image"]["name"];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = ["gif", "giff", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "apng", "png", "webp", "svg"];

            if (!in_array($file_extension, $allowed_extensions)) {
                $errorMsg = generateErrorMsg('Incorrect file extension!');
                return;
            }

            $allowed_types = ["image/gif", "image/jpeg", "image/apng", "image/png", "image/webp", "image/svg+xml"];
            $file_type = mime_content_type($file_temp);
            if (!in_array($file_type, $allowed_types)) {
                die("Don't even try to change file extension!");
            }

            $image_size = getimagesize($file_temp);
            $width = $image_size[0];
            $height = $image_size[1];
            if ($width !== 256 || $height !== 198) { 
                $errorMsg = generateErrorMsg('Image has to be in size 256x198!.');
                return;
            }

            $image_name = preg_replace('/[^A-Za-z0-9]+/', '_', $_POST['case_name']);

            $full_path = $upload_dir . basename($image_name . '.' . $file_extension);
            if (!move_uploaded_file($file_temp, $full_path)) {
                $errorMsg = generateErrorMsg('Failed to upload image!');
                return;
            }
        }

        $stmt = $connection -> prepare('UPDATE cases SET case_name = ?, case_value = ?, image = ?, for_ad = ?, hidden = ? WHERE id_case = ?');
        $stmt -> execute([$_POST['case_name'], $_POST['case_value'], $image_name, isset($_POST['for_ad']) ? true : false, isset($_POST['hidden']) ? true : false, $_GET['id']]);

        $connection -> close();
        
        header("Location: /keydrop/profile");
        return;
    }

    connectToServer();
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/header.php'; ?>

<div id="manage_container" class="login_register_contents">
    <form action="" method="POST" class="manage_form" enctype="multipart/form-data">
        <label for="case_name">Case name:</label><br>
        <span style="font-size: 11px;">(allowed signs in case name: letters, numbers, apostrophes and spaces)</span><br>
        <input value="<?php print(htmlspecialchars($_POST['case_name'] ?? $case['case_name'])); ?>" type="text" name="case_name" minlength="3" maxlength="25" pattern="[A-Za-z0123456789' ]+" required><br>

        <label for="case_value">Case value (price):</label><br>
        <span style="font-size: 11px;">(leave 0.00 if you want make this case unpurchasable)</span><br>
        <input value="<?php print(htmlspecialchars($_POST['case_value'] ?? $case['case_value'])); ?>" type="number" step="0.01" name="case_value" min="0.00" max="99999.99" required><br>

        <label for="case_image">Case image:</label><br>
        <div style="width: 100%; text-align: center;">
            <img src="/keydrop/case_images/<?php echo $case['image']; ?>" alt="Case image" width="256" height="198"><br>
        </div>
        <input type="file" name="case_image" accept=".gif, .giff, .jpg, .jpeg, .jfif, .pjpeg, .pjp, .apng, .png, .webp, .svg"><br>
        
        <label for="for_ad">For ad (can be opened for watching an ad?):</label>
        <input <?php print(htmlspecialchars(isset($_POST['for_ad']) || $case['for_ad'] ? 'checked' : '')); ?> type="checkbox" name="for_ad"><br>
        
        <label for="hidden">Hidden (should be hidden and only avaliable after typing its exact name?):</label>
        <input <?php print(htmlspecialchars(isset($_POST['hidden']) || $case['hidden'] ? 'checked' : '')); ?> type="checkbox" name="hidden"><br>

        <div class="g-recaptcha" data-sitekey="<?php echo $captcha_site_key; ?>"></div><br>
        
        <?php print($errorMsg); ?>
        
        <input type="submit" id="manage_submit" name="save" class="button green_button" value="Save">
        <input type="submit" id="manage_submit" name="delete" class="button red_button" value="Delete" style="margin-top: 20px;">
    </form>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/footer.php'; ?>