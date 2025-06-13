<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_GET['id']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$id_registro = $_GET['id'];

$sql = "SELECT pa.data_aula, pa.titulo_aula, pa.conteudo_planejado,
               ra.nome_professor, ra.contato, ra.resumo_aula, ra.conteudo_dado
        FROM registro_aulas ra
        JOIN planos_aula pa ON ra.id_plano = pa.id
        WHERE ra.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_registro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<div class='container mt-5 alert alert-danger'>Aula não encontrada.</div>";
    exit;
}

$aula = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Aula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--sombra);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-container th {
            background-color: #f1f1f1;
            color: var(--verde-escuro);
            padding: 0.75rem;
            width: 35%;
        }

        .form-container td {
            padding: 0.75rem;
        }

        .modal-title {
            color: var(--verde-escuro);
        }

        .modal-body {
            white-space: pre-line;
        }

        .modal-content {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-chalkboard-teacher me-2"></i>Detalhes da Aula</h1>
        </div>
    </section>

    <div class="container pb-5">
        <div class="form-container">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th><i class="far fa-calendar-alt me-2"></i>Data da Aula</th>
                        <td><?= date('d/m/Y', strtotime($aula['data_aula'])); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-heading me-2"></i>Título</th>
                        <td><?= htmlspecialchars($aula['titulo_aula']); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        <td><?= htmlspecialchars($aula['resumo_aula']); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-user me-2"></i>Professor</th>
                        <td><?= htmlspecialchars($aula['nome_professor']); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-phone me-2"></i>Contato</th>
                        <td><?= htmlspecialchars($aula['contato']); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-4">
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPlanejado">
                        <i class="fas fa-book-open me-2"></i>Ver Conteúdo Planejado
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMinistrado">
                        <i class="fas fa-chalkboard me-2"></i>Ver Conteúdo Ministrado
                    </button>

                    <a href="historico_aulas.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPlanejado" tabindex="-1" aria-labelledby="modalPlanejadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPlanejadoLabel">
                        <i class="fas fa-book-open me-2"></i>Conteúdo Planejado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <?= nl2br(htmlspecialchars($aula['conteudo_planejado'])); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMinistrado" tabindex="-1" aria-labelledby="modalMinistradoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMinistradoLabel">
                        <i class="fas fa-chalkboard me-2"></i>Conteúdo Ministrado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <?= nl2br(htmlspecialchars($aula['conteudo_dado'])); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
