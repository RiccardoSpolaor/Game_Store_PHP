<?php
include_once "..\config\configurations.php";

if ($_SESSION['user'] != 'admin') {
    header ('Location: ..\Utils\logout.php');
}
?>

<html>

    <body>

        <?php
        include_once "../templates/header.php";
        ?>

        <?php

        $id = null;

        if (isset($_POST['products_button']))
            $id = 'products';


        elseif (isset($_POST['admins_button']))
            $id = 'admins';


        elseif (isset($_POST['users_button']))
            $id = 'users';

        elseif (isset($_POST['orders_button']))
            $id = 'orders';

        else
            $id = 'users';

        ?>

        <section class="main_content">

            <br>
            <br>

            <div class = 'admin_layout' align="center">
                <?php

                switch ($id) {
                    case ('products'): {
                        include_once 'admin_product.php';
                        break;
                    }
                    case ('users'): {
                        include_once 'admin_user.php';
                        break;
                    }
                    case ('orders'): {
                        include_once 'admin_order.php';
                        break;
                    }
                    case ('admins'): {
                        include_once 'admin_admin.php';
                        break;
                    }
                    default: {
                        include_once 'admin_user.php';
                        break;
                    }
                }
                ?>
            </div>
        </section>

    <?php
    include_once '..\templates\footer.php';
    ?>

    </body>

</html>