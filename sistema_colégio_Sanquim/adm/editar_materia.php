<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

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
        $mostrarModal = true;
        $tituloModal = "Matéria Atualizada!";
        $mensagemModal = "As alterações foram salvas com sucesso.";
        $classeModal = "modal-success";
        echo "<meta http-equiv='refresh' content='2;URL=controle_materias.php'>";
    } else {
        $mostrarModal = true;
        $tituloModal = "Erro ao Atualizar!";
        $mensagemModal = "Ocorreu um erro ao salvar as alterações: " . $conn->error;
        $classeModal = "modal-danger";
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--sombra);
        }
        .form-label {
            font-weight: 500;
            color: #555;
        }
        textarea {
            min-height: 200px;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-light">
<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-cogs me-2"></i>Edição de Matérias</h1>
        <p class="lead">Edite e salve dados referentes a matéria</p>
    </div>
</section>
<div class="container py-4">
    <div class="edit-container">
        <h1><i class="fas fa-book me-2"></i>Editar Matéria</h1>
        <p class="text-muted">Atualize os dados da matéria</p>

        <form method="POST">
            <div class="mb-3">
                <label for="nome_materia" class="form-label">Nome da Matéria</label>
                <input type="text" name="nome_materia" class="form-control" value="<?= htmlspecialchars($materia['nome_materia']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="login_materia" class="form-label">Login da Matéria</label>
                <input type="text" name="login_materia" class="form-control" value="<?= htmlspecialchars($materia['login_materia']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="senha_materia" class="form-label">Nova Senha</label>
                <input type="password" name="senha_materia" class="form-control" placeholder="Deixe em branco para manter a senha atual">
            </div>

            <div class="mb-3">
                <label for="curso" class="form-label">Curso</label>
                <select name="curso" class="form-select" required>
                    <option value="Pré-Vestibular" <?= $materia['curso'] === 'Pré-Vestibular' ? 'selected' : '' ?>>Pré-Vestibular</option>
                    <option value="Pré-Vestibulinho" <?= $materia['curso'] === 'Pré-Vestibulinho' ? 'selected' : '' ?>>Pré-Vestibulinho</option>
                    <option value="Ambos" <?= $materia['curso'] === 'Ambos' ? 'selected' : '' ?>>Ambos</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ementa" class="form-label">Ementa (Plano Base)</label>
                <textarea name="ementa" class="form-control"><?= htmlspecialchars($materia['ementa'] ?? '') ?></textarea>
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

<?php if ($mostrarModal): ?>
<div class="modal fade <?= $classeModal ?>" id="modalFeedback" tabindex="-1" aria-labelledby="modalFeedbackLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= $tituloModal ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-center">
        <p><?= $mensagemModal ?></p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('modalFeedback'));
    modal.show();
});
</script>
<?php endif; ?>

<?php include '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
