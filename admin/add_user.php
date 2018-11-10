<?php
include_once "..\config\configurations.php";


if (!isset($_SESSION['user']) || (isset($_SESSION['user']) && $_SESSION['user'] != 'admin')) {
    header ('Location: ..\utils\logout.php');
}
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
                <section class = 'panel'>

                    <?php include_once 'admin_buttons.php'; ?>

                    <br>
                    <br>
                    <div class = 'panel_title' id = 'users_title' align="center">
                        AGGIUNTA NUOVO UTENTE
                    </div>
                </section>
            </div>

            <div class="container">
                <form class = 'login' action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST">

                    <label for="name">
                        Name: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'name' required maxlength="20" type="text" name = "name" />

                    <br>
                    <br>

                    <label for="surname">
                        Surname: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'surname' required maxlength="20" type="text" name = "surname" />

                    <br>
                    <br>

                    <label for="adress">
                        Indirizzo: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'adress' required maxlength="100" type="text" name = "adress" />

                    <br>
                    <br>

                    <label for="phone">
                        Telefono:
                        <br>
                    </label>
                    <input id = 'phone'  maxlength="10" type="tel" name = "phone" />

                    <br>
                    <br>

                    <label for="username">
                        Username: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'username' required maxlength="20" type="text" name = "username" />

                    <br>
                    <br>

                    <label for="repeatUsername">
                        Ripeti Username: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'repeatUsername' required maxlength="20" type="text" name = "repeatUsername" />

                    <br>
                    <br>

                    <label for="password">
                        Password: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'password' required maxlength="20" type="password" name = "password" />

                    <br>
                    <br>

                    <label for="repeatPassword">
                        Ripeti Password: <b style="color: darkred">*</b>
                        <br>
                    </label>
                    <input id = 'repeatPassword' required maxlength="20" type="password" name = "repeatPassword" />

                    <br>
                    <br>

                    <button class = 'action_button' type="submit" name="submit">
                        <img src="../web/icons/add_user.png" height= '20' style="vertical-align: middle"/>
                        Registra Nuovo Utente
                    </button>

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

                    if ($_POST['username']==$_POST['repeatUsername'] && $_POST['password']==$_POST['repeatPassword']) {
                        try {
                            $database_handler = new PDO ($dns);
                            $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                            die();
                        }
                        try {
                            $query = "
                              INSERT INTO user_table (name, surname, adress, phone, login, password) 
                              VALUES (:name, :surname, :adress, :phone, :username, :password)
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
                            header("Refresh: 4; url = admin_panel.php");
                            echo 'Registrazione nuovo utente effettuata';
                            $database_handler = null;
                        } catch (PDOException $e) {
                            echo "Username non disponibile";
                            $database_handler = null;
                        }
                    }

                    else
                        echo 'Inserire stesso username e Password';
                }
                ?>

                </div>
                <br>
                <br>
                <br>
                <br>
                <br>

            </div>

        </section>

    <?php
    include_once '..\templates\footer.php';
    ?>

    </body>
</html>