<?php

include_once "..\config\configurations.php";

if (isset($_SESSION['username'])) {
    if ($_SESSION['user'] == 'admin') {
        header ('Location: ..\admin\admin_panel.php');
    }
}
else
    header ('Location: ..\utils\login.php')
?>

<html>

    <body>
        <?php
        include_once '../templates/header.php';
        ?>

        <?php
        try {
            $database_handler = new PDO ($dns);
            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }

        $idOrders = array();

        $query = "
        SELECT *
        FROM order_table
        WHERE  userid = :username
        ";
        $statement = $database_handler->prepare($query);
        $statement->bindParam(':username', $_SESSION['username']);
        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $idOrders[$row['id']] = array ('date' => $row['date'], 'payment' => $row['payment'], 'games' => array());
            $query = "
            SELECT v.*, g_o.copies
            FROM game_order g_o JOIN videogame v on g_o.gameid = v.id
            WHERE g_o.orderid = :id
            ";
            $statement = $database_handler->prepare($query);
            $statement->bindParam(':id', $row['id']);
            $database_handler->beginTransaction();
            $statement->execute();
            $database_handler->commit();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $res) {
                $idOrders[$row['id']]['games'][$res['id']] = array (
                    'title' => $res['title'],
                    'console' => $res['console'],
                    'amount' => $res['copies'],
                    'price' => floatval($res['price']*intval($res['copies']))
                );
            }
        }
        ?>

        <section class="main_content">

            <br>
            <br>
            <br>

            <div align="center">

                <section class="panel" align="center" style="text-align: center">

                    <br>

                    <div class = 'panel_title' id = 'users_title'>
                        I MIEI ORDINI
                    </div>

                    <br>
                    <br>

                    <?php if ($idOrders != null): ?>

                    <?php foreach ($idOrders as $order=>$value) :?>

                    <div class = 'panel_title' id = 'users_title' style="border-radius: 20px">
                        ORDINE NUMERO <?php echo $order; ?>
                        <br>
                        <?php
                        $total_price = 0;
                        $total_products = 0;
                        foreach ($value['games'] as $attribute) {
                            $total_price += $attribute['price'];
                            $total_products += $attribute['amount'];
                        }
                        echo "Prodotti: " . $total_products . " Totale: €" . $total_price;
                        ?>

                    </div>

                    <br>

                    <table align="center" class = 'box' width="100%" id = 'users_table'>

                        <tr>
                            <th></th>
                            <th>Titolo</th>
                            <th>Quantità</th>
                            <th>Prezzo</th>
                        </tr>

                        <?php foreach ($value['games'] as $game => $attribute): ?>
                        <tr>
                            <td style="text-align: center">
                                <img height="200"
                                     src="<?php file_exists("../web/images/" . $game . ".jpg") ? print "../web/images/" . $game . ".jpg" :
                                         print '../web/images/NA.jpg'; ?>"
                                >
                            </td>
                            <td><?php echo $attribute['title']; ?></td>
                            <td><?php echo $attribute['amount'] ?></td>
                            <td><?php echo '€'.$attribute['price']; ?></td>
                        </tr>
                        <?php endforeach; ?>

                    </table>

                    <br>

                    <div class = 'panel_title' id = 'users_title' style="border-radius: 20px">
                        Data: <?php echo date('d/m/Y - H:m', strtotime($value['date']));?> &nbsp;
                        Pagamento: <?php echo $value['payment'];?>
                    </div>

                    <br>
                    <br>

                    <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST">
                        <button type="submit" name = "delete" value="<?php echo $order; ?>">
                            <img src="../web/icons/delete.png" height= '20' style="vertical-align: middle"/>
                            Elimina Ordine
                        </button>
                    </form>


                    <br>

                    <hr style="border: dashed 1px; width: 90%">

                    <br>
                    <br>
                    <br>

                    <?php endforeach;?>

                    <?php else: ?>
                    <div class="error_messages">Nessun ordine effettuato.</div>
                    <?php endif;?>

                    <div class="error_messages">
                    <?php
                    if (isset($_POST['delete'])) {
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
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', $_POST['delete']);
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
                        $statement->bindParam(':id', $_POST['delete']);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetchAll(PDO::FETCH_OBJ);
                        $database_handler = null;
                        if ($result) {
                            echo "Eliminazione effettuata.";
                        }
                        else {
                            echo "Eliminazione non riuscita.";
                        }
                        header('Refresh:5');
                    }
                    ?>
                    </div>

                </section>

            </div>

            <br>
            <br>

        </section>

        <?php
        include_once '..\templates\footer.php';
        ?>

    </body>

</html>