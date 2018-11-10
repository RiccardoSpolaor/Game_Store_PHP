<?php
include_once "..\config\configurations.php";

if (isset($_SESSION['user'])&&$_SESSION['user'] == 'admin') {
    header ('Location: ..\admin\admin_panel.php');
}

try {
    $database_handler = new PDO ($dns);
    $database_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}

if (!isset ($_SESSION['cart']))
    $_SESSION ['cart'] = new Cart();

$temp_cart = $_SESSION['cart'];

if (isset($_GET['id']) && isset($_GET['action']) && isset($_GET['amount'])) {
    switch ($_GET['action']) {
        case ('add'): {
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
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $database_handler = null;
            $item = array();
            foreach ($result as $row) {
                $item = array('id' => $row['id'], 'title' => $row['title'], 'price' => $row['price'], 'console' => $row['console']);
            }
            $temp_cart->addItem($item, $_GET['amount']);
            break;
        }

        case ('remove'): {
            $temp_cart->removeItem($_GET['id'], $_GET['amount']);
            if ($temp_cart->getTotalItems()==0) {
                $temp_cart = null;
            }
            break;
        }
        default: {break;}
    }
}

$_SESSION['cart'] = $temp_cart;

header('Location: cart_view.php');