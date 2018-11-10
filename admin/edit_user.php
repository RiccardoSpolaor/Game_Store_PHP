<?php
include_once "..\config\configurations.php";


if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $_SESSION['user'] != 'admin')) {
    header ('Location: ..\utils\logout.php');
}

if (!isset($_GET['login']))
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
                    FROM user_table 
                    WHERE login = :login AND admin is false
                    ";
                    $statement = $database_handler->prepare($query);
                    $statement->bindParam(':login', $_GET['login']);
                    $database_handler->beginTransaction();
                    $statement->execute();
                    $database_handler->commit();
                    $result = $statement->fetch(PDO::FETCH_ASSOC);
                    $database_handler = null;
                    if (!$result) {
                        header('Location: admin_panel.php');
                    }

                    ?>

                    <div class = 'panel_title' id = 'users_title'>

                        MODIFICA UTENTE &nbsp; <?php echo $result['login']; ?>

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

                    <div class = 'edit_panel' id="edit_user_panel" align="center">

                        <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF'] . '?login=' . $result['login']); ?>" method = "POST">

                            <div class="menu_header" id = 'menu1' onclick="show(1)" style="position: relative">
                                <b>
                                    USERNAME  (attuale = <?php echo $result['login'];?>)
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
                                    Nuovo Username: <br> <br>
                                    <input id = 'login' maxlength="20" type="text" name = "login" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu2' onclick="show(2)" style="position: relative">
                                <b>
                                    PASSWORD  (attuale = <?php echo $result['password'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show2' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x2" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content2' hidden>
                                <label>
                                    Nuova Password: <br> <br>
                                    <input id = 'password'  maxlength="20" type="password" name = "password" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu3' onclick="show(3)" style="position: relative">
                                <b>
                                    NOME  (attuale = <?php echo $result['name'];?>)
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
                                    Nuovo Nome: <br> <br>
                                    <input id = 'name' maxlength="20" type="text" name = "name" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>



                            <div class="menu_header" id = 'menu4' onclick="show(4)" style="position: relative">
                                <b>
                                    COGNOME  (attuale = <?php echo $result['surname'];?>)
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
                                    Nuovo Cognome: <br> <br>
                                    <input id = 'surname'  maxlength="20" type="text" name = "surname" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu5' onclick="show(5)" style="position: relative">
                                <b>
                                    INDIRIZZO  (attuale = <?php echo $result['adress'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show5' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x5" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content5' hidden>
                                <label>
                                    Nuovo Indirizzo: <br> <br>
                                    <input id = 'adress' maxlength="100" type="text" name = "adress" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>




                            <div class="menu_header" id = 'menu6' onclick="show(6)" style="position: relative">
                                <b>
                                    TELEFONO   (attuale = <?php echo $result['phone']==null?'N/D':$result['phone'];?>)
                                </b>

                                <div style="position: absolute; right:6px; top: 5px; display: inline">
                                    <div id = 'show6' style="display: inline; font-size: 80%">
                                        - Show
                                    </div>
                                    <img id="x6" height="20" src="../web/icons/shown.png" style="vertical-align: middle"/>
                                </div>
                            </div>

                            <div class= 'menu_content' id ='content6' hidden>
                                <label>
                                    Nuovo Telefono: <br> <br>
                                    <input id = 'phone' maxlength="10" type="tel" name = "phone" />
                                    <button type="submit" name="submit">
                                        <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                        Modifica
                                    </button>
                                </label>
                            </div>



                            <br>

                            <div align="right">
                                <button class="action_button" type="submit" name="submit">
                                    <img src="../web/icons/edit.png" height= '20'  style="vertical-align: middle"/>
                                    Modifica Utente
                                </button>

                                <button class="action_button" type="submit" name="delete">
                                    <img src="../web/icons/delete.png" height= '20'  style="vertical-align: middle"/>
                                    Elimina Utente
                                </button>
                            </div>

                        </form>

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
                            $query = "
                            DELETE FROM user_table 
                            WHERE login = :login
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':login', $_GET['login']);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $result = $statement->fetchAll(PDO::FETCH_OBJ);
                            $database_handler = null;
                            if ($result) {
                                echo "eliminazione riuscita";
                            }
                            else {
                                echo 'eliminazione non riuscita';
                            }
                            $database_handler = null;
                            header('Refresh:4; url = admin_panel.php');
                        }

                        if (isset ($_POST['submit'])) {

                            if (!empty($_POST['login']) || !empty($_POST['password']) || !empty($_POST['adress']) || !empty($_POST['phone'])|| !empty($_POST['name'])
                                || !empty($_POST['surname'])) {

                                try {
                                    $database_handler = new PDO ($dns);
                                    $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                } catch (PDOException $e) {
                                    echo $e->getMessage();
                                    die();
                                }

                                $success = true;

                                if (!empty($_POST['login'])) {
                                    try {
                                        $query = "
                                        UPDATE user_table
                                        SET login = :newlogin
                                        WHERE login = :login
                                        ";
                                        $statement = $database_handler->prepare($query);
                                        $statement->bindParam(':login', $_GET['login']);
                                        $statement->bindParam(':newlogin', $_POST['login']);
                                        $database_handler->beginTransaction();
                                        $statement->execute();
                                        $database_handler->commit();
                                        $_GET['login']=$_POST['login'];
                                    } catch (PDOException $error) {
                                        $success = false;
                                        echo 'Login non disponibile';
                                    }
                                }

                                if (!empty($_POST['password'])) {
                                    $query = "
                                    UPDATE user_table
                                    SET password = :password
                                    WHERE login = :login
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':login', $_GET['login']);
                                    $statement->bindParam(':password', $_POST['password']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }

                                if (!empty($_POST['name'])) {
                                    $query = "
                                    UPDATE user_table
                                    SET name = :name
                                    WHERE login = :login
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':login', $_GET['login']);
                                    $statement->bindParam(':name', $_POST['name']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }

                                if (!empty($_POST['surname'])) {
                                    $query = "
                                    UPDATE user_table
                                    SET surname = :surname
                                    WHERE login = :login
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':login', $_GET['login']);
                                    $statement->bindParam(':surname', $_POST['surname']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }

                                if (!empty($_POST['adress'])) {
                                    $query = "
                                    UPDATE user_table
                                    SET adress = :adress
                                    WHERE login = :login
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':login', $_GET['login']);
                                    $statement->bindParam(':adress', $_POST['adress']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }

                                if (!empty($_POST['phone'])) {
                                    $query = "
                                    UPDATE user_table  
                                    SET phone = :phone
                                    WHERE login = :login
                                    ";
                                    $statement = $database_handler->prepare($query);
                                    $statement->bindParam(':login', $_GET['login']);
                                    $statement->bindParam(':phone', $_POST['phone']);
                                    $database_handler->beginTransaction();
                                    $statement->execute();
                                    $database_handler->commit();
                                }

                                $database_handler = null;

                                if ($success) {
                                    header ('Location: edit_user.php?login='.$_GET['login']);
                                }
                            }

                            else {
                                echo 'Inserire almeno un nuovo dato';
                            }
                        }

                        ?>
                        </div>

                    </div>

                </section>

            </div>

        </section>

        <?php
        include_once '..\templates\footer.php';
        ?>

    </body>
</html>
