<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


$nome = 'Administrador';
if (isset($_SESSION['id_usuario'])) {
    $id = $_SESSION['id_usuario'];
    $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nomeBuscado);
    if ($stmt->fetch()) {
        $nome = $nomeBuscado;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../style.css" rel="stylesheet">
    <style>

        .section-title {
            color: var(--verde-escuro);
            font-weight: 700;
            text-align: center;
            margin: 2rem 0 1.5rem;
        }

        .card-opcao {
            background: white;
            border-radius: 12px;
            padding: 1.5rem 1rem;
            box-shadow: var(--sombra);
            border-top: 4px solid var(--amarelo-destaque);
            text-align: center;
            transition: all 0.3s;
        }

        .card-opcao:hover {
            transform: translateY(-5px);
        }

        .card-opcao i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--verde-escuro);
        }

        .card-opcao h5 {
            font-weight: 700;
            color: var(--verde-escuro);
        }

        .card-opcao p {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-user-shield me-2"></i>Bem-vindo, <?= $nome ?>!</h1>
        <p class="lead">Gerencie alunos, matérias e permissões administrativas</p>
    </div>
</section>

<div class="container pb-5">
    <h3 class="section-title"><i class="fas fa-tools me-2"></i>Ferramentas Administrativas</h3>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6 col-lg-3">
            <div class="card-opcao">
                <i class="fas fa-book"></i>
                <h5>Cadastro de Matérias</h5>
                <p>Adicione novas matérias ao sistema</p>
                <a href="cadastro_materia.php" class="btn btn-acessar">→ Acessar</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-opcao">
                <i class="fas fa-user-plus"></i>
                <h5>Cadastro de Admins</h5>
                <p>Adicione novos administradores</p>
                <a href="cadastro_admin.php" class="btn btn-acessar">→ Acessar</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-opcao">
                <i class="fas fa-cogs"></i>
                <h5>Controle de Matérias</h5>
                <p>Edite ou desative matérias existentes</p>
                <a href="controle_materias.php" class="btn btn-acessar">→ Acessar</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card-opcao">
                <i class="fas fa-users"></i>
                <h5>Controle de Alunos</h5>
                <p>Gerencie os alunos do sistema</p>
                <a href="controle_alunos.php" class="btn btn-acessar">→ Acessar</a>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
