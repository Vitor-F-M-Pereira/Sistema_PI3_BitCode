<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$id_materia = $_SESSION['id_materia'];

$sql = "SELECT nome_materia, ementa, updated_at FROM materias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_materia);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<div class='container mt-5 alert alert-danger'>Erro ao carregar ementa.</div>";
    exit;
}

$materia = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ementa – <?php echo htmlspecialchars($materia['nome_materia']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container-custom {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: var(--sombra);
            flex: 1;
            margin-bottom: 2rem;
        }

        .ementa-header {
            border-bottom: 2px solid var(--verde-principal);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .ementa-title {
            color: var(--verde-principal);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .update-info {
            font-size: 0.95rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ementa-content {
            line-height: 1.8;
            white-space: pre-line;
            font-size: 1.1rem;
        }

        .ementa-content p {
            margin-bottom: 1.5rem;
        }

        .ementa-content ul,
        .ementa-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .ementa-content li {
            margin-bottom: 0.75rem;
        }

        .empty-ementa {
            background-color: #fff9e6;
            border-left: 4px solid var(--amarelo-destaque);
            padding: 1.5rem;
            border-radius: 8px;
            font-size: 1.1rem;
        }

    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-book-open me-2"></i>Ementa – <?php echo htmlspecialchars($materia['nome_materia']); ?></h1>
        </div>
    </section>

    <div class="container container-custom">
        <div class="ementa-header">
            <p class="update-info">
                <i class="fas fa-clock me-1"></i>
                <strong>Última atualização:</strong> 
                <?php echo date("d/m/Y H:i", strtotime($materia['updated_at'])); ?>
            </p>
        </div>
        
        <?php if (!empty($materia['ementa'])): ?>
            <div class="ementa-content">
                <?php 
                $ementa = htmlspecialchars($materia['ementa']);
                $ementa = nl2br($ementa);
                $ementa = preg_replace('/(<br \/>){2,}/', '</p><p>', $ementa);
                echo '<p>' . $ementa . '</p>';
                ?>
            </div>
        <?php else: ?>
            <div class="empty-ementa alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                Nenhuma ementa foi cadastrada ou disponibilizada até o momento.
            </div>
        <?php endif; ?>

        <div class="text-center btn-voltar">
            <a href="painel_professor.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>