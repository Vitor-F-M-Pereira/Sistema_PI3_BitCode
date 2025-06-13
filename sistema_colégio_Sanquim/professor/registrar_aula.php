<?php
session_start();
include '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login.php");
    exit;
}

$id_materia = $_SESSION['id_materia'];
$mensagem = '';

$stmtCurso = $conn->prepare("SELECT curso FROM materias WHERE id = ?");
$stmtCurso->bind_param("i", $id_materia);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();
$cursoMateria = $resultCurso->fetch_assoc()['curso'] ?? '';

$planos = [];
$stmt = $conn->prepare("SELECT pa.id, pa.data_aula, pa.titulo_aula, pa.conteudo_planejado
                        FROM planos_aula pa
                        WHERE pa.id_materia = ? AND NOT EXISTS (
                            SELECT 1 FROM registro_aulas ra WHERE ra.id_plano = pa.id
                        )
                        ORDER BY pa.data_aula DESC");
$stmt->bind_param("i", $id_materia);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $planos[] = $row;
}

$cursoMateria = trim($cursoMateria);
if ($cursoMateria === 'Ambos') {
    $queryAlunos = "SELECT id_aluno, nome, faltas FROM alunos_aceitos WHERE ativo = 1 ORDER BY nome";
    $resultAlunos = $conn->query($queryAlunos);
} else {
    $stmtAlunos = $conn->prepare("SELECT id_aluno, nome, faltas FROM alunos_aceitos WHERE curso = ? AND ativo = 1 ORDER BY nome");
    $stmtAlunos->bind_param("s", $cursoMateria);
    $stmtAlunos->execute();
    $resultAlunos = $stmtAlunos->get_result();
}

$alunos = [];
while ($row = $resultAlunos->fetch_assoc()) {
    $alunos[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_plano'])) {
    $verifica = $conn->prepare("SELECT id FROM registro_aulas WHERE id_plano = ?");
    $verifica->bind_param("i", $_POST['id_plano']);
    $verifica->execute();
    $resultVerifica = $verifica->get_result();

    if ($resultVerifica->num_rows > 0) {
        $mensagem = "Essa aula já foi registrada!";
    } else {
        $id_plano = $_POST['id_plano'];
        $nome = $_POST['nome_professor'];
        $contato = $_POST['contato'];
        $status = $_POST['resumo_aula'];
        $conteudo = ($status === 'Aula ocorreu normalmente') ? $_POST['conteudo_planejado'] : $_POST['conteudo_dado'];

        $stmtReg = $conn->prepare("INSERT INTO registro_aulas (id_plano, nome_professor, contato, resumo_aula, conteudo_dado) VALUES (?, ?, ?, ?, ?)");
        $stmtReg->bind_param("issss", $id_plano, $nome, $contato, $status, $conteudo);

        if ($stmtReg->execute()) {
            if (isset($_POST['presenca'])) {
                foreach ($_POST['presenca'] as $idAluno => $valor) {
                    if ($valor === 'Faltou') {
                        $stmtFalta = $conn->prepare("UPDATE alunos_aceitos SET faltas = faltas + 1 WHERE id_aluno = ?");
                        $stmtFalta->bind_param("i", $idAluno);
                        $stmtFalta->execute();
                    }
                }
            }
            $_SESSION['mensagem_sucesso'] = "Aula registrada com sucesso!";
            header("Location: registrar_aula.php");
            exit;
        } else {
            $mensagem = "Erro ao registrar aula: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Aula</title>
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

        .form-label {
            font-weight: 600;
            color: var(--verde-escuro);
            font-size: 110%;
        }

        textarea.form-control {
            height: 120px;
        }

        .table thead th {
            background-color: var(--verde-principal);
            color: white;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
<section class="hero-section">
    <div class="container">
        <h1><i class="fas fa-clipboard-list me-2"></i>Registro de Aula</h1>
    </div>
</section>

<div class="container pb-5">
    <?php if ($mensagem): ?>
        <div class="alert alert-<?= strpos($mensagem, 'Erro') !== false ? 'danger' : 'success' ?>">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <div class="form-container mx-auto" style="max-width: 900px;">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Aula Planejada</label>
                <select name="id_plano" class="form-select" required id="id_plano">
                    <option value="">Selecione...</option>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?= $plano['id'] ?>">
                            <?= date('d/m/Y', strtotime($plano['data_aula'])) . ' - ' . htmlspecialchars($plano['titulo_aula']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome do Professor</label>
                <input type="text" name="nome_professor" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contato</label>
                <input type="text" name="contato" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status da Aula</label>
                <select name="resumo_aula" class="form-select" onchange="atualizarCampoConteudo()" required>
                    <option value="">Selecione o status...</option>
                    <option value="Aula ocorreu normalmente">Aula ocorreu normalmente</option>
                    <option value="Aula parcialmente ministrada">Aula parcialmente ministrada</option>
                    <option value="Aula não aconteceu">Aula não aconteceu</option>
                    <option value="Aula interrompida">Aula interrompida</option>
                </select>
            </div>

            <div class="mb-3" id="campoConteudoNormal" style="display: none;">
                <label class="form-label">Conteúdo Planejado</label>
                <textarea name="conteudo_planejado" class="form-control"></textarea>
            </div>

            <div class="mb-3" id="campoConteudoParcial" style="display: none;">
                <label class="form-label">Conteúdo Ministrado</label>
                <textarea name="conteudo_dado" class="form-control"></textarea>
            </div>

            <h4 class="mt-4 mb-2 text-secondary">Chamada dos Alunos</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Presença</th>
                        <th>Faltas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?= htmlspecialchars($aluno['nome']) ?></td>
                            <td>
                                <select name="presenca[<?= $aluno['id_aluno'] ?>]" class="form-select">
                                    <option value="Presente">Presente</option>
                                    <option value="Faltou">Faltou</option>
                                </select>
                            </td>
                            <td><?= $aluno['faltas'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Registrar Aula
                </button>
                <a href="painel_professor.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>
<script>
    const planos = <?= json_encode($planos) ?>;
    function atualizarCampoConteudo() {
        const status = document.querySelector('[name="resumo_aula"]').value;
        const idPlano = document.getElementById('id_plano').value;
        const planoSelecionado = planos.find(p => p.id == idPlano);
        const conteudo = planoSelecionado ? planoSelecionado.conteudo_planejado : '';

        document.getElementById('campoConteudoNormal').style.display = (status === "Aula ocorreu normalmente") ? 'block' : 'none';
        document.getElementById('campoConteudoParcial').style.display = (status !== "Aula ocorreu normalmente" && status !== "") ? 'block' : 'none';

        if (status === "Aula ocorreu normalmente") {
            document.querySelector('[name="conteudo_planejado"]').value = conteudo;
        }
    }

    document.getElementById('id_plano').addEventListener('change', atualizarCampoConteudo);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
