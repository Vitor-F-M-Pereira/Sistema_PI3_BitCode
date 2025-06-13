<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Paginação
$porPagina = 5;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $porPagina;

// Contagem total de registros pendentes
$totalRegistros = $conn->query("SELECT COUNT(*) as total FROM alunos_pendentes WHERE ativo = 1")->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $porPagina);

// Contadores de alunos aceitos por curso
$quantidade = ['Pré-Vestibular' => 0, 'Pré-Vestibulinho' => 0];
$contagem = $conn->query("SELECT curso, COUNT(*) AS total FROM alunos_aceitos WHERE ativo = 1 GROUP BY curso");
if ($contagem) {
    while ($row = $contagem->fetch_assoc()) {
        $quantidade[$row['curso']] = $row['total'];
    }
}

$sql = "SELECT id_aluno, nome, curso FROM alunos_pendentes WHERE ativo = 1 ORDER BY nome LIMIT $inicio, $porPagina";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alunos Pendentes</title>
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
    
    .badge-limite {
        background-color: #fff3e0;
        color: #fd7e14;
        font-size: 0.8rem;
    }

    .btn-action {
        border-radius: 4px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        margin: 0 0.25rem;
    }

    .contadores {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .contador {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .contador-vestibular {
        background-color: #e3f2fd;
        color: #0d6efd;
        border: 1px solid #bbdefb;
    }
    
    .contador-vestibulinho {
        background-color: #e8f5e9;
        color: #198754;
        border: 1px solid #c8e6c9;
    }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-user-clock me-2"></i>Alunos com Matrículas Pendentes</h1>
        </div>
    </section>

    <div class="container container-custom">
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['mensagem_sucesso'] ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>
        <div class="contadores">
    <div class="contador contador-vestibular">
        Pré-Vestibular: <?= $quantidade['Pré-Vestibular'] ?>/20
    </div>
    <div class="contador contador-vestibulinho">
        Pré-Vestibulinho: <?= $quantidade['Pré-Vestibulinho'] ?>/20
    </div>
</div>

<div class="table-responsive">
    <table class="table-custom">
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th>Curso</th>
                <th style="width: 300px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $curso = $row['curso'];
                    $bloqueado = ($quantidade[$curso] >= 20);
                ?>
                    <tr>
                        <td class="text-start"><?= htmlspecialchars($row['nome']) ?></td>
                        <td>
                            <span class="badge-course <?= $curso === 'Pré-Vestibular' ? 'badge-prevestibular' : 'badge-prevestibulinho' ?>">
                                <?= htmlspecialchars($curso) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="visualizar_aluno.php?id=<?= $row['id_aluno'] ?>" class="btn-action btn btn-primary">
                                    <i class="fas fa-eye me-1"></i>Visualizar
                                </a>
                                <?php if (!$bloqueado): ?>
                                    <a href="aprovar_aluno.php?id=<?= $row['id_aluno'] ?>" class="btn-action btn btn-success">
                                        <i class="fas fa-check me-1"></i>Aceitar
                                    </a>
                                <?php else: ?>
                                    <button class="btn-action btn btn-secondary" disabled>
                                        <i class="fas fa-lock me-1"></i>Limite
                                    </button>
                                <?php endif; ?>
                                <a href="rejeitar_aluno.php?id=<?= $row['id_aluno'] ?>" 
                                   class="btn-action btn btn-danger"
                                   onclick="return confirm('Tem certeza que deseja rejeitar este aluno?')">
                                    <i class="fas fa-times me-1"></i>Rejeitar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-muted text-center py-4">Nenhum aluno pendente no momento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

        <?php if ($totalPaginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <div class="text-end btn-voltar">
            <a href="controle_alunos.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
