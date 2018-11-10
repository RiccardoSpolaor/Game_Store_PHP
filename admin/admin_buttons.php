<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>
    <body>

        <?php
        if (!isset($id)) {
            $id = 'default';
        }
        ?>

        <div style="text-align: center">
            <br>
            <br>
            <form align="center" action="<?php echo htmlentities('admin_panel.php');?>" method="POST">
                <button class = panel_button name="users_button" id="users_button" <?php if ($id == 'users') echo 'disabled'; ?>>
                    <img src="../web/icons/user.png" style="vertical-align: middle">
                    Amministra Utenti &nbsp;
                </button>
                <button class = panel_button name="orders_button" id="orders_button" <?php if ($id == 'orders') echo 'disabled'; ?>>
                    <img src="../web/icons/purchase_order.png" style="vertical-align: middle">
                    Amministra Ordini
                </button>
                <button class = panel_button name="admins_button" id="admins_button" <?php if ($id == 'admins') echo 'disabled'; ?>>
                    <img src="../web/icons/admin.png" style="vertical-align: middle">
                    Amministra Admin
                </button>
                <button class = panel_button name ="products_button" id ="products_button" <?php if ($id == 'products') echo 'disabled'; ?>>
                    <img src="../web/icons/products.png" style="vertical-align: middle">
                    Amministra Prodotti
                </button>
            </form>
        </div>

    </body>
</html>
