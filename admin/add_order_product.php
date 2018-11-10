<?php
include_once "..\config\configurations.php";


if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $_SESSION['user'] != 'admin')) {
    header ('Location: ..\utils\logout.php');
}


if (!isset($_GET['id'])) {
    header ('Location: admin_panel.php');
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

            <div class="admin_layout" align="center">

                <section class = 'panel' align="center">

                    <?php include_once 'admin_buttons.php'; ?>

                    <br>
                    <br>

                    <div class = 'panel_title' id = 'orders_title'>
                        AGGIUNTA PRODOTTO ALL'ORDINE <?php echo $_GET['id'];?>
                    </div>

                    <br>

                    <form align="center" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="GET">

                        <label>
                            <input hidden name = 'id' value="<?php print $_GET['id']; ?>"/>
                            <input name = 'gameSearch' type="text"/>
                        </label>

                        <label>
                            <select class="option" name="method">
                                <option value="id">ID</option>
                                <option value="title">Titolo</option>
                            </select>
                        </label>

                        <input hidden name = 'products_button'/>
                        <button type="submit">Cerca</button>

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

                    if (isset($_GET['gameSearch'])) {

                        switch ($_GET['method']) {

                            case ('id'): {
                                if (filter_var($_GET['gameSearch'], FILTER_VALIDATE_INT)) {
                                    $query = "
                                    SELECT *
                                    FROM videogame
                                    WHERE id = :input
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(":input", intval($_GET['gameSearch']));
                                }
                                else {
                                    echo "<div class=\"error_messages\">Inserire valore numerico intero per l'ID. </div>";
                                    $errors = true;
                                }
                                break;
                            }

                            case ('title'): {
                                $gameTitle = strtoupper($_GET['gameSearch']);
                                $query = "
                                SELECT *
                                FROM videogame
                                WHERE upper(title) LIKE :input OR upper(console) LIKE :input
                                ORDER BY id DESC
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement -> bindValue (":input", "%$gameTitle%", PDO::PARAM_STR);
                                break;
                            }

                            default: {
                                $query = "
                                SELECT *
                                FROM videogame
                                ORDER BY title ASC
                                ";
                                $statement = $database_handler->prepare($query);
                                break;
                            }
                        }
                    }

                    else {
                        $query = "
                        SELECT *
                        FROM videogame
                        ORDER BY id DESC
                        ";
                        $statement = $database_handler->prepare($query);
                    }

                    if (!$errors) {
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    }

                    else {
                        $result=null;
                    }

                    $database_handler = null;
                    ?>

                    <?php if ($result != null): ?>

                    <table class = 'box' id = 'orders_table' align="center" width="100%" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">

                        <tr>

                        <th>Title</th>
                        <th>Console</th>
                        <th>Price</th>
                        <th>Disponibili</th>
                        <th>Aggiungi</th>

                        </tr>

                        <?php foreach ($result as $row): ?>

                        <tr>

                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['console']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['amount'] == 0 ? "N/D" : $row['amount']; ?></td>

                        <td>
                            <?php if ($row['amount']>0) : ?>
                            <form action="<?php echo htmlentities($_SERVER["PHP_SELF"].'?id='.$_GET['id']);?>" method="POST">
                                <input hidden name='gameId' value="<?php echo $row['id'];?>"/>
                                <input hidden name ='orderId' value ='<?php echo $_GET['id'];?>'/>
                                <label>
                                    <br>
                                    <input name='numberToAdd' type="number" min="1"/>
                                </label>
                                <button type="submit" class = 'action_button' name="add_games" style="vertical-align: middle">
                                    <img src="../web/icons/add_product.png" height= '20' style="vertical-align: middle"/>
                                </button>
                            </form>
                            <?php else : ?>
                            <form style="color: darkslategray">
                                <label>
                                    <br>
                                    <input type="number" disabled placeholder="N/D"/>
                                </label>
                                <button type="submit" class = 'action_button' name="add_games" style="vertical-align: middle" disabled>
                                    <img src="../web/icons/add_product.png" height= '20' style="vertical-align: middle; -webkit-filter: invert(40%)"/>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>

                        </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php

                    else : {
                        if (!$errors) {
                            echo "<div class='error_messages'> Nessun prodotto corrisponde alla ricerca. </div>";
                        }
                    }
                    endif;

                    ?>

                    <form align="right" action="<?php echo htmlentities($_SERVER["PHP_SELF"].'?id='.$_GET['id']); ?>" method="POST">
                        <button type= 'submit' name = 'products_button' class = 'action_button'>
                            <img src="../web/icons/show_all.png" height= '20' style="vertical-align: middle"/>
                            Mostra Tutto
                        </button>
                    </form>


                    <?php
                    if (isset($_POST['add_games']) && intval($_POST['numberToAdd']) > 0) {
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }

                        $query = "
                            SELECT amount
                            FROM videogame
                            WHERE id = :id;
                        ";

                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', intval($_POST['gameId']));
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetch(PDO::FETCH_ASSOC);

                        $available = $result['amount'] - $_POST['numberToAdd'] >= 0;

                        if ($available) {
                            $query = "
                                UPDATE videogame
                                SET amount = amount - :amount
                                WHERE id = :id
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':id', intval($_POST['gameId']));
                            $statement->bindValue(':amount', $_POST['numberToAdd'], PDO::PARAM_INT);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();

                            $query = "
                                    SELECT * 
                                    FROM game_order 
                                    WHERE orderid = :orderId and gameid = :gameId
                                ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(":gameId", intval($_POST['gameId']));
                            $statement->bindParam(":orderId", intval($_POST['orderId']));
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $result = $statement->fetchColumn();

                            if (!$result) {
                                $query = "
                                        INSERT INTO game_order (gameid, orderid, copies) 
                                        values (:gameId, :orderId, :amount)
                                    ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(":gameId", $_POST['gameId']);
                                $statement->bindParam(":orderId", $_POST['orderId']);
                                $statement->bindParam(":amount", $_POST['numberToAdd']);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();
                                $database_handler = null;
                            } else {
                                $query = "
                                        UPDATE game_order
                                        SET copies = :amount + copies
                                        WHERE gameid = :gameId and orderid = :orderId 
                                    ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(":gameId", intval($_POST['gameId']));
                                $statement->bindParam(":orderId", intval($_POST['orderId']));
                                $statement->bindParam(":amount", $_POST['numberToAdd']);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();
                                $database_handler = null;
                            }
                            header('Location: edit_order.php?id=' . $_POST['orderId']);
                        }

                        else {
                            echo "<div class= 'error_messages'>Prodotti Insufficienti!</div>";
                        }
                    }
                    ?>

                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                </section>
            </div>
        </section>

    <?php
    include_once '..\templates\footer.php';
    ?>

    </body>

</html>

