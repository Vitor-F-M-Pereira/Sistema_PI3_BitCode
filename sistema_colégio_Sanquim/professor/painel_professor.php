<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$nomeProfessor = $_SESSION['nome_usuario'] ?? 'Professor';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor | Colégio Sanquim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../style.css" rel="stylesheet">
    <style>
        .tool-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--amarelo-sanquim);
        }
        
        .tool-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .tool-icon {
            font-size: 2.2rem;
            color: var(--azul-sanquim);
            margin-bottom: 1rem;
            transition: transform 0.3s;
        }
        
        .tool-card:hover .tool-icon {
            transform: scale(1.1);
        }
        
        .tool-title {
            font-weight: 700;
            color: var(--azul-sanquim);
            margin-bottom: 0.5rem;
        }
        
        .tool-description {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-light">
<header class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-11">
                <h1 class="mb-2"><i class="fas fa-chalkboard-teacher me-2"></i>Bem-vindo, <?= htmlspecialchars($nomeProfessor) ?>!</h1>
                <p class="lead mb-0">Gerencie suas aulas e materiais didáticos</p>
            </div>
        </div>
    </div>
    <div class="header-transition"></div>
</header>

<div class="container py-4" style="margin-top: -0.5rem;">
    <h2 class="text-center mb-4" style="color: var(--azul-sanquim);">
        <i class="fas fa-tools me-2"></i> Minhas Ferramentas
    </h2>
    
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="tool-card p-4 text-center">
                <div class="tool-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="tool-title">Planejar Aula</h3>
                <p class="tool-description">Crie e organize seus planos de aula</p>
                <a href="planejar_aula.php" class="btn btn-acessar">
                    <i class="fas fa-arrow-right me-1"></i> Acessar
                </a>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="tool-card p-4 text-center">
                <div class="tool-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="tool-title">Visualizar Ementa</h3>
                <p class="tool-description">Consulte o conteúdo programático</p>
                <a href="ementa.php" class="btn btn-acessar">
                    <i class="fas fa-arrow-right me-1"></i> Acessar
                </a>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="tool-card p-4 text-center">
                <div class="tool-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3 class="tool-title">Registrar Aula</h3>
                <p class="tool-description">Registre o conteúdo ministrado</p>
                <a href="registrar_aula.php" class="btn btn-acessar">
                    <i class="fas fa-arrow-right me-1"></i> Acessar
                </a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="tool-card p-4 text-center">
                <div class="tool-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="tool-title">Histórico</h3>
                <p class="tool-description">Acesse seu histórico de aulas</p>
                <a href="historico_aulas.php" class="btn btn-acessar">
                    <i class="fas fa-arrow-right me-1"></i> Acessar
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>