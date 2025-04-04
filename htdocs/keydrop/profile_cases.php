<div id="profile_manage_contents">
    <table id="user_table">
        <tr>
            <th>ID</th>
            <th>Case name</th>
            <th>Case value</th>
            <th>Manage case</th>
            <!-- <th>Manage contents</th> -->
        </tr>
        <?php
            require "./connection.php";

            $sql = "SELECT id_case, case_name, case_value FROM cases";
            $stmt = $connection -> prepare($sql);
            $stmt -> execute();
            $users = $stmt -> get_result();

            foreach ($users as $user) {
                ?>
                    <tr>
                        <td><?php print(htmlspecialchars($user['id_case'])); ?></td>
                        <td><?php print(htmlspecialchars($user['case_name'])); ?></td>
                        <td><?php print(htmlspecialchars($user['case_value'])); ?></td>
                        <td class="manage_cell"><a href="./manage_case/<?php print(htmlspecialchars($user['id_case'])); ?>">Manage case</a></td>
                        <!-- <td class="manage_cell"><a href="./manage_contents/<?php // print(htmlspecialchars($user['id_case'])); ?>">Manage contents</a></td> -->
                    </tr>
                <?php
            }
        ?>
    </table>
    <div style="width: 100%; text-align: center; margin-top: 20px;">
        <a href="./add_case"><button class="button green_button" style="height: 50px">Add new case</button></a>
    </div>
</div>