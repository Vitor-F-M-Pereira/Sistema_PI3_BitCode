<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$id_materia = $_SESSION['id_materia'];

$sqlVerifica = "SELECT id FROM materias WHERE id = ? AND ativo = 1";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("i", $id_materia);
$stmtVerifica->execute();
$resultado = $stmtVerifica->get_result();

if ($resultado->num_rows === 0) {
    $_SESSION['erro'] = "Esta matéria está inativa. Acesso não permitido.";
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data_aula = $_POST['data_aula'];
    $titulo = $_POST['titulo'];
    $conteudo = $_POST['conteudo'];
    $tipo = $_POST['tipo_aula'];

    $sql = "INSERT INTO planos_aula (id_materia, data_aula, titulo_aula, conteudo_planejado, tipo_aula)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $id_materia, $data_aula, $titulo, $conteudo, $tipo);

    if ($stmt->execute()) {
        $mostrarModal = true;
        $tituloModal = "Sucesso!";
        $mensagemModal = "Planejamento salvo com sucesso!";
        $classeModal = "modal-success";
    } else {
        $mostrarModal = true;
        $tituloModal = "Erro ao Salvar!";
        $mensagemModal = "Ocorreu um erro ao salvar o planejamento.";
        $classeModal = "modal-danger";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Planejar Aula</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: var(--sombra);
        border-top: 4px solid var(--amarelo-destaque);
    }
    h1 {
        font-weight: 700;
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }
    .form-label {
        font-weight: 600;
        color: var(--verde-escuro);
        font-size: 150%;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        font-size: 0.95rem;
    }
    textarea.form-control {
        height: 120px;
    }
    .alert {
        border-radius: 8px;
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
    }
    </style>
</head>
<body>
<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-chalkboard-teacher me-2"></i>Planejar Próxima Aula</h1>
        <a href="ementa.php" target="_blank" class="btn btn-acessar">
            <i class="fas fa-book-open me-2"></i>Ver Ementa
        </a>
    </div>
</section>

<div class="container pb-5">
    <div class="form-container mx-auto" style="max-width: 800px;">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="data_aula" class="form-label">
                        <i class="far fa-calendar-alt me-2"></i>Data da Aula
                    </label>
                    <input type="date" name="data_aula" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="col-md-6">
                    <label for="tipo_aula" class="form-label">
                        <i class="fas fa-tags me-2"></i>Tipo de Aula
                    </label>
                    <select name="tipo_aula" class="form-select">
                        <option value="Normal">Normal</option>
                        <option value="Revisão">Revisão</option>
                        <option value="Extra">Extra</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="titulo" class="form-label">
                        <i class="fas fa-heading me-2"></i>Título da Aula
                    </label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>

                <div class="col-12">
                    <label for="conteudo" class="form-label">
                        <i class="fas fa-book me-2"></i>Conteúdo Planejado
                    </label>
                    <textarea name="conteudo" class="form-control" rows="6" required></textarea>
                </div>

                <div class="col-12 d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                    <a href="painel_professor.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
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
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
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
<script>
    document.querySelector("form").addEventListener("submit", function(event) {
        const campoData = document.querySelector('input[name="data_aula"]');
        const dataSelecionada = campoData.value;

        const hoje = new Date();
        const yyyy = hoje.getFullYear();
        const mm = String(hoje.getMonth() + 1).padStart(2, '0');
        const dd = String(hoje.getDate()).padStart(2, '0');
        const dataHoje = `${yyyy}-${mm}-${dd}`;

        if (dataSelecionada < dataHoje) {
            event.preventDefault();
            alert("Você não pode planejar uma aula para uma data anterior ao dia atual.");
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
