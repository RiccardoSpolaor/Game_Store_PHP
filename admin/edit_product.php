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

            <div class = 'admin_layout' align="center">

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

                    $query = "
                    SELECT * 
                    FROM videogame 
                    WHERE id = :id
                    ";
                    $statement = $database_handler->prepare($query);
                    $statement->bindParam(':id', $_GET['id']);
                    $database_handler->beginTransaction();
                    $statement->execute();
                    $database_handler->commit();
                    $result = $statement->fetch(PDO::FETCH_ASSOC);
                    $database_handler = null;
                    if (!$result) {
                        header('Location: admin_panel.php');
                    }
                    ?>

                    <div class = 'panel_title' id = 'products_title'>

                        MODIFICA PRODOTTO &nbsp; <?php echo $result['id']; ?>

                    </div>

                    <br>

                    <script type="text/javascript">
                        function show (num) {
                            if (document.getElementById('content' + num).hidden === true) {
                                document.getElementById('content' + num).hidden = false;
                                document.getElementById('show' + num).innerHTML = '- Hide';
                                document.getElementById('x' + num).src="../web/icons/hidden.png";
                            }
                            else {
                                document.getElementById('content' + num).hidden = true;
                                document.getElementById('show' + num).innerHTML = '- Show';
                                document.getElementById('x' + num).src="../web/icons/shown.png";
                            }
                        }
                    </script>


                    <div class = 'edit_panel' id="edit_product_panel" align="center">
                        <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF'] . '?id=' . $result['id']); ?>" method = "POST" enctype="multipart/form-data">

                            <div class="menu_header" id = 'menu1' onclick="show(1)" style="position: relative">
                                <b>
                                    TITOLO  (attuale = <?php echo $result['title'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show1' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x1" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content1' hidden>
                                <label>
                                    Nuovo Titolo: <br> <br>
                                    <input id = 'title'  maxlength="50" type="text" name = "title" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu2' onclick="show(2)" style="position: relative">
                                <b>
                                    CONSOLE  (attuale = <?php echo $result['console'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show2' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x2" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content2' hidden>
                                    Nuova Console: <br> <br>
                                    <ul class='fieldset'>
                                        <li>
                                            <label>
                                            <input type ='radio' value = 'PS4' name="console" />
                                            <img src="../web/icons/PS.png" height="20" width="20" style="vertical-align: middle"/>
                                            &nbsp; PS4
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                            <input type ='radio' value = 'Xbox One' name="console" />
                                            <img src="../web/icons/XBOX.png" height="20" style="vertical-align: middle"/>
                                            &nbsp; Xbox One
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                            <input type ='radio' value = 'Switch' name="console" />
                                            <img src="../web/icons/NINTENDO.png" height="20" style="vertical-align: middle"/>
                                            &nbsp; Switch
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                            <input type ='radio' value = 'PC' name="console" />
                                            <img src="../web/icons/PC.png" height="20" style="vertical-align: middle"/>
                                            &nbsp; PC
                                            </label>
                                        </li>
                                    </ul>
                                    <br>
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                            </div>




                            <div class="menu_header" id = 'menu3' onclick="show(3)" style="position: relative">
                                <b>
                                    PREZZO  (attuale = €<?php echo $result['price'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show3' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x3" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content3' hidden>
                                <label>
                                    Nuovo Prezzo: <br> <br>
                                    € &nbsp;
                                    <input id = 'price' type="number" name = 'price_round' min="0"/> .
                                    <input id = 'price' type="number" name = 'price_cents' min="0" max="99" value="0" style="display: inline">
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu4' onclick="show(4)" style="position: relative">
                                <b>
                                    DISPONIBILI  (attuale = <?php echo $result['amount'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show4' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x4" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content4' hidden>
                                <label>
                                    Nuova Disponiblità: <br> <br>
                                    <input id = 'amount' name="amount" type="number" min="0" value="<?php echo $result['amount']; ?>"/>
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu5' onclick="show(5)" style="position: relative">
                                <b>
                                    COPERTINA
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show5' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x5" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content5' hidden>
                                <table align="center" style="text-align: center">
                                    <tr>
                                        <td>
                                            Attuale:
                                        </td>
                                        <td>
                                            Nuova Copertina:
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br>
                                            <img height="200"
                                                 src="<?php file_exists("../web/images/" . $result['id'] . ".jpg") ? print "../web/images/" . $result['id'] . ".jpg" :
                                                     print '../web/images/NA.jpg'; ?>"
                                            >
                                        </td>
                                        <td>
                                            <label>
                                                <input type= "file" name ='file'>
                                                <br>
                                                <br>
                                                <button type="submit" name="submit">
                                                    <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                                    Modifica
                                                </button>
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <br>

                            <div align="right">
                                <button class="action_button" type="submit" name="submit">
                                    <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                    Modifica Gioco
                                </button>

                                <input hidden name="id" value= '<?php echo $result['id'];?>'/>
                                <button class="action_button" type="submit" name="delete">
                                    <img src="../web/icons/delete.png" height= '20'  style="vertical-align: middle"/>
                                    Elimina Gioco
                                </button>
                            </div>
                        </form>

                        <br>
                        <br>

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
                            try {
                                $query = "
                                    DELETE FROM videogame 
                                    WHERE id = :id
                                ";
                                $statement = $database_handler->prepare($query);
                                $statement->bindParam(':id', $_POST['id']);
                                $database_handler->beginTransaction();
                                $statement->execute();
                                $database_handler->commit();
                                $result = $statement->fetchAll(PDO::FETCH_OBJ);
                                if ($result) {
                                    echo "eliminazione riuscita";
                                    header('Refresh:4; url = admin_panel.php');
                                } else {
                                    echo 'eliminazione non riuscita';
                                }
                            }
                            catch (PDOException $error) {
                                echo "Sono presenti ordini con questo videogioco, impossibile eliminare!";
                            }
                            $database_handler = null;
                        }

                        if (isset ($_POST['submit'])) {


                            try {
                                $database_handler = new PDO ($dns);
                                $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                echo $e->getMessage();
                                die();
                            }

                            $alright = true;
                            $upload = false;
                            $fileName = null;
                            $fileTmp = null;
                            $fileError = null;
                            $fileSize = null;
                            $fileExt = null;

                            if (is_uploaded_file($_FILES['file']['tmp_name'])) {

                                $file = $_FILES['file'];

                                $fileName = $_FILES['file']['name'];
                                $fileTmp = $_FILES['file']['tmp_name'];
                                $fileError = $_FILES['file']['error'];
                                $fileSize = $_FILES['file']['size'];

                                $fileExt = explode('.', $fileName);
                                $fileExt = strtolower(end($fileExt));

                                $allowed = 'jpg';

                                if ($fileExt == $allowed) {
                                    if ($fileError === 0) {
                                        if ($fileSize < 1000000) {
                                            $upload = true;
                                        } else {
                                            echo 'File troppo grande';
                                            $alright = false;
                                        }
                                    } else {
                                        echo "Errore nell'Upload";
                                        $alright = false;
                                    }
                                } else {
                                    echo 'Estensione non utilizzabile';
                                    $alright = false;
                                }
                            }

                            if ($alright) {

                                if (!empty($_POST['title'])||!empty($_POST['price_round'])||!empty($_POST['console'])||!empty($_POST['amount'])) {
                                    if (!empty($_POST['title'])) {
                                        $query = "
                                          UPDATE videogame
                                          SET title = :title
                                          WHERE id = :id
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $statement->bindParam(':id', $_GET['id']);
                                        $statement->bindParam(':title', $_POST['title']);
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                    }

                                    if (!empty($_POST['price_round'])) {
                                        $query = "
                                          UPDATE videogame
                                          SET price = :price
                                          WHERE id = :id
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $price = intval($_POST['price_round']);
                                        $price_cents = isset($_POST['price_cents'])? intval($_POST['price_cents']):0;
                                        $price += ($price_cents * 0.01);
                                        $statement->bindParam(':id', $_GET['id']);
                                        $statement->bindParam(':price', $price);
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                    }

                                    if (!empty($_POST['console'])) {
                                        $query = "
                                          UPDATE videogame
                                          SET console = :console
                                          WHERE id = :id
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $statement->bindParam(':id', $_GET['id']);
                                        $statement->bindParam(':console', $_POST['console']);
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                    }

                                    if (isset($_POST['amount'])) {
                                        $query = "
                                          UPDATE videogame
                                          SET amount = :amount
                                          WHERE id = :id
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $statement->bindParam(':id', $_GET['id']);
                                        $statement->bindParam(':amount', intval($_POST['amount']));
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                    }
                                }

                                else {
                                    if (!$upload)
                                        $alright = false;
                                }

                                $database_handler = null;
                            }

                            if ($upload) {
                                $fileDestination = "..\web\images\\".$_GET['id'].'.'.$fileExt;
                                move_uploaded_file($fileTmp, $fileDestination);
                            }

                            if ($alright) {
                                header ('Location: edit_product.php?id='.$_GET['id']);
                            }
                            else {
                                echo 'INSERIRE DATI';
                            }
                        }
                        ?>
                        </div>
                    </div>
                </section>
            </div>

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
