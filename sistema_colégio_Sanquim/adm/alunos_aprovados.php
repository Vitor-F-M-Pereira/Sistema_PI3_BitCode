<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$cursoSelecionado = $_GET['curso'] ?? 'todos';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$limite = 5;
$offset = ($pagina - 1) * $limite;

$params = [];
$tipos = "";

$queryBase = "FROM alunos_aceitos";
$filtro = " WHERE ativo = 1";

if ($cursoSelecionado !== 'todos') {
    $filtro .= " AND curso = ?";
    $params[] = $cursoSelecionado;
    $tipos .= "s";
}

$queryCount = "SELECT COUNT(*) $queryBase $filtro";
$queryData = "SELECT id_aluno, nome, cpf, curso, faltas $queryBase $filtro ORDER BY nome LIMIT $limite OFFSET $offset";

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

    .table-custom td, .table-custom th {
        height: 70px;
        padding: 1rem;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .table-custom tbody tr:hover {
        background-color: rgba(0, 136, 148, 0.05);
    }

    .badge-course {
        padding: 0.5em 0.8em;
        font-weight: 600;
        border-radius: 4px;
    }

    .badge-prevestibular { 
        background-color: #e3f2fd; 
        color: #0d6efd; 
    }
    
    .badge-prevestibulinho { 
        background-color: #e8f5e9; 
        color: #198754; 
    }
    
    .badge-excesso-faltas {
        background-color: #f8d7da;
        color: #842029;
        font-size: 0.8rem;
        letter-spacing: normal;
    }

    .btn-action {
        border-radius: 4px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-user-check me-2"></i>Alunos Aprovados</h1>
        </div>
    </section>

    <div class="container container-custom">
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['mensagem_sucesso'] ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>

        <form method="get" class="text-center mb-4">
            <label for="curso" class="form-label me-2">Filtrar por Curso:</label>
            <select name="curso" id="curso" class="form-select d-inline-block" onchange="this.form.submit()">
                <option value="todos" <?= $cursoSelecionado === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="Pré-Vestibular" <?= $cursoSelecionado === 'Pré-Vestibular' ? 'selected' : '' ?>>Pré-Vestibular</option>
                <option value="Pré-Vestibulinho" <?= $cursoSelecionado === 'Pré-Vestibulinho' ? 'selected' : '' ?>>Pré-Vestibulinho</option>
            </select>
        </form>

        <div class="table-responsive">
    <table class="table-custom">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>CPF</th>
                <th>Curso</th>
                <th>Faltas</th>
                <th style="width: 150px;">Ações</th>
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
                        <td><?= $row['faltas'] ?></td>
                        <td>
                            <a href="excluir_aluno_aprovado.php?id=<?= $row['id_aluno'] ?>"
                               class="btn-action btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza que deseja excluir este aluno?')">
                                <i class="fas fa-trash-alt me-1"></i>Excluir
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-muted text-center py-4">Nenhum aluno aprovado encontrado.</td>
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
                        <a class="page-link" href="?curso=<?= urlencode($cursoSelecionado) ?>&pagina=<?= $i ?>">
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

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
