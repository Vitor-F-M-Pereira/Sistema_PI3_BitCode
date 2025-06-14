<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$cursoSelecionado = $_GET['curso'] ?? 'todos';
$statusSelecionado = $_GET['status'] ?? 'ativos';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$limite = 5;
$offset = ($pagina - 1) * $limite;

$params = [];
$tipos = "";

$queryBase = "FROM alunos_aceitos";
$filtro = " WHERE 1=1";


if ($cursoSelecionado !== 'todos') {
    $filtro .= " AND curso = ?";
    $params[] = $cursoSelecionado;
    $tipos .= "s";
}


if ($statusSelecionado === 'ativos') {
    $filtro .= " AND ativo = 1";
} elseif ($statusSelecionado === 'inativos') {
    $filtro .= " AND ativo = 0";
}

$queryCount = "SELECT COUNT(*) $queryBase $filtro";
$queryData = "SELECT id_aluno, nome, cpf, curso, faltas, ativo $queryBase $filtro ORDER BY nome LIMIT $limite OFFSET $offset";

$stmtCount = $conn->prepare($queryCount);
if ($params) $stmtCount->bind_param($tipos, ...$params);
$stmtCount->execute();
$stmtCount->bind_result($totalAlunos);
$stmtCount->fetch();
$stmtCount->close();

$stmt = $conn->prepare($queryData);
if ($params) $stmt->bind_param($tipos, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$totalPaginas = ceil($totalAlunos / $limite);


if (isset($_SESSION['mensagem_sucesso'])) {
    $mostrarModal = true;
    $tituloModal = "Sucesso!";
    $mensagemModal = $_SESSION['mensagem_sucesso'];
    $classeModal = "modal-success";
    unset($_SESSION['mensagem_sucesso']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alunos Aprovados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

    .table-custom td, .table-custom th {
        height: 70px;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
        text-align: center;
    }
    .table-custom td:first-child {
        text-align: left;
    }

    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-custom thead,
    .table-custom tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    .table-custom tbody {
        display: block;
        min-height: 320px;
    }
    .table-custom thead th {
        background-color: var(--verde-principal);
        color: white;
        font-weight: 600;
        padding: 1rem;
        text-align: center;
    }
    
    .table-custom tbody tr:hover {
        background-color: rgba(0, 136, 148, 0.05);
    }
    .badge-course {
        padding: 0.5em 0.8em;
        font-weight: 600;
        border-radius: 4px;
    }
    .badge-prevestibular { background-color: #e3f2fd; color: #0d6efd; }
    .badge-prevestibulinho { background-color: #e8f5e9; color: #198754; }
    .badge-excesso-faltas { background-color: #f8d7da; color: #842029; font-size: 0.8rem; letter-spacing: normal; }
    .btn-action { border-radius: 4px; padding: 0.5rem 1rem; font-size: 0.85rem; }
    </style>
</head>
<body>
<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-user-check me-2"></i>Alunos Aprovados</h1>
    </div>
</section>

<div class="container container-custom">
    <form method="get" class="text-center mb-4">
        <div class="row justify-content-center">
            <div class="col-md-4 mb-2">
                <label for="curso" class="form-label me-2">Filtrar por Curso:</label>
                <select name="curso" id="curso" class="form-select" onchange="this.form.submit()">
                    <option value="todos" <?= $cursoSelecionado === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="Pré-Vestibular" <?= $cursoSelecionado === 'Pré-Vestibular' ? 'selected' : '' ?>>Pré-Vestibular</option>
                    <option value="Pré-Vestibulinho" <?= $cursoSelecionado === 'Pré-Vestibulinho' ? 'selected' : '' ?>>Pré-Vestibulinho</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label for="status" class="form-label me-2">Status:</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="ativos" <?= $statusSelecionado === 'ativos' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inativos" <?= $statusSelecionado === 'inativos' ? 'selected' : '' ?>>Inativos</option>
                    <option value="todos" <?= $statusSelecionado === 'todos' ? 'selected' : '' ?>>Todos</option>
                </select>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nome do Aluno</th>
                    <th>CPF</th>
                    <th>Curso</th>
                    <th>Faltas</th>
                    <th style="width: 260px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-start">
                                <?= htmlspecialchars($row['nome']) ?>
                                <?php if ($row['faltas'] > 10): ?>
                                    <span class="badge-excesso-faltas ms-2">Excesso de faltas</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['cpf']) ?></td>
                            <td>
                                <span class="badge-course <?= $row['curso'] === 'Pré-Vestibular' ? 'badge-prevestibular' : 'badge-prevestibulinho' ?>">
                                    <?= htmlspecialchars($row['curso']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="faltas_aluno.php?id=<?= $row['id_aluno'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver Faltas
                                </a>
                            </td>
                            <td>
                                <?php if ($row['ativo'] == 1): ?>
                                    <a href="excluir_aluno_aprovado.php?id=<?= $row['id_aluno'] ?>" class="btn-action btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">
                                        <i class="fas fa-trash-alt me-1"></i>Inativar
                                    </a>
                                <?php else: ?>
                                    <a href="reativar_aluno.php?id=<?= $row['id_aluno'] ?>" class="btn-action btn btn-success btn-sm" onclick="return confirm('Tem certeza que deseja reativar este aluno?')">
                                        <i class="fas fa-redo-alt me-1"></i>Reativar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted text-center py-4">Nenhum aluno encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
    <nav>
        <ul class="pagination justify-content-center mt-3">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?curso=<?= urlencode($cursoSelecionado) ?>&status=<?= urlencode($statusSelecionado) ?>&pagina=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <div class="text-end mt-4">
        <a href="controle_alunos.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
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
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
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