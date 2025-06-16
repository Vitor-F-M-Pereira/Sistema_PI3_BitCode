<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$id_materia = $_SESSION['id_materia'];
$verifica = $conn->prepare("SELECT id FROM materias WHERE id = ? AND ativo = 1");
$verifica->bind_param("i", $id_materia);
$verifica->execute();
$resultado = $verifica->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erro'] = "Esta matéria foi desativada.";
    header("Location: ../login.php");
    exit;
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 4;
$offset = ($pagina - 1) * $por_pagina;

$sql = "SELECT ra.id, pa.data_aula, pa.titulo_aula 
        FROM registro_aulas ra
        JOIN planos_aula pa ON ra.id_plano = pa.id
        WHERE pa.id_materia = ?
        ORDER BY pa.data_aula DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_materia, $por_pagina, $offset);
$stmt->execute();
$result = $stmt->get_result();

$sql_total = "SELECT COUNT(*) as total FROM registro_aulas ra
              JOIN planos_aula pa ON ra.id_plano = pa.id
              WHERE pa.id_materia = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $id_materia);
$stmt_total->execute();
$result_total = $stmt_total->get_result()->fetch_assoc();
$stmt_total->close();
$total_paginas = ceil($result_total['total'] / $por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Aulas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .historico-container {
            background-color: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--sombra);
        }

        .table-historico {
            width: 100%;
            border-collapse: collapse;
        }

        .table-historico thead {
            background-color: var(--verde-principal);
            color: white;
        }
.table-historico th{
background-color: var(--verde-principal);
            color: white;
            font-weight: 600;
            padding: 1rem;
            }

        .table-historico th,
        .table-historico td {
            
            padding: 1rem;
            text-align: left;
            vertical-align: middle;
        }

        .table-historico tbody tr:hover {
            background-color: rgba(0, 109, 119, 0.05);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--verde-principal);
            border-color: var(--verde-principal);
            color: #f8f9fa;
        }

        .pagination .page-link {
            color: var(--verde-principal);
        }

        .btn-outline-primary {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-history me-2"></i>Histórico de Aulas</h1>
        </div>
    </section>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="painel_professor.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <div class="historico-container">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table-historico table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Título</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($aula = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($aula['data_aula'])) ?></td>
                                <td><?= htmlspecialchars($aula['titulo_aula']) ?></td>
                                <td>
                                    <a href="visualizar_aula.php?id=<?= $aula['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Visualizar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <nav class="pagination justify-content-center mt-3">
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="text-center text-muted">Nenhuma aula registrada ainda.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
