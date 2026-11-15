-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/11/2026 às 20:09
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `meu_banco`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_tentativas`
--

CREATE TABLE `login_tentativas` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `tipo` varchar(20) NOT NULL DEFAULT 'login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `login_tentativas`
--

INSERT INTO `login_tentativas` (`id`, `username`, `ip`, `criado_em`, `tipo`) VALUES
(6, 'ana', '127.0.0.1', '2026-11-15 14:13:31', 'login'),
(19, '/* ========================================    PAI', '::1', '2026-11-15 15:13:59', 'login'),
(22, '', '::1', '2026-11-15 17:31:59', 'cadastro'),
(23, '', '::1', '2026-11-15 17:32:12', 'cadastro'),
(27, '', '::1', '2026-11-15 18:09:43', 'cadastro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `senha_hash`, `criado_em`, `role`) VALUES
(1, 'admin', 'my_site_domain@outlook.com', '$2y$12$56BEpii.G0TZt0f3W8DpweOUQUb731QzYwU1CHE3FFMS6rLu89y6y', '2026-11-15 12:40:47', 'admin'),
(2, 'maria_oliveira', 'maria.oliveira@email.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '2026-11-14 15:12:38', 'user'),
(3, 'pedro_santos', 'pedro.santos@email.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF412.2lR8uE2z7fM1o6r5Y.nB2tA9iK', '2026-11-14 15:12:38', 'user'),
(4, 'ana_costa', 'ana.costa@email.com', '$2y$10$7rLSvRVyTQORapkDOqmkYOuF412.2lR8uE2z7fM1o6r5Y.nB2tA9iL', '2026-11-14 15:12:38', 'user'),
(5, 'lucas_ferreira', 'lucas.ferreira@email.com', '$2y$10$1aB2c3D4e5F6g7H8i9J0kLmNoPqRsTuVwXyZ0123456789abcdefG', '2026-11-14 15:12:38', 'user'),
(6, 'joao_silva', 'joao.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-11-14 15:12:38', 'user');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `login_tentativas`
--
ALTER TABLE `login_tentativas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`criado_em`),
  ADD KEY `ip` (`ip`,`criado_em`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `login_tentativas`
--
ALTER TABLE `login_tentativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
