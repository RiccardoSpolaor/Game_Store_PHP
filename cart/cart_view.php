<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['user'])&&$_SESSION['user'] == 'admin') {
    header ('Location: ..\admin\admin_panel.php');
}

if (!isset($_SESSION['cart']))
    header('Location: ../index/home.php')
?>

<html>

    <body>

        <?php
        include_once '..\templates\header.php';
        ?>

        <section class="main_content">
            <br>
            <br>
            <br>

            <div align="center">

                <section class="panel" align="center" style="text-align: center">


                    <?php if ($cart->getCartContent() != null): ?>

                    <div class = 'panel_title' id = 'users_title'>
                        IL MIO CARRELLO <br>
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
                            <th>Elimina</th>
                        </tr>


                        <?php foreach ($cart->getCartContent() as $item=>$row) :?>
                        <tr>
                            <td class="image_cell">
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
                                <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                                    <br>
                                    <label>
                                        <input hidden name="current_amount" value="<?php echo $row['quantity']; ?>" />
                                        <input type="number" min = '0' name="new_amount" value="<?php echo $row['quantity'];?>"/>
                                    </label>
                                    <button type="submit" class = 'action_button' name="edit_amount" value="<?php echo $item;?>">
                                        <img src="../web/icons/edit.png" height= '20' style="vertical-align: middle"/>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                                    <input hidden name="amount" value="<?php echo $row['quantity']; ?>" />
                                    <button type="submit" class = 'action_button' name="remove_game" value="<?php echo $item;?>">
                                        <br>
                                        <img src="../web/icons/delete.png" height= '20' style="vertical-align: middle"/>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>


                        <caption align="bottom" style="margin-top: 20px; text-align: right; padding-right: 10px">

                            <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST" style="display: inline">
                                <button type="submit" name="clear">
                                    <img src="../web/icons/delete.png" height= '20' style="vertical-align: middle"/>
                                    Svuota
                                </button>
                            </form>

                            <a href="../orders/add_order.php">
                                <button type="button">
                                    <img src="../web/icons/purchase_order.png" height= '20' style="vertical-align: middle"/>
                                    Ordina
                                </button>
                            </a>
                        </caption>

                    </table>

                    <?php endif; ?>

                    <br>
                    <br>

                    <?php

                    if (isset($_POST['edit_amount'])) {
                        if (($_POST['new_amount'] - $_POST['current_amount']) >= 0) {
                            header('Location: cart_action.php?id=' . $_POST['edit_amount'] .
                                '&action=add&amount='.($_POST['new_amount'] - $_POST['current_amount']));
                        }
                        else {
                            header('Location: cart_action.php?id=' . $_POST['edit_amount'] .
                                '&action=remove&amount='.($_POST['current_amount'] - $_POST['new_amount']));
                        }
                    }

                    if (isset($_POST['remove_game'])) {
                        header('Location: cart_action.php?id=' . $_POST['remove_game'] .
                            '&action=remove&amount='.($_POST['amount']));

                    }

                    if (isset($_POST['clear'])) {
                        unset($_SESSION ['cart']);
                        header('Refresh: 0');
                    }

                    ?>
                </section>

            </div>

        </section>

        <?php
        include_once '..\templates\footer.php';
        ?>

    </body>
</html>
