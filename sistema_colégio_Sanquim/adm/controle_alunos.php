<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Controle de Alunos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .card-controle {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--sombra);
            flex: 1;
        }

        .card-option {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.2s;
            border-top: 5px solid var(--amarelo-destaque);
        }

        .card-option:hover {
            transform: translateY(-5px);
        }

        .card-option i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--verde-escuro);
        }

        .card-option h5 {
            font-weight: 700;
            color: var(--verde-escuro);
        }

        .card-option p {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-users me-2"></i>Controle de Alunos</h1>
        <p class="lead">Gerencie as matrículas e o status dos alunos</p>
    </div>
</section>

<div class="container pb-4">
    <div class="card-controle">
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card-option">
                    <i class="fas fa-hourglass-half"></i>
                    <h5>Alunos Pendentes</h5>
                    <p>Visualize alunos aguardando aprovação</p>
                    <a href="alunos_pendentes.php" class="btn btn-acessar text-white">
                        <i class="fas fa-arrow-right me-1"></i> Acessar
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="card-option">
                    <i class="fas fa-check-circle"></i>
                    <h5>Alunos Aprovados</h5>
                    <p>Gerencie os alunos já aceitos no sistema</p>
                    <a href="alunos_aprovados.php" class="btn btn-acessar text-white">
                        <i class="fas fa-arrow-right me-1"></i> Acessar
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="painel_admin.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>