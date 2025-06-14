<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$idAluno = intval($_GET['id'] ?? 0);
$filtro = $_GET['filtro'] ?? 'por_materia';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$limite = 4;
$offset = ($pagina - 1) * $limite;


$stmtAluno = $conn->prepare("SELECT nome FROM alunos_aceitos WHERE id_aluno = ?");
$stmtAluno->bind_param("i", $idAluno);
$stmtAluno->execute();
$stmtAluno->bind_result($nomeAluno);
$stmtAluno->fetch();
$stmtAluno->close();

if ($filtro === 'por_materia') {
    $stmtTotal = $conn->prepare("
        SELECT COUNT(DISTINCT m.nome_materia)
        FROM frequencia f
        JOIN registro_aulas ra ON f.id_aula = ra.id
        JOIN planos_aula pa ON ra.id_plano = pa.id
        JOIN materias m ON pa.id_materia = m.id
        WHERE f.id_aluno = ? AND f.presenca = 0
    ");
    $stmtTotal->bind_param("i", $idAluno);
    $stmtTotal->execute();
    $stmtTotal->bind_result($totalMaterias);
    $stmtTotal->fetch();
    $stmtTotal->close();

    $totalPaginas = ceil($totalMaterias / $limite);

    $stmt = $conn->prepare("
        SELECT m.nome_materia AS materia, COUNT(f.id_frequencia) AS total_faltas
        FROM frequencia f
        JOIN registro_aulas ra ON f.id_aula = ra.id
        JOIN planos_aula pa ON ra.id_plano = pa.id
        JOIN materias m ON pa.id_materia = m.id
        WHERE f.id_aluno = ? AND f.presenca = 0
        GROUP BY m.nome_materia
        ORDER BY m.nome_materia
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $idAluno, $limite, $offset);
} else {
    $totalPaginas = 1;
    $stmt = $conn->prepare("
        SELECT 'Total Geral' AS materia, COUNT(f.id_frequencia) AS total_faltas
        FROM frequencia f
        WHERE f.id_aluno = ? AND f.presenca = 0
    ");
    $stmt->bind_param("i", $idAluno);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Faltas de <?= htmlspecialchars($nomeAluno) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .card-custom {
        background-color: white;
        border-radius: 1rem;
        box-shadow: var(--sombra);
        padding: 2rem;
        margin-top: 2rem;
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
        max-height: 300px;
        overflow-y: auto;
    }

    .table-custom thead th {
        background-color: var(--verde-principal);
        color: white;
        text-align: center;
        padding: 1rem;
    }

    .table-custom td, .table-custom th {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .table-custom tbody tr:hover {
        background-color: rgba(0, 136, 148, 0.05);
    }

    .falta-limite {
        color: red;
        font-weight: bold;
    }
    </style>
</head>
<body>
<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-user-clock me-2"></i>Faltas de <?= htmlspecialchars($nomeAluno) ?></h1>
    </div>
</section>

<div class="container container-custom">
    <div class="card-custom">
        <form method="get" class="row g-3 mb-4">
            <input type="hidden" name="id" value="<?= $idAluno ?>">
            <div class="col-md-6">
                <label for="filtro" class="form-label">Visualizar:</label>
                <select name="filtro" id="filtro" class="form-select" onchange="this.form.submit()">
                    <option value="por_materia" <?= $filtro === 'por_materia' ? 'selected' : '' ?>>Por Matéria</option>
                    <option value="total_geral" <?= $filtro === 'total_geral' ? 'selected' : '' ?>>Total Geral</option>
                </select>
            </div>
            <?php if ($filtro === 'por_materia'): ?>
                <input type="hidden" name="pagina" value="<?= $pagina ?>">
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>Matéria</th>
                        <th>Quantidade de Faltas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['materia']) ?></td>
                                <td class="<?= ($row['total_faltas'] >= 10) ? 'falta-limite' : '' ?>">
                                    <?= $row['total_faltas'] ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Nenhuma falta registrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($filtro === 'por_materia' && $totalPaginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?id=<?= $idAluno ?>&filtro=<?= $filtro ?>&pagina=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <div class="text-end mt-3">
            <a href="alunos_aprovados.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>
</div>
<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
