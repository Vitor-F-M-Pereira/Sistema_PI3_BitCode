<?php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['mensagem_logout'] = "Logout realizado com sucesso!";
header("Location: index.php");
exit;
