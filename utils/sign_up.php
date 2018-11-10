<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['username'])) {
    if ($_SESSION['user'] == 'user') {
        header ('Location: ..\index\home.php');
    }
    else {
        header('Location: ..\admin\admin_panel.php');
    }
}
?>

<html>

    <body>


        <?php
        include_once '../templates/header.php';
        ?>


        <section class= "main_content">

            <div class="container">
                <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST">
                   <table>
                       <tr>
                           <td>
                                <label for="name">
                                Nome: <b style="color: darkred">*</b>
                                </label>
                           </td>
                           <td>
                                <label for="surname">
                                Cognome: <b style="color: darkred">*</b>
                                </label>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <input id = 'name' required maxlength="20" type="text" name = "name" />
                               <br>
                               <br>
                           </td>
                           <td>
                               <input id = 'surname' required maxlength="20" type="text" name = "surname" />
                               <br>
                               <br>
                           </td>
                       </tr>

                       <tr>
                           <td>
                               <label for="adress">
                                   Indirizzo: <b style="color: darkred">*</b>
                               </label>
                           </td>
                           <td>
                               <label for="phone">
                                   Telefono:
                               </label>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <input id = 'adress' required maxlength="100" type="text" name = "adress" />
                               <br>
                               <br>
                           </td>
                           <td>
                               <input id = 'phone' maxlength="10" type="tel" name = "phone" />
                               <br>
                               <br>
                           </td>
                       </tr>

                       <tr>
                           <td>
                               <label for="username">
                                   Username: <b style="color: darkred">*</b>
                               </label>
                           </td>
                           <td>
                               <label for="repeatUsername">
                                   Ripeti Username: <b style="color: darkred">*</b>
                               </label>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <input id = 'username' required maxlength="20" type="text" name = "username" />
                               <br>
                               <br>
                           </td>
                           <td>
                               <input id = 'repeatUsername' required maxlength="20" type="text" name = "repeatUsername" />
                               <br>
                               <br>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <label for="password">
                                   Password: <b style="color: darkred">*</b>
                               </label>
                           </td>
                           <td>
                               <label for="repeatPassword">
                                   Ripeti Password: <b style="color: darkred">*</b>
                               </label>
                           </td>
                       </tr>
                       <tr>
                           <td>
                               <input id = 'password' required maxlength="20" type="password" name = "password" />
                               <br>
                               <br>
                           </td>
                           <td>
                               <input id = 'repeatPassword' required maxlength="20" type="password" name = "repeatPassword" />
                               <br>
                               <br>
                           </td>
                       </tr>
                       <tr>
                           <td>
                           <button class = "confirmButton" type="submit" name = "submit">Registrati</button>
                           </td>
                           <td>
                               <span class = "info">
                               Hai già un account?<a href="login.php"> Login!</a>
                               </span>
                           </td>
                       </tr>
                   </table>
                </form>

                <div class="error_messages">
                    * Campi Obbligatori.
                </div>


                <div class="error_messages">
                <?php
                if (!empty ($_POST['name']) && !empty ($_POST['surname']) &&
                    !empty($_POST['adress']) && !empty($_POST['username']) &&
                    !empty($_POST['repeatUsername']) && !empty($_POST['password']) &&
                    !empty($_POST['repeatPassword']) && isset ($_POST['submit'])) {

                    if ($_POST['username'] == $_POST['repeatUsername'] && $_POST['password'] == $_POST['repeatPassword']) {
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }
                        try {
                            $query = "
                              INSERT INTO user_table (name, surname, adress, phone, login, password, admin) 
                              VALUES (:name, :surname, :adress, :phone, :username, :password, false)
                            ";
                            $statement = $database_handler->prepare($query);
                            $statement->bindParam(':name', $_POST['name']);
                            $statement->bindParam(':surname', $_POST['surname']);
                            $statement->bindParam(':adress', $_POST['adress']);
                            $statement->bindParam(':phone', $_POST['phone']);
                            $statement->bindParam(':username', $_POST['username']);
                            $statement->bindParam(':password', $_POST['password']);
                            $database_handler->beginTransaction();
                            $statement->execute();
                            $database_handler->commit();
                            $result = $statement->fetch();
                            $_SESSION['username'] = $_POST['username'];
                            $_SESSION['user'] = 'user';
                            header("Refresh: 4; url = ..\index\home.php");
                            echo 'Registrazione effettuata';
                            $database_handler = null;
                        } catch (PDOException $e) {
                            echo "index non disponibile";
                            $database_handler = null;
                        }
                    }

                    else
                        if ($_POST['username'] != $_POST['repeatUsername'] && $_POST['password'] != $_POST['repeatPassword']) {
                            echo 'Inserire stesso Username e stessa Password';
                        }

                        else {
                            if ($_POST['username'] != $_POST['repeatUsername']) {
                                echo 'Inserire stesso Username';
                            }
                            else {
                                echo 'Inserire stessa Password';
                            }
                        }
                }

                ?>
                </div>

            </div>

            <br>
            <br>

        </section>

        <?php
        include_once '..\templates\footer.php';
        ?>

    </body>

</html>