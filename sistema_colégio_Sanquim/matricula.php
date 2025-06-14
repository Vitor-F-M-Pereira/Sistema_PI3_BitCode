<?php
include 'conexao.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$mostrarModal = false;
$tituloModal = "";
$mensagemModal = "";
$classeModal = "";

function calcularIdade($dataNascimento) {
    $dataAtual = new DateTime();
    $dataNasc = new DateTime($dataNascimento);
    return $dataNasc->diff($dataAtual)->y;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $data_nascimento = $_POST['data_nascimento'];
    $cep = $_POST['cep'];
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $numero = $_POST['numero'];
    $tipo_residencia = $_POST['tipo_residencia'];
    $responsavel = $_POST['responsavel'] ?? null;
    $cpf_responsavel = $_POST['cpf_responsavel'] ?? null;
    $curso = $_POST['curso'];

    $comprovante_nome = null;
    if (isset($_FILES['documentos']) && $_FILES['documentos']['error'][0] === UPLOAD_ERR_OK) {
        if (!is_dir('comprovantes/')) mkdir('comprovantes/', 0777, true);
        $ext = pathinfo($_FILES['documentos']['name'][0], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . '.' . $ext;
        $comprovante_nome = 'comprovantes/' . $nome_arquivo;
        move_uploaded_file($_FILES['documentos']['tmp_name'][0], $comprovante_nome);
    }

    $verificaCpf = $conn->prepare("SELECT id_aluno FROM alunos_pendentes WHERE cpf = ?");
    $verificaCpf->bind_param("s", $cpf);
    $verificaCpf->execute();
    $verificaCpf->store_result();

    if ($verificaCpf->num_rows > 0) {
        $mostrarModal = true;
        $tituloModal = "Erro na Matrícula!";
        $mensagemModal = "Este CPF já está cadastrado.";
        $classeModal = "modal-danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO alunos_pendentes (nome, cpf, data_nascimento, cep, rua, bairro, cidade, estado, numero, tipo_residencia, nome_responsavel, cpf_responsavel, curso, comprovante_residencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssss", $nome, $cpf, $data_nascimento, $cep, $rua, $bairro, $cidade, $estado, $numero, $tipo_residencia, $responsavel, $cpf_responsavel, $curso, $comprovante_nome);
        if ($stmt->execute()) {
            $mostrarModal = true;
            $tituloModal = "Matrícula Realizada!";
            $mensagemModal = "Sua pré-matrícula foi enviada com sucesso!";
            $classeModal = "modal-success";
        } else {
            $mostrarModal = true;
            $tituloModal = "Erro ao Salvar!";
            $mensagemModal = "Ocorreu um erro ao registrar a matrícula.";
            $classeModal = "modal-danger";
        }
    }
}

include 'menu.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Matrícula de Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--sombra);
            padding: 2rem;
            border-top: 4px solid var(--amarelo-destaque);
            margin-bottom: 2rem;
        }
        
        h2 {
            color: var(--verde-principal);
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--amarelo-destaque);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--verde-escuro);
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--verde-claro);
            box-shadow: 0 0 0 0.25rem rgba(0, 140, 156, 0.25);
        }
        
        .highlight-box {
            background-color: rgba(0, 109, 119, 0.05);
            border-left: 4px solid var(--verde-principal);
            border-radius: 0 8px 8px 0;
            padding: 1.25rem;
            margin: 1.5rem 0;
        }
        
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .file-upload:hover {
            border-color: var(--verde-claro);
            background-color: rgba(0, 140, 156, 0.05);
        }
        
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="mb-3">Sistema de Matrículas</h1>
                <p class="lead mb-0">Preencha o formulário para realizar sua matrícula</p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-container">
                <h2>Formulário de Matrícula</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome do Aluno</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cpf" class="form-label">CPF do Aluno</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required onchange="verificarIdade()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="curso" class="form-label">Curso</label>
                            <select class="form-select" id="curso" name="curso" required>
                                <option value="">Selecione o curso</option>
                                <option value="Pré-Vestibular">Pré-Vestibular</option>
                                <option value="Pré-Vestibulinho">Pré-Vestibulinho</option>
                            </select>
                        </div>
                    </div>

                    <div class="highlight-box">
                        <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep" required onblur="buscarEndereco()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rua" class="form-label">Rua</label>
                                <input type="text" class="form-control" id="rua" name="rua" readonly required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" readonly required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" readonly required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" class="form-control" id="estado" name="estado" readonly required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipo_residencia" class="form-label">Tipo de Residência</label>
                                <select class="form-select" id="tipo_residencia" name="tipo_residencia" required>
                                    <option value="">Selecione</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Prédio">Prédio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="campos-responsavel" style="display: none;">
                        <div class="highlight-box">
                            <h5 class="fw-bold mb-3"><i class="fas fa-user-shield me-2"></i>Responsável Legal</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="responsavel" class="form-label">Nome do Responsável</label>
                                    <input type="text" class="form-control" id="responsavel" name="responsavel">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cpf_responsavel" class="form-label">CPF do Responsável</label>
                                    <input type="text" class="form-control" id="cpf_responsavel" name="cpf_responsavel">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="file-upload mb-4">
                            <label for="documentos" class="form-label d-block">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color: var(--verde-principal);"></i>
                                <h5>Comprovante de Residência</h5>
                                <p class="text-muted">Arraste ou clique para enviar (PDF, JPG, PNG)</p>
                                <input class="d-none" type="file" id="documentos" name="documentos[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                <span class="btn btn-principal">Selecionar Arquivos</span>
                            </label>
                        </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-success">Enviar Pré-Matrícula</button>
                        <a href="index.php" class="btn btn-outline-primary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
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
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($mostrarModal): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('modalFeedback'));
    modal.show();
});
</script>
<?php endif; ?>

<script>
function verificarIdade() {
    const dataNascimento = new Date(document.getElementById('data_nascimento').value);
    const hoje = new Date();
    let idade = hoje.getFullYear() - dataNascimento.getFullYear();
    const m = hoje.getMonth() - dataNascimento.getMonth();
    if (m < 0 || (m === 0 && hoje.getDate() < dataNascimento.getDate())) idade--;
    document.getElementById('campos-responsavel').style.display = (idade < 18) ? 'block' : 'none';
}

function buscarEndereco() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) return alert("CEP não encontrado!");
            document.getElementById('rua').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('estado').value = data.uf || '';
        })
        .catch(() => alert("Erro ao buscar CEP."));
}
</script>
<script>
        $(document).ready(function () {
            $('#cpf').inputmask('999.999.999-99');
            $('#cpf_responsavel').inputmask('999.999.999-99');
            $('#cep').inputmask('99999-999');
            
            $('.file-upload').click(function() {
                $('#documentos').click();
            });
            
            $('#documentos').change(function() {
                if (this.files.length > 0) {
                    $('.file-upload h5').html('<i class="fas fa-check-circle text-success me-2"></i>' + this.files.length + ' arquivo(s) selecionado(s)');
                }
            });
        });
    </script>
<?php include 'footer.php'; ?>
</body>
</html>
