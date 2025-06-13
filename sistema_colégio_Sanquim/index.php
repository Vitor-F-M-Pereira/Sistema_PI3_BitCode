<?php
session_start();
include 'menu.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Sistema Educacional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
       
        .welcome-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            border-top: 4px solid var(--amarelo-destaque);
            background: white;
            margin-bottom: 1.5rem;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .card-title {
            font-size: 1.5rem;
            color: var(--verde-escuro);
            font-weight: 700;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--amarelo-destaque);
        }

        .card-text.fs-5 {
            font-size: 1rem !important;
        }

        .highlight-box {
            background-color: rgba(0, 109, 119, 0.1);
            border-left: 4px solid var(--verde-principal);
            border-radius: 0 8px 8px 0;
            padding: 1.25rem;
            margin: 1rem 0;
        }

        .highlight-box h5 {
            font-size: 1rem;
        }

        .modal-success .modal-header {
            background-color: var(--verde-principal);
            color: white;
            border-bottom: none;
        }

        .modal-success .modal-footer {
            border-top: none;
        }

    </style>
</head>
<body class="bg-light">
    <div class="content-wrapper">
        <section class="hero-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title display-4 mb-3">Sistema de Planejamento de Aulas</h1>
                        <p class="lead mb-0">A ferramenta completa para gestão educacional</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="container mb-3">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="welcome-card p-4 p-md-4">
                        <div class="card-body text-center">
                            <h2 class="card-title mb-3">Bem-vindo ao Sistema!</h2>
                            <p class="card-text mb-3">Este sistema foi desenvolvido para otimizar o planejamento e registro de aulas do cursinho pré-vestibular.</p>

                            <div class="highlight-box text-start">
                                <h5 class="fw-bold mb-2"><i class="fas fa-lightbulb me-2"></i>Experimente</h5>
                                <p class="mb-1">Este novo sistema de planejamento e registro de aulas desenvolvido pelo grupo BitCode.</p>
                                <small class="text-muted">(Recurso disponível apenas para funcionários)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['mensagem_logout'])): ?>
        <div class="modal fade show" id="logoutModal" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content modal-success">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Sucesso</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><?= $_SESSION['mensagem_logout'] ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                logoutModal.show();

                document.getElementById('logoutModal').addEventListener('hidden.bs.modal', () => {
                    <?php unset($_SESSION['mensagem_logout']); ?>
                });
            });
        </script>
        <?php endif; ?>
              <?php include 'footer.php'; ?>
    </div>

  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
