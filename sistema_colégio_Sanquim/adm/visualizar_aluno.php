<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

if (isset($_SESSION['mensagem_sucesso'])) {
    $mostrarModal = true;
    $tituloModal = "Sucesso!";
    $mensagemModal = $_SESSION['mensagem_sucesso'];
    $classeModal = "modal-success";
    unset($_SESSION['mensagem_sucesso']);
} elseif (isset($_SESSION['mensagem_erro'])) {
    $mostrarModal = true;
    $tituloModal = "Erro!";
    $mensagemModal = $_SESSION['mensagem_erro'];
    $classeModal = "modal-danger";
    unset($_SESSION['mensagem_erro']);
}

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger text-center'>ID do aluno não informado.</div>";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM alunos_pendentes WHERE id_aluno = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning text-center'>Aluno não encontrado.</div>";
    exit;
}

$aluno = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dados do Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .topo {
            background: linear-gradient(135deg, var(--verde-escuro), var(--verde-principal));
            color: white;
            padding: 2rem 0;
            text-align: center;
            border-radius: 0 0 30px 30px;
            box-shadow: var(--sombra);
            margin-bottom: 2rem;
        }

        .card-info {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--sombra);
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .card-info p {
            margin-bottom: 0.8rem;
        }

        .card-info strong {
            color: var(--verde-escuro);
        }
    </style>
</head>
<body>
    <section class="topo">
        <div class="container">
            <h1><i class="fas fa-user me-2"></i>Visualizar Dados do Aluno</h1>
        </div>
    </section>

    <div class="container mb-5">
        <div class="card-info">
            <p><strong>Nome:</strong> <?= htmlspecialchars($aluno['nome']) ?></p>
            <p><strong>CPF:</strong> <?= htmlspecialchars($aluno['cpf']) ?></p>
            <p><strong>Data de Nascimento:</strong> <?= date("d/m/Y", strtotime($aluno['data_nascimento'])) ?></p>
            <p><strong>CEP:</strong> <?= htmlspecialchars($aluno['cep']) ?></p>
            <p><strong>Endereço:</strong> <?= "{$aluno['rua']}, Nº {$aluno['numero']} - {$aluno['bairro']} - {$aluno['cidade']}/{$aluno['estado']}" ?></p>
            <p><strong>Tipo de Residência:</strong> <?= htmlspecialchars($aluno['tipo_residencia']) ?></p>

            <?php if (!empty($aluno['nome_responsavel'])): ?>
                <p><strong>Responsável:</strong> <?= htmlspecialchars($aluno['nome_responsavel']) ?></p>
                <p><strong>CPF do Responsável:</strong> <?= htmlspecialchars($aluno['cpf_responsavel']) ?></p>
            <?php endif; ?>

            <p><strong>Curso:</strong> <?= htmlspecialchars($aluno['curso']) ?></p>

            <?php if (!empty($aluno['comprovante_residencia'])): ?>
                <p><strong>Comprovante de Residência:</strong>
                    <a href="../comprovantes/<?= htmlspecialchars($aluno['comprovante_residencia']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-alt me-1"></i>Visualizar Arquivo
                    </a>
                </p>
            <?php else: ?>
                <p><strong>Comprovante de Residência:</strong> Não enviado.</p>
            <?php endif; ?>
        </div>

        <div class="text-end mt-4">
            <a href="alunos_pendentes.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
            <a href="aprovar_aluno.php?id=<?= $aluno['id_aluno'] ?>" class="btn btn-success">
                <i class="fas fa-check me-1"></i>Aprovar
            </a>
            <a href="rejeitar_aluno.php?id=<?= $aluno['id_aluno'] ?>" class="btn btn-danger"
               onclick="return confirm('Tem certeza que deseja rejeitar este aluno?')">
               <i class="fas fa-times me-1"></i>Rejeitar
            </a>
        </div>
    </div>

    <?php if ($mostrarModal): ?>
    <div class="modal fade <?= $classeModal ?>" id="modalFeedback" tabindex="-1" aria-labelledby="modalFeedbackLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= $tituloModal ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body text-center">
            <p><?= $mensagemModal ?></p>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalFeedback'));
        modal.show();
    });
    </script>
    <?php endif; ?>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
