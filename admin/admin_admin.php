<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>

    <body>

        <section class = 'panel' align="center">

            <?php include_once 'admin_buttons.php'; ?>

            <br>
            <br>

            <div class = 'panel_title' id = 'admins_title'>
                AMMINISTRAZIONE ADMIN
            </div>

            <br>

            <form align="center" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                <label>
                    <input name = 'inputSearch' type="text"/>
                </label>
                <label>
                    <select class="option" name="method">
                        <option value="all">Tutto</option>
                        <option value="username">Username</option>
                        <option value="name">Nome e Cognome</option>
                    </select>
                </label>

                <input hidden name = 'admins_button'/>

                <button type="submit" name = 'searchAdmin'>Cerca</button>
            </form>

            <?php

            try {
                $database_handler = new PDO ($dns);
                $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }

            $query = '';

            if (isset ($_POST['searchAdmin']) && !empty($_POST['inputSearch'])) {

                if (isset($_POST['method'])) {
                    $input = strtoupper($_POST['inputSearch']);

                    switch ($_POST['method']) {

                        case ('all'): {
                            $query = "
                                SELECT *
                                FROM user_table
                                WHERE admin is true AND (upper(login) LIKE :input OR upper(name) LIKE :input OR upper(surname) LIKE :input) 
                                GROUP BY login
                                ORDER BY login ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "%$input%", PDO::PARAM_STR);
                            break;
                        }

                        case ('username'): {
                            $query = "
                                SELECT *
                                FROM user_table
                                WHERE upper(login) LIKE :input AND admin is true 
                                ORDER BY login ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "%$input%", PDO::PARAM_STR);
                            break;
                        }

                        case ('name'): {
                            $query = "
                                SELECT *
                                FROM user_table
                                WHERE (upper(name) LIKE :input or upper(surname) LIKE :input) AND admin is true
                                GROUP BY login
                                ORDER BY login ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "%$input%", PDO::PARAM_STR);
                            break;
                        }

                        default: {
                            $query = "
                                SELECT *
                                FROM user_table
                                WHERE admin is true
                                GROUP BY login
                                ORDER BY login ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            break;
                        }
                    }
                }
            }

            else {
                $query = "
                SELECT *
                FROM user_table
                WHERE admin is true
                GROUP BY login
                ORDER BY login ASC
                ";
                $statement = $database_handler->prepare($query);
            }


            $database_handler->beginTransaction();
            $statement->execute();
            $database_handler->commit();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $database_handler = null;
            ?>

            <?php if ($result != null): ?>

            <table class = 'box' id = 'admins_table' align="center" width="100%">

                <tr>
                    <th>User</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Indirizzo</th>
                    <th>Telefono</th>
                    <th>Modifica </th>
                </tr>

                <?php foreach ($result as $row): ?>

                <tr <?php if ($row['login'] == $_SESSION['username']) echo 'style="font-weight: bold"'; ?>>
                    <td><?php echo $row['login']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['surname']; ?></td>
                    <td><?php echo $row['adress']; ?></td>
                    <td><?php $row['phone'] == null ? print"-" : print $row['phone']; ?></td>
                    <td>
                        <form>
                            <br>
                            <a href="edit_admin.php?login=<?php echo $row['login'];?>">
                                <button type="button" class = 'action_button'>
                                    <img src="../web/icons/edit.png" height= '20' style="vertical-align: middle"/>
                                </button>
                            </a>
                        </form>
                    </td>
                </tr>

                <?php endforeach; ?>

            </table>

            <?php
            else:
                echo "<div class=error_messages>Nessun risultato corrisponde alla ricerca.</div>";
            endif;
            ?>

            <br>
            <br>

            <div align="right">

               <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST" style="display: inline-block">
                    <button type= 'submit' name = 'admins_button' class = 'action_button'>
                        <img src="../web/icons/show_all.png" height= '20' style="vertical-align: middle"/>
                        Mostra Tutto
                    </button>
                </form>

                <form action="<?php echo htmlentities("add_admin.php");?>" style="display: inline-block">
                    <button class = 'action_button'>
                        <img src="../web/icons/add_admin.png" height= '20' style="vertical-align: middle"/>
                        Aggiungi Admin
                    </button>
                </form>

            </div>

        </section>

    </body>

</html>