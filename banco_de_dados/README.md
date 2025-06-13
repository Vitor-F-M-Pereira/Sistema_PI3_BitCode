📚 Banco de Dados - Sistema Escolar Sanquim
Este repositório contém o banco de dados utilizado pelo sistema de planejamento de aulas e gerenciamento de alunos do Projeto Interdisciplinar - 3º Semestre DSM - Equipe BitCode.

📌 Estrutura do Banco
O banco de dados contém as seguintes tabelas principais:

usuarios → Administradores (superusuários)

materias → Dados das matérias (disciplinas)

alunos_pendentes → Alunos aguardando aprovação

alunos_aceitos → Alunos aprovados

planos_aula → Planejamento de aulas

registro_aulas → Registro das aulas ministradas

🛠 Como Criar um Administrador (Superusuário)
O sistema só permite acesso ao painel administrativo para usuários cadastrados na tabela usuarios com o campo tipo_usuario definido como 'admin'.

✅ Exemplo de INSERT SQL para criar um superusuário
⚠ Importante:
A senha precisa ser cadastrada no formato hash gerado pelo PHP, utilizando a função password_hash() com o algoritmo PASSWORD_DEFAULT.

Se quiser gerar um hash manualmente, pode rodar este comando num arquivo PHP local:

php
Copiar
Editar
<?php
echo password_hash('sua_senha_aqui', PASSWORD_DEFAULT);
?>
Exemplo de inserção (supondo que você gerou o hash):
sql
Copiar
Editar
INSERT INTO usuarios (nome, login, senha, tipo_usuario, ativo)
VALUES ('Administrador Master', 'admin', '$2y$10$sua_hash_aqui', 'admin', 1);
Campos:

Campo	Tipo	Exemplo
nome	Texto	Administrador Master
login	Texto	admin
senha	Hash	(gerado com password_hash)
tipo_usuario	Enum	admin
ativo	Tinyint	1

✅ Exemplo real de hash de senha gerado (não use esta senha em produção):
sql
Copiar
Editar
INSERT INTO usuarios (nome, login, senha, tipo_usuario, ativo)
VALUES ('Administrador Master', 'admin', '$2y$10$WjlcCLpXIskFbBk5gKzq1.B7kfaTgRAsak9KMu/Fy4D3PSAej0nOq', 'admin', 1);
(Essa senha corresponde a: admin123)

✅ Fluxo esperado após o cadastro do admin:
O usuário poderá acessar o sistema no /login.php

Informar o login e a senha criados

Será redirecionado automaticamente para o painel administrativo

💾 Requisitos do Banco
MariaDB ou MySQL

Charset: utf8mb4

Engine: InnoDB

👥 Equipe de Desenvolvimento - BitCode (3º Semestre DSM)
Brenda Vitória Scarpioni

César Antonio de Oliveira Rocha

João Vitor Vieira da Silva

Vitor Francisco Moraes Pereira