<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

if (isset($_SESSION['sucesso'])) {
    $mostrarModal = true;
    $tituloModal = "Sucesso!";
    $mensagemModal = $_SESSION['sucesso'];
    $classeModal = "modal-success";
    unset($_SESSION['sucesso']);
}

if (isset($_SESSION['erro'])) {
    $mostrarModal = true;
    $tituloModal = "Erro!";
    $mensagemModal = $_SESSION['erro'];
    $classeModal = "modal-danger";
    unset($_SESSION['erro']);
}

$filtro = $_GET['filtro'] ?? 'ambos';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 4;
$offset = ($pagina - 1) * $porPagina;

$where = "";

if ($filtro == 'prevestibular') {
    $where = "WHERE ativo = 1 AND (curso = 'Pré-Vestibular' OR curso = 'Ambos')";
} elseif ($filtro == 'prevestibulinho') {
    $where = "WHERE ativo = 1 AND (curso = 'Pré-Vestibulinho' OR curso = 'Ambos')";
} elseif ($filtro == 'inativas') {
    $where = "WHERE ativo = 0";
} else {
    $where = "WHERE ativo = 1";
}

$sql_total = "SELECT COUNT(*) AS total FROM materias $where";
$total_result = $conn->query($sql_total)->fetch_assoc();
$total_paginas = ceil($total_result['total'] / $porPagina);

$sql = "SELECT id, nome_materia, login_materia, curso, ativo FROM materias $where ORDER BY nome_materia ASC LIMIT $porPagina OFFSET $offset";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Controle de Matérias - Colégio Sanquim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .control-container {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--sombra);
        }
        .control-header {
            color: var(--verde-escuro);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .filter-btn {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .filter-btn.active {
            background-color: var(--verde-principal);
            color: white;
        }
        .filter-btn:not(.active) {
            border: 1px solid #dee2e6;
        }
        .filter-btn:not(.active):hover {
            background-color: #f8f9fa;
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
        }
        .table-custom td, .table-custom th {
            height: 70px;
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .table-custom tbody tr:hover {
            background-color: rgba(0, 136, 148, 0.05);
        }
        .password-dots {
            letter-spacing: 3px;
            font-weight: bold;
            color: #6c757d;
        }
        .badge-course {
            padding: 0.5em 0.8em;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-prevestibular { background-color: #e3f2fd; color: #0d6efd; }
        .badge-prevestibulinho { background-color: #e8f5e9; color: #198754; }
        .badge-ambos { background-color: #fff3e0; color: #fd7e14; }
        .badge-ativo { background-color: #d1e7dd; color: #0f5132; }
        .badge-inativo { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-cogs me-2"></i>Controle de Matérias</h1>
        <p class="lead">Edite, filtre ou exclua matérias cadastradas</p>
    </div>
</section>

<div class="container pb-4">
    <div class="control-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="control-header">Lista de Matérias</h2>
            <div class="d-flex gap-2 flex-wrap">
                <a href="controle_materias.php?filtro=ambos&pagina=1" class="filter-btn <?= $filtro == 'ambos' ? 'active' : '' ?>">Todos</a>
                <a href="controle_materias.php?filtro=prevestibular&pagina=1" class="filter-btn <?= $filtro == 'prevestibular' ? 'active' : '' ?>">Pré-Vestibular</a>
                <a href="controle_materias.php?filtro=prevestibulinho&pagina=1" class="filter-btn <?= $filtro == 'prevestibulinho' ? 'active' : '' ?>">Pré-Vestibulinho</a>
                <a href="controle_materias.php?filtro=inativas&pagina=1" class="filter-btn <?= $filtro == 'inativas' ? 'active' : '' ?>">Inativas</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Matéria</th>
                        <th>Login</th>
                        <th>Senha</th>
                        <th>Curso</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($materia = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($materia['nome_materia']) ?></td>
                            <td><?= htmlspecialchars($materia['login_materia']) ?></td>
                            <td class="password-dots">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</td>
                            <td>
                                <?php
                                $badgeClass = 'badge-ambos';
                                if ($materia['curso'] === 'Pré-Vestibular') $badgeClass = 'badge-prevestibular';
                                if ($materia['curso'] === 'Pré-Vestibulinho') $badgeClass = 'badge-prevestibulinho';
                                ?>
                                <span class="badge-course <?= $badgeClass ?>"><?= htmlspecialchars($materia['curso']) ?></span>
                            </td>
                            <td>
                                <?php if ($materia['ativo']): ?>
                                    <span class="badge badge-ativo">Ativa</span>
                                <?php else: ?>
                                    <span class="badge badge-inativo">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="editar_materia.php?id=<?= $materia['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </a>
                                    <?php if ($materia['ativo'] == 1): ?>
                                        <a href="excluir_materia.php?id=<?= $materia['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja inativar esta matéria?')">
                                            <i class="fas fa-trash-alt me-1"></i>Inativar
                                        </a>
                                    <?php else: ?>
                                        <a href="reativar_materia.php?id=<?= $materia['id'] ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Deseja reativar esta matéria?')">
                                            <i class="fas fa-undo me-1"></i>Reativar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-book-open fa-2x mb-3"></i>
                            <h5>Nenhuma matéria encontrada</h5>
                            <p class="mb-0">Verifique os filtros aplicados.</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                            <a class="page-link" href="controle_materias.php?filtro=<?= $filtro ?>&pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="cadastro_materia.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Nova Matéria
            </a>
            <a href="painel_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
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
