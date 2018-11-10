<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>

    <body>

        <section class = 'panel' align="center">

            <?php include_once 'admin_buttons.php'; ?>

            <br>
            <br>

            <div class = 'panel_title' id = 'products_title'>
                AMMINISTRAZIONE PRODOTTI
            </div>

            <br>

            <form align="center" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">
                <label>
                    <input name = 'inputSearch' type="text"/>
                </label>
                <label>
                    <select class="option" name="method">
                        <option value="id">ID</option>
                        <option value="title">Titolo</option>
                    </select>
                </label>
                <input hidden name = 'products_button'/>
                <button type="submit" name = 'searchGame'>Cerca</button>
            </form>

            <?php

            try {
                $database_handler = new PDO ($dns);
                $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }

            $query = '';
            $errors = false;

            if (isset ($_POST['searchGame']) && !empty($_POST['inputSearch'])) {
                if (isset($_POST['method'])) {
                    $input = strtoupper($_POST['inputSearch']);
                    switch ($_POST['method']) {
                        case ('id'): {
                            if (filter_var($input, FILTER_VALIDATE_INT)) {
                                $query = "
                                SELECT *
                                FROM videogame
                                WHERE id = :input
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(":input", $input);
                            }
                            else {
                                echo "<div class = 'error_messages'>Inserire valore numerico intero per l'ID.</div>";
                                $errors = true;
                            }
                            break;
                        }

                        case ('title'): {
                            $query = "
                            SELECT *
                            FROM videogame
                            WHERE upper(title) LIKE :input
                            ORDER BY title ASC
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement -> bindValue (":input", "%$input%", PDO::PARAM_STR);
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
            }

            else {
                $query = "
                SELECT *
                FROM videogame
                ORDER BY title ASC
                ";
                $statement = $database_handler->prepare($query);
            }

            $result = null;

            if (!$errors) {
                $database_handler->beginTransaction();
                $statement->execute();
                $database_handler->commit();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            $database_handler = null;

            ?>

            <?php if ($result != null): ?>

            <table class = 'box' id = 'products_table' align="center" width="100%" action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST">

                <tr>
                    <th>Title</th>
                    <th>Console</th>
                    <th>Price</th>
                    <th>Disponibili</th>
                    <th>Modifica</th>
                </tr>

                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['console']; ?></td>
                    <td> € <?php echo $row['price']; ?></td>
                    <td><?php $row['amount'] == 0 ? print"N/D" : print $row['amount']; ?></td>
                    <td>
                        <form>
                            <a href = "edit_product.php?id=<?php echo $row['id'];?>">
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

            <?php else :
                if (!$errors)
                    echo "<div class='error_messages'>Nessun prodotto corrisponde alla ricerca.</div>";
            endif; ?>

            <br>
            <br>

            <div align="right">
                <form action="<?php echo htmlentities($_SERVER["PHP_SELF"]);?>" method="POST" style="display: inline-block">
                    <button type= 'submit' name = 'products_button' class = 'action_button'>
                        <img src="../web/icons/show_all.png" height= '20' style="vertical-align: middle"/>
                        Mostra Tutto
                    </button>
                </form>

                <form action="<?php echo htmlentities('add_product.php');?>" style="display: inline-block">
                    <button class = 'action_button'>
                        <img src="../web/icons/add_product.png" height= '20' style="vertical-align: middle"/>
                        Aggiungi Prodotto
                    </button>
                </form>
            </div>

        </section>
    </body>

</html>