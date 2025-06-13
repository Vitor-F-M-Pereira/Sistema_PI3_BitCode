<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: controle_materias.php");
    exit;
}

$sql = "SELECT * FROM materias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    header("Location: controle_materias.php");
    exit;
}

$materia = $resultado->fetch_assoc();
$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_materia'];
    $login = $_POST['login_materia'];
    $ementa = $_POST['ementa'];
    $curso = $_POST['curso'];

    if (!empty($_POST['senha_materia'])) {
        $senha = password_hash($_POST['senha_materia'], PASSWORD_DEFAULT);
        $sql = "UPDATE materias SET nome_materia = ?, login_materia = ?, senha_materia = ?, curso = ?, ementa = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $login, $senha, $curso, $ementa, $id);
    } else {
        $sql = "UPDATE materias SET nome_materia = ?, login_materia = ?, curso = ?, ementa = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $login, $curso, $ementa, $id);
    }

    if ($stmt->execute()) {
    $mensagem = "Matéria atualizada com sucesso!";
    echo "<meta http-equiv='refresh' content='2;URL=controle_materias.php'>";
} else {
    $mensagem = "Erro ao atualizar: " . $conn->error;
}
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Matéria - Colégio Sanquim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .edit-header {
            color: var(--color-primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .edit-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--color-primary);
        }
        
        .edit-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .section-title {
            font-weight: 600;
            color: var(--color-primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .section-title i {
            margin-right: 0.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
        }
        
        .password-container {
            position: relative;
        }

        textarea {
            min-height: 200px;
            line-height: 1.6;
        }
        
        .btn-save {
            background-color: var(--color-primary);
            border: none;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-save:hover {
            background-color: var(--color-footer);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="edit-container">
        <div class="edit-header">
            <h1>
                <i class="fas fa-book me-2"></i>Editar Matéria
            </h1>
            <p class="edit-subtitle">Atualize os dados da matéria</p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo strpos($mensagem, 'Erro') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show">
                <?= $mensagem ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <h5 class="section-title">
                    <i class="fas fa-info-circle"></i>Informações Básicas
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nome_materia" class="form-label">Nome da Matéria</label>
                        <input type="text" name="nome_materia" class="form-control" 
                               value="<?= htmlspecialchars($materia['nome_materia']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="login_materia" class="form-label">Login da Matéria</label>
                        <input type="text" name="login_materia" class="form-control" 
                               value="<?= htmlspecialchars($materia['login_materia']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="section-title">
                    <i class="fas fa-lock"></i>Segurança
                </h5>
                <div class="password-container">
                    <label for="senha_materia" class="form-label">Nova Senha</label>
                    <input type="password" name="senha_materia" id="senha_materia" class="form-control" 
                           placeholder="Deixe em branco para manter a senha atual">
                </div>
            </div>

            <div class="mb-4">
                <h5 class="section-title">
                    <i class="fas fa-cog"></i>Configurações
                </h5>
                <div>
                    <label for="curso" class="form-label">Curso</label>
                    <select name="curso" class="form-select" required>
                        <option value="Pré-Vestibular" <?= $materia['curso'] === 'Pré-Vestibular' ? 'selected' : '' ?>>Pré-Vestibular</option>
                        <option value="Pré-Vestibulinho" <?= $materia['curso'] === 'Pré-Vestibulinho' ? 'selected' : '' ?>>Pré-Vestibulinho</option>
                        <option value="Ambos" <?= $materia['curso'] === 'Ambos' ? 'selected' : '' ?>>Ambos</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="section-title">
                    <i class="fas fa-file-alt"></i>Conteúdo
                </h5>
                <div>
                     <textarea rows="6" name="ementa" class="form-control ementa-container"><?= htmlspecialchars($materia['ementa'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Salvar Alterações
                </button>
                <a href="controle_materias.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>
<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>