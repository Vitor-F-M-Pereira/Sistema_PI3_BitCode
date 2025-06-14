<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $conn->begin_transaction();
        $sql_update_planos = "UPDATE planos_aula SET ativo = 0 WHERE id_materia = ?";
        $stmt = $conn->prepare($sql_update_planos);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $sql_update_registros = "UPDATE registro_aulas SET ativo = 0 WHERE id_plano IN (SELECT id FROM planos_aula WHERE id_materia = ?)";
        $stmt = $conn->prepare($sql_update_registros);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $sql_update_materia = "UPDATE materias SET ativo = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql_update_materia);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $_SESSION['sucesso'] = "Matéria inativada com sucesso!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['erro'] = "Erro ao inativar matéria: " . $e->getMessage();
    }
}

header("Location: controle_materias.php");
exit;
?>
