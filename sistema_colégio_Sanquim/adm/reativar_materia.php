<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "ID da matéria não informado.";
    header("Location: controle_materias.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE materias SET ativo = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['sucesso'] = "Matéria reativada com sucesso!";
} else {
    $_SESSION['erro'] = "Não foi possível reativar. Verifique se a matéria já está ativa ou se o ID é válido.";
}

header("Location: controle_materias.php?filtro=inativas&pagina=1");
exit;
