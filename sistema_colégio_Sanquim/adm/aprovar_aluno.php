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


$stmt = $conn->prepare("SELECT * FROM alunos_pendentes WHERE id_aluno = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensagem_sucesso'] = "Aluno não encontrado.";
    header("Location: alunos_pendentes.php");
    exit;
}

$aluno = $result->fetch_assoc();
$curso = $aluno['curso'];


$verifica = $conn->prepare("SELECT COUNT(*) AS total FROM alunos_aceitos WHERE curso = ? AND ativo = 1");
$verifica->bind_param("s", $curso);
$verifica->execute();
$verifica->bind_result($total);
$verifica->fetch();
$verifica->close();

if ($total >= 20) {
    $_SESSION['mensagem_sucesso'] = "Não é possível aprovar mais alunos para o curso $curso. Limite de 20 atingido.";
    header("Location: alunos_pendentes.php");
    exit;
}

$stmtInsert = $conn->prepare("
    INSERT INTO alunos_aceitos (
        nome, cpf, data_nascimento, cep, rua, bairro, cidade, estado, numero, tipo_residencia,
        nome_responsavel, cpf_responsavel, curso, comprovante_residencia
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmtInsert->bind_param(
    "ssssssssssssss",
    $aluno['nome'],
    $aluno['cpf'],
    $aluno['data_nascimento'],
    $aluno['cep'],
    $aluno['rua'],
    $aluno['bairro'],
    $aluno['cidade'],
    $aluno['estado'],
    $aluno['numero'],
    $aluno['tipo_residencia'],
    $aluno['nome_responsavel'],
    $aluno['cpf_responsavel'],
    $aluno['curso'],
    $aluno['comprovante_residencia']
);

if ($stmtInsert->execute()) {
    $stmtDelete = $conn->prepare("DELETE FROM alunos_pendentes WHERE id_aluno = ?");
    $stmtDelete->bind_param("i", $id);
    $stmtDelete->execute();
    $stmtDelete->close();

    $_SESSION['mensagem_sucesso'] = "Aluno aprovado e removido da lista de pendentes!";
} else {
    $_SESSION['mensagem_sucesso'] = "Erro ao aprovar o aluno: " . $conn->error;
}

$stmtInsert->close();
header("Location: alunos_pendentes.php");
exit;
?>
