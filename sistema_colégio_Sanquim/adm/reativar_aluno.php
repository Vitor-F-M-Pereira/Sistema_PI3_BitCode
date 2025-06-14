<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE alunos_aceitos SET ativo = 1 WHERE id_aluno = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['mensagem_sucesso'] = "Aluno reativado com sucesso!";
}

header("Location: alunos_aprovados.php");
exit;
?>
