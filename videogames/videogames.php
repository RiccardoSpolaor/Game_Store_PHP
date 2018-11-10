<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['user'])&&$_SESSION['user'] == 'admin') {
    header ('Location: ..\admin\admin_panel.php');
}

?>

<html>

    <body>

        <?php
        include_once "../templates/header.php";
        ?>

        <?php
        $id = null;
        if (!empty($_GET['q']))
            $id =strtoupper($_GET['q']);
        ?>

        <?php
        try {
            $database_handler = new PDO ($dns);
            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }

        if ($id) {
            $query = "
                        SELECT *
                        FROM videogame
                        WHERE upper(title) LIKE :input OR upper(console) LIKE :input
                        GROUP BY id
                        ORDER BY id DESC
                    ";
            $statement = $database_handler->prepare($query);
            $statement->bindValue(":input", "%$id%", PDO::PARAM_STR);
        }
        else {
            $query = "
                        SELECT *
                        FROM videogame
                        GROUP BY id
                        ORDER BY id DESC
                    ";
            $statement = $database_handler->prepare($query);
        }

        $database_handler->beginTransaction();
        $statement->execute();
        $database_handler->commit();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $database_handler = null;
        ?>

        <section class="main_content">

            <br>
            <br>
            <br>

            <div align="center">

                <section class="panel" align="center" style="text-align: center">

                    <div class = 'panel_title' id = 'users_title'>
                    <?php if ($id): ?>
                        <?php echo count($result); ?> risultati per: '<?php echo $id?>'
                    <?php else: ?>
                        Catalogo Videogiochi
                    <?php endif; ?>
                    </div>

                    <?php if ($result != null): ?>
                    <table align="center" class = 'box' width="100%" id = 'users_table'>

                        <?php foreach ($result as $row):?>
                        <tr>
                            <td class="image_cell">
                                <img height="200"
                                     src="<?php file_exists("../web/images/" . $row['id'] . ".jpg") ? print "../web/images/" . $row['id'] . ".jpg" :
                                         print '../web/images/NA.jpg'; ?>"
                                >
                            </td>

                            <td>
                                <?php echo $row['title']; ?>
                                <br>
                                <br>
                                <?php echo $row['console']; ?>
                                <br>
                                <?php echo $row['amount']>0 ? 'Disponibili ancora: '.$row['amount'] : 'Prodotto non disponibile'; ?>
                            </td>

                            <td style= "text-align: center">

                                <?php if ($row['amount']>0) : ?>
                                <a href="../cart/cart_action.php?id=<?php echo $row['id'];?>&price=<?php echo $row['price'];?>&action=add&amount=1">
                                    <button class="cart_add_button">
                                        ACQUISTA A € <?php echo $row['price']; ?>
                                    </button>
                                </a>

                                <?php else: ?>
                                <b style="color: darkred">
                                    PRODOTTO NON DISPONIBILE
                                </b>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php else: ?>
                    <div class="error_messages">La ricerca di '<?php echo $id?>' non ha prodotto alcun risultato.</div>
                    <?php endif; ?>

                </section>

            </div>

        </section>

        <?php
        include_once '../templates/footer.php';
        ?>
    </body>

</html>