<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_COOKIE['redirect'])) {
        header('Location: https://www.finanse.mf.gov.pl/inne-podatki/podatek-od-gier-gry-hazardowe/komunikat');
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/keydrop/clear_unactive_sessions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/keydrop/check_session_validity.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/keydrop/check_ban.php";

    if (!in_array(basename($_SERVER['PHP_SELF']), ['changes.php', 'delete_account.php', 'tos.php', 'login.php', 'register.php', 'index.php', 'changes', 'delete_account', 'tos', 'login', 'register', 'index'])) {
        if (!isset($_SESSION['session_id'])) {
            $current_url = urlencode($_SERVER['REQUEST_URI']);
            http_response_code(403);
            header("Location: /keydrop/login?redirect=$current_url");
            exit();
        }
    } else if (in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php']) && isset($_SESSION['session_id'])) {
        header("Location: /keydrop/");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geydrop - Najlepsze skiny do CS2!</title>
    <link rel="stylesheet" href="/keydrop/style.css">
    <link rel="icon" type="image/x-icon" href="/keydrop/images/icon.ico">
    <script src="/keydrop/script.js" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php
        $case_name = rawurldecode($_GET['case'] ?? '');

        if ($case_name === 'Szucka\'s Coffin') {
            $end_item_gradient = 0.1;
            ?>
            <style>
                main {
                    background-image: url('/keydrop/images/graveyard.jpg') !important;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                }

                #case_page {
                    background-color: rgba(26, 25, 31, 0.5);
                }

                #case_contents_items {
                    background-color: transparent;
                }

                #case_contents {
                    background-color: rgba(0, 0, 0, 0.3);
                }

                main img {
                    opacity: 0.25;
                }

                .item_chance {
                    opacity: 0.4;
                }
            </style>
    <?php } ?>
</head>
<body>
    <div id="abContainer" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: black; z-index: 9999;">
        <video id="abVideo" style="width: 100%; height: 100%;" controls></video>
    </div>
    <div id="page">
        <header>
            <nav>
                <div id="nav_left">
                    <a href="/keydrop/"><img src="/keydrop/images/logo.webp" alt="logo" id="logo"></a>
                    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/keydrop/country_select.php' ?>
                </div>
                <div id="nav_right">
                    <?php
                        if (!isset($_SESSION['session_id'])) {
                        ?>
                            <button onclick='window.location.href = "/keydrop/login";' class="yellow_button button">LOG IN</button>
                            <button onclick='window.location.href = "/keydrop/register";' class="yellow_button button">REGISTER</button>
                        <?php      
                        } else {
                            require $_SERVER['DOCUMENT_ROOT'] . "/keydrop/connection.php";

                            $sql = "SELECT money FROM user_sessions INNER JOIN users USING(id_user) WHERE id_session = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> execute([$_SESSION['session_id']]);
                            $money = $stmt -> get_result() -> fetch_row()[0];
                            ?>
                                <div id="profile_container">
                                    <div id="money_container">
                                        <div id="money_text"><span id="money_value"><?php print(htmlspecialchars($money)); ?></span> USD</div>
                                        <a href="/keydrop/add_funds"><button id="add_founds_button">ADD FUNDS</button></a>
                                    </div>
                                    <a href="/keydrop/profile" id="manage_profile">
                                        <img src="/keydrop/images/settings.png" alt="Manage profile" id="manage_profile_button">
                                    </a>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </nav>
        </header>
        <main>