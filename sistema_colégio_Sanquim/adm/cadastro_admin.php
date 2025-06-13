<?php
session_start();
include '../conexao.php';
include '../menu.php';
$mensagem = '';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $login = $_POST['login'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $verifica_sql = "SELECT id FROM usuarios WHERE login = ?";
    $verifica_stmt = $conn->prepare($verifica_sql);
    $verifica_stmt->bind_param("s", $login);
    $verifica_stmt->execute();
    $verifica_stmt->store_result();

    if ($verifica_stmt->num_rows > 0) {
        $mensagem = "Erro: Este login já está em uso. Escolha outro.";
    } else {
        $sql = "INSERT INTO usuarios (nome, login, senha, tipo_usuario)
                VALUES (?, ?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $login, $senha);

        if ($stmt->execute()) {
            $mensagem = "Administrador cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar administrador: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--sombra);
        }

        .form-label {
            font-weight: 600;
            color: var(--verde-escuro);
        }

        .form-control {
            border-radius: 8px;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-user-shield me-2"></i>Cadastro de Administrador</h1>
        <p class="lead">Crie um novo administrador do sistema</p>
    </div>
</section>

<div class="container py-4">
    <div class="admin-container">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo strpos($mensagem, 'Erro') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show text-center">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-4">
                <label for="nome" class="form-label"><i class="fas fa-user me-2"></i>Nome Completo</label>
                <input type="text" name="nome" class="form-control" required>
                <div class="invalid-feedback">Informe o nome do administrador.</div>
            </div>

            <div class="mb-4">
                <label for="login" class="form-label"><i class="fas fa-sign-in-alt me-2"></i>Login de Acesso</label>
                <input type="text" name="login" class="form-control" required>
                <div class="invalid-feedback">Informe um login válido.</div>
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label"><i class="fas fa-lock me-2"></i>Senha</label>
                <input type="password" name="senha" class="form-control" required>
                <div class="invalid-feedback">Informe uma senha.</div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Cadastrar Administrador
                </button>
                <a href="painel_admin.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>
