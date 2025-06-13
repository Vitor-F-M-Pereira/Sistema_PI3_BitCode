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
    $nome = $_POST['nome_materia'];
    $login = $_POST['login_materia'];
    $senha = password_hash($_POST['senha_materia'], PASSWORD_DEFAULT);
    $curso = $_POST['curso'];
    $ementa = $_POST['ementa'];

    $verifica_sql = "SELECT id FROM materias WHERE nome_materia = ? OR login_materia = ?";
    $verifica_stmt = $conn->prepare($verifica_sql);
    $verifica_stmt->bind_param("ss", $nome, $login);
    $verifica_stmt->execute();
    $verifica_stmt->store_result();

    if ($verifica_stmt->num_rows > 0) {
        $mensagem = "Erro: Já existe uma matéria com este nome ou login.";
    } else {
        $sql = "INSERT INTO materias (nome_materia, login_materia, senha_materia, curso, ementa)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $login, $senha, $curso, $ementa);

        if ($stmt->execute()) {
            $mensagem = "Matéria cadastrada com sucesso!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Matéria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        
        .form-container {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--sombra);
            max-width: 900px;
            margin: 0 auto;
        }

        .form-label {
            font-weight: 600;
            color: var(--verde-escuro);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
        }

        textarea.form-control {
            min-height: 150px;
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-book me-2"></i>Cadastro de Matéria</h1>
        <p class="lead">Adicione uma nova matéria ao sistema</p>
    </div>
</section>

<div class="container py-4">
    <div class="form-container">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo strpos($mensagem, 'Erro') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show text-center">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-tag me-2"></i>Nome da Matéria</label>
                    <input type="text" name="nome_materia" class="form-control" required>
                    <div class="invalid-feedback">Informe o nome da matéria.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Login</label>
                    <input type="text" name="login_materia" class="form-control" required>
                    <div class="invalid-feedback">Informe o login da matéria.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-lock me-2"></i>Senha</label>
                    <input type="password" name="senha_materia" class="form-control" required>
                    <div class="invalid-feedback">Informe a senha da matéria.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-graduation-cap me-2"></i>Curso</label>
                    <select name="curso" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="Pré-Vestibular">Pré-Vestibular</option>
                        <option value="Pré-Vestibulinho">Pré-Vestibulinho</option>
                        <option value="Ambos">Ambos</option>
                    </select>
                    <div class="invalid-feedback">Selecione o curso.</div>
                </div>

                <div class="col-12">
                    <label class="form-label"><i class="fas fa-file-alt me-2"></i>Ementa (Plano Base)</label>
                    <textarea name="ementa" class="form-control" required></textarea>
                    <div class="invalid-feedback">Insira a ementa da matéria.</div>
                </div>

                <div class="col-12 d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Cadastrar Matéria
                    </button>
                    <a href="painel_admin.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
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
