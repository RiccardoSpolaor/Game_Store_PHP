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

        <section class="main_content">

            <br>
            <br>
            <br>

            <div align="center">

                <section class="panel" align="center" style="text-align: center">

                    <?php if ($cart->getCartContent() != null): ?>

                    <div class = 'panel_title' id = 'users_title'>
                        DETTAGLI ORDINE <br>
                        <?php echo 'Prodotti: ' .$cart -> getTotalItems();?> &nbsp;
                        <?php echo 'Totale: €' .$cart -> getTotalPrice();?>
                    </div>

                    <br>
                    <br>

                    <table align="center" class = 'box' width="100%" id = 'users_table'>

                        <tr>
                            <th></th>
                            <th>Prodotto</th>
                            <th>Quantità</th>
                        </tr>

                        <?php foreach ($cart->getCartContent() as $item=>$row) :?>

                        <tr>
                            <td style="text-align: center">
                                <img height="200"
                                     src="<?php file_exists("../web/images/" . $item . ".jpg") ? print "../web/images/" . $item . ".jpg" :
                                         print '../web/images/NA.jpg'; ?>"
                                >
                            </td>
                            <td>
                                <?php echo $row['title']; ?> <br>
                                <?php echo $row['console'] ?>
                                € <?php echo floatval($row['price'])*intval($row['quantity']); ?>
                            </td>
                            <td>
                                <?php echo $row['quantity']; ?>

                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </table>

                    <br>
                    <br>

                    <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST">

                        Pagamento:

                        <input id='master_card' type = 'radio' name = 'payment' value = 'Master Card' checked required>

                        <label for="master_card">
                            <img src="../web/icons/MasterCardIcon.png" height="50" style="vertical-align: middle">
                        </label>

                        <input id='visa' type = 'radio' name = 'payment' value = 'Visa' required>

                        <label for="visa">
                            <img src="../web/icons/Visa.png" height="50" style="vertical-align: middle">
                        </label>

                        <input id='paypal' type = 'radio' name = 'payment' value = 'Paypal' required>

                        <label for="paypal">
                            <img src="../web/icons/Paypal.png" height="50" style="vertical-align: middle">
                        </label>

                        <input id='bitcoin' type = 'radio' name = 'payment' value = 'Bitcoin' required>

                        <label for="bitcoin">
                            <img src="../web/icons/bitcoin.png" height="50" style="vertical-align: middle">
                        </label>

                        <br>

                        <button type="submit" name = 'add_order'>
                            <img src="../web/icons/purchase_order.png" height= '20' style="vertical-align: middle"/>
                            Procedi All'Ordine
                        </button>

                        <br>
                        <br>

                        <div class="error_messages" style="width: auto">
                        <?php
                        if (isset ($_POST['add_order'])) {
                            try {
                                $database_handler = new PDO ($dns);
                                $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                echo $e->getMessage();
                                die();
                            }

                            $query = "
                              SELECT MAX(id) as idMax
                              FROM order_table
                            ";
                            $statement = $database_handler->prepare($query);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $result = $statement->fetchColumn();

                            $idOrder = null;
                            if ($result) {
                                $idOrder = $result + 1;
                            }
                            else
                                $idOrder = 1;

                            $flag = true;

                            foreach ($cart->getCartContent() as $item => $array) {
                                $query = "
                                    SELECT amount
                                    FROM videogame
                                    WHERE id = :id
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(':id',$item);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();
                                $available = $statement->fetch();
                                if (intval($available['amount'])<$array['quantity']) {
                                    echo 'Sono disponibili solo '.$available['amount'].' prodotti del tipo "'.$array['title'].'" per '.$array['console'].' <br>';
                                    $flag = false;
                                }
                            }
                            if ($flag == false) {
                                $database_handler = null;
                                echo 'Apportare le necessarie modifiche al carrello';
                                header ('Refresh:4; url = ..\cart\cart_view.php');
                            }
                            else {
                                $query = "
                                    INSERT INTO order_table (id, date, payment, userid)
                                    VALUES (:id, now(), :payment, :userid)
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(':id',$idOrder);
                                $statement->bindParam(':payment',$_POST['payment']);
                                $statement->bindParam(':userid',$_SESSION['username']);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();

                                foreach ($cart->getCartContent() as $item => $array) {

                                    $query = "
                                        INSERT INTO game_order (gameid, orderid, copies)
                                        VALUES (:game, :userorder, :amount)
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':game',$item);
                                    $statement->bindParam(':userorder',$idOrder);
                                    $statement->bindParam(':amount',$array['quantity']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                    $query = "
                                        UPDATE videogame
                                        SET amount = amount - :amount
                                        WHERE id = :id
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':amount',$array['quantity']);
                                    $statement->bindParam(':id',$item);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }
                                $database_handler = null;
                                unset($_SESSION['cart']);
                                header ('Location: orders.php');

                            }
                        }
                        ?>
                        </div>

                    </form>

                    <?php else: ?>
                        <div class="error_messages">Il tuo carrello è vuoto.</div>
                    <?php endif; ?>

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