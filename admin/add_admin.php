<?php
include_once "..\config\configurations.php";

if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $_SESSION['user'] != 'admin')) {
    header ('Location: ..\utils\logout.php');
}
?>

<html>

    <body>

    <?php
    include_once '..\templates\header.php';
    ?>

        <section class= "main_content">

            <br>
            <br>
            <div class = 'admin_layout' align="center">

                <section class="panel">

                    <?php include_once 'admin_buttons.php'; ?>

                    <br>
                    <br>

                    <div class = 'panel_title' id = 'admins_title'>
                        AGGIUNTA NUOVO ADMIN
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
                        <button type="submit" name = 'search_admin'>Cerca</button>
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

                    if (isset ($_POST['search_admin']) && !empty($_POST['inputSearch'])) {

                        if (isset($_POST['method'])) {

                            $input = strtoupper($_POST['inputSearch']);

                            switch ($_POST['method']) {

                                case ('all'): {
                                    $query = "
                                        SELECT *
                                        FROM user_table
                                        WHERE admin is false AND (upper(login) LIKE :input OR upper(name) LIKE :input OR upper(surname) LIKE :input) 
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
                                        WHERE upper(login) LIKE :input AND admin is false 
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
                                        WHERE (upper(name) LIKE :input or upper(surname) LIKE :input) AND admin is false
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
                                        WHERE admin is false
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
                            WHERE admin is false
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
                            <th>Promuovi Utente</th>
                        </tr>

                        <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?php echo $row['login']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['surname']; ?></td>
                            <td><?php echo $row['adress']; ?></td>
                            <td><?php $row['phone'] == null ? print"-" : print $row['phone']; ?></td>

                            <td>
                                <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST">
                                    <br>
                                    <button type="submit" class = 'action_button' name="add_admin" value="<?php echo $row['login']; ?>">
                                        <img src="../web/icons/add_admin.png" height= '20' style="vertical-align: middle"/>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </table>

                    <?php else:

                        echo "<div class=error_messages>Nessun risultato corrisponde alla ricerca.</div>";

                    endif;?>

                    <br>
                    <br>

                    <div align="right">
                        <a href="add_admin.php">
                            <button type= 'submit' name = 'users_button' class = 'action_button'>
                                <img src="../web/icons/show_all.png" height= '20' style="vertical-align: middle"/>
                                Mostra Tutto
                            </button>
                        </a>
                    </div>


                    <div class="error_messages">
                    <?php

                    if (isset($_POST['add_admin'])) {

                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }

                        try {
                            $query = "
                            UPDATE user_table
                            SET admin = true 
                            WHERE login = :login
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':login', $_POST['add_admin']);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $result = $statement->fetch();
                            header("Refresh: 4; url = admin_panel.php");
                            echo 'Promozione nuovo admin effettuata.';
                        } catch (PDOException $e) {
                            echo 'Promozione nuovo admin non riuscita.';
                        }

                        $database_handler = null;
                    }

                    ?>

                    </div>
                </section>
            </div>
        </section>

    <?php
    include_once '..\templates\footer.php';
    ?>

    </body>

</html>