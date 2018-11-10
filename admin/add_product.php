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

                    <div class = 'panel_title' id = 'products_title' align="center">
                        AGGIUNTA NUOVO PRODOTTO
                    </div>

                </section>
            </div>

            <div class="container">
                <form action = "<?php echo htmlentities ($_SERVER['PHP_SELF']); ?>" method = "POST" enctype="multipart/form-data">

                    <label for="title">
                        Titolo: <b style="color: darkred">*</b> <br>
                    </label>
                    <input id = 'title' required maxlength="50" type="text" name = "title" />

                    <br>
                    <br>

                    <label for="price">
                        Prezzo: <b style="color: darkred">*</b> <br>
                    </label>
                    € &nbsp;
                    <input id = 'price' type="number" name = 'price_round' min="0"/> .
                    <input id = 'price' type="number" name = 'price_cents' min="0" max="99" value="0" style="display: inline">

                    <br>
                    <br>

                    <label for="console">
                        Console: <b style="color: darkred">*</b> <br>
                    </label>
                    <ul class='fieldset' style="padding-left: 0">
                        <li style="margin-left: 0">
                            <label>
                            <input type ='radio' value = 'PS4' name="console" checked required />
                            <img src="../web/icons/PS.png" height="20" width="20" style="vertical-align: middle"/>
                            &nbsp; PS4
                            </label>
                        </li>
                        <li style="margin-left: 0">
                            <label>
                            <input type ='radio' value = 'Xbox One' name="console" required/>
                            <img src="../web/icons/XBOX.png" height="20" style="vertical-align: middle"/>
                            &nbsp; Xbox One
                            </label>
                        </li>
                        <li style="margin-left: 0">
                            <label>
                            <input type ='radio' value = 'Switch' name="console" required/>
                            <img src="../web/icons/NINTENDO.png" height="20" style="vertical-align: middle"/>
                            &nbsp; Switch
                            </label>
                        </li>
                        <li style="margin-left: 0">
                            <label>
                            <input type ='radio' value = 'PC' name="console" required/>
                            <img src="../web/icons/PC.png" height="20" style="vertical-align: middle"/>
                            &nbsp; PC
                            </label>
                        </li>
                    </ul>

                    <br>

                    <label for = 'numberToAdd'>
                        Quantità: <b style="color: darkred">*</b> <br>
                    </label>
                    <input id = 'numberToAdd' name="numberToAdd" type="number" min="0"/>

                    <br>
                    <br>

                    <label for = 'file'>
                        Copertina: <br>
                    </label>
                    <input type="file" name ='file'>

                    <br>
                    <br>

                    <button class = "action_button" type="submit" name = "submit">
                        <img src="../web/icons/add_product.png" height= '20'  style="vertical-align: middle"/>
                        Aggiungi Gioco
                    </button>
                </form>

                <div class="error_messages">
                    * Campi Obbligatori.
                </div>

                <div class="error_messages">

                <?php
                if (!empty ($_POST['title']) && !empty ($_POST['price_round']) &&
                    isset($_POST['console']) && !empty($_POST['numberToAdd']) && isset ($_POST['submit'])) {


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

                        $query = "
                          SELECT MAX(id) as idMax
                          FROM videogame
                        ";
                        $statement = $database_handler->prepare($query);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetchColumn();

                        $idGame = null;
                        if ($result) {
                            $idGame = $result + 1;
                        } else
                            $idGame = 1;
                        $query = "
                          INSERT INTO videogame (id, title, price, console, amount) 
                          VALUES (:id, :title, :price, :console, :amount)
                        ";
                        $statement = $database_handler->prepare($query);
                        $statement->bindParam(':id', $idGame);
                        $statement->bindParam(':title', $_POST['title']);
                        $price = intval($_POST['price_round']);
                        $price_cents = isset($_POST['price_cents'])? intval($_POST['price_cents']):0;
                        $price += ($price_cents * 0.01);
                        $statement->bindParam(':price', $price);
                        $statement->bindParam(':console', $_POST['console']);
                        $statement->bindParam(':amount', $_POST['numberToAdd']);
                        $database_handler->beginTransaction();
                        $statement->execute();
                        $database_handler->commit();
                        $result = $statement->fetch();
                        $database_handler = null;
                    }
                    if ($upload) {
                        $fileDestination = "..\web\images\\".$idGame.'.'.$fileExt;
                        move_uploaded_file($fileTmp, $fileDestination);
                    }

                    if ($alright) {
                        header ('Location: admin_panel.php');
                    }

                    else {
                        echo 'Inserire dati.';
                    }

                }

                ?>
                </div>
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