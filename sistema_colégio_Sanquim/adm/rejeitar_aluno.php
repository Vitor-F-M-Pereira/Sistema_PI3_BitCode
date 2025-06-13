<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['mensagem_sucesso'] = "ID do aluno não informado.";
    header("Location: alunos_pendentes.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM alunos_pendentes WHERE id_aluno = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensagem_sucesso'] = "Aluno rejeitado e removido do sistema com sucesso!";
} else {
    $_SESSION['mensagem_sucesso'] = "Não foi possível excluir o aluno. Ele pode já ter sido removido.";
}

header("Location: alunos_pendentes.php");
exit;
