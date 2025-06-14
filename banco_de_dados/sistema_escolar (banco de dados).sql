-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/06/2025 às 19:30
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_escolar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_aceitos`
--

CREATE TABLE `alunos_aceitos` (
  `id_aluno` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `tipo_residencia` varchar(20) DEFAULT NULL,
  `nome_responsavel` varchar(100) DEFAULT NULL,
  `cpf_responsavel` varchar(14) DEFAULT NULL,
  `curso` enum('Pré-Vestibular','Pré-Vestibulinho') NOT NULL,
  `comprovante_residencia` varchar(255) DEFAULT NULL,
  `data_aprovacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `faltas` int(11) DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_pendentes`
--

CREATE TABLE `alunos_pendentes` (
  `id_aluno` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date NOT NULL,
  `cep` varchar(9) NOT NULL,
  `rua` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `tipo_residencia` varchar(20) DEFAULT NULL,
  `nome_responsavel` varchar(100) DEFAULT NULL,
  `cpf_responsavel` varchar(14) DEFAULT NULL,
  `curso` enum('Pré-Vestibular','Pré-Vestibulinho') NOT NULL,
  `comprovante_residencia` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencia`
--

CREATE TABLE `frequencia` (
  `id_frequencia` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_aula` int(11) NOT NULL,
  `presenca` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nome_materia` varchar(100) NOT NULL,
  `login_materia` varchar(50) NOT NULL,
  `senha_materia` varchar(255) NOT NULL,
  `curso` enum('Pré-Vestibular','Pré-Vestibulinho','Ambos') NOT NULL,
  `ementa` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_aula`
--

CREATE TABLE `planos_aula` (
  `id` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `data_aula` date NOT NULL,
  `titulo_aula` varchar(100) NOT NULL,
  `conteudo_planejado` text NOT NULL,
  `observacoes` text DEFAULT NULL,
  `tipo_aula` enum('Normal','Revisão','Extra') DEFAULT 'Normal',
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `registro_aulas`
--

CREATE TABLE `registro_aulas` (
  `id` int(11) NOT NULL,
  `id_plano` int(11) NOT NULL,
  `nome_professor` varchar(100) NOT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `resumo_aula` text NOT NULL,
  `conteudo_dado` text NOT NULL,
  `data_registro` datetime DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin') NOT NULL DEFAULT 'admin',
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `login`, `senha`, `tipo_usuario`, `ativo`) VALUES
(9, 'William', 'will', '$2y$10$jdOe6l1ASLJAMlLp3ulw6OfAjPj3Hs0dWguc5pV/GlaT/saxY9GYy', 'admin', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos_aceitos`
--
ALTER TABLE `alunos_aceitos`
  ADD PRIMARY KEY (`id_aluno`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `alunos_pendentes`
--
ALTER TABLE `alunos_pendentes`
  ADD PRIMARY KEY (`id_aluno`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `frequencia`
--
ALTER TABLE `frequencia`
  ADD PRIMARY KEY (`id_frequencia`),
  ADD KEY `id_aluno` (`id_aluno`),
  ADD KEY `id_aula` (`id_aula`);

--
-- Índices de tabela `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_materia` (`login_materia`);

--
-- Índices de tabela `planos_aula`
--
ALTER TABLE `planos_aula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Índices de tabela `registro_aulas`
--
ALTER TABLE `registro_aulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_plano` (`id_plano`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos_aceitos`
--
ALTER TABLE `alunos_aceitos`
  MODIFY `id_aluno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `alunos_pendentes`
--
ALTER TABLE `alunos_pendentes`
  MODIFY `id_aluno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `frequencia`
--
ALTER TABLE `frequencia`
  MODIFY `id_frequencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `planos_aula`
--
ALTER TABLE `planos_aula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `registro_aulas`
--
ALTER TABLE `registro_aulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `frequencia`
--
ALTER TABLE `frequencia`
  ADD CONSTRAINT `frequencia_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos_aceitos` (`id_aluno`),
  ADD CONSTRAINT `frequencia_ibfk_2` FOREIGN KEY (`id_aula`) REFERENCES `registro_aulas` (`id`);

--
-- Restrições para tabelas `planos_aula`
--
ALTER TABLE `planos_aula`
  ADD CONSTRAINT `planos_aula_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id`);

--
-- Restrições para tabelas `registro_aulas`
--
ALTER TABLE `registro_aulas`
  ADD CONSTRAINT `registro_aulas_ibfk_1` FOREIGN KEY (`id_plano`) REFERENCES `planos_aula` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
