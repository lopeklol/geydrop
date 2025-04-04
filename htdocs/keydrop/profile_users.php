<div id="profile_manage_contents">
    <table id="user_table">
        <tr>
            <th>ID</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Manage</th>
        </tr>
        <?php
            require "./connection.php";

            $sql = "SELECT id_user, firstName, lastName, username, email FROM users";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute();
            $users = $stmt -> get_result();

            foreach ($users as $user) {
                ?>
                    <tr>
                        <td><?php print(htmlspecialchars($user['id_user'])); ?></td>
                        <td><?php print(htmlspecialchars($user['firstName'])); ?></td>
                        <td><?php print(htmlspecialchars($user['lastName'])); ?></td>
                        <td><?php print(htmlspecialchars($user['username'])); ?></td>
                        <td><?php print(htmlspecialchars($user['email'])); ?></td>
                        <td class="manage_cell"><a href="./manage_user/<?php print(htmlspecialchars($user['id_user'])); ?>">Manage</a></td>
                    </tr>
                <?php
            }
        ?>
    </table>
</div>