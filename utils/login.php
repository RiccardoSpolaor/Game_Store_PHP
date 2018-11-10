<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['username'])) {
    if ($_SESSION['user'] == 'user') {
        header("Location: ..\index\home.php");
    }
    else {
        header("Location: ..\admin\admin_panel.php");
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

                    <label for="username">
                    Username:
                    </label>
                    <br>

                    <input id = 'username' required maxlength="20" type="text" name = "username" />
                    <br>
                    <br>


                    <label for="password">
                    Password:
                    </label>
                    <br>

                    <input id = 'password' required maxlength="20" type="password" name = "password" />
                    <br>
                    <br>


                    <button type="submit" name = "submit">
                        Login
                    </button>

                    <span class = "info">
                        Non possiedi un account?<a href="sign_up.php"> Registrati!</a>
                    </span>

                </form>

                <div class="error_messages">
                <?php

                if (!empty ($_POST['username']) && !empty ($_POST['password']) && isset ($_POST['submit'])) {

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
                        WHERE login = :username AND password = :password
                    ";

                    $statement = $database_handler->prepare($query);
                    $statement -> bindParam (":username", $_POST['username']);
                    $statement -> bindParam (":password", $_POST['password']);
                    $database_handler->beginTransaction();
                    $statement->execute();
                    $database_handler->commit();
                    $result = $statement->fetch(PDO::FETCH_OBJ);
                    if ($result) {
                        $_SESSION['username'] = $_POST['username'];
                        if ($result->admin == 'true') {
                            $_SESSION['user'] = 'admin';
                            if (isset($_SESSION['cart']))
                                unset ($_SESSION['cart']);
                            header("Location: ..\admin\admin_panel.php");
                        }
                        else {
                            $_SESSION['user'] = 'user';
                            header("Location: ..\index\home.php");
                        }
                    }
                    else {
                        echo "Login o password errati.";
                    }
                    $database_handler = null;
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