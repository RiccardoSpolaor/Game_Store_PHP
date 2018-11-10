<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}
?>

<html>

    <body>

        <button onclick="topFunction()" id = 'top_button' style="display: none">
            TOP
        </button>

        <script type="text/javascript">
            window.onscroll = function () {
                if (document.body.scrollTop > 20) {
                    document.getElementById('top_button').style.display = 'block';
                }

                else {
                    document.getElementById('top_button').style.display = 'none';
                }
            }

            function topFunction() {
                document.body.scrollTop = 0;

            }
        </script>

        <footer>
            <p align="center" >
                © 2018 Riccardo Spolaor. <br>
                PlayStation © Sony Corporation , Xbox © Microsoft, Nintendo Switch © Nintendo. <br>
                All icons © their respective owners.
            </p>
        </footer>

    </body>

</html>