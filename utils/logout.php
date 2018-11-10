<?php

session_start();

unset($_SESSION['username']);
unset($_SESSION['user']);
unset($_SESSION['cart']);

session_gc();
session_destroy();

header ('Location: ..\Utils\login.php');