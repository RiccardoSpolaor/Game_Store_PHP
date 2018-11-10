<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>


    <body>

    <?php
    include_once '..\templates\header.php';
    ?>

    <?php
    if ($_SESSION['user'] != 'admin') {
        header ('Location: ../utils/logout.php');
    }
    ?>

        <section class = 'panel' align="center">

            <?php include_once 'admin_buttons.php'; ?>

            <br>
            <br>
            <div class = 'panel_title' id = 'orders_title'>
                AMMINISTRAZIONE ORDINI
            </div>

            <br>

            <form align="center" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                <label>
                    <input name = 'inputSearch' type="text"/>
                </label>
                <label>
                    <select class="option" name="method">
                        <option value="username">Per Utente</option>
                        <option value="id">Per Numero</option>
                        <option value="game">Per Videogioco</option>
                    </select>
                </label>
                <input hidden name = 'orders_button'>
                <button type="submit" name = 'searchOrder'>Cerca</button>
            </form>

            <?php

            try {
                $database_handler = new PDO ($dns);
                $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }

            $errors = false;
            $query = '';

            if (isset ($_POST['searchOrder']) && !empty($_POST['inputSearch'])) {

                if (isset($_POST['method'])) {

                    $input = strtoupper($_POST['inputSearch']);

                    switch ($_POST['method']) {

                        case ('username'): {
                            $query = "
                                SELECT *
                                FROM order_table
                                WHERE upper(userid) LIKE :input
                                GROUP BY id
                                ORDER BY id ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "$input%", PDO::PARAM_STR);
                            break;
                        }

                        case ('id'): {
                            if (filter_var($input, FILTER_VALIDATE_INT)) {
                                $query = "
                                    SELECT *
                                    FROM order_table
                                    WHERE id = :input
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(":input", $input);
                            }
                            else {
                                echo "<div class= error_messages>Inserire valore numerico intero per l'ID.</div>";
                                $errors = true;
                            }
                            break;
                        }

                        case ('game'): {
                            $query = "
                                SELECT DISTINCT o.*
                                FROM order_table o, game_order g_o, videogame v
                                WHERE upper(v.title) LIKE :input AND o.id = g_o.orderid AND v.id = g_o.gameid
                                GROUP BY o.id
                                ORDER BY o.id ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "%$input%", PDO::PARAM_STR);
                            break;
                        }

                        default: {
                            $query = "
                                SELECT *
                                FROM order_table
                                GROUP BY id
                                ORDER BY id ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            break;
                        }
                    }
                }
            }

            else {
                if (isset($_POST['user_to_check'])) {
                    $input = $_POST['user_to_check'];
                    $query = "
                        SELECT *
                        FROM order_table
                        WHERE userid = :input
                    ";
                    $statement = $database_handler->prepare($query);
                    $statement->bindParam(":input", $input);
                }
                else {
                    $query = "
                        SELECT *
                        FROM order_table
                        ";
                    $statement = $database_handler->prepare($query);
                }
            }

            $result = null;

            if(!$errors) {
                $database_handler->beginTransaction();
                $statement->execute();
                $database_handler->commit();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            }
            $database_handler = null;

            ?>

            <?php
            if ($result != null): ?>

            <table class = 'box' id = 'orders_table' width="100%" align="center" width="60%" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">

                <tr>
                    <th>Ordine</th>
                    <th>Utente</th>
                    <th>Data</th>
                    <th>Pagamento</th>
                    <th>Modifica</th>
                </tr>

                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['userid']; ?></td>
                    <td><?php echo date('d/m/Y - H:m', strtotime($row['date'])); ?></td>
                    <td><?php echo $row['payment']; ?></td>
                    <td>
                        <form>
                            <a href="edit_order.php?id=<?php echo $row['id']; ?>">
                            <button type="button" class = 'action_button'>
                                <br>
                                <img src="../web/icons/edit.png" height= '20' style="vertical-align: middle"/>
                            </button>
                            </a>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>

            </table>

            <?php elseif (!$errors) :
                echo "<div class= 'error_messages'>Nessun risultato corrisponde alla ricerca</div>";
            endif;?>

            <br>

            <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" align="right" method="POST">
                <button type= 'submit' name = 'orders_button' class = 'action_button'>
                    <img src="../web/icons/show_all.png" height= '20' style="vertical-align: middle"/>
                    Mostra Tutto
                </button>
            </form>

        </section>

    </body>

</html>