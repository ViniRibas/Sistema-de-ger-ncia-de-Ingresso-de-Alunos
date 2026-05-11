<?php
session_start();

$_SESSION = array();

session_destroy();

setcookie("usuario_logado", "", time() - 7000, "/");

header("Location: ../paginas/login.php");
exit();
?>
