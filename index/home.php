<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['user'])&&$_SESSION['user'] == 'admin') {
    header ('Location: ..\admin\admin_panel.php');
}
?>


<html>

    <body>

        <?php
        include_once '..\templates\header.php';
        ?>

        <?php
        try {
            $database_handler = new PDO ($dns);
            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        $query = "
        SELECT *
        FROM videogame
        WHERE console = 'Xbox One'
        GROUP BY id
        ORDER BY id DESC
        LIMIT 4
        ";
        $statement = $database_handler->prepare($query);
        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $xboxone_result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $query = "
        SELECT *
        FROM videogame
        WHERE console = 'PS4'
        GROUP BY id
        ORDER BY id DESC
        LIMIT 4
        ";
        $statement = $database_handler->prepare($query);
        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $ps4_result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $query = "
        SELECT *
        FROM videogame
        WHERE console = 'Switch'
        GROUP BY id
        ORDER BY id DESC
        LIMIT 4
        ";
        $statement = $database_handler->prepare($query);
        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $switch_result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $query = "
        SELECT *
        FROM videogame
        WHERE console = 'PC'
        GROUP BY id
        ORDER BY id DESC
        LIMIT 4
        ";
        $statement = $database_handler->prepare($query);
        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $pc_result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $database_handler = null;
        ?>

        <section class="main_content">
            <br>
            <br>
            <br>

            <table class = 'games_table' align="center" style="text-align: center">

                <caption id = 'ps_caption'>
                    ULTIME USCITE PS4
                    <img src="../web/icons/PS.png" style="-webkit-filter: invert(100%); vertical-align: middle" height = '30'/>
                </caption>

                <tr>
                <?php

                $result = $ps4_result;

                if ($result != null):
                    foreach ($result as $row): ?>
                    <td>
                        <img height="200"
                             src="<?php file_exists("../web/images/" . $row['id'] . ".jpg") ? print "../web/images/" . $row['id'] . ".jpg" :
                                 print '../web/images/NA.jpg'; ?>"
                        >

                        <br>
                        <br>

                        <?php echo $row['title'].' - '.$row['console']; ?>

                        <br>

                        <?php echo '€ '.$row['price'].' - ';
                        $row['amount']>0 ? print 'Disponibile' : print 'Non Disponibile' ?>

                        <br>
                        <br>

                        <?php if ($row['amount']>0) : ?>
                        <a href="../cart/cart_action.php?id=<?php echo $row['id']?>&action=add&amount=1">
                            <button class="cart_add_button">
                                ACQUISTA ORA!
                            </button>
                        </a>

                        <?php else: ?>
                        <b style="color: darkred;">
                            AL MOMENTO NON DISPONIBILE
                        </b>
                        <?php endif; ?>

                    </td>
                    <?php endforeach;
                endif;?>
                </tr>
            </table>

            <br>
            <br>

            <table class="games_table" align="center" style="text-align: center">
                <caption id="xbox_caption">
                    ULTIME USCITE Xbox One
                    <img src="../web/icons/XBOX.png" style="-webkit-filter: invert(100%); vertical-align: middle" height = '30'/>
                </caption>

                <tr>
                <?php
                $result = $xboxone_result;

                if ($result != null):
                    foreach ($result as $row): ?>
                    <td>
                        <img height="200"
                             src="<?php file_exists("../web/images/" . $row['id'] . ".jpg") ? print "../web/images/" . $row['id'] . ".jpg" :
                                 print '../web/images/NA.jpg'; ?>"
                        >

                        <br>
                        <br>

                        <?php echo $row['title'].' - '.$row['console']; ?>

                        <br>

                        <?php echo '€ '.$row['price'].' - ';
                        $row['amount']>0 ? print 'Disponibile' : print 'Non Disponibile' ?>

                        <br>
                        <br>

                        <?php if ($row['amount']>0) : ?>

                        <a href="../cart/cart_action.php?id=<?php echo $row['id']?>&price=<?php echo $row['price']?>&action=add&amount=1">

                            <button class="cart_add_button">
                                ACQUISTA ORA!
                            </button>
                        </a>

                        <?php else: ?>
                        <b style="color: darkred;">
                            AL MOMENTO NON DISPONIBILE
                        </b>
                        <?php endif; ?>

                    </td>

                    <?php endforeach;
                endif;?>

                </tr>
            </table>

            <br>
            <br>

            <table class='games_table' align="center" style="text-align: center">

                <caption id = 'nintendo_caption'>
                    ULTIME USCITE Switch
                    <img src="../web/icons/NINTENDO.png" style="-webkit-filter: invert(100%); vertical-align: middle" height = '30'/>
                </caption>

                <tr>
                <?php
                $result = $switch_result;
                if ($result != null):
                    foreach ($result as $row): ?>
                    <td>
                        <img height="200"
                             src="<?php file_exists("../web/images/" . $row['id'] . ".jpg") ? print "../web/images/" . $row['id'] . ".jpg" :
                                 print '../web/images/NA.jpg'; ?>"
                        >

                        <br>
                        <br>

                        <?php echo $row['title'].' - '.$row['console']; ?>

                        <br>

                        <?php echo '€ '.$row['price'].' - ';
                        $row['amount']>0 ? print 'Disponibile' : print 'Non Disponibile' ?>

                        <br>
                        <br>

                        <?php if ($row['amount']>0) : ?>
                        <a href="../cart/cart_action.php?id=<?php echo $row['id']?>&price=<?php echo $row['price']?>&action=add&amount=1">
                            <button class="cart_add_button">
                                ACQUISTA ORA!
                            </button>
                        </a>

                        <?php else: ?>
                        <b style="color: darkred;">
                            AL MOMENTO NON DISPONIBILE
                        </b>
                        <?php endif; ?>

                    </td>

                    <?php endforeach;
                endif;?>

                </tr>
            </table>

            <br>
            <br>

            <table class="games_table" align="center" style="text-align: center">

                <caption id="pc_caption">
                    ULTIME USCITE PC
                    <img src="../web/icons/PC.png" style="-webkit-filter: invert(100%); vertical-align: middle" height = '30'/>
                </caption>

                <tr>
                <?php
                $result = $pc_result;
                if ($result != null):
                    foreach ($result as $row): ?>
                    <td>
                        <img height="200"
                             src="<?php file_exists("../web/images/" . $row['id'] . ".jpg") ? print "../web/images/" . $row['id'] . ".jpg" :
                                 print '../web/images/NA.jpg'; ?>"
                        >

                        <br>
                        <br>

                        <?php echo $row['title'].' - '.$row['console']; ?>

                        <br>

                        <?php echo '€ '.$row['price'].' - ';

                        $row['amount']>0 ? print 'Disponibile' : print 'Non Disponibile' ?>

                        <br>
                        <br>

                        <?php if ($row['amount']>0) : ?>
                        <a href="../cart/cart_action.php?id=<?php echo $row['id']?>&price=<?php echo $row['price']?>&action=add&amount=1">
                            <button class="cart_add_button">
                                ACQUISTA ORA!
                            </button>
                        </a>

                        <?php else: ?>
                        <b style="color: darkred;">
                            AL MOMENTO NON DISPONIBILE
                        </b>
                        <?php endif; ?>

                    </td>
                    <?php endforeach;
                endif;?>
                </tr>

            </table>

            <br>
            <br>
            <br>
            <br>
            <br>

        </section>

        <?php
        include_once '..\templates\footer.php';
        ?>

    </body>

</html>