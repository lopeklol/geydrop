<?php
    $result = "";
    $result_nexis = "";
    $result_success = true;
    function addMoney() {
        global $result, $result_success;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            require_once 'connection.php';

            $sql = "SELECT users.id_user FROM users INNER JOIN user_sessions USING(id_user) WHERE id_session = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$_SESSION['session_id']]);
            $user = $stmt -> get_result() -> fetch_assoc();

            $user_id = $user['id_user'];

            $sql = "SELECT date FROM transactions WHERE id_user = ? AND DATE_ADD(date, INTERVAL 5 SECOND) > CURRENT_TIMESTAMP()";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute([$user_id]);
            $result = $stmt -> get_result();

            if ($result -> num_rows > 0) {
                $result = "You can add funds only once in 5 seconds!";
                $result_success = false;
                return;
            }

            $amount = $_POST['amount'] ?? null;
            $amount_nexis = $_POST['amount_nexis'] ?? null;

            $connection -> begin_transaction();
            
            try {
                $sql = "UPDATE users SET money = money + ? WHERE id_user = ?";
                $stmt = $connection -> prepare($sql);

                if ($amount !== null) {
                    if ($amount < 1) {
                        $result = "Amount must be greater than 0!";
                        $result_success = false;
                        return;
                    } else if ($amount > 999999) {
                        $result = "Amount must be less than 999999!";
                        $result_success = false;
                        return;
                    }
                    $stmt -> execute([$amount, $user_id]);
                } elseif ($amount_nexis !== null) {
                    if ($amount_nexis < 1) {
                        $result = "Amount must be greater than 0!";
                        $result_success = false;
                        return;
                    } else if ($amount_nexis > 999999) {
                        $result = "Amount must be less than 999999!";
                        $result_success = false;
                        return;
                    }
                    $stmt -> execute([$amount_nexis, $user_id]);
                }
                
                $sql = "INSERT INTO transactions (id_user, money, date) VALUES (?, ?, CURRENT_TIMESTAMP())";
                $stmt = $connection -> prepare($sql);

                if ($amount !== null) {
                    $stmt -> execute([$user_id, $amount]);
                    $result = "Successfully added ".htmlspecialchars($amount)." USD to your account!";
                } elseif ($amount_nexis !== null) {
                    $stmt -> execute([$user_id, $amount_nexis]);
                    $result = "Successfully added ".htmlspecialchars($amount_nexis)." USD to your account!";
                }

                $connection -> commit();
            } catch (Exception) {
                $connection -> rollback();
                $result = "Error while adding funds!";
                $result_success = false;
            }
        }
    }

    addMoney();
?>

<?php require_once 'header.php'; ?>

<div id="add_funds">
    <h1 id="funds_h1">ADD FUNDS</h1>
    <div id="add_funds_container">
        <form action="./add_funds" method="POST" id="add_funds_form">
            <h2>Add virtual money:</h2>
            <label for="amount" id="amount_label">Amount (in USD):</label><br>
            <input placeholder="max: 999999" type="number" name="amount" id="amount" min="1.00" max="999999.00" required><br>
            <button class="button green_button" id="add_funds_button" type="submit">Add funds</button>
        </form>
        <div id="add_funds_nexis" style="display: none;">
            <h2>Or use Nexis Bank:</h2>
            <div id="nexis_bank_container">
                <div id="nexis_logo_container">
                    <img src="images/nexisbank.png" alt="Nexis Bank Logo" id="nexis_bank_logo">
                </div>
                <form action="./add_funds" method="POST" id="nexis_bank_form">
                    <label for="amount" id="amount_label">Amount (in USD):</label><br>
                    <input placeholder="max: 999999" type="number" name="amount_nexis" id="amount_nexis" min="1.00" max="999999.00" required><br>
                    <button class="button blue_button" id="add_funds_nexis_button" type="submit">Redirect to bank</button>
                </form>
            </div>
        </div>
    </div>
    <p style="margin: 10px; font-size: 20px; color: <?php print($result_success ? 'green' : 'red') ?>;"><?php print($result); ?></p>
</div>

<?php require_once 'footer.php'; ?>