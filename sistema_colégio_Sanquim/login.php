<?php session_start(); 
include 'menu.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Acesso Restrito - Colégio Sanquim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>

        .login-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .login-card {
            border: none;
            border-top: 4px solid var(--amarelo-destaque);
            box-shadow: var(--sombra);
            border-radius: 15px;
            padding: 2rem;
            background: white;
        }

        h2 {
            color: var(--verde-principal);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--verde-escuro);
        }

        .form-control {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid #ccc;
        }

        .form-control:focus {
            border-color: var(--verde-claro);
            box-shadow: 0 0 0 0.25rem rgba(0, 140, 156, 0.25);
        }

        .alert {
            border-radius: 10px;
        }

        .text-muted {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="mb-2">Acesso Restrito</h1>
            <p class="lead mb-0">Sistema de Gestão Educacional</p>
        </div>
    </section>

    <div class="container mt-4">
        <div class="login-container">
            <?php if (isset($_SESSION['erro_login'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['erro_login']; unset($_SESSION['erro_login']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="login-card">
                <form method="POST" action="autenticar.php">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-acessar w-100 py-2">Acessar Sistema</button>
                </form>
            </div>

            <div class="mt-3 text-center">
                <small class="text-muted">Problemas com acesso? Contate o administrador do sistema.</small>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
