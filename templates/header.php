<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>

    <body>

        <header class="main_header">

            <div class = "text">
                <?php if (isset ($_SESSION['username'])) : ?>

                    <?php if ($_SESSION['user'] == 'user'): ?>

                        <img style="vertical-align: middle; -webkit-filter: invert(100%)" height = '20' src="../web/icons/user.png"/>
                        <b>Benvenuto <?php echo $_SESSION['username']; ?></b>

                        <a class = 'util_link' href="../orders/orders.php">
                            <img style="vertical-align: middle" height = '20' src="../web/icons/purchase_order.png"/>
                            I Miei Ordini
                        </a>

                    <?php else: ?>

                        <img style="vertical-align: middle; -webkit-filter: invert(100%)" height = '20' src="../web/icons/admin.png"/>
                        <b>Benvenuto <?php echo $_SESSION['username']; ?></b>

                    <?php endif; ?>

                    <a class = 'util_link' href="../utils/logout.php">
                        <img style="vertical-align: middle" height = '20' src="../web/icons/log_out.png"/>
                        Log Out
                    </a>

                <?php else : ?>
                    <a class = 'util_link' href="../utils/login.php">
                        <img style="vertical-align: middle" height = '20' src="../web/icons/log_in.png"/>
                        Login
                    </a>
                    <a class = 'util_link' href="../utils/sign_up.php">
                        <img style="vertical-align: middle" height = '20' src="../web/icons/sign_up.png"/>
                        Registrati
                    </a>
                <?php endif; ?>

                <?php if ((isset($_SESSION['user'])&& $_SESSION['user']!='admin') || !isset($_SESSION['user'])) :

                    if (isset($_SESSION['cart'])) : ?>
                        <a class = 'util_link' href="../cart/cart_view.php" >
                            <img style="vertical-align: middle" height = '17' src="../web/icons/Cart.png"/>
                            <div id="cart_items"></div>
                        </a>

                    <?php else: ?>
                    <a id = 'inactive_cart'>
                        <img style="vertical-align: middle" height = '17' src="../web/icons/Cart.png"/>
                    </a>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </header>



        <?php if ((isset($_SESSION['user'])&& $_SESSION['user']!='admin') || !isset($_SESSION['user'])) : ?>
        <header class="second_header">
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                <table>
                    <tr>
                        <td>
                            <a href="../index/home.php"><img src="https://www.gamestop.it/Views/Locale/Content/Images/logo.png"/></a>
                        </td>

                        <td>
                            <label>
                                <input name='inputSearch' type="text"/>
                            </label>
                        </td>

                        <td>
                            <button type="submit" name='q'>Cerca</button>
                        </td>

                        <td width="100%" align="right">
                            <a class = 'console_link' href="../videogames/videogames.php?q=PS4">
                                <table class = 'console_table'>
                                    <tr>
                                        <td>
                                            &nbsp;<img  height="25" src="../web/icons/PS.png"/>
                                        </td>
                                        <td>
                                            &nbsp; PS4 &nbsp; &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </a>

                            <a class = 'console_link' href="../videogames/videogames.php?q=Switch">
                                <table class = 'console_table'>
                                    <tr>
                                        <td>
                                            &nbsp;<img height="25" src="../web/icons/NINTENDO.png"/>
                                        </td>
                                        <td>
                                            &nbsp; Switch &nbsp; &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </a>

                            <a class = 'console_link' href="../videogames/videogames.php?q=Xbox One">
                                <table class = 'console_table'>
                                    <tr>
                                        <td>
                                            &nbsp;<img height="25" src="../web/icons/XBOX.png"/>
                                        </td>
                                        <td>
                                            &nbsp; Xbox One &nbsp; &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </a>

                            <a class = 'console_link' href="../videogames/videogames.php?q=PC">
                                <table class = 'console_table'>
                                    <tr>
                                        <td>
                                            &nbsp;<img height="25" src="../web/icons/PC.png"/>
                                        </td>
                                        <td>
                                            &nbsp; PC &nbsp; &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </a>

                        </td>
                    </tr>
                </table>
            </form>
        </header>
        <?php endif; ?>

        <?php
        if (!empty($_POST['inputSearch']) && isset($_POST['q'])) {
            header('Location: ..\videogames\videogames.php?q=' . $_POST['inputSearch']);
        }
        ?>

    </body>

</html>