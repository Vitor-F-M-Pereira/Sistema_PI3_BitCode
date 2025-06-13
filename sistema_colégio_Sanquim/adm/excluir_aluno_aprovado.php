<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['mensagem_sucesso'] = "ID do aluno não informado.";
    header("Location: alunos_aprovados.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE alunos_aceitos SET ativo = 0 WHERE id_aluno = ? AND ativo = 1");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensagem_sucesso'] = "Aluno excluído com sucesso.";
} else {
    $_SESSION['mensagem_sucesso'] = "Este aluno já foi excluído ou não existe.";
}

header("Location: alunos_aprovados.php");
exit;
?>
