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

        $sql_select_planos = "SELECT id FROM planos_aula WHERE id_materia = ?";
        $stmt = $conn->prepare($sql_select_planos);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids_planos = [];
        while ($row = $result->fetch_assoc()) {
            $ids_planos[] = $row['id'];
        }
        $stmt->close();

        if (!empty($ids_planos)) {
            $placeholders = implode(',', array_fill(0, count($ids_planos), '?'));
            $sql_update_registros = "UPDATE registro_aulas SET ativo = 0 WHERE id_plano IN ($placeholders)";
            $stmt = $conn->prepare($sql_update_registros);
            $types = str_repeat('i', count($ids_planos));
            $stmt->bind_param($types, ...$ids_planos);
            $stmt->execute();
            $stmt->close();
        }

        $sql_update_planos = "UPDATE planos_aula SET ativo = 0 WHERE id_materia = ?";
        $stmt = $conn->prepare($sql_update_planos);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $sql_update_materia = "UPDATE materias SET ativo = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql_update_materia);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $_SESSION['sucesso'] = "Matéria excluída com sucesso!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['erro'] = "Erro ao excluir matéria: " . $e->getMessage();
    }
}

header("Location: controle_materias.php");
exit;
