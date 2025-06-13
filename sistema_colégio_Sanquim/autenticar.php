<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $queryAdmin = "SELECT * FROM usuarios WHERE login = ?";
    $stmtAdmin = $conn->prepare($queryAdmin);
    $stmtAdmin->bind_param("s", $login);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows === 1) {
        $admin = $resultAdmin->fetch_assoc();
        if (password_verify($senha, $admin['senha'])) {
            $_SESSION['id_usuario'] = $admin['id'];
            $_SESSION['login'] = $admin['login'];
            $_SESSION['tipo_usuario'] = 'admin';
            $_SESSION['mensagem_boas_vindas'] = "Bem-vindo, administrador!";
            header("Location: adm/painel_admin.php");
            exit;
        }
    }


    $queryProf = "SELECT * FROM materias WHERE login_materia = ? AND ativo = 1";
    $stmtProf = $conn->prepare($queryProf);
    $stmtProf->bind_param("s", $login);
    $stmtProf->execute();
    $resultProf = $stmtProf->get_result();

    if ($resultProf->num_rows === 1) {
        $prof = $resultProf->fetch_assoc();
        if (password_verify($senha, $prof['senha_materia'])) {
            $_SESSION['id_materia'] = $prof['id'];
            $_SESSION['login'] = $prof['login_materia'];
            $_SESSION['tipo_usuario'] = 'professor';
            $_SESSION['mensagem_boas_vindas'] = "Bem-vindo ao painel do professor!";
            header("Location: professor/painel_professor.php");
            exit;
        }
    }

    $_SESSION['erro_login'] = "Login ou senha inválidos!";
    header("Location: login.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}

