<?php
if (!isset($_SESSION)) session_start();

$tipo = $_SESSION['tipo_usuario'] ?? null;
$logado = isset($_SESSION['login']);
$currentPage = basename($_SERVER['PHP_SELF']);

$pastaAtual = $_SERVER['PHP_SELF'];
$caminhoBase = (strpos($pastaAtual, '/adm/') !== false || strpos($pastaAtual, '/professor/') !== false) ? '../' : '';
?>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    :root {
        --amarelo-principal: rgb(245, 203, 34);
        --amarelo-claro: rgb(255, 222, 75);
        --azul-destaque: #006d77;
        --branco: #ffffff;
    }

    .navbar-degrade {
        background: var(--amarelo-principal);
        padding: 0.8rem clamp(1rem, 5vw, 3rem);
        font-family: 'Roboto', sans-serif;
        position: relative;
        z-index: 1000;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand img {
        height: 50px;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover img {
        transform: scale(1.05);
    }

    .nav-link {
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--azul-destaque) !important;
        margin-left: 1.2rem;
        padding: 0.5rem 1.2rem;
        border-radius: 0.5rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .nav-link:hover {
        background-color: var(--azul-destaque);
        color: var(--branco) !important;
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .nav-link.active {
        background-color: var(--azul-destaque);
        color: var(--branco) !important;
    }

    #dataHora {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--azul-destaque);
    }

    .onda-abaixo {
        position: relative;
        width: 100%;
        margin-top: -1px;
        z-index: 999;
    }

    .onda-abaixo svg {
        display: block;
        width: 100%;
        height: 50px;
        transform: rotateX(180deg);
        fill: var(--amarelo-claro);
    }

    @media (max-width: 992px) {
        .navbar-nav {
            padding-top: 1rem;
        }

        .nav-link {
            margin-left: 0;
            text-align: center;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-degrade mb-0">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="<?= $caminhoBase ?>index.php">
            <img src="<?= $caminhoBase ?>img/logo.png" alt="Logo Sanquim">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <?php if ($logado): ?>
                    <li class="nav-item me-3">
                        <span class="nav-link disabled" id="dataHora"></span>
                    </li>

                    <?php if ($tipo === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'painel_admin.php' ? 'active' : '' ?>" 
                               href="<?= $caminhoBase ?>adm/painel_admin.php">
                               Painel do Administrador
                            </a>
                        </li>
                    <?php elseif ($tipo === 'professor'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'painel_professor.php' ? 'active' : '' ?>" 
                               href="<?= $caminhoBase ?>professor/painel_professor.php">
                               Painel do Professor
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $caminhoBase ?>logout.php">Sair</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $caminhoBase ?>matricula.php">Fazer pré matrícula</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $caminhoBase ?>login.php">Login para Funcionários</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="onda-abaixo">
    <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
        <path d="M0,0 C480,60 960,40 1440,80 L1440,100 L0,100 Z"></path>
    </svg>
</div>

<?php if ($logado): ?>
<script>
    function atualizarDataHora() {
        const agora = new Date();
        const dia = String(agora.getDate()).padStart(2, '0');
        const mes = String(agora.getMonth() + 1).padStart(2, '0');
        const ano = agora.getFullYear();
        const horas = String(agora.getHours()).padStart(2, '0');
        const minutos = String(agora.getMinutes()).padStart(2, '0');
        document.getElementById('dataHora').textContent = `${dia}/${mes}/${ano} ${horas}:${minutos}`;
    }

    atualizarDataHora();
    setInterval(atualizarDataHora, 60000);
</script>
<?php endif; ?>