<?php
include_once "..\config\configurations.php";


if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $_SESSION['user'] != 'admin')) {
    header ('Location: ..\utils\logout.php');
}

if (!isset($_GET['id']))
    header ('Location: admin_panel.php');
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

                    <?php

                    try {
                        $database_handler = new PDO ($dns);
                        $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                        die();
                    }

                    try {
                        $database_handler = new PDO ($dns);
                        $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                        die();
                    }

                    $order = array();

                    $query = "
                    SELECT *
                    FROM order_table
                    WHERE  id = :id
                    ";
                    $statement = $database_handler->prepare($query);
                    $statement->bindParam(':id', $_GET['id']);
                    $database_handler->beginTransaction();
                    $statement->execute();
                    $database_handler->commit();
                    $result = $statement->fetch(PDO::FETCH_OBJ);

                    $order = array ('id' => $result->id, 'date' => $result->date, 'payment' => $result->payment, 'games' => array());
                    $query = "
                    SELECT v.*, g_o.copies
                    FROM game_order g_o JOIN videogame v on g_o.gameid = v.id
                    WHERE g_o.orderid = :id
                    GROUP BY v.id, g_o.copies
                    ORDER BY v.id ASC 
                    ";
                    $statement = $database_handler->prepare($query);
                    $statement->bindParam(':id', $order['id']);
                    $database_handler->beginTransaction();
                    $statement->execute();
                    $database_handler->commit();
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    $database_handler = null;
                    if (!$result) {
                        header('Location: admin_panel.php');
                    }
                    foreach ($result as $res) {
                        $order['games'][$res['id']] = array (
                            'id' => $res['id'],
                            'title' => $res['title'],
                            'console' => $res['console'],
                            'amount' => $res['copies'],
                            'price' => floatval($res['price']*intval($res['copies']))
                        );
                    }
                    ?>

                    <div class = 'panel_title' id = 'orders_title'>

                        MODIFICA ORDINE &nbsp; <?php echo $order['id']; ?>

                    </div>

                    <br>

                    <table class = 'box' id = 'orders_table' align="center" width="100%" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                        <tr>
                            <th>Title</th>
                            <th>Console</th>
                            <th>Price</th>
                            <th>Quantità</th>
                            <th>Elimina</th>
                        </tr>

                        <?php foreach ($order['games'] as $game): ?>
                        <tr>
                            <td><?php echo $game['title']; ?></td>
                            <td><?php echo $game['console']; ?></td>
                            <td><?php echo $game['price']; ?></td>
                            <td>
                                <form action="<?php echo htmlentities($_SERVER["PHP_SELF"].'?id='.$order['id']);?>" method="POST">
                                    <br>
                                    <input hidden name = 'amount' value="<?php echo $game['amount'];?>">
                                    <input type="number" min = '0' name="new_amount" value="<?php echo $game['amount']; ?>">
                                    <button type="submit" class = 'action_button' name="edit_amount" value="<?php echo $game['id']; ?>">
                                        <img src="../web/icons/edit.png" height= '20' style="vertical-align: middle"/>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="<?php echo htmlentities($_SERVER["PHP_SELF"].'?id='.$order['id']);?>" method="POST">
                                    <button type="submit" class = 'action_button' name="delete_game" value="<?php echo $game['id']; ?>">
                                        <br>
                                        <img src="../web/icons/delete.png" height= '20' style="vertical-align: middle"/>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </table>

                    <br>
                    <br>

                    <table align="right" style="text-align: right">
                        <tr>
                            <td>
                                DATA: <?php echo date('d/m/Y - H:m', strtotime($order['date']));?>
                            </td>
                            <td>
                                &nbsp; PAGAMENTO: <?php echo $order['payment'];?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <form>
                                    <a href="add_order_product.php?id=<?php echo $order['id']; ?>">
                                        <button class="action_button" type="button">
                                            <img src="../web/icons/add_product.png" height= '20' style="vertical-align: middle"/>
                                            Aggiungi Prodotto
                                        </button>
                                    </a>
                                </form>
                            </td>
                            <td>
                                <form action="<?php echo htmlentities($_SERVER['PHP_SELF'].'?id='.$order['id']);?>" method="POST">
                                    <button type="submit" class = 'action_button' name="delete_order" value="<?php echo $order['id']; ?>">
                                        <img src="../web/icons/delete.png" height= '20' style="vertical-align: middle"/>
                                        Elimina Ordine
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </table>

                    <br>
                    <br>
                    <br>

                    <div class="error_messages">
                    <?php

                    if (isset ($_POST['edit_amount'])) {
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }

                        $gamesId = array();

                        $query = "
                        SELECT copies
                        FROM game_order
                        WHERE  orderid = :id AND gameid = :game
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', $_GET['id']);
                        $statement->bindParam(':game', $_POST['edit_amount']);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetch(PDO::FETCH_ASSOC);

                        if ($result) {
                            if ($_POST['new_amount']>$result['copies']) {
                                $query = "
                                UPDATE videogame
                                SET amount = amount - :amount
                                WHERE id = :id
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(':id', $_POST['edit_amount']);
                                $new_amount = intval($_POST['new_amount']) - intval($result['copies']);
                            }
                            else {
                                $query = "
                                UPDATE videogame
                                SET amount = amount + :amount
                                WHERE id = :id
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(':id', $_POST['edit_amount']);
                                $new_amount = intval($result['copies']) - intval($_POST['new_amount']) ;
                            }

                            try {
                                $statement->bindParam(':amount', $new_amount);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();

                                if (intval($_POST['new_amount']) > 0) {
                                    $query = "
                                    UPDATE game_order
                                    SET copies = :amount
                                    WHERE  orderid = :id AND gameid = :game
                                    ";
                                    $statement = $database_handler->prepare($query);

                                    $statement->bindParam(':id', $_GET['id']);
                                    $statement->bindParam(':amount', intval($_POST['new_amount']));
                                    $statement->bindParam(':game', $_POST['edit_amount']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                } else {
                                    $query = "
                                    DELETE FROM game_order
                                    WHERE  orderid = :id AND gameid = :game
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':id', $_GET['id']);
                                    $statement->bindParam(':game', $_POST['edit_amount']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();

                                    $query = "
                                    SELECT FROM game_order
                                    WHERE orderid = :id;
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':id', $_GET['id']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                    $result = $statement->rowCount();
                                    if (!$result) {
                                        $query = "
                                        DELETE FROM order_table
                                        WHERE id = :id;
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $statement->bindParam(':id', $_GET['id']);
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                    }
                                }
                                $database_handler = null;
                                header('Refresh: 0');
                            }
                            catch (PDOException $e){
                                echo "Quantità di prodotti Insufficiente per la richiesta.";
                            }
                        }
                    }

                    elseif (isset($_POST['delete_order'])) {
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }

                        $gamesId = array();

                        $query = "
                            SELECT gameid, copies
                            FROM game_order
                            WHERE  orderid = :id
                            GROUP BY gameid, copies
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', $_POST['delete_order']);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($result as $row) {
                            $gamesId[$row['gameid']] = $row['copies'];
                        }

                        foreach ($gamesId as $game=>$amount) {
                            $query = "
                                UPDATE videogame
                                SET amount = amount + :amount
                                WHERE id = :id
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':id', $game);
                            $statement->bindParam(':amount', intval($amount));
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                        }


                        $query = "
                        DELETE FROM order_table
                        WHERE  id = :id
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', intval($_POST['delete_order']));
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $database_handler = null;
                        header('admin_panel.php');
                    }

                    elseif (isset($_POST['delete_game'])){
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }

                        $gamesId = array();

                        $query = "
                            SELECT copies
                            FROM game_order
                            WHERE  orderid = :id AND gameid = :game
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', $_GET['id']);
                        $statement->bindParam(':game', $_POST['delete_game']);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetch(PDO::FETCH_ASSOC);

                        if ($result) {
                            $query = "
                                UPDATE videogame
                                SET amount = amount + :amount
                                WHERE id = :id
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':id', $_POST['delete_game']);
                            $statement->bindParam(':amount', intval($result['copies']));
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();

                            $query = "
                                DELETE FROM game_order
                                WHERE  orderid = :id AND gameid = :game
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':id', $_GET['id']);
                            $statement->bindParam(':game', $_POST['delete_game']);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $database_handler = null;
                            header('Location: admin_panel.php');
                        }
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
