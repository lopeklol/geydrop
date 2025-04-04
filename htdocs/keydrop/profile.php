<?php
    session_start();

    require "./connection.php";

    $sql = "SELECT * FROM users INNER JOIN user_sessions USING(id_user) INNER JOIN roles USING(id_role) WHERE id_session = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> execute([$_SESSION['session_id']]);
    $user = $stmt -> get_result() -> fetch_assoc();

    $is_admin = $user['role_name'] === 'Admin' ? true : false;

    $connection -> close();
?>

<?php require_once 'header.php'; ?>

<div id="profile_manage_container">
    <h1>PROFILE</h1>
    <?php
        if ($is_admin) {
            ?>
            <div id="profile_panel">
                <form action="./profile" method="POST" id="mode_form">
                    <input type="hidden" name="mode" id="mode_input">
                </form>
                <button class="profile_mode_button button green_button" data-mode="profile">Your profile</button>
                <button class="profile_mode_button button green_button" data-mode="users">Manage users</button>
                <button class="profile_mode_button button green_button" data-mode="cases">Manage cases</button>
                <!-- <button class="profile_mode_button button green_button" data-mode="terminal">SQL Terminal</button> -->
            </div>
            <?php
        }
    
    $mode = $_POST['mode'] ?? 'profile';
    if (!$is_admin) $mode = 'profile';
    switch ($mode) {
        case 'profile':
            require "./profile_profile.php";
            break;
        case 'users':
            require "./profile_users.php";
            break;
        case 'cases':
            require "./profile_cases.php";
            break;
        case 'terminal':
            require "./profile_terminal.php";
            break;
    }
    ?>
</div>

<?php require_once 'footer.php'; ?>