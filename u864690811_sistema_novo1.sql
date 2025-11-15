-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 15, 2025 at 09:09 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u864690811_sistema_novo1`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id_log` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `data` timestamp NULL DEFAULT current_timestamp(),
  `id_usuario_acao` int(11) DEFAULT NULL,
  `nome_usuario_acao` varchar(255) DEFAULT NULL,
  `role_usuario_acao` varchar(50) DEFAULT NULL,
  `acao_tipo` varchar(50) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id_log`, `org_id`, `data`, `id_usuario_acao`, `nome_usuario_acao`, `role_usuario_acao`, `acao_tipo`, `descricao`) VALUES
(1, 1, '2025-10-27 20:29:29', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_CREATE', 'Usuário \'Kevin\' (ID: 4) foi criado.'),
(2, 1, '2025-10-27 20:32:01', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 120.'),
(3, 1, '2025-10-27 20:32:23', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 31.'),
(4, 1, '2025-10-27 20:32:37', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 48.'),
(5, 1, '2025-10-27 20:32:48', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 51.'),
(6, 1, '2025-10-27 20:35:36', 5, 'teste', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'teste\' (ID: 5). Lucro: 40.'),
(7, 1, '2025-10-27 20:35:56', 10, 'leo adm', 'admin', 'USER_DELETE', 'Usuário \'teste doidin\' (ID: 3) foi permanentemente apagado.'),
(8, 1, '2025-10-27 20:41:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_CREATE', 'Usuário \'Iago\' (ID: 6) foi criado.'),
(9, 1, '2025-10-27 20:42:12', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_CREATE', 'Usuário \'Davi\' (ID: 7) foi criado.'),
(10, 1, '2025-10-27 20:42:48', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_CREATE', 'Usuário \'Miguel\' (ID: 8) foi criado.'),
(11, 1, '2025-10-27 20:45:02', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 81.'),
(12, 1, '2025-10-27 20:45:44', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 144.'),
(13, 1, '2025-10-27 20:46:27', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: -11.'),
(14, 1, '2025-10-27 20:47:02', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 34.'),
(15, 1, '2025-10-27 20:47:32', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 75.'),
(16, 1, '2025-10-27 20:48:06', 10, 'leo adm', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'teste\' (ID: 5). Lucro: 140.'),
(17, 1, '2025-10-27 20:49:52', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 172.'),
(18, 1, '2025-10-27 20:50:03', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: 48.'),
(19, 1, '2025-10-27 20:50:21', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: 118.'),
(20, 1, '2025-10-27 20:50:42', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: -20.'),
(21, 1, '2025-10-27 20:51:02', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: 121.'),
(22, 1, '2025-10-27 20:51:22', 8, 'leonardo', 'super_adm', 'MANAGER_UPDATE', 'Gerente \'leo adm\' (ID: 10) foi atualizado.'),
(23, 1, '2025-10-27 20:51:22', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: 58.'),
(24, 1, '2025-10-27 20:51:48', 8, 'Miguel', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Miguel\' (ID: 8). Lucro: 87.'),
(25, 1, '2025-10-27 21:04:50', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_CREATE', 'Usuário \'Douglas\' (ID: 9) foi criado.'),
(26, 1, '2025-10-27 21:06:31', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 122.'),
(27, 1, '2025-10-27 21:06:39', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 232.'),
(28, 1, '2025-10-27 21:06:49', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 47.'),
(29, 1, '2025-10-27 21:06:55', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 73.'),
(30, 1, '2025-10-27 21:08:28', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(31, 1, '2025-10-27 21:14:02', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: -3.'),
(32, 1, '2025-10-27 21:14:22', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: -78.'),
(33, 1, '2025-10-27 21:15:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: -78.'),
(34, 1, '2025-10-27 21:16:12', 7, 'masterchief', 'super_adm', 'REPORT_EDIT', 'Relatório (ID: 24) foi corrigido. Novo Lucro: 73.'),
(35, 1, '2025-10-27 21:20:49', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 26) foi corrigido. Novo Lucro: 0.'),
(36, 1, '2025-10-27 21:30:49', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 211.'),
(37, 1, '2025-10-27 21:33:12', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 279.'),
(38, 1, '2025-10-27 21:35:14', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 198.'),
(39, 1, '2025-10-27 21:35:22', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 26) do usuário \'Kevin\' foi apagada.'),
(40, 1, '2025-10-27 21:39:26', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 111.'),
(41, 1, '2025-10-27 21:49:44', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 24) do usuário \'Douglas\' foi apagada.'),
(42, 1, '2025-10-27 21:49:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 23) do usuário \'Douglas\' foi apagada.'),
(43, 1, '2025-10-27 21:50:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 21) do usuário \'Douglas\' foi apagada.'),
(44, 1, '2025-10-27 21:50:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 22) do usuário \'Douglas\' foi apagada.'),
(45, 1, '2025-10-27 21:52:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 122.'),
(46, 1, '2025-10-27 21:54:04', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 232.'),
(47, 1, '2025-10-27 21:54:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 47.'),
(48, 1, '2025-10-27 21:54:50', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 73.'),
(49, 1, '2025-10-27 22:30:48', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 70.'),
(50, 1, '2025-10-27 22:45:31', 10, 'correia', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'correia\' (ID: 10). Lucro: 75.'),
(51, 1, '2025-10-27 22:49:24', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 52.'),
(52, 1, '2025-10-27 22:58:32', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 42.'),
(53, 1, '2025-10-27 23:39:54', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 111.'),
(54, 1, '2025-10-27 23:40:01', 11, 'MINHOCA', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'MINHOCA\' (ID: 11). Lucro: 150.'),
(55, 1, '2025-10-27 23:41:13', 10, 'leo adm', 'admin', 'MANAGER_CREATE', 'Gerente \'doidin\' (ID: 11) foi criado com role \'sub_adm\'.'),
(56, 1, '2025-10-27 23:43:39', 11, 'doidin', 'sub_adm', 'USER_CREATE', 'Usuário \'sacola\' (ID: 12) foi criado.'),
(57, 1, '2025-10-27 23:45:27', 12, 'sacola', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'sacola\' (ID: 12). Lucro: 100.'),
(58, 1, '2025-10-27 23:46:22', 11, 'doidin', 'sub_adm', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'sacola\' (ID: 12). Lucro: 60.'),
(59, 1, '2025-10-27 23:47:13', 11, 'doidin', 'sub_adm', 'REPORT_SAVE', 'Relatório salvo com o nome: sacola.'),
(60, 1, '2025-10-28 00:19:01', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 49.'),
(61, 1, '2025-10-28 00:38:09', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 6.'),
(62, 1, '2025-10-28 01:11:31', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 157.'),
(63, 1, '2025-10-28 01:23:29', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 36.'),
(64, 1, '2025-10-28 01:35:04', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 25.'),
(65, 1, '2025-10-28 01:49:40', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: -8.'),
(66, 1, '2025-10-28 01:51:57', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 46.'),
(67, 1, '2025-10-28 04:41:59', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_SAVE', 'Relatório salvo com o nome: Relatório 27/10.'),
(68, 1, '2025-10-28 04:44:52', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(69, 1, '2025-10-28 13:07:12', NULL, 'Sistema', 'SYSTEM', 'LOGIN_FAIL', 'Tentativa de login falhou para o email: r3nato90@hotmail.com.'),
(70, 1, '2025-10-28 13:07:30', NULL, 'Sistema', 'SYSTEM', 'LOGIN_FAIL', 'Tentativa de login falhou para o email: r3nato90@hotmail.com.'),
(71, 1, '2025-10-28 13:15:55', 7, 'masterchief', 'super_adm', 'MANAGER_UPDATE', 'Gerente \'DOUGLAS DALPICCOL\' (ID: 6) foi atualizado.'),
(72, 1, '2025-10-28 13:26:58', 7, 'masterchief', 'super_adm', 'MANAGER_UPDATE', 'Gerente \'DOUGLAS DALPICCOL\' (ID: 6) foi atualizado.'),
(73, 1, '2025-10-28 19:31:47', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 353. Com. User: 176.5. Com. Gerente: 176.5.'),
(74, 1, '2025-10-28 20:35:37', NULL, 'Sistema', 'SYSTEM', 'LOGIN_FAIL', 'Tentativa de login falhou para o email: zkillersop@gmail.com.'),
(75, 1, '2025-10-28 21:07:03', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 123. Com. User: 61.5. Com. Gerente: 61.5.'),
(76, 1, '2025-10-28 21:13:19', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 52) foi corrigido. Novo Lucro: 73.'),
(77, 1, '2025-10-28 23:16:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(78, 1, '2025-10-28 23:45:06', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 44. Com. User: 0. Com. Gerente: 44.'),
(79, 1, '2025-10-29 00:05:05', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 34. Com. User: 17. Com. Gerente: 17.'),
(80, 1, '2025-10-29 00:19:55', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 174. Com. User: 0. Com. Gerente: 174.'),
(81, 1, '2025-10-29 00:41:17', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: -4. Com. User: -2. Com. Gerente: 0.'),
(82, 1, '2025-10-29 00:57:26', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 52. Com. User: 0. Com. Gerente: 52.'),
(83, 1, '2025-10-29 01:13:08', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 76. Com. User: 38. Com. Gerente: 38.'),
(84, 1, '2025-10-29 01:31:46', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 70. Com. User: 35. Com. Gerente: 35.'),
(85, 1, '2025-10-29 01:46:27', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 57) foi corrigido. Novo Lucro: 2.'),
(86, 1, '2025-10-29 02:12:05', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 118. Com. User: 59. Com. Gerente: 59.'),
(87, 1, '2025-10-29 02:22:20', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 10. Com. User: 5. Com. Gerente: 5.'),
(88, 1, '2025-10-29 02:41:31', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 92. Com. User: 46. Com. Gerente: 46.'),
(89, 1, '2025-10-29 02:44:50', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 278. Com. User: 0. Com. Gerente: 278.'),
(90, 1, '2025-10-29 02:51:42', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: -50. Com. User: -25. Com. Gerente: 0.'),
(91, 1, '2025-10-29 02:51:48', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 60) foi corrigido. Novo Lucro: 68.'),
(92, 1, '2025-10-29 02:52:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 64) do usuário \'Kevin\' foi apagada.'),
(93, 1, '2025-10-29 03:06:57', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 136. Com. User: 68. Com. Gerente: 68.'),
(94, 1, '2025-10-29 03:26:05', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 67. Com. User: 33.5. Com. Gerente: 33.5.'),
(95, 1, '2025-10-29 03:36:31', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 56. Com. User: 28. Com. Gerente: 28.'),
(96, 1, '2025-10-29 03:44:09', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Davi\' (ID: 7). Lucro: 11. Com. User: 5.5. Com. Gerente: 5.5.'),
(97, 1, '2025-10-29 03:47:45', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 195. Com. User: 0. Com. Gerente: 195.'),
(98, 1, '2025-10-29 03:52:22', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 63) foi corrigido. Novo Lucro: 228.'),
(99, 1, '2025-10-29 04:00:52', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 77. Com. User: 38.5. Com. Gerente: 38.5.'),
(100, 1, '2025-10-29 04:04:59', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 70) foi corrigido. Novo Lucro: -23.'),
(101, 1, '2025-10-29 04:05:02', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: -100. Com. User: -50. Com. Gerente: 0.'),
(102, 1, '2025-10-29 04:06:46', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 67) foi corrigido. Novo Lucro: -44.'),
(103, 1, '2025-10-29 07:07:36', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_SAVE', 'Relatório salvo com o nome: Relatório 28/10.'),
(104, 1, '2025-10-29 07:08:10', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 32) foi corrigido. Novo Lucro: 122.'),
(105, 1, '2025-10-29 07:08:26', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 33) foi corrigido. Novo Lucro: 232.'),
(106, 1, '2025-10-29 07:08:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 34) foi corrigido. Novo Lucro: 47.'),
(107, 1, '2025-10-29 07:08:54', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 35) foi corrigido. Novo Lucro: 73.'),
(108, 1, '2025-10-29 07:09:01', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 38) foi corrigido. Novo Lucro: 52.'),
(109, 1, '2025-10-29 07:09:09', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 46) foi corrigido. Novo Lucro: 157.'),
(110, 1, '2025-10-29 07:09:36', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 63) foi corrigido. Novo Lucro: 228.'),
(111, 1, '2025-10-29 07:09:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 50) foi corrigido. Novo Lucro: 46.'),
(112, 1, '2025-10-29 07:10:18', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(113, 1, '2025-10-29 07:10:41', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(114, 1, '2025-10-29 07:10:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(115, 1, '2025-10-29 07:11:05', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 32) foi corrigido. Novo Lucro: 122.'),
(116, 1, '2025-10-29 16:42:13', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 163. Com. User: 81.5. Com. Gerente: 81.5.'),
(117, 1, '2025-10-29 17:20:46', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 72) foi corrigido. Novo Lucro: 155.'),
(118, 1, '2025-10-29 17:20:55', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 72) foi corrigido. Novo Lucro: 165.'),
(119, 1, '2025-10-29 20:12:44', NULL, 'Sistema', 'SYSTEM', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Iago\' (ID: 6). Lucro: 69. Com. User: 34.5. Com. Gerente: 34.5.'),
(120, 1, '2025-10-29 20:25:34', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 92. Com. User: 46. Com. Gerente: 46.'),
(121, 1, '2025-10-29 23:36:43', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 105. Com. User: 52.5. Com. Gerente: 52.5.'),
(122, 1, '2025-10-30 00:16:13', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 222. Com. User: 111. Com. Gerente: 111.'),
(123, 1, '2025-10-30 00:36:06', NULL, 'Sistema', 'SYSTEM', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 58. Com. User: 29. Com. Gerente: 29.'),
(124, 1, '2025-10-30 00:36:45', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 58. Com. User: 29. Com. Gerente: 29.'),
(125, 1, '2025-10-30 00:44:31', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 10. Com. User: 5. Com. Gerente: 5.'),
(126, 1, '2025-10-30 01:51:59', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 128. Com. User: 64. Com. Gerente: 64.'),
(127, 1, '2025-10-30 02:38:03', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 78) do usuário \'Kevin\' foi apagada.'),
(128, 1, '2025-10-30 02:38:18', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 77) foi corrigido. Novo Lucro: 68.'),
(129, 1, '2025-10-30 02:38:22', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 79) do usuário \'Kevin\' foi apagada.'),
(130, 1, '2025-10-30 02:38:43', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 71) do usuário \'Kevin\' foi apagada.'),
(131, 1, '2025-10-30 02:40:27', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Kevin\' (ID: 4). Lucro: 62. Com. User: 31. Com. Gerente: 31.'),
(132, 1, '2025-10-30 03:14:08', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 126. Com. User: 63. Com. Gerente: 63.'),
(133, 1, '2025-10-30 03:17:02', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(134, 1, '2025-10-30 03:17:08', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 82) foi corrigido. Novo Lucro: 126.'),
(135, 1, '2025-10-30 05:06:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_SAVE', 'Relatório salvo com o nome: Relatório 29/10.'),
(136, 1, '2025-10-30 05:31:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 61. Com. User: 0. Com. Gerente: 61.'),
(137, 1, '2025-10-30 06:08:43', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 119. Com. User: 0. Com. Gerente: 119.'),
(138, 1, '2025-10-30 06:09:03', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 82) foi corrigido. Novo Lucro: 126.'),
(139, 1, '2025-10-30 06:09:18', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 126. Com. User: 0. Com. Gerente: 126.'),
(140, 1, '2025-10-30 06:09:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 82) do usuário \'Douglas\' foi apagada.'),
(141, 1, '2025-10-30 06:50:46', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 190. Com. User: 0. Com. Gerente: 190.'),
(142, 1, '2025-10-30 07:14:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório enviado para o usuário \'Douglas\' (ID: 9). Lucro: 137. Com. User: 0. Com. Gerente: 137.'),
(143, 1, '2025-10-30 07:27:23', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 33) foi corrigido. Novo Lucro: 232.'),
(144, 1, '2025-10-30 13:08:22', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 32) foi corrigido. Novo Lucro: 122.'),
(145, 1, '2025-10-30 13:15:48', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 32) foi corrigido. Novo Lucro: 122.'),
(146, 1, '2025-10-30 13:18:09', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 76) foi corrigido. Novo Lucro: 222.'),
(147, 1, '2025-10-30 13:27:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'ERROR_TRANSACAO', 'Tentativa de envio retroativo (data: 2025-10-27) sem confirmação.'),
(148, 1, '2025-10-30 13:27:35', 6, 'DOUGLAS DALPICCOL', 'admin', 'ERROR_TRANSACAO', 'Tentativa de envio retroativo (data: 2025-10-27) sem confirmação.'),
(149, 1, '2025-10-30 13:31:03', 9, 'Doda CPA', 'super_adm', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-20)) enviado para \'CRIATTUS PROPAGANDA E MARKETING LTDA\' (ID: 2). Lucro: -6555.'),
(150, 1, '2025-10-30 13:31:25', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 122.'),
(151, 1, '2025-10-30 13:31:40', 9, 'Doda CPA', 'super_adm', 'REPORT_SAVE', 'Relatório salvo com o nome: 333.'),
(152, 1, '2025-10-30 13:31:52', 9, 'Doda CPA', 'super_adm', 'REPORT_DELETE', 'Relatório salvo (ID: 6) foi apagado.'),
(153, 1, '2025-10-30 13:31:58', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 32) do usuário \'Douglas\' foi apagada.'),
(154, 1, '2025-10-30 13:32:15', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 232.'),
(155, 1, '2025-10-30 13:32:28', 9, 'Doda CPA', 'super_adm', 'USER_DELETE', 'Usuário \'CRIATTUS PROPAGANDA E MARKETING LTDA\' (ID: 2) foi permanentemente apagado.'),
(156, 1, '2025-10-30 13:32:40', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 47.'),
(157, 1, '2025-10-30 13:32:50', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 73.'),
(158, 1, '2025-10-30 13:33:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 33) do usuário \'Douglas\' foi apagada.'),
(159, 1, '2025-10-30 13:33:28', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 34) do usuário \'Douglas\' foi apagada.'),
(160, 1, '2025-10-30 13:33:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 35) do usuário \'Douglas\' foi apagada.'),
(161, 1, '2025-10-30 13:34:15', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 52.'),
(162, 1, '2025-10-30 13:34:26', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 157.'),
(163, 1, '2025-10-30 13:34:39', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 46.'),
(164, 1, '2025-10-30 13:35:04', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 38) do usuário \'Douglas\' foi apagada.'),
(165, 1, '2025-10-30 13:35:20', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 50) do usuário \'Douglas\' foi apagada.'),
(166, 1, '2025-10-30 13:35:52', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 46) do usuário \'Douglas\' foi apagada.'),
(167, 1, '2025-10-30 13:36:15', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 2.'),
(168, 1, '2025-10-30 13:36:29', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Douglas\' (ID: 9). Lucro: 228.'),
(169, 1, '2025-10-30 13:36:43', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-28)) enviado para \'Douglas\' (ID: 9). Lucro: 228.'),
(170, 1, '2025-10-30 13:37:08', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-29)) enviado para \'Douglas\' (ID: 9). Lucro: 222.'),
(171, 1, '2025-10-30 13:37:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 97) do usuário \'Douglas\' foi apagada.'),
(172, 1, '2025-10-30 13:38:05', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 57) do usuário \'Douglas\' foi apagada.'),
(173, 1, '2025-10-30 13:38:18', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 63) do usuário \'Douglas\' foi apagada.'),
(174, 1, '2025-10-30 13:38:49', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 76) do usuário \'Douglas\' foi apagada.'),
(175, 1, '2025-10-30 13:39:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Davi\' (ID: 7). Lucro: 279.'),
(176, 1, '2025-10-30 13:39:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Davi\' (ID: 7). Lucro: 198.'),
(177, 1, '2025-10-30 13:39:58', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Davi\' (ID: 7). Lucro: 111.'),
(178, 1, '2025-10-30 13:40:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Davi\' (ID: 7). Lucro: 25.'),
(179, 1, '2025-10-30 13:40:39', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 29) do usuário \'Davi\' foi apagada.'),
(180, 1, '2025-10-30 13:40:56', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 40) do usuário \'Davi\' foi apagada.'),
(181, 1, '2025-10-30 13:41:07', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 30) do usuário \'Davi\' foi apagada.'),
(182, 1, '2025-10-30 13:41:16', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 48) do usuário \'Davi\' foi apagada.'),
(183, 1, '2025-10-30 13:42:24', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 81.'),
(184, 1, '2025-10-30 13:42:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 144.'),
(185, 1, '2025-10-30 13:42:44', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -11.'),
(186, 1, '2025-10-30 13:42:55', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 34.'),
(187, 1, '2025-10-30 13:43:14', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 75.'),
(188, 1, '2025-10-30 13:43:25', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 172.'),
(189, 1, '2025-10-30 13:43:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -3.'),
(190, 1, '2025-10-30 13:44:08', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -78.'),
(191, 1, '2025-10-30 13:44:19', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 111.'),
(192, 1, '2025-10-30 13:44:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 42.'),
(193, 1, '2025-10-30 13:44:45', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: 6.'),
(194, 1, '2025-10-30 13:44:56', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -8.'),
(195, 1, '2025-10-30 13:45:07', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-28)) enviado para \'Iago\' (ID: 6). Lucro: 76.'),
(196, 1, '2025-10-30 13:45:23', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-28)) enviado para \'Iago\' (ID: 6). Lucro: -44.'),
(197, 1, '2025-10-30 13:45:39', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-29)) enviado para \'Iago\' (ID: 6). Lucro: -44.'),
(198, 1, '2025-10-30 13:46:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 8) do usuário \'Iago\' foi apagada.'),
(199, 1, '2025-10-30 13:47:05', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 9) do usuário \'Iago\' foi apagada.'),
(200, 1, '2025-10-30 13:47:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 10) do usuário \'Iago\' foi apagada.'),
(201, 1, '2025-10-30 13:47:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 11) do usuário \'Iago\' foi apagada.'),
(202, 1, '2025-10-30 13:47:40', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 12) do usuário \'Iago\' foi apagada.'),
(203, 1, '2025-10-30 13:48:07', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 14) do usuário \'Iago\' foi apagada.'),
(204, 1, '2025-10-30 13:48:21', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 25) do usuário \'Iago\' foi apagada.'),
(205, 1, '2025-10-30 13:48:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 27) do usuário \'Iago\' foi apagada.'),
(206, 1, '2025-10-30 13:48:45', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 31) do usuário \'Iago\' foi apagada.'),
(207, 1, '2025-10-30 13:49:06', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 39) do usuário \'Iago\' foi apagada.'),
(208, 1, '2025-10-30 13:49:35', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 45) do usuário \'Iago\' foi apagada.'),
(209, 1, '2025-10-30 13:49:45', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 49) do usuário \'Iago\' foi apagada.'),
(210, 1, '2025-10-30 13:50:44', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 118) do usuário \'Iago\' foi apagada.'),
(211, 1, '2025-10-30 13:52:07', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 117) do usuário \'Iago\' foi apagada.'),
(212, 1, '2025-10-30 13:53:15', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 58) do usuário \'Iago\' foi apagada.'),
(213, 1, '2025-10-30 15:55:31', 10, 'leo adm', 'admin', 'USER_DELETE', 'Usuário \'MINHOCA\' (ID: 11) foi permanentemente apagado.'),
(214, 1, '2025-10-30 15:55:33', 10, 'leo adm', 'admin', 'USER_DELETE', 'Usuário \'correia\' (ID: 10) foi permanentemente apagado.'),
(215, 1, '2025-10-30 16:50:22', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 8. Com. User: 4. Com. Gerente: 4.'),
(216, 1, '2025-10-30 16:57:30', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 90. Com. User: 45. Com. Gerente: 45.'),
(217, 1, '2025-10-30 17:41:34', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 118. Com. User: 59. Com. Gerente: 59.'),
(218, 1, '2025-10-30 17:53:20', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 100. Com. User: 50. Com. Gerente: 50.'),
(219, 1, '2025-10-30 18:46:35', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 174. Com. User: 87. Com. Gerente: 87.'),
(220, 1, '2025-10-30 19:07:39', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 53. Com. User: 26.5. Com. Gerente: 26.5.'),
(221, 1, '2025-10-30 21:00:34', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 118. Com. User: 59. Com. Gerente: 59.'),
(222, 1, '2025-10-30 21:02:57', 13, 'viado', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'viado\' (ID: 13). Lucro: 269. Com. User: 67.25. Com. Gerente: 134.5.'),
(223, 1, '2025-10-30 21:10:43', 9, 'Doda CPA', 'super_adm', 'USER_UPDATE', 'Usuário \'Davi\' (ID: 7) foi atualizado.'),
(224, 1, '2025-10-30 21:10:45', 9, 'Doda CPA', 'super_adm', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(225, 1, '2025-10-30 21:10:47', 9, 'Doda CPA', 'super_adm', 'USER_UPDATE', 'Usuário \'Iago\' (ID: 6) foi atualizado.'),
(226, 1, '2025-10-30 21:10:49', 9, 'Doda CPA', 'super_adm', 'USER_UPDATE', 'Usuário \'Kevin\' (ID: 4) foi atualizado.'),
(227, 1, '2025-10-30 21:10:52', 9, 'Doda CPA', 'super_adm', 'USER_UPDATE', 'Usuário \'Miguel\' (ID: 8) foi atualizado.'),
(228, 1, '2025-10-30 21:12:01', 9, 'Doda CPA', 'super_adm', 'REPORT_DELETE', 'Relatório salvo (ID: 1) foi apagado.'),
(229, 1, '2025-10-30 21:14:19', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -11. Com. User: -5.5. Com. Gerente: -5.5.'),
(230, 1, '2025-10-30 21:14:44', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 106) do usuário \'Iago\' foi apagada.'),
(231, 1, '2025-10-30 21:15:09', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -3. Com. User: -1.5. Com. Gerente: -1.5.'),
(232, 1, '2025-10-30 21:15:20', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -78. Com. User: -39. Com. Gerente: -39.'),
(233, 1, '2025-10-30 21:15:49', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Retroativo (2025-10-27)) enviado para \'Iago\' (ID: 6). Lucro: -8. Com. User: -4. Com. Gerente: -4.'),
(234, 1, '2025-10-30 21:16:37', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: -5. Com. User: -2.5. Com. Gerente: -2.5.'),
(235, 1, '2025-10-30 21:17:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 115) do usuário \'Iago\' foi apagada.'),
(236, 1, '2025-10-30 21:18:08', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 110) do usuário \'Iago\' foi apagada.'),
(237, 1, '2025-10-30 21:18:39', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 111) do usuário \'Iago\' foi apagada.'),
(238, 1, '2025-10-30 21:28:28', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 3) foi corrigido. Novo Lucro: 120.'),
(239, 1, '2025-10-30 21:28:50', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 4) foi corrigido. Novo Lucro: 31.'),
(240, 1, '2025-10-30 21:28:56', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 5) foi corrigido. Novo Lucro: 48.'),
(241, 1, '2025-10-30 21:29:27', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 6) foi corrigido. Novo Lucro: 51.'),
(242, 1, '2025-10-30 21:29:36', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 28) foi corrigido. Novo Lucro: 211.'),
(243, 1, '2025-10-30 21:29:53', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 36) foi corrigido. Novo Lucro: 70.'),
(244, 1, '2025-10-30 21:30:01', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 44) foi corrigido. Novo Lucro: 49.'),
(245, 1, '2025-10-30 21:30:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 56) foi corrigido. Novo Lucro: -4.'),
(246, 1, '2025-10-30 21:30:55', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 47) foi corrigido. Novo Lucro: 36.'),
(247, 1, '2025-10-30 21:31:20', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 15) foi corrigido. Novo Lucro: 48.'),
(248, 1, '2025-10-30 21:31:46', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 16) foi corrigido. Novo Lucro: 118.'),
(249, 1, '2025-10-30 21:31:52', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 17) foi corrigido. Novo Lucro: -20.'),
(250, 1, '2025-10-30 21:31:58', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 18) foi corrigido. Novo Lucro: 121.'),
(251, 1, '2025-10-30 21:32:05', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 19) foi corrigido. Novo Lucro: 58.'),
(252, 1, '2025-10-30 21:32:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 20) foi corrigido. Novo Lucro: 87.'),
(253, 1, '2025-10-30 21:40:42', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 86. Com. User: 43. Com. Gerente: 43.'),
(254, 1, '2025-10-30 21:43:06', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 125) foi corrigido. Novo Lucro: 118.'),
(255, 1, '2025-10-30 21:43:25', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 131) do usuário \'Kevin\' foi apagada.'),
(256, 1, '2025-10-30 22:25:13', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-30)) enviado para \'Douglas\' (ID: 9). Lucro: 88. Com. User: 0. Com. Gerente: 44.'),
(257, 1, '2025-10-30 22:25:28', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 133) foi corrigido. Novo Lucro: 88.'),
(258, 1, '2025-10-30 22:26:25', 6, 'DOUGLAS DALPICCOL', 'admin', 'USER_UPDATE', 'Usuário \'Douglas\' (ID: 9) foi atualizado.'),
(259, 1, '2025-10-30 22:26:29', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 133) foi corrigido. Novo Lucro: 88.'),
(260, 1, '2025-10-30 22:26:35', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 133) do usuário \'Douglas\' foi apagada.'),
(261, 1, '2025-10-30 22:26:49', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-30)) enviado para \'Douglas\' (ID: 9). Lucro: 88. Com. User: 0. Com. Gerente: 44.'),
(262, 1, '2025-10-30 23:05:25', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 85. Com. User: 42.5. Com. Gerente: 42.5.'),
(263, 1, '2025-10-30 23:09:03', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 4. Com. User: 2. Com. Gerente: 2.'),
(264, 1, '2025-10-30 23:25:52', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 134) foi corrigido. Novo Lucro: 88.'),
(265, 1, '2025-10-30 23:26:10', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-30)) enviado para \'Douglas\' (ID: 9). Lucro: 88. Com. User: 0. Com. Gerente: 88.'),
(266, 1, '2025-10-30 23:26:21', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 134) do usuário \'Douglas\' foi apagada.'),
(267, 1, '2025-10-31 00:30:15', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 272. Com. User: 136. Com. Gerente: 136.'),
(268, 1, '2025-10-31 01:08:48', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 81. Com. User: 40.5. Com. Gerente: 40.5.'),
(269, 1, '2025-10-31 01:17:12', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 116. Com. User: 58. Com. Gerente: 58.'),
(270, 1, '2025-10-31 01:53:46', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 31. Com. User: 15.5. Com. Gerente: 15.5.'),
(271, 1, '2025-10-31 03:40:59', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 0. Com. User: 0. Com. Gerente: 0.'),
(272, 1, '2025-10-31 03:41:13', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 5. Com. User: 2.5. Com. Gerente: 2.5.'),
(273, 1, '2025-10-31 03:41:31', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 2. Com. User: 1. Com. Gerente: 1.'),
(274, 1, '2025-10-31 03:41:43', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 1. Com. User: 0.5. Com. Gerente: 0.5.'),
(275, 1, '2025-10-31 03:41:52', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: -10. Com. User: -5. Com. Gerente: -5.'),
(276, 1, '2025-10-31 04:38:01', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 3. Com. User: 1.5. Com. Gerente: 1.5.'),
(277, 1, '2025-10-31 04:39:18', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 3. Com. User: 1.5. Com. Gerente: 1.5.'),
(278, 1, '2025-10-31 05:16:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: 154. Com. User: 0. Com. Gerente: 154.'),
(279, 1, '2025-10-31 05:19:28', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 147) do usuário \'Iago\' foi apagada.'),
(280, 1, '2025-10-31 05:19:32', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 146) do usuário \'Iago\' foi apagada.'),
(281, 1, '2025-10-31 05:19:34', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 145) do usuário \'Iago\' foi apagada.'),
(282, 1, '2025-10-31 05:19:36', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 144) do usuário \'Iago\' foi apagada.'),
(283, 1, '2025-10-31 05:19:38', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 143) do usuário \'Iago\' foi apagada.'),
(284, 1, '2025-10-31 05:19:43', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 142) foi corrigido. Novo Lucro: 1.'),
(285, 1, '2025-10-31 05:22:21', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 144. Com. User: 72. Com. Gerente: 72.'),
(286, 1, '2025-10-31 06:37:42', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: -252. Com. User: 0. Com. Gerente: -252.'),
(287, 1, '2025-10-31 06:46:01', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 151) foi corrigido. Novo Lucro: -202.'),
(288, 1, '2025-10-31 06:46:11', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 151) foi corrigido. Novo Lucro: -202.'),
(289, 1, '2025-10-31 06:46:17', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 151) do usuário \'Douglas\' foi apagada.'),
(290, 1, '2025-10-31 06:46:33', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: -202. Com. User: 0. Com. Gerente: -202.'),
(291, 1, '2025-10-31 07:00:40', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: -26. Com. User: -13. Com. Gerente: -13.'),
(292, 1, '2025-10-31 07:04:03', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 153) foi corrigido. Novo Lucro: -12.'),
(293, 1, '2025-10-31 07:05:36', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 153) foi corrigido. Novo Lucro: 14.'),
(294, 1, '2025-10-31 17:01:51', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: 59. Com. User: 0. Com. Gerente: 59.'),
(295, 1, '2025-10-31 17:08:07', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_SAVE', 'Relatório salvo com o nome: Teste dia 31.'),
(296, 1, '2025-10-31 17:08:53', 9, 'Douglas', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Douglas\' (ID: 9). Lucro: 300. Com. User: 0. Com. Gerente: 300.'),
(297, 1, '2025-10-31 17:09:30', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 155) do usuário \'Douglas\' foi apagada.'),
(298, 1, '2025-10-31 17:27:59', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 36. Com. User: 18. Com. Gerente: 18.'),
(299, 1, '2025-10-31 17:58:24', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 73. Com. User: 36.5. Com. Gerente: 36.5.'),
(300, 1, '2025-10-31 18:39:19', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 130. Com. User: 65. Com. Gerente: 65.'),
(301, 1, '2025-10-31 20:31:16', NULL, 'Sistema', 'SYSTEM', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 19. Com. User: 9.5. Com. Gerente: 9.5.'),
(302, 1, '2025-10-31 20:40:24', NULL, 'Sistema', 'SYSTEM', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 134. Com. User: 67. Com. Gerente: 67.'),
(303, 1, '2025-10-31 21:16:39', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 10. Com. User: 5. Com. Gerente: 5.'),
(304, 1, '2025-10-31 21:42:05', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 77. Com. User: 38.5. Com. Gerente: 38.5.'),
(305, 1, '2025-10-31 22:21:20', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 85. Com. User: 42.5. Com. Gerente: 42.5.'),
(306, 1, '2025-10-31 22:33:56', NULL, 'Sistema', 'SYSTEM', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: 53. Com. User: 26.5. Com. Gerente: 26.5.'),
(307, 1, '2025-10-31 22:34:13', NULL, 'Sistema', 'SYSTEM', 'LOGIN_FAIL', 'Tentativa de login falhou para o email: darkeden1nsid@gmail.com.'),
(308, 1, '2025-10-31 22:34:29', NULL, 'Sistema', 'SYSTEM', 'LOGIN_FAIL', 'Tentativa de login falhou para o email: darkeden1nsid@gmail.com.'),
(309, 1, '2025-10-31 23:30:01', 6, 'Iago', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Iago\' (ID: 6). Lucro: -98. Com. User: -49. Com. Gerente: -49.'),
(310, 1, '2025-10-31 23:37:15', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 85. Com. User: 42.5. Com. Gerente: 42.5.'),
(311, 1, '2025-10-31 23:52:32', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: -369. Com. User: 0. Com. Gerente: -369.'),
(312, 1, '2025-10-31 23:57:27', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 165) foi corrigido. Novo Lucro: -148.'),
(313, 1, '2025-10-31 23:57:31', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_EDIT', 'Relatório (ID: 167) foi corrigido. Novo Lucro: -469.'),
(314, 1, '2025-10-31 23:57:41', 6, 'DOUGLAS DALPICCOL', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 167) do usuário \'Douglas\' foi apagada.'),
(315, 1, '2025-10-31 23:57:56', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-10-31)) enviado para \'Douglas\' (ID: 9). Lucro: -469. Com. User: 0. Com. Gerente: -469.'),
(316, 1, '2025-11-01 00:30:12', 4, 'Kevin', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Kevin\' (ID: 4). Lucro: 146. Com. User: 73. Com. Gerente: 73.'),
(317, 1, '2025-11-01 04:36:15', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 139. Com. User: 69.5. Com. Gerente: 69.5.'),
(318, 1, '2025-11-01 05:08:25', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 92. Com. User: 46. Com. Gerente: 46.'),
(319, 1, '2025-11-01 06:58:28', 7, 'Davi', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Padrão) enviado para \'Davi\' (ID: 7). Lucro: 69. Com. User: 34.5. Com. Gerente: 34.5.'),
(320, 1, '2025-11-01 08:40:00', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-01)) enviado para \'Douglas\' (ID: 9). Lucro: 171. Com. User: 0. Com. Gerente: 171.'),
(321, 1, '2025-11-01 09:21:51', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-01)) enviado para \'Douglas\' (ID: 9). Lucro: 57. Com. User: 0. Com. Gerente: 57.'),
(322, 1, '2025-11-01 10:00:06', 6, 'DOUGLAS DALPICCOL', 'admin', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-01)) enviado para \'Douglas\' (ID: 9). Lucro: 122. Com. User: 0. Com. Gerente: 122.'),
(323, 1, '2025-11-01 13:02:17', 7, 'Davi', 'platform_owner', 'ORG_CREATE', 'Organização \'Criattus\' (ID: 2) foi criada.'),
(324, 1, '2025-11-01 13:31:41', 7, 'Davi', 'platform_owner', 'ERROR_USER_UPDATE_GLOBAL', 'Falha ao editar usuário (ID: 7): SQLSTATE[42S22]: Column not found: 1054 Unknown column \'data_atualizacao\' in \'SET\'');
INSERT INTO `logs` (`id_log`, `org_id`, `data`, `id_usuario_acao`, `nome_usuario_acao`, `role_usuario_acao`, `acao_tipo`, `descricao`) VALUES
(325, 1, '2025-11-01 13:31:45', 7, 'Davi', 'platform_owner', 'ERROR_USER_UPDATE_GLOBAL', 'Falha ao editar usuário (ID: 7): SQLSTATE[42S22]: Column not found: 1054 Unknown column \'data_atualizacao\' in \'SET\''),
(327, 1, '2025-11-01 21:05:10', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 1) foi atualizado.'),
(328, 1, '2025-11-01 21:05:16', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 1) foi atualizado.'),
(329, 1, '2025-11-01 21:06:23', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 2) foi atualizado.'),
(330, 1, '2025-11-01 21:07:12', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 3) foi atualizado.'),
(331, 1, '2025-11-01 21:15:47', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 1) foi atualizado.'),
(332, 1, '2025-11-01 21:17:18', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 2) foi atualizado.'),
(333, 1, '2025-11-01 21:17:44', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 3) foi atualizado.'),
(334, 1, '2025-11-01 21:17:48', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 3) foi atualizado.'),
(335, 1, '2025-11-01 21:18:51', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 3) foi atualizado.'),
(336, 1, '2025-11-01 21:18:51', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 2) foi atualizado.'),
(337, 1, '2025-11-01 21:18:52', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 1) foi atualizado.'),
(338, 1, '2025-11-01 21:19:50', 6, 'Iago', 'platform_owner', 'PLAN_UPDATE', 'Plano (ID: 3) foi atualizado.'),
(339, 1, '2025-11-01 23:33:35', 6, 'Iago', 'platform_owner', 'ORG_REGISTER_PENDING', 'Nova organização \'Teste\' (ID: 4) foi criada por \'Jore\'. Aguardando pagamento.'),
(340, 1, '2025-11-15 07:16:19', 7, 'Davi', 'platform_owner', 'ORG_DELETE', 'Organização \'Teste\' (ID: 4) foi permanentemente apagada. Todos os dados associados (usuários, relatórios, etc.) foram excluídos.'),
(341, 1, '2025-11-15 07:16:26', 7, 'Davi', 'platform_owner', 'ORG_DELETE', 'Organização \'Criattus\' (ID: 2) foi permanentemente apagada. Todos os dados associados (usuários, relatórios, etc.) foram excluídos.'),
(342, 1, '2025-11-15 07:16:32', 7, 'Davi', 'platform_owner', 'ORG_DELETE', 'Organização \'Criattus222\' (ID: 3) foi permanentemente apagada. Todos os dados associados (usuários, relatórios, etc.) foram excluídos.'),
(343, 1, '2025-11-15 07:56:31', 6, 'Iago', 'platform_owner', 'ERROR_CREATE_GLOBAL', 'Falha (email duplicado global): douglasdalpiccol04@gmail.com.'),
(344, 1, '2025-11-15 07:58:14', 6, 'Iago', 'platform_owner', 'MANAGER_CREATE_GLOBAL', 'Gerente \'Douglas\' (ID: 14, Role: admin) foi criado pelo Platform Owner.'),
(345, 1, '2025-11-15 08:00:52', 6, 'Iago', 'platform_owner', 'ORG_UPDATE', 'Organização \'Doda CPA\' (ID: 1) foi atualizada.'),
(346, 1, '2025-11-15 08:02:06', 6, 'Iago', 'platform_owner', 'MANAGER_CREATE_GLOBAL', 'Gerente \'Doda\' (ID: 15, Role: admin) foi criado pelo Platform Owner.'),
(347, 1, '2025-11-15 08:03:17', 14, 'Teste Doda', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Teste Doda\' (ID: 14). Lucro: 100.'),
(348, 1, '2025-11-15 08:09:10', 15, 'Teste Doda', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Teste Doda\' (ID: 15). Lucro: 200.'),
(349, 1, '2025-11-15 08:11:18', 15, 'Doda', 'admin', 'USER_DELETE', 'Usuário \'Teste Doda\' (ID: 14) foi apagado.'),
(350, 1, '2025-11-15 08:11:20', 15, 'Doda', 'admin', 'USER_DELETE', 'Usuário \'Teste Doda\' (ID: 15) foi apagado.'),
(351, 1, '2025-11-15 08:16:54', 7, 'Davi', 'platform_owner', 'MANAGER_CREATE_GLOBAL', 'Gerente \'Platform Owner\' (ID: 16, Role: super_adm) foi criado pelo Platform Owner.'),
(352, 1, '2025-11-15 08:20:10', 16, 'Teste Doda', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Teste Doda\' (ID: 16). Lucro: 100.'),
(353, 1, '2025-11-15 08:22:59', 15, 'Doda', 'admin', 'MANAGER_CREATE', 'Gerente \'testepaulista\' (ID: 17, Role: sub_adm) foi criado.'),
(354, 1, '2025-11-15 08:24:51', 17, 'testepaulista', 'sub_adm', 'USER_CREATE', 'Usuário \'Teste ger\' (ID: 17) foi criado.'),
(355, 1, '2025-11-15 08:25:04', 17, 'testepaulista', 'sub_adm', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Teste ger\' (ID: 17). Lucro: 600.'),
(356, 1, '2025-11-15 08:27:33', 7, 'Davi', 'platform_owner', 'ORG_UPDATE', 'Organização \'Doda CPA\' (ID: 1) foi atualizada.'),
(357, 1, '2025-11-15 08:40:29', 16, 'Teste Doda', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Teste Doda\' (ID: 16). Lucro: 200.'),
(358, 1, '2025-11-15 08:44:04', 6, 'Iago', 'platform_owner', 'MANAGER_UPDATE_GLOBAL', 'Gerente \'testepaulista\' (ID: 17) foi atualizado.'),
(359, 1, '2025-11-15 08:47:15', 15, 'Doda', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 178) do \'Teste Doda\' foi apagada.'),
(360, 1, '2025-11-15 08:47:16', 15, 'Doda', 'admin', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 180) do \'Teste Doda\' foi apagada.'),
(361, 1, '2025-11-15 08:48:38', 18, 'Judas', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'Judas\' (ID: 18). Lucro: 200.'),
(362, 1, '2025-11-15 08:49:01', 17, 'testepaulista', 'sub_adm', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 179) do \'Teste ger\' foi apagada.'),
(363, 1, '2025-11-15 08:50:25', 6, 'Iago', 'platform_owner', 'ORG_STATUS_UPDATE', 'Status da Organização (ID: 1) foi alterado para \'suspended\'.'),
(364, 1, '2025-11-15 08:52:42', 6, 'Iago', 'platform_owner', 'ORG_CREATE', 'Organização \'Doda CPA7\' (ID: 5) foi criada.'),
(365, 1, '2025-11-15 08:53:35', 6, 'Iago', 'platform_owner', 'MANAGER_CREATE_GLOBAL', 'Gerente \'Doda CPA7\' (ID: 18, Role: admin) foi criado pelo Platform Owner.'),
(366, 1, '2025-11-15 08:53:50', 6, 'Iago', 'platform_owner', 'MANAGER_UPDATE_GLOBAL', 'Gerente \'Doda CPA7\' (ID: 18) foi atualizado.'),
(367, 5, '2025-11-15 08:54:36', 18, 'Doda CPA7', 'admin', 'MANAGER_CREATE', 'Gerente \'Paulista\' (ID: 19, Role: sub_adm) foi criado. Pai ID: 18.'),
(368, 5, '2025-11-15 08:55:14', 18, 'Doda CPA7', 'admin', 'MANAGER_UPDATE', 'Gerente \'Paulista\' (ID: 19) foi atualizado.'),
(369, 5, '2025-11-15 08:56:33', 19, 'torvic', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'torvic\' (ID: 19). Lucro: 1000.'),
(370, 5, '2025-11-15 08:56:46', 18, 'Doda CPA7', 'admin', 'USER_UPDATE', 'Usuário \'torvic\' (ID: 19) foi atualizado.'),
(371, 5, '2025-11-15 08:56:53', 19, 'torvic', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'torvic\' (ID: 19). Lucro: 1000.'),
(372, 5, '2025-11-15 08:57:08', 19, 'Paulista', 'sub_adm', 'REPORT_ENTRY_DELETE', 'Linha de relatório (ID: 182) do \'torvic\' foi apagada.'),
(373, 5, '2025-11-15 09:00:19', 18, 'Doda CPA7', 'admin', 'USER_UPDATE', 'Usuário \'torvic\' (ID: 19) foi atualizado.'),
(374, 5, '2025-11-15 09:00:25', 19, 'torvic', 'usuario', 'RELATORIO_ENVIO', 'Relatório (Data Manual (2025-11-15)) enviado para \'torvic\' (ID: 19). Lucro: 1000.'),
(375, 5, '2025-11-15 09:06:45', 18, 'Doda CPA7', 'admin', 'MANAGER_UPDATE', 'Gerente \'Paulista\' (ID: 19) foi atualizado.');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `org_id` int(11) NOT NULL,
  `org_name` varchar(255) NOT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `plan_type` varchar(50) DEFAULT 'pro',
  `max_admins` int(11) NOT NULL DEFAULT 1,
  `max_users` int(11) NOT NULL DEFAULT 5,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `super_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`org_id`, `org_name`, `cpf_cnpj`, `plan_type`, `max_admins`, `max_users`, `status`, `super_admin_id`, `created_at`) VALUES
(1, 'Doda CPA', '', 'pro', 999, 999, 'suspended', 9, '2025-11-01 11:53:21'),
(5, 'Doda CPA7', NULL, 'pro', 999, 999, 'active', NULL, '2025-11-15 08:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price_description` varchar(100) NOT NULL COMMENT 'Ex: R$ 49,90 /mês',
  `feature_1` varchar(255) DEFAULT NULL,
  `feature_2` varchar(255) DEFAULT NULL,
  `feature_3` varchar(255) DEFAULT NULL,
  `feature_4` varchar(255) DEFAULT NULL,
  `mercadopago_link` text DEFAULT NULL COMMENT 'Link de pagamento do MP',
  `default_max_users` int(11) NOT NULL DEFAULT 5,
  `default_max_admins` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_name`, `price_description`, `feature_1`, `feature_2`, `feature_3`, `feature_4`, `mercadopago_link`, `default_max_users`, `default_max_admins`) VALUES
(1, 'Básico', 'R$ 59,90 / mês', '1 Usuários (Operadores)', '1 Gerente (Admin)', 'Relatórios Completos', 'Suporte Básico', 'https://mpago.la/2NLfery', 2, 1),
(2, 'Pro', 'R$ 99,90 / MÊS', '5 Usuários (Operadores)', '2 Gerentes (Admin/Sub-Admin)', 'Relatórios Completos', 'Suporte Prioritário', 'https://mpago.la/1QXUYqQ', 5, 2),
(3, 'Empresarial', 'R$ 197,90 / MÊS', '20 Usuários (Operadores)', '2 Gerentes (Admin/Sub-Admin)', 'Relatórios Completos', 'Suporte Dedicado', 'https://mpago.la/11xpnR7', 20, 2);

-- --------------------------------------------------------

--
-- Table structure for table `platform_settings`
--

CREATE TABLE `platform_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `platform_settings`
--

INSERT INTO `platform_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'mp_public_key', '', '2025-11-01 12:41:57'),
(2, 'mp_access_token', '', '2025-11-01 12:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `relatorios`
--

CREATE TABLE `relatorios` (
  `id_relatorio` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `data` timestamp NULL DEFAULT current_timestamp(),
  `lucro_diario` decimal(10,2) DEFAULT NULL,
  `comissao_usuario` decimal(10,2) DEFAULT NULL,
  `comissao_sub_adm` decimal(10,2) DEFAULT NULL,
  `comissao_admin` decimal(10,2) DEFAULT 0.00,
  `valor_deposito` decimal(10,2) DEFAULT 0.00,
  `valor_saque` decimal(10,2) DEFAULT 0.00,
  `valor_bau` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `relatorios`
--

INSERT INTO `relatorios` (`id_relatorio`, `org_id`, `id_usuario`, `data`, `lucro_diario`, `comissao_usuario`, `comissao_sub_adm`, `comissao_admin`, `valor_deposito`, `valor_saque`, `valor_bau`) VALUES
(3, 1, 4, '2025-10-27 17:32:01', 120.00, 60.00, 60.00, 0.00, 835.00, 805.00, 150.00),
(4, 1, 4, '2025-10-27 17:32:23', 31.00, 15.50, 15.50, 0.00, 550.00, 481.00, 100.00),
(5, 1, 4, '2025-10-27 17:32:37', 48.00, 24.00, 24.00, 0.00, 545.00, 493.00, 100.00),
(6, 1, 4, '2025-10-27 17:32:48', 51.00, 25.50, 25.50, 0.00, 559.00, 510.00, 100.00),
(7, 1, 5, '2025-10-27 17:35:36', 40.00, 10.00, 0.00, 0.00, 550.00, 490.00, 100.00),
(13, 1, 5, '2025-10-27 17:48:06', 140.00, 35.00, 0.00, 0.00, 520.00, 560.00, 100.00),
(15, 1, 8, '2025-10-27 17:50:03', 48.00, 24.00, 24.00, 0.00, 538.00, 486.00, 100.00),
(16, 1, 8, '2025-10-27 17:50:21', 118.00, 59.00, 59.00, 0.00, 540.00, 558.00, 100.00),
(17, 1, 8, '2025-10-27 17:50:42', -20.00, -10.00, -10.00, 0.00, 540.00, 420.00, 100.00),
(18, 1, 8, '2025-10-27 17:51:02', 121.00, 60.50, 60.50, 0.00, 548.00, 569.00, 100.00),
(19, 1, 8, '2025-10-27 17:51:22', 58.00, 29.00, 29.00, 0.00, 548.00, 506.00, 100.00),
(20, 1, 8, '2025-10-27 17:51:48', 87.00, 43.50, 43.50, 0.00, 552.00, 539.00, 100.00),
(28, 1, 4, '2025-10-27 18:30:49', 211.00, 105.50, 105.50, 0.00, 829.00, 890.00, 150.00),
(36, 1, 4, '2025-10-27 19:30:48', 70.00, 35.00, 35.00, 0.00, 547.00, 517.00, 100.00),
(37, 1, NULL, '2025-10-27 19:45:31', 75.00, 18.75, 37.50, 0.00, 545.00, 520.00, 100.00),
(41, 1, NULL, '2025-10-27 20:40:01', 150.00, 37.50, 75.00, 0.00, 1100.00, 1050.00, 200.00),
(42, 1, 12, '2025-10-27 20:45:27', 100.00, 25.00, 25.00, 0.00, 750.00, 700.00, 150.00),
(43, 1, 12, '2025-10-27 20:46:22', 60.00, 15.00, 15.00, 0.00, 540.00, 500.00, 100.00),
(44, 1, 4, '2025-10-27 21:19:01', 49.00, 24.50, 24.50, 0.00, 547.00, 496.00, 100.00),
(47, 1, 4, '2025-10-27 22:23:29', 36.00, 18.00, 18.00, 0.00, 822.00, 708.00, 150.00),
(51, 1, 7, '2025-10-28 16:31:47', 353.00, 176.50, 176.50, 0.00, 1080.00, 1233.00, 200.00),
(52, 1, 7, '2025-10-28 18:07:03', 73.00, 36.50, 36.50, 0.00, 1076.00, 999.00, 150.00),
(53, 1, 9, '2025-10-28 20:45:06', 44.00, 0.00, 44.00, 0.00, 806.00, 700.00, 150.00),
(54, 1, 4, '2025-10-28 21:05:05', 34.00, 17.00, 17.00, 0.00, 816.00, 700.00, 150.00),
(55, 1, 9, '2025-10-28 21:19:55', 174.00, 0.00, 174.00, 0.00, 810.00, 834.00, 150.00),
(56, 1, 4, '2025-10-28 21:41:17', -4.00, -2.00, -2.00, 0.00, 551.00, 447.00, 100.00),
(59, 1, 4, '2025-10-28 22:31:46', 70.00, 35.00, 35.00, 0.00, 559.00, 529.00, 100.00),
(60, 1, 4, '2025-10-28 23:12:05', 68.00, 34.00, 34.00, 0.00, 562.00, 580.00, 50.00),
(61, 1, 4, '2025-10-28 23:22:20', 10.00, 5.00, 5.00, 0.00, 0.00, 10.00, 0.00),
(62, 1, 7, '2025-10-28 23:41:31', 92.00, 46.00, 46.00, 0.00, 1087.00, 979.00, 200.00),
(65, 1, 4, '2025-10-29 00:06:57', 136.00, 68.00, 68.00, 0.00, 552.00, 588.00, 100.00),
(66, 1, 7, '2025-10-29 00:26:05', 67.00, 33.50, 33.50, 0.00, 534.00, 501.00, 100.00),
(67, 1, 6, '2025-10-29 00:36:31', -44.00, -22.00, -22.00, 0.00, 544.00, 500.00, 0.00),
(68, 1, 7, '2025-10-29 00:44:09', 11.00, 5.50, 5.50, 0.00, 271.00, 232.00, 50.00),
(69, 1, 9, '2025-10-29 00:47:45', 195.00, 0.00, 195.00, 0.00, 1080.00, 1075.00, 200.00),
(70, 1, 4, '2025-10-29 01:00:52', -23.00, -11.50, -11.50, 0.00, 550.00, 527.00, 0.00),
(72, 1, 6, '2025-10-29 13:42:13', 165.00, 82.50, 82.50, 0.00, 547.00, 612.00, 100.00),
(73, 1, 6, '2025-10-29 17:12:44', 69.00, 34.50, 34.50, 0.00, 545.00, 514.00, 100.00),
(74, 1, 4, '2025-10-29 17:25:34', 92.00, 46.00, 46.00, 0.00, 550.00, 542.00, 100.00),
(75, 1, 4, '2025-10-29 20:36:43', 105.00, 52.50, 52.50, 0.00, 558.00, 563.00, 100.00),
(77, 1, 4, '2025-10-29 21:36:06', 68.00, 34.00, 34.00, 0.00, 846.00, 764.00, 150.00),
(80, 1, 4, '2025-10-29 22:51:59', 128.00, 64.00, 64.00, 0.00, 566.00, 594.00, 100.00),
(81, 1, 4, '2025-10-29 23:40:27', 62.00, 31.00, 31.00, 0.00, 554.00, 516.00, 100.00),
(83, 1, 9, '2025-10-30 02:31:53', 61.00, 0.00, 61.00, 0.00, 804.00, 715.00, 150.00),
(84, 1, 9, '2025-10-30 03:08:43', 119.00, 0.00, 119.00, 0.00, 1066.00, 985.00, 200.00),
(85, 1, 9, '2025-10-30 03:09:18', 126.00, 0.00, 126.00, 0.00, 804.00, 780.00, 150.00),
(86, 1, 9, '2025-10-30 03:50:46', 190.00, 0.00, 190.00, 0.00, 1076.00, 1066.00, 200.00),
(87, 1, 9, '2025-10-30 04:14:42', 137.00, 0.00, 137.00, 0.00, 809.00, 796.00, 150.00),
(88, 1, NULL, '2025-10-20 10:31:03', -6555.00, -1638.75, 0.00, 0.00, 6666.00, 66.00, 45.00),
(89, 1, 9, '2025-10-27 10:31:25', 122.00, 0.00, 122.00, 0.00, 1080.00, 1002.00, 200.00),
(90, 1, 9, '2025-10-27 10:32:15', 232.00, 0.00, 232.00, 0.00, 804.00, 886.00, 150.00),
(91, 1, 9, '2025-10-27 10:32:40', 47.00, 0.00, 47.00, 0.00, 1080.00, 927.00, 200.00),
(92, 1, 9, '2025-10-27 10:32:50', 73.00, 0.00, 73.00, 0.00, 1080.00, 953.00, 200.00),
(93, 1, 9, '2025-10-27 10:34:15', 52.00, 0.00, 52.00, 0.00, 812.00, 714.00, 150.00),
(94, 1, 9, '2025-10-27 10:34:26', 157.00, 0.00, 157.00, 0.00, 1095.00, 1052.00, 200.00),
(95, 1, 9, '2025-10-27 10:34:39', 46.00, 0.00, 46.00, 0.00, 1080.00, 926.00, 200.00),
(96, 1, 9, '2025-10-27 10:36:15', 2.00, 0.00, 2.00, 0.00, 789.00, 691.00, 100.00),
(98, 1, 9, '2025-10-28 10:36:43', 228.00, 0.00, 228.00, 0.00, 1080.00, 1158.00, 150.00),
(99, 1, 9, '2025-10-29 10:37:08', 222.00, 0.00, 222.00, 0.00, 1080.00, 1102.00, 200.00),
(100, 1, 7, '2025-10-27 10:39:30', 279.00, 139.50, 139.50, 0.00, 1080.00, 1159.00, 200.00),
(101, 1, 7, '2025-10-27 10:39:42', 198.00, 99.00, 99.00, 0.00, 789.00, 837.00, 150.00),
(102, 1, 7, '2025-10-27 10:39:58', 111.00, 55.50, 55.50, 0.00, 1064.00, 975.00, 200.00),
(103, 1, 7, '2025-10-27 10:40:14', 25.00, 12.50, 12.50, 0.00, 804.00, 679.00, 150.00),
(104, 1, 6, '2025-10-27 10:42:24', 81.00, 40.50, 40.50, 0.00, 542.00, 523.00, 100.00),
(105, 1, 6, '2025-10-27 10:42:34', 144.00, 72.00, 72.00, 0.00, 543.00, 587.00, 100.00),
(107, 1, 6, '2025-10-27 10:42:55', 34.00, 17.00, 17.00, 0.00, 544.00, 478.00, 100.00),
(108, 1, 6, '2025-10-27 10:43:14', 75.00, 37.50, 37.50, 0.00, 544.00, 519.00, 100.00),
(109, 1, 6, '2025-10-27 10:43:25', 172.00, 86.00, 86.00, 0.00, 536.00, 608.00, 100.00),
(112, 1, 6, '2025-10-27 10:44:19', 111.00, 55.50, 55.50, 0.00, 548.00, 559.00, 100.00),
(113, 1, 6, '2025-10-27 10:44:34', 42.00, 21.00, 21.00, 0.00, 534.00, 476.00, 100.00),
(114, 1, 6, '2025-10-27 10:44:45', 6.00, 3.00, 3.00, 0.00, 544.00, 450.00, 100.00),
(116, 1, 6, '2025-10-28 10:45:07', 76.00, 38.00, 38.00, 0.00, 546.00, 522.00, 100.00),
(119, 1, 6, '2025-10-30 13:50:22', 8.00, 4.00, 4.00, 0.00, 10.00, 18.00, 0.00),
(120, 1, 4, '2025-10-30 13:57:30', 90.00, 45.00, 45.00, 0.00, 555.00, 545.00, 100.00),
(121, 1, 6, '2025-10-30 14:41:34', 118.00, 59.00, 59.00, 0.00, 548.00, 566.00, 100.00),
(122, 1, 4, '2025-10-30 14:53:20', 100.00, 50.00, 50.00, 0.00, 562.00, 562.00, 100.00),
(123, 1, 4, '2025-10-30 15:46:35', 174.00, 87.00, 87.00, 0.00, 550.00, 624.00, 100.00),
(124, 1, 6, '2025-10-30 16:07:39', 53.00, 26.50, 26.50, 0.00, 549.00, 502.00, 100.00),
(125, 1, 4, '2025-10-30 18:00:34', 118.00, 59.00, 59.00, 0.00, 842.00, 810.00, 150.00),
(126, 1, 13, '2025-10-30 18:02:57', 269.00, 67.25, 134.50, 0.00, 1231.00, 1000.00, 500.00),
(127, 1, 6, '2025-10-27 18:14:19', -11.00, -5.50, -5.50, 0.00, 541.00, 430.00, 100.00),
(128, 1, 6, '2025-10-27 18:15:09', -3.00, -1.50, -1.50, 0.00, 542.00, 439.00, 100.00),
(129, 1, 6, '2025-10-27 18:15:20', -78.00, -39.00, -39.00, 0.00, 588.00, 410.00, 100.00),
(130, 1, 6, '2025-10-27 18:15:49', -8.00, -4.00, -4.00, 0.00, 541.00, 433.00, 100.00),
(132, 1, 7, '2025-10-30 18:40:42', 86.00, 43.00, 43.00, 0.00, 1096.00, 982.00, 200.00),
(135, 1, 4, '2025-10-30 20:05:25', 85.00, 42.50, 42.50, 0.00, 548.00, 533.00, 100.00),
(136, 1, 7, '2025-10-30 20:09:03', 4.00, 2.00, 2.00, 0.00, 828.00, 682.00, 150.00),
(137, 1, 9, '2025-10-30 20:26:10', 88.00, 0.00, 88.00, 0.00, 801.00, 739.00, 150.00),
(138, 1, 7, '2025-10-30 21:30:15', 272.00, 136.00, 136.00, 0.00, 1072.00, 1144.00, 200.00),
(139, 1, 6, '2025-10-30 22:08:48', 81.00, 40.50, 40.50, 0.00, 547.00, 528.00, 100.00),
(140, 1, 4, '2025-10-30 22:17:12', 116.00, 58.00, 58.00, 0.00, 560.00, 576.00, 100.00),
(141, 1, 6, '2025-10-30 22:53:46', 31.00, 15.50, 15.50, 0.00, 538.00, 469.00, 100.00),
(142, 1, 6, '2025-10-31 00:40:59', 1.00, 0.50, 0.50, 0.00, 60.00, 61.00, 0.00),
(148, 1, 4, '2025-10-31 01:39:18', 3.00, 1.50, 1.50, 0.00, 10.00, 13.00, 0.00),
(149, 1, 9, '2025-10-31 02:16:42', 154.00, 0.00, 154.00, 0.00, 804.00, 808.00, 150.00),
(150, 1, 7, '2025-10-31 02:22:21', 144.00, 72.00, 72.00, 0.00, 810.00, 804.00, 150.00),
(152, 1, 9, '2025-10-31 03:46:33', -202.00, 0.00, -202.00, 0.00, 1070.00, 668.00, 200.00),
(153, 1, 7, '2025-10-31 04:00:40', 14.00, 7.00, 7.00, 0.00, 1064.00, 878.00, 200.00),
(154, 1, 9, '2025-10-31 14:01:51', 59.00, 0.00, 59.00, 0.00, 1000.00, 859.00, 200.00),
(156, 1, 6, '2025-10-31 14:27:59', 36.00, 18.00, 18.00, 0.00, 593.00, 529.00, 100.00),
(157, 1, 4, '2025-10-31 14:58:24', 73.00, 36.50, 36.50, 0.00, 552.00, 525.00, 100.00),
(158, 1, 6, '2025-10-31 15:39:19', 130.00, 65.00, 65.00, 0.00, 541.00, 571.00, 100.00),
(159, 1, 6, '2025-10-31 17:31:16', 19.00, 9.50, 9.50, 0.00, 538.00, 457.00, 100.00),
(160, 1, 4, '2025-10-31 17:40:24', 134.00, 67.00, 67.00, 0.00, 831.00, 815.00, 150.00),
(161, 1, 6, '2025-10-31 18:16:39', 10.00, 5.00, 5.00, 0.00, 535.00, 445.00, 100.00),
(162, 1, 4, '2025-10-31 18:42:05', 77.00, 38.50, 38.50, 0.00, 550.00, 527.00, 100.00),
(163, 1, 4, '2025-10-31 19:21:20', 85.00, 42.50, 42.50, 0.00, 552.00, 537.00, 100.00),
(164, 1, 6, '2025-10-31 19:33:56', 53.00, 26.50, 26.50, 0.00, 538.00, 491.00, 100.00),
(165, 1, 6, '2025-10-31 20:30:01', -148.00, -74.00, -74.00, 0.00, 543.00, 345.00, 50.00),
(166, 1, 4, '2025-10-31 20:37:15', 85.00, 42.50, 42.50, 0.00, 550.00, 535.00, 100.00),
(168, 1, 9, '2025-10-31 20:57:56', -469.00, 0.00, -469.00, 0.00, 1000.00, 431.00, 100.00),
(169, 1, 4, '2025-10-31 21:30:12', 146.00, 73.00, 73.00, 0.00, 550.00, 596.00, 100.00),
(170, 1, 7, '2025-11-01 01:36:15', 139.00, 69.50, 69.50, 0.00, 1060.00, 999.00, 200.00),
(171, 1, 7, '2025-11-01 02:08:25', 92.00, 46.00, 46.00, 0.00, 532.00, 524.00, 100.00),
(172, 1, 7, '2025-11-01 03:58:28', 69.00, 34.50, 34.50, 0.00, 1060.00, 929.00, 200.00),
(173, 1, 9, '2025-11-01 05:40:00', 171.00, 0.00, 171.00, 0.00, 1000.00, 971.00, 200.00),
(174, 1, 9, '2025-11-01 06:21:51', 57.00, 0.00, 57.00, 0.00, 750.00, 657.00, 150.00),
(175, 1, 9, '2025-11-01 07:00:06', 122.00, 0.00, 122.00, 0.00, 750.00, 722.00, 150.00),
(176, 1, NULL, '2025-11-15 05:03:17', 100.00, 25.00, 37.50, 0.00, 500.00, 500.00, 100.00),
(177, 1, NULL, '2025-11-15 05:09:10', 200.00, 50.00, 75.00, 0.00, 500.00, 600.00, 100.00),
(181, 1, 18, '2025-11-15 05:48:38', 200.00, 50.00, 20.00, 130.00, 500.00, 600.00, 100.00),
(183, 5, 19, '2025-11-15 05:56:53', 1000.00, 400.00, 100.00, 500.00, 500.00, 500.00, 1000.00),
(184, 5, 19, '2025-11-15 06:00:25', 1000.00, 300.00, 100.00, 600.00, 500.00, 500.00, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `saved_reports`
--

CREATE TABLE `saved_reports` (
  `id_report_salvo` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `nome_relatorio` varchar(255) NOT NULL,
  `id_salvo_por` int(11) DEFAULT NULL,
  `nome_salvo_por` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `filtros` text DEFAULT NULL COMMENT 'Armazena os filtros (datas, IDs de usuário) como JSON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_reports`
--

INSERT INTO `saved_reports` (`id_report_salvo`, `org_id`, `nome_relatorio`, `id_salvo_por`, `nome_salvo_por`, `data_criacao`, `filtros`) VALUES
(2, 1, 'sacola', 11, 'doidin', '2025-10-27 20:47:13', '{\"date_start\":\"2025-10-27\",\"date_end\":\"2025-10-27\",\"user_ids\":[\"12\"],\"admin_id\":\"\"}'),
(3, 1, 'Relatório 27/10', 6, 'DOUGLAS DALPICCOL', '2025-10-28 01:41:59', '{\"date_start\":\"2025-10-27\",\"date_end\":\"2025-10-27\",\"user_ids\":[],\"admin_id\":\"\"}'),
(4, 1, 'Relatório 28/10', 6, 'DOUGLAS DALPICCOL', '2025-10-29 04:07:36', '{\"date_start\":\"2025-10-28\",\"date_end\":\"2025-10-28\",\"user_ids\":[],\"admin_id\":\"\"}'),
(5, 1, 'Relatório 29/10', 6, 'DOUGLAS DALPICCOL', '2025-10-30 02:06:34', '{\"date_start\":\"2025-10-29\",\"date_end\":\"2025-10-29\",\"user_ids\":[],\"admin_id\":\"\"}'),
(7, 1, 'Teste dia 31', 6, 'DOUGLAS DALPICCOL', '2025-10-31 14:08:07', '{\"date_start\":\"2025-10-30\",\"date_end\":\"2025-10-30\",\"user_ids\":[\"7\"],\"admin_id\":\"\"}');

-- --------------------------------------------------------

--
-- Table structure for table `sub_administradores`
--

CREATE TABLE `sub_administradores` (
  `id_sub_adm` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `role` enum('admin','sub_adm','super_adm','platform_owner') NOT NULL DEFAULT 'sub_adm',
  `percentual_comissao` decimal(5,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `parent_admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_administradores`
--

INSERT INTO `sub_administradores` (`id_sub_adm`, `org_id`, `nome`, `email`, `username`, `senha`, `role`, `percentual_comissao`, `saldo`, `parent_admin_id`) VALUES
(6, 1, 'DOUGLAS DALPICCOL', 'douglasdalpiccol04@gmail.com', 'user6', '9657424pdP!', 'platform_owner', 50.00, NULL, NULL),
(7, 1, 'masterchief', 'masterchief@masterchief.com', 'masterchief', 'masterchief', 'platform_owner', NULL, NULL, NULL),
(8, 1, 'leonardo', 'leonardo7TRAVENSOLLI@gmail.com', 'leonardo7TRAVENSOLLI@gmail.com', 'mZrT7hgTPJYaPKC', 'platform_owner', NULL, NULL, NULL),
(9, 1, 'Doda CPA', 'zkillersop@gmail.com', 'coeedoda', '9657424pdP!', 'platform_owner', NULL, NULL, NULL),
(10, 1, 'leo adm', 'leotravensolli@icloud.com', 'leotrvs', 'mZrT7hgTPJYaPKC', 'platform_owner', 50.00, NULL, NULL),
(11, 1, 'doidin', 'doidin@gmail.com', 'doidingerencia', 'xvF53nUPe8No', 'platform_owner', 25.00, NULL, NULL),
(14, 1, 'Douglas', 'dodadalpiccol@icloud.com', 'douglas1', '6mW5FqG@xlTe', 'admin', 50.00, NULL, NULL),
(15, 1, 'Doda', 'coeedoda@gmail.com', 'doda1', 'iXIbY1AnklHM', 'super_adm', 50.00, NULL, NULL),
(17, 1, 'testepaulista', 'testepaulista@gmail.com', 'paupau', '8AbS1Q2zZG#g', 'sub_adm', 10.00, NULL, NULL),
(18, 5, 'Doda CPA7', 'dodacpa@gmail.com', 'dodacpa75', '9657424pdP!', 'admin', 50.00, NULL, NULL),
(19, 5, 'Paulista', 'Paulista@gmail.com', 'paulista7', 'paulista7', 'sub_adm', 10.00, NULL, 18);

-- --------------------------------------------------------

--
-- Table structure for table `transacoes`
--

CREATE TABLE `transacoes` (
  `id_transacao` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo_transacao` enum('deposito','saque','bau') DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transacoes`
--

INSERT INTO `transacoes` (`id_transacao`, `org_id`, `id_usuario`, `tipo_transacao`, `valor`, `data`) VALUES
(7, 1, 4, 'deposito', 835.00, '2025-10-27 17:32:01'),
(8, 1, 4, 'saque', 805.00, '2025-10-27 17:32:01'),
(9, 1, 4, 'bau', 150.00, '2025-10-27 17:32:01'),
(10, 1, 4, 'deposito', 550.00, '2025-10-27 17:32:23'),
(11, 1, 4, 'saque', 481.00, '2025-10-27 17:32:23'),
(12, 1, 4, 'bau', 100.00, '2025-10-27 17:32:23'),
(13, 1, 4, 'deposito', 545.00, '2025-10-27 17:32:37'),
(14, 1, 4, 'saque', 493.00, '2025-10-27 17:32:37'),
(15, 1, 4, 'bau', 100.00, '2025-10-27 17:32:37'),
(16, 1, 4, 'deposito', 559.00, '2025-10-27 17:32:48'),
(17, 1, 4, 'saque', 510.00, '2025-10-27 17:32:48'),
(18, 1, 4, 'bau', 100.00, '2025-10-27 17:32:48'),
(19, 1, 5, 'deposito', 550.00, '2025-10-27 17:35:36'),
(20, 1, 5, 'saque', 490.00, '2025-10-27 17:35:36'),
(21, 1, 5, 'bau', 100.00, '2025-10-27 17:35:36'),
(22, 1, 6, 'deposito', 542.00, '2025-10-27 17:45:02'),
(23, 1, 6, 'saque', 523.00, '2025-10-27 17:45:02'),
(24, 1, 6, 'bau', 100.00, '2025-10-27 17:45:02'),
(25, 1, 6, 'deposito', 543.00, '2025-10-27 17:45:44'),
(26, 1, 6, 'saque', 587.00, '2025-10-27 17:45:44'),
(27, 1, 6, 'bau', 100.00, '2025-10-27 17:45:44'),
(28, 1, 6, 'deposito', 541.00, '2025-10-27 17:46:27'),
(29, 1, 6, 'saque', 430.00, '2025-10-27 17:46:27'),
(30, 1, 6, 'bau', 100.00, '2025-10-27 17:46:27'),
(31, 1, 6, 'deposito', 544.00, '2025-10-27 17:47:02'),
(32, 1, 6, 'saque', 478.00, '2025-10-27 17:47:02'),
(33, 1, 6, 'bau', 100.00, '2025-10-27 17:47:02'),
(34, 1, 6, 'deposito', 544.00, '2025-10-27 17:47:32'),
(35, 1, 6, 'saque', 519.00, '2025-10-27 17:47:32'),
(36, 1, 6, 'bau', 100.00, '2025-10-27 17:47:32'),
(37, 1, 5, 'deposito', 520.00, '2025-10-27 17:48:06'),
(38, 1, 5, 'saque', 560.00, '2025-10-27 17:48:06'),
(39, 1, 5, 'bau', 100.00, '2025-10-27 17:48:06'),
(40, 1, 6, 'deposito', 536.00, '2025-10-27 17:49:52'),
(41, 1, 6, 'saque', 608.00, '2025-10-27 17:49:52'),
(42, 1, 6, 'bau', 100.00, '2025-10-27 17:49:52'),
(43, 1, 8, 'deposito', 538.00, '2025-10-27 17:50:03'),
(44, 1, 8, 'saque', 486.00, '2025-10-27 17:50:03'),
(45, 1, 8, 'bau', 100.00, '2025-10-27 17:50:03'),
(46, 1, 8, 'deposito', 540.00, '2025-10-27 17:50:21'),
(47, 1, 8, 'saque', 558.00, '2025-10-27 17:50:21'),
(48, 1, 8, 'bau', 100.00, '2025-10-27 17:50:21'),
(49, 1, 8, 'deposito', 540.00, '2025-10-27 17:50:42'),
(50, 1, 8, 'saque', 420.00, '2025-10-27 17:50:42'),
(51, 1, 8, 'bau', 100.00, '2025-10-27 17:50:42'),
(52, 1, 8, 'deposito', 548.00, '2025-10-27 17:51:02'),
(53, 1, 8, 'saque', 569.00, '2025-10-27 17:51:02'),
(54, 1, 8, 'bau', 100.00, '2025-10-27 17:51:02'),
(55, 1, 8, 'deposito', 548.00, '2025-10-27 17:51:22'),
(56, 1, 8, 'saque', 506.00, '2025-10-27 17:51:22'),
(57, 1, 8, 'bau', 100.00, '2025-10-27 17:51:22'),
(58, 1, 8, 'deposito', 552.00, '2025-10-27 17:51:48'),
(59, 1, 8, 'saque', 539.00, '2025-10-27 17:51:48'),
(60, 1, 8, 'bau', 100.00, '2025-10-27 17:51:48'),
(61, 1, 9, 'deposito', 1080.00, '2025-10-27 18:06:31'),
(62, 1, 9, 'saque', 1002.00, '2025-10-27 18:06:31'),
(63, 1, 9, 'bau', 200.00, '2025-10-27 18:06:31'),
(64, 1, 9, 'deposito', 805.00, '2025-10-27 18:06:39'),
(65, 1, 9, 'saque', 887.00, '2025-10-27 18:06:39'),
(66, 1, 9, 'bau', 150.00, '2025-10-27 18:06:39'),
(67, 1, 9, 'deposito', 1080.00, '2025-10-27 18:06:49'),
(68, 1, 9, 'saque', 927.00, '2025-10-27 18:06:49'),
(69, 1, 9, 'bau', 200.00, '2025-10-27 18:06:49'),
(70, 1, 9, 'deposito', 1080.00, '2025-10-27 18:06:55'),
(71, 1, 9, 'saque', 953.00, '2025-10-27 18:06:55'),
(72, 1, 9, 'bau', 200.00, '2025-10-27 18:06:55'),
(73, 1, 6, 'deposito', 542.00, '2025-10-27 18:14:02'),
(74, 1, 6, 'saque', 439.00, '2025-10-27 18:14:02'),
(75, 1, 6, 'bau', 100.00, '2025-10-27 18:14:02'),
(76, 1, 4, 'deposito', 588.00, '2025-10-27 18:14:22'),
(77, 1, 4, 'saque', 410.00, '2025-10-27 18:14:22'),
(78, 1, 4, 'bau', 100.00, '2025-10-27 18:14:22'),
(79, 1, 6, 'deposito', 588.00, '2025-10-27 18:15:14'),
(80, 1, 6, 'saque', 410.00, '2025-10-27 18:15:14'),
(81, 1, 6, 'bau', 100.00, '2025-10-27 18:15:14'),
(82, 1, 4, 'deposito', 829.00, '2025-10-27 18:30:49'),
(83, 1, 4, 'saque', 890.00, '2025-10-27 18:30:49'),
(84, 1, 4, 'bau', 150.00, '2025-10-27 18:30:49'),
(85, 1, 7, 'deposito', 1080.00, '2025-10-27 18:33:12'),
(86, 1, 7, 'saque', 1159.00, '2025-10-27 18:33:12'),
(87, 1, 7, 'bau', 200.00, '2025-10-27 18:33:12'),
(88, 1, 7, 'deposito', 789.00, '2025-10-27 18:35:14'),
(89, 1, 7, 'saque', 837.00, '2025-10-27 18:35:14'),
(90, 1, 7, 'bau', 150.00, '2025-10-27 18:35:14'),
(91, 1, 6, 'deposito', 548.00, '2025-10-27 18:39:26'),
(92, 1, 6, 'saque', 559.00, '2025-10-27 18:39:26'),
(93, 1, 6, 'bau', 100.00, '2025-10-27 18:39:26'),
(94, 1, 9, 'deposito', 1080.00, '2025-10-27 18:52:30'),
(95, 1, 9, 'saque', 1002.00, '2025-10-27 18:52:30'),
(96, 1, 9, 'bau', 200.00, '2025-10-27 18:52:30'),
(97, 1, 9, 'deposito', 805.00, '2025-10-27 18:54:04'),
(98, 1, 9, 'saque', 887.00, '2025-10-27 18:54:04'),
(99, 1, 9, 'bau', 150.00, '2025-10-27 18:54:04'),
(100, 1, 9, 'deposito', 1080.00, '2025-10-27 18:54:42'),
(101, 1, 9, 'saque', 927.00, '2025-10-27 18:54:42'),
(102, 1, 9, 'bau', 200.00, '2025-10-27 18:54:42'),
(103, 1, 9, 'deposito', 1080.00, '2025-10-27 18:54:50'),
(104, 1, 9, 'saque', 953.00, '2025-10-27 18:54:50'),
(105, 1, 9, 'bau', 200.00, '2025-10-27 18:54:50'),
(106, 1, 4, 'deposito', 547.00, '2025-10-27 19:30:48'),
(107, 1, 4, 'saque', 517.00, '2025-10-27 19:30:48'),
(108, 1, 4, 'bau', 100.00, '2025-10-27 19:30:48'),
(109, 1, NULL, 'deposito', 545.00, '2025-10-27 19:45:31'),
(110, 1, NULL, 'saque', 520.00, '2025-10-27 19:45:31'),
(111, 1, NULL, 'bau', 100.00, '2025-10-27 19:45:31'),
(112, 1, 9, 'deposito', 812.00, '2025-10-27 19:49:24'),
(113, 1, 9, 'saque', 714.00, '2025-10-27 19:49:24'),
(114, 1, 9, 'bau', 150.00, '2025-10-27 19:49:24'),
(115, 1, 6, 'deposito', 534.00, '2025-10-27 19:58:32'),
(116, 1, 6, 'saque', 476.00, '2025-10-27 19:58:32'),
(117, 1, 6, 'bau', 100.00, '2025-10-27 19:58:32'),
(118, 1, 7, 'deposito', 1064.00, '2025-10-27 20:39:54'),
(119, 1, 7, 'saque', 975.00, '2025-10-27 20:39:54'),
(120, 1, 7, 'bau', 200.00, '2025-10-27 20:39:54'),
(121, 1, NULL, 'deposito', 1100.00, '2025-10-27 20:40:01'),
(122, 1, NULL, 'saque', 1050.00, '2025-10-27 20:40:01'),
(123, 1, NULL, 'bau', 200.00, '2025-10-27 20:40:01'),
(124, 1, 12, 'deposito', 750.00, '2025-10-27 20:45:27'),
(125, 1, 12, 'saque', 700.00, '2025-10-27 20:45:27'),
(126, 1, 12, 'bau', 150.00, '2025-10-27 20:45:27'),
(127, 1, 12, 'deposito', 540.00, '2025-10-27 20:46:22'),
(128, 1, 12, 'saque', 500.00, '2025-10-27 20:46:22'),
(129, 1, 12, 'bau', 100.00, '2025-10-27 20:46:22'),
(130, 1, 4, 'deposito', 547.00, '2025-10-27 21:19:01'),
(131, 1, 4, 'saque', 496.00, '2025-10-27 21:19:01'),
(132, 1, 4, 'bau', 100.00, '2025-10-27 21:19:01'),
(133, 1, 6, 'deposito', 544.00, '2025-10-27 21:38:09'),
(134, 1, 6, 'saque', 450.00, '2025-10-27 21:38:09'),
(135, 1, 6, 'bau', 100.00, '2025-10-27 21:38:09'),
(136, 1, 9, 'deposito', 1095.00, '2025-10-27 22:11:31'),
(137, 1, 9, 'saque', 1052.00, '2025-10-27 22:11:31'),
(138, 1, 9, 'bau', 200.00, '2025-10-27 22:11:31'),
(139, 1, 4, 'deposito', 822.00, '2025-10-27 22:23:29'),
(140, 1, 4, 'saque', 708.00, '2025-10-27 22:23:29'),
(141, 1, 4, 'bau', 150.00, '2025-10-27 22:23:29'),
(142, 1, 7, 'deposito', 804.00, '2025-10-27 22:35:04'),
(143, 1, 7, 'saque', 679.00, '2025-10-27 22:35:04'),
(144, 1, 7, 'bau', 150.00, '2025-10-27 22:35:04'),
(145, 1, 6, 'deposito', 541.00, '2025-10-27 22:49:40'),
(146, 1, 6, 'saque', 433.00, '2025-10-27 22:49:40'),
(147, 1, 6, 'bau', 100.00, '2025-10-27 22:49:40'),
(148, 1, 9, 'deposito', 1080.00, '2025-10-27 22:51:57'),
(149, 1, 9, 'saque', 926.00, '2025-10-27 22:51:57'),
(150, 1, 9, 'bau', 200.00, '2025-10-27 22:51:57'),
(151, 1, 7, 'deposito', 1080.00, '2025-10-28 16:31:47'),
(152, 1, 7, 'saque', 1233.00, '2025-10-28 16:31:47'),
(153, 1, 7, 'bau', 200.00, '2025-10-28 16:31:47'),
(154, 1, 7, 'deposito', 1076.00, '2025-10-28 18:07:03'),
(155, 1, 7, 'saque', 999.00, '2025-10-28 18:07:03'),
(156, 1, 7, 'bau', 200.00, '2025-10-28 18:07:03'),
(157, 1, 9, 'deposito', 806.00, '2025-10-28 20:45:06'),
(158, 1, 9, 'saque', 700.00, '2025-10-28 20:45:06'),
(159, 1, 9, 'bau', 150.00, '2025-10-28 20:45:06'),
(160, 1, 4, 'deposito', 816.00, '2025-10-28 21:05:05'),
(161, 1, 4, 'saque', 700.00, '2025-10-28 21:05:05'),
(162, 1, 4, 'bau', 150.00, '2025-10-28 21:05:05'),
(163, 1, 9, 'deposito', 810.00, '2025-10-28 21:19:55'),
(164, 1, 9, 'saque', 834.00, '2025-10-28 21:19:55'),
(165, 1, 9, 'bau', 150.00, '2025-10-28 21:19:55'),
(166, 1, 4, 'deposito', 551.00, '2025-10-28 21:41:17'),
(167, 1, 4, 'saque', 447.00, '2025-10-28 21:41:17'),
(168, 1, 4, 'bau', 100.00, '2025-10-28 21:41:17'),
(169, 1, 9, 'deposito', 789.00, '2025-10-28 21:57:26'),
(170, 1, 9, 'saque', 691.00, '2025-10-28 21:57:26'),
(171, 1, 9, 'bau', 150.00, '2025-10-28 21:57:26'),
(172, 1, 6, 'deposito', 546.00, '2025-10-28 22:13:08'),
(173, 1, 6, 'saque', 522.00, '2025-10-28 22:13:08'),
(174, 1, 6, 'bau', 100.00, '2025-10-28 22:13:08'),
(175, 1, 4, 'deposito', 559.00, '2025-10-28 22:31:46'),
(176, 1, 4, 'saque', 529.00, '2025-10-28 22:31:46'),
(177, 1, 4, 'bau', 100.00, '2025-10-28 22:31:46'),
(178, 1, 4, 'deposito', 562.00, '2025-10-28 23:12:05'),
(179, 1, 4, 'saque', 580.00, '2025-10-28 23:12:05'),
(180, 1, 4, 'bau', 100.00, '2025-10-28 23:12:05'),
(181, 1, 4, 'deposito', 0.00, '2025-10-28 23:22:20'),
(182, 1, 4, 'saque', 10.00, '2025-10-28 23:22:20'),
(183, 1, 4, 'bau', 0.00, '2025-10-28 23:22:20'),
(184, 1, 7, 'deposito', 1087.00, '2025-10-28 23:41:31'),
(185, 1, 7, 'saque', 979.00, '2025-10-28 23:41:31'),
(186, 1, 7, 'bau', 200.00, '2025-10-28 23:41:31'),
(187, 1, 9, 'deposito', 1080.00, '2025-10-28 23:44:50'),
(188, 1, 9, 'saque', 1158.00, '2025-10-28 23:44:50'),
(189, 1, 9, 'bau', 200.00, '2025-10-28 23:44:50'),
(190, 1, 4, 'deposito', 0.00, '2025-10-28 23:51:42'),
(191, 1, 4, 'saque', 0.00, '2025-10-28 23:51:42'),
(192, 1, 4, 'bau', -50.00, '2025-10-28 23:51:42'),
(193, 1, 4, 'deposito', 552.00, '2025-10-29 00:06:57'),
(194, 1, 4, 'saque', 588.00, '2025-10-29 00:06:57'),
(195, 1, 4, 'bau', 100.00, '2025-10-29 00:06:57'),
(196, 1, 7, 'deposito', 534.00, '2025-10-29 00:26:05'),
(197, 1, 7, 'saque', 501.00, '2025-10-29 00:26:05'),
(198, 1, 7, 'bau', 100.00, '2025-10-29 00:26:05'),
(199, 1, 6, 'deposito', 544.00, '2025-10-29 00:36:31'),
(200, 1, 6, 'saque', 500.00, '2025-10-29 00:36:31'),
(201, 1, 6, 'bau', 100.00, '2025-10-29 00:36:31'),
(202, 1, 7, 'deposito', 271.00, '2025-10-29 00:44:09'),
(203, 1, 7, 'saque', 232.00, '2025-10-29 00:44:09'),
(204, 1, 7, 'bau', 50.00, '2025-10-29 00:44:09'),
(205, 1, 9, 'deposito', 1080.00, '2025-10-29 00:47:45'),
(206, 1, 9, 'saque', 1075.00, '2025-10-29 00:47:45'),
(207, 1, 9, 'bau', 200.00, '2025-10-29 00:47:45'),
(208, 1, 4, 'deposito', 550.00, '2025-10-29 01:00:52'),
(209, 1, 4, 'saque', 527.00, '2025-10-29 01:00:52'),
(210, 1, 4, 'bau', 100.00, '2025-10-29 01:00:52'),
(211, 1, 4, 'deposito', 0.00, '2025-10-29 01:05:02'),
(212, 1, 4, 'saque', -100.00, '2025-10-29 01:05:02'),
(213, 1, 4, 'bau', 0.00, '2025-10-29 01:05:02'),
(214, 1, 6, 'deposito', 537.00, '2025-10-29 13:42:13'),
(215, 1, 6, 'saque', 600.00, '2025-10-29 13:42:13'),
(216, 1, 6, 'bau', 100.00, '2025-10-29 13:42:13'),
(217, 1, 6, 'deposito', 545.00, '2025-10-29 17:12:44'),
(218, 1, 6, 'saque', 514.00, '2025-10-29 17:12:44'),
(219, 1, 6, 'bau', 100.00, '2025-10-29 17:12:44'),
(220, 1, 4, 'deposito', 550.00, '2025-10-29 17:25:34'),
(221, 1, 4, 'saque', 542.00, '2025-10-29 17:25:34'),
(222, 1, 4, 'bau', 100.00, '2025-10-29 17:25:34'),
(223, 1, 4, 'deposito', 558.00, '2025-10-29 20:36:43'),
(224, 1, 4, 'saque', 563.00, '2025-10-29 20:36:43'),
(225, 1, 4, 'bau', 100.00, '2025-10-29 20:36:43'),
(226, 1, 9, 'deposito', 1080.00, '2025-10-29 21:16:13'),
(227, 1, 9, 'saque', 1102.00, '2025-10-29 21:16:13'),
(228, 1, 9, 'bau', 200.00, '2025-10-29 21:16:13'),
(229, 1, 4, 'deposito', 846.00, '2025-10-29 21:36:06'),
(230, 1, 4, 'saque', 754.00, '2025-10-29 21:36:06'),
(231, 1, 4, 'bau', 150.00, '2025-10-29 21:36:06'),
(232, 1, 4, 'deposito', 846.00, '2025-10-29 21:36:45'),
(233, 1, 4, 'saque', 754.00, '2025-10-29 21:36:45'),
(234, 1, 4, 'bau', 150.00, '2025-10-29 21:36:45'),
(235, 1, 4, 'deposito', 0.00, '2025-10-29 21:44:31'),
(236, 1, 4, 'saque', 10.00, '2025-10-29 21:44:31'),
(237, 1, 4, 'bau', 0.00, '2025-10-29 21:44:31'),
(238, 1, 4, 'deposito', 566.00, '2025-10-29 22:51:59'),
(239, 1, 4, 'saque', 594.00, '2025-10-29 22:51:59'),
(240, 1, 4, 'bau', 100.00, '2025-10-29 22:51:59'),
(241, 1, 4, 'deposito', 554.00, '2025-10-29 23:40:27'),
(242, 1, 4, 'saque', 516.00, '2025-10-29 23:40:27'),
(243, 1, 4, 'bau', 100.00, '2025-10-29 23:40:27'),
(244, 1, 9, 'deposito', 804.00, '2025-10-30 00:14:08'),
(245, 1, 9, 'saque', 780.00, '2025-10-30 00:14:08'),
(246, 1, 9, 'bau', 150.00, '2025-10-30 00:14:08'),
(247, 1, 9, 'deposito', 804.00, '2025-10-30 02:31:53'),
(248, 1, 9, 'saque', 715.00, '2025-10-30 02:31:53'),
(249, 1, 9, 'bau', 150.00, '2025-10-30 02:31:53'),
(250, 1, 9, 'deposito', 1066.00, '2025-10-30 03:08:43'),
(251, 1, 9, 'saque', 985.00, '2025-10-30 03:08:43'),
(252, 1, 9, 'bau', 200.00, '2025-10-30 03:08:43'),
(253, 1, 9, 'deposito', 804.00, '2025-10-30 03:09:18'),
(254, 1, 9, 'saque', 780.00, '2025-10-30 03:09:18'),
(255, 1, 9, 'bau', 150.00, '2025-10-30 03:09:18'),
(256, 1, 9, 'deposito', 1076.00, '2025-10-30 03:50:46'),
(257, 1, 9, 'saque', 1066.00, '2025-10-30 03:50:46'),
(258, 1, 9, 'bau', 200.00, '2025-10-30 03:50:46'),
(259, 1, 9, 'deposito', 809.00, '2025-10-30 04:14:42'),
(260, 1, 9, 'saque', 796.00, '2025-10-30 04:14:42'),
(261, 1, 9, 'bau', 150.00, '2025-10-30 04:14:42'),
(262, 1, NULL, 'deposito', 6666.00, '2025-10-20 10:31:03'),
(263, 1, NULL, 'saque', 66.00, '2025-10-20 10:31:03'),
(264, 1, NULL, 'bau', 45.00, '2025-10-20 10:31:03'),
(265, 1, 9, 'deposito', 1080.00, '2025-10-27 10:31:25'),
(266, 1, 9, 'saque', 1002.00, '2025-10-27 10:31:25'),
(267, 1, 9, 'bau', 200.00, '2025-10-27 10:31:25'),
(268, 1, 9, 'deposito', 804.00, '2025-10-27 10:32:15'),
(269, 1, 9, 'saque', 886.00, '2025-10-27 10:32:15'),
(270, 1, 9, 'bau', 150.00, '2025-10-27 10:32:15'),
(271, 1, 9, 'deposito', 1080.00, '2025-10-27 10:32:40'),
(272, 1, 9, 'saque', 927.00, '2025-10-27 10:32:40'),
(273, 1, 9, 'bau', 200.00, '2025-10-27 10:32:40'),
(274, 1, 9, 'deposito', 1080.00, '2025-10-27 10:32:50'),
(275, 1, 9, 'saque', 953.00, '2025-10-27 10:32:50'),
(276, 1, 9, 'bau', 200.00, '2025-10-27 10:32:50'),
(277, 1, 9, 'deposito', 812.00, '2025-10-27 10:34:15'),
(278, 1, 9, 'saque', 714.00, '2025-10-27 10:34:15'),
(279, 1, 9, 'bau', 150.00, '2025-10-27 10:34:15'),
(280, 1, 9, 'deposito', 1095.00, '2025-10-27 10:34:26'),
(281, 1, 9, 'saque', 1052.00, '2025-10-27 10:34:26'),
(282, 1, 9, 'bau', 200.00, '2025-10-27 10:34:26'),
(283, 1, 9, 'deposito', 1080.00, '2025-10-27 10:34:39'),
(284, 1, 9, 'saque', 926.00, '2025-10-27 10:34:39'),
(285, 1, 9, 'bau', 200.00, '2025-10-27 10:34:39'),
(286, 1, 9, 'deposito', 789.00, '2025-10-27 10:36:15'),
(287, 1, 9, 'saque', 691.00, '2025-10-27 10:36:15'),
(288, 1, 9, 'bau', 100.00, '2025-10-27 10:36:15'),
(289, 1, 9, 'deposito', 1080.00, '2025-10-27 10:36:29'),
(290, 1, 9, 'saque', 1158.00, '2025-10-27 10:36:29'),
(291, 1, 9, 'bau', 150.00, '2025-10-27 10:36:29'),
(292, 1, 9, 'deposito', 1080.00, '2025-10-28 10:36:43'),
(293, 1, 9, 'saque', 1158.00, '2025-10-28 10:36:43'),
(294, 1, 9, 'bau', 150.00, '2025-10-28 10:36:43'),
(295, 1, 9, 'deposito', 1080.00, '2025-10-29 10:37:08'),
(296, 1, 9, 'saque', 1102.00, '2025-10-29 10:37:08'),
(297, 1, 9, 'bau', 200.00, '2025-10-29 10:37:08'),
(298, 1, 7, 'deposito', 1080.00, '2025-10-27 10:39:30'),
(299, 1, 7, 'saque', 1159.00, '2025-10-27 10:39:30'),
(300, 1, 7, 'bau', 200.00, '2025-10-27 10:39:30'),
(301, 1, 7, 'deposito', 789.00, '2025-10-27 10:39:42'),
(302, 1, 7, 'saque', 837.00, '2025-10-27 10:39:42'),
(303, 1, 7, 'bau', 150.00, '2025-10-27 10:39:42'),
(304, 1, 7, 'deposito', 1064.00, '2025-10-27 10:39:58'),
(305, 1, 7, 'saque', 975.00, '2025-10-27 10:39:58'),
(306, 1, 7, 'bau', 200.00, '2025-10-27 10:39:58'),
(307, 1, 7, 'deposito', 804.00, '2025-10-27 10:40:14'),
(308, 1, 7, 'saque', 679.00, '2025-10-27 10:40:14'),
(309, 1, 7, 'bau', 150.00, '2025-10-27 10:40:14'),
(310, 1, 6, 'deposito', 542.00, '2025-10-27 10:42:24'),
(311, 1, 6, 'saque', 523.00, '2025-10-27 10:42:24'),
(312, 1, 6, 'bau', 100.00, '2025-10-27 10:42:24'),
(313, 1, 6, 'deposito', 543.00, '2025-10-27 10:42:34'),
(314, 1, 6, 'saque', 587.00, '2025-10-27 10:42:34'),
(315, 1, 6, 'bau', 100.00, '2025-10-27 10:42:34'),
(316, 1, 6, 'deposito', 541.00, '2025-10-27 10:42:44'),
(317, 1, 6, 'saque', 430.00, '2025-10-27 10:42:44'),
(318, 1, 6, 'bau', 100.00, '2025-10-27 10:42:44'),
(319, 1, 6, 'deposito', 544.00, '2025-10-27 10:42:55'),
(320, 1, 6, 'saque', 478.00, '2025-10-27 10:42:55'),
(321, 1, 6, 'bau', 100.00, '2025-10-27 10:42:55'),
(322, 1, 6, 'deposito', 544.00, '2025-10-27 10:43:14'),
(323, 1, 6, 'saque', 519.00, '2025-10-27 10:43:14'),
(324, 1, 6, 'bau', 100.00, '2025-10-27 10:43:14'),
(325, 1, 6, 'deposito', 536.00, '2025-10-27 10:43:25'),
(326, 1, 6, 'saque', 608.00, '2025-10-27 10:43:25'),
(327, 1, 6, 'bau', 100.00, '2025-10-27 10:43:25'),
(328, 1, 6, 'deposito', 542.00, '2025-10-27 10:43:42'),
(329, 1, 6, 'saque', 439.00, '2025-10-27 10:43:42'),
(330, 1, 6, 'bau', 100.00, '2025-10-27 10:43:42'),
(331, 1, 6, 'deposito', 588.00, '2025-10-27 10:44:08'),
(332, 1, 6, 'saque', 410.00, '2025-10-27 10:44:08'),
(333, 1, 6, 'bau', 100.00, '2025-10-27 10:44:08'),
(334, 1, 6, 'deposito', 548.00, '2025-10-27 10:44:19'),
(335, 1, 6, 'saque', 559.00, '2025-10-27 10:44:19'),
(336, 1, 6, 'bau', 100.00, '2025-10-27 10:44:19'),
(337, 1, 6, 'deposito', 534.00, '2025-10-27 10:44:34'),
(338, 1, 6, 'saque', 476.00, '2025-10-27 10:44:34'),
(339, 1, 6, 'bau', 100.00, '2025-10-27 10:44:34'),
(340, 1, 6, 'deposito', 544.00, '2025-10-27 10:44:45'),
(341, 1, 6, 'saque', 450.00, '2025-10-27 10:44:45'),
(342, 1, 6, 'bau', 100.00, '2025-10-27 10:44:45'),
(343, 1, 6, 'deposito', 541.00, '2025-10-27 10:44:56'),
(344, 1, 6, 'saque', 433.00, '2025-10-27 10:44:56'),
(345, 1, 6, 'bau', 100.00, '2025-10-27 10:44:56'),
(346, 1, 6, 'deposito', 546.00, '2025-10-28 10:45:07'),
(347, 1, 6, 'saque', 522.00, '2025-10-28 10:45:07'),
(348, 1, 6, 'bau', 100.00, '2025-10-28 10:45:07'),
(349, 1, 6, 'deposito', 544.00, '2025-10-28 10:45:23'),
(350, 1, 6, 'saque', 500.00, '2025-10-28 10:45:23'),
(351, 1, 6, 'bau', 0.00, '2025-10-28 10:45:23'),
(352, 1, 6, 'deposito', 544.00, '2025-10-29 10:45:39'),
(353, 1, 6, 'saque', 500.00, '2025-10-29 10:45:39'),
(354, 1, 6, 'bau', 0.00, '2025-10-29 10:45:39'),
(355, 1, 6, 'deposito', 10.00, '2025-10-30 13:50:22'),
(356, 1, 6, 'saque', 18.00, '2025-10-30 13:50:22'),
(357, 1, 6, 'bau', 0.00, '2025-10-30 13:50:22'),
(358, 1, 4, 'deposito', 555.00, '2025-10-30 13:57:30'),
(359, 1, 4, 'saque', 545.00, '2025-10-30 13:57:30'),
(360, 1, 4, 'bau', 100.00, '2025-10-30 13:57:30'),
(361, 1, 6, 'deposito', 548.00, '2025-10-30 14:41:34'),
(362, 1, 6, 'saque', 566.00, '2025-10-30 14:41:34'),
(363, 1, 6, 'bau', 100.00, '2025-10-30 14:41:34'),
(364, 1, 4, 'deposito', 562.00, '2025-10-30 14:53:20'),
(365, 1, 4, 'saque', 562.00, '2025-10-30 14:53:20'),
(366, 1, 4, 'bau', 100.00, '2025-10-30 14:53:20'),
(367, 1, 4, 'deposito', 550.00, '2025-10-30 15:46:35'),
(368, 1, 4, 'saque', 624.00, '2025-10-30 15:46:35'),
(369, 1, 4, 'bau', 100.00, '2025-10-30 15:46:35'),
(370, 1, 6, 'deposito', 549.00, '2025-10-30 16:07:39'),
(371, 1, 6, 'saque', 502.00, '2025-10-30 16:07:39'),
(372, 1, 6, 'bau', 100.00, '2025-10-30 16:07:39'),
(373, 1, 4, 'deposito', 812.00, '2025-10-30 18:00:34'),
(374, 1, 4, 'saque', 780.00, '2025-10-30 18:00:34'),
(375, 1, 4, 'bau', 150.00, '2025-10-30 18:00:34'),
(376, 1, 13, 'deposito', 1231.00, '2025-10-30 18:02:57'),
(377, 1, 13, 'saque', 1000.00, '2025-10-30 18:02:57'),
(378, 1, 13, 'bau', 500.00, '2025-10-30 18:02:57'),
(379, 1, 6, 'deposito', 541.00, '2025-10-27 18:14:19'),
(380, 1, 6, 'saque', 430.00, '2025-10-27 18:14:19'),
(381, 1, 6, 'bau', 100.00, '2025-10-27 18:14:19'),
(382, 1, 6, 'deposito', 542.00, '2025-10-27 18:15:09'),
(383, 1, 6, 'saque', 439.00, '2025-10-27 18:15:09'),
(384, 1, 6, 'bau', 100.00, '2025-10-27 18:15:09'),
(385, 1, 6, 'deposito', 588.00, '2025-10-27 18:15:20'),
(386, 1, 6, 'saque', 410.00, '2025-10-27 18:15:20'),
(387, 1, 6, 'bau', 100.00, '2025-10-27 18:15:20'),
(388, 1, 6, 'deposito', 541.00, '2025-10-27 18:15:49'),
(389, 1, 6, 'saque', 433.00, '2025-10-27 18:15:49'),
(390, 1, 6, 'bau', 100.00, '2025-10-27 18:15:49'),
(391, 1, 4, 'deposito', 30.00, '2025-10-30 18:16:37'),
(392, 1, 4, 'saque', 25.00, '2025-10-30 18:16:37'),
(393, 1, 4, 'bau', 0.00, '2025-10-30 18:16:37'),
(394, 1, 7, 'deposito', 1096.00, '2025-10-30 18:40:42'),
(395, 1, 7, 'saque', 982.00, '2025-10-30 18:40:42'),
(396, 1, 7, 'bau', 200.00, '2025-10-30 18:40:42'),
(397, 1, 9, 'deposito', 801.00, '2025-10-30 19:25:13'),
(398, 1, 9, 'saque', 739.00, '2025-10-30 19:25:13'),
(399, 1, 9, 'bau', 150.00, '2025-10-30 19:25:13'),
(400, 1, 9, 'deposito', 801.00, '2025-10-30 19:26:49'),
(401, 1, 9, 'saque', 739.00, '2025-10-30 19:26:49'),
(402, 1, 9, 'bau', 150.00, '2025-10-30 19:26:49'),
(403, 1, 4, 'deposito', 548.00, '2025-10-30 20:05:25'),
(404, 1, 4, 'saque', 533.00, '2025-10-30 20:05:25'),
(405, 1, 4, 'bau', 100.00, '2025-10-30 20:05:25'),
(406, 1, 7, 'deposito', 828.00, '2025-10-30 20:09:03'),
(407, 1, 7, 'saque', 682.00, '2025-10-30 20:09:03'),
(408, 1, 7, 'bau', 150.00, '2025-10-30 20:09:03'),
(409, 1, 9, 'deposito', 801.00, '2025-10-30 20:26:10'),
(410, 1, 9, 'saque', 739.00, '2025-10-30 20:26:10'),
(411, 1, 9, 'bau', 150.00, '2025-10-30 20:26:10'),
(412, 1, 7, 'deposito', 1072.00, '2025-10-30 21:30:15'),
(413, 1, 7, 'saque', 1144.00, '2025-10-30 21:30:15'),
(414, 1, 7, 'bau', 200.00, '2025-10-30 21:30:15'),
(415, 1, 6, 'deposito', 547.00, '2025-10-30 22:08:48'),
(416, 1, 6, 'saque', 528.00, '2025-10-30 22:08:48'),
(417, 1, 6, 'bau', 100.00, '2025-10-30 22:08:48'),
(418, 1, 4, 'deposito', 560.00, '2025-10-30 22:17:12'),
(419, 1, 4, 'saque', 576.00, '2025-10-30 22:17:12'),
(420, 1, 4, 'bau', 100.00, '2025-10-30 22:17:12'),
(421, 1, 6, 'deposito', 538.00, '2025-10-30 22:53:46'),
(422, 1, 6, 'saque', 469.00, '2025-10-30 22:53:46'),
(423, 1, 6, 'bau', 100.00, '2025-10-30 22:53:46'),
(424, 1, 6, 'deposito', 10.00, '2025-10-31 00:40:59'),
(425, 1, 6, 'saque', 10.00, '2025-10-31 00:40:59'),
(426, 1, 6, 'bau', 0.00, '2025-10-31 00:40:59'),
(427, 1, 6, 'deposito', 10.00, '2025-10-31 00:41:13'),
(428, 1, 6, 'saque', 15.00, '2025-10-31 00:41:13'),
(429, 1, 6, 'bau', 0.00, '2025-10-31 00:41:13'),
(430, 1, 6, 'deposito', 10.00, '2025-10-31 00:41:31'),
(431, 1, 6, 'saque', 12.00, '2025-10-31 00:41:31'),
(432, 1, 6, 'bau', 0.00, '2025-10-31 00:41:31'),
(433, 1, 6, 'deposito', 10.00, '2025-10-31 00:41:43'),
(434, 1, 6, 'saque', 11.00, '2025-10-31 00:41:43'),
(435, 1, 6, 'bau', 0.00, '2025-10-31 00:41:43'),
(436, 1, 6, 'deposito', 10.00, '2025-10-31 00:41:52'),
(437, 1, 6, 'saque', 0.00, '2025-10-31 00:41:52'),
(438, 1, 6, 'bau', 0.00, '2025-10-31 00:41:52'),
(439, 1, 6, 'deposito', 10.00, '2025-10-31 01:38:01'),
(440, 1, 6, 'saque', 13.00, '2025-10-31 01:38:01'),
(441, 1, 6, 'bau', 0.00, '2025-10-31 01:38:01'),
(442, 1, 4, 'deposito', 10.00, '2025-10-31 01:39:18'),
(443, 1, 4, 'saque', 13.00, '2025-10-31 01:39:18'),
(444, 1, 4, 'bau', 0.00, '2025-10-31 01:39:18'),
(445, 1, 9, 'deposito', 804.00, '2025-10-31 02:16:42'),
(446, 1, 9, 'saque', 808.00, '2025-10-31 02:16:42'),
(447, 1, 9, 'bau', 150.00, '2025-10-31 02:16:42'),
(448, 1, 7, 'deposito', 810.00, '2025-10-31 02:22:21'),
(449, 1, 7, 'saque', 804.00, '2025-10-31 02:22:21'),
(450, 1, 7, 'bau', 150.00, '2025-10-31 02:22:21'),
(451, 1, 9, 'deposito', 1070.00, '2025-10-31 03:37:42'),
(452, 1, 9, 'saque', 668.00, '2025-10-31 03:37:42'),
(453, 1, 9, 'bau', 150.00, '2025-10-31 03:37:42'),
(454, 1, 9, 'deposito', 1070.00, '2025-10-31 03:46:33'),
(455, 1, 9, 'saque', 668.00, '2025-10-31 03:46:33'),
(456, 1, 9, 'bau', 200.00, '2025-10-31 03:46:33'),
(457, 1, 7, 'deposito', 1064.00, '2025-10-31 04:00:40'),
(458, 1, 7, 'saque', 838.00, '2025-10-31 04:00:40'),
(459, 1, 7, 'bau', 200.00, '2025-10-31 04:00:40'),
(460, 1, 9, 'deposito', 1000.00, '2025-10-31 14:01:51'),
(461, 1, 9, 'saque', 859.00, '2025-10-31 14:01:51'),
(462, 1, 9, 'bau', 200.00, '2025-10-31 14:01:51'),
(463, 1, 9, 'deposito', 500.00, '2025-10-31 14:08:53'),
(464, 1, 9, 'saque', 600.00, '2025-10-31 14:08:53'),
(465, 1, 9, 'bau', 200.00, '2025-10-31 14:08:53'),
(466, 1, 6, 'deposito', 593.00, '2025-10-31 14:27:59'),
(467, 1, 6, 'saque', 529.00, '2025-10-31 14:27:59'),
(468, 1, 6, 'bau', 100.00, '2025-10-31 14:27:59'),
(469, 1, 4, 'deposito', 552.00, '2025-10-31 14:58:24'),
(470, 1, 4, 'saque', 525.00, '2025-10-31 14:58:24'),
(471, 1, 4, 'bau', 100.00, '2025-10-31 14:58:24'),
(472, 1, 6, 'deposito', 541.00, '2025-10-31 15:39:19'),
(473, 1, 6, 'saque', 571.00, '2025-10-31 15:39:19'),
(474, 1, 6, 'bau', 100.00, '2025-10-31 15:39:19'),
(475, 1, 6, 'deposito', 538.00, '2025-10-31 17:31:16'),
(476, 1, 6, 'saque', 457.00, '2025-10-31 17:31:16'),
(477, 1, 6, 'bau', 100.00, '2025-10-31 17:31:16'),
(478, 1, 4, 'deposito', 831.00, '2025-10-31 17:40:24'),
(479, 1, 4, 'saque', 815.00, '2025-10-31 17:40:24'),
(480, 1, 4, 'bau', 150.00, '2025-10-31 17:40:24'),
(481, 1, 6, 'deposito', 535.00, '2025-10-31 18:16:39'),
(482, 1, 6, 'saque', 445.00, '2025-10-31 18:16:39'),
(483, 1, 6, 'bau', 100.00, '2025-10-31 18:16:39'),
(484, 1, 4, 'deposito', 550.00, '2025-10-31 18:42:05'),
(485, 1, 4, 'saque', 527.00, '2025-10-31 18:42:05'),
(486, 1, 4, 'bau', 100.00, '2025-10-31 18:42:05'),
(487, 1, 4, 'deposito', 552.00, '2025-10-31 19:21:20'),
(488, 1, 4, 'saque', 537.00, '2025-10-31 19:21:20'),
(489, 1, 4, 'bau', 100.00, '2025-10-31 19:21:20'),
(490, 1, 6, 'deposito', 538.00, '2025-10-31 19:33:56'),
(491, 1, 6, 'saque', 491.00, '2025-10-31 19:33:56'),
(492, 1, 6, 'bau', 100.00, '2025-10-31 19:33:56'),
(493, 1, 6, 'deposito', 543.00, '2025-10-31 20:30:01'),
(494, 1, 6, 'saque', 345.00, '2025-10-31 20:30:01'),
(495, 1, 6, 'bau', 100.00, '2025-10-31 20:30:01'),
(496, 1, 4, 'deposito', 550.00, '2025-10-31 20:37:15'),
(497, 1, 4, 'saque', 535.00, '2025-10-31 20:37:15'),
(498, 1, 4, 'bau', 100.00, '2025-10-31 20:37:15'),
(499, 1, 9, 'deposito', 1000.00, '2025-10-31 20:52:32'),
(500, 1, 9, 'saque', 431.00, '2025-10-31 20:52:32'),
(501, 1, 9, 'bau', 200.00, '2025-10-31 20:52:32'),
(502, 1, 9, 'deposito', 1000.00, '2025-10-31 20:57:56'),
(503, 1, 9, 'saque', 431.00, '2025-10-31 20:57:56'),
(504, 1, 9, 'bau', 100.00, '2025-10-31 20:57:56'),
(505, 1, 4, 'deposito', 550.00, '2025-10-31 21:30:12'),
(506, 1, 4, 'saque', 596.00, '2025-10-31 21:30:12'),
(507, 1, 4, 'bau', 100.00, '2025-10-31 21:30:12'),
(508, 1, 7, 'deposito', 1060.00, '2025-11-01 01:36:15'),
(509, 1, 7, 'saque', 999.00, '2025-11-01 01:36:15'),
(510, 1, 7, 'bau', 200.00, '2025-11-01 01:36:15'),
(511, 1, 7, 'deposito', 532.00, '2025-11-01 02:08:25'),
(512, 1, 7, 'saque', 524.00, '2025-11-01 02:08:25'),
(513, 1, 7, 'bau', 100.00, '2025-11-01 02:08:25'),
(514, 1, 7, 'deposito', 1060.00, '2025-11-01 03:58:28'),
(515, 1, 7, 'saque', 929.00, '2025-11-01 03:58:28'),
(516, 1, 7, 'bau', 200.00, '2025-11-01 03:58:28'),
(517, 1, 9, 'deposito', 1000.00, '2025-11-01 05:40:00'),
(518, 1, 9, 'saque', 971.00, '2025-11-01 05:40:00'),
(519, 1, 9, 'bau', 200.00, '2025-11-01 05:40:00'),
(520, 1, 9, 'deposito', 750.00, '2025-11-01 06:21:51'),
(521, 1, 9, 'saque', 657.00, '2025-11-01 06:21:51'),
(522, 1, 9, 'bau', 150.00, '2025-11-01 06:21:51'),
(523, 1, 9, 'deposito', 750.00, '2025-11-01 07:00:06'),
(524, 1, 9, 'saque', 722.00, '2025-11-01 07:00:06'),
(525, 1, 9, 'bau', 150.00, '2025-11-01 07:00:06'),
(526, 1, NULL, 'deposito', 500.00, '2025-11-15 05:03:17'),
(527, 1, NULL, 'saque', 500.00, '2025-11-15 05:03:17'),
(528, 1, NULL, 'bau', 100.00, '2025-11-15 05:03:17'),
(529, 1, NULL, 'deposito', 500.00, '2025-11-15 05:09:10'),
(530, 1, NULL, 'saque', 600.00, '2025-11-15 05:09:10'),
(531, 1, NULL, 'bau', 100.00, '2025-11-15 05:09:10'),
(532, 1, 16, 'deposito', 500.00, '2025-11-15 05:20:10'),
(533, 1, 16, 'saque', 500.00, '2025-11-15 05:20:10'),
(534, 1, 16, 'bau', 100.00, '2025-11-15 05:20:10'),
(535, 1, 17, 'deposito', 500.00, '2025-11-15 05:25:04'),
(536, 1, 17, 'saque', 500.00, '2025-11-15 05:25:04'),
(537, 1, 17, 'bau', 600.00, '2025-11-15 05:25:04'),
(538, 1, 16, 'deposito', 500.00, '2025-11-15 05:40:29'),
(539, 1, 16, 'saque', 600.00, '2025-11-15 05:40:29'),
(540, 1, 16, 'bau', 100.00, '2025-11-15 05:40:29'),
(541, 1, 18, 'deposito', 500.00, '2025-11-15 05:48:38'),
(542, 1, 18, 'saque', 600.00, '2025-11-15 05:48:38'),
(543, 1, 18, 'bau', 100.00, '2025-11-15 05:48:38'),
(544, 5, 19, 'deposito', 500.00, '2025-11-15 05:56:33'),
(545, 5, 19, 'saque', 500.00, '2025-11-15 05:56:33'),
(546, 5, 19, 'bau', 1000.00, '2025-11-15 05:56:33'),
(547, 5, 19, 'deposito', 500.00, '2025-11-15 05:56:53'),
(548, 5, 19, 'saque', 500.00, '2025-11-15 05:56:53'),
(549, 5, 19, 'bau', 1000.00, '2025-11-15 05:56:53'),
(550, 5, 19, 'deposito', 500.00, '2025-11-15 06:00:25'),
(551, 5, 19, 'saque', 500.00, '2025-11-15 06:00:25'),
(552, 5, 19, 'bau', 1000.00, '2025-11-15 06:00:25');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `percentual_comissao` decimal(5,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `id_sub_adm` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `org_id`, `nome`, `email`, `senha`, `percentual_comissao`, `saldo`, `id_sub_adm`) VALUES
(4, 1, 'Kevin', 'kevinpinto@gmail.com', 'HrdXlkFQyc8#', 50.00, NULL, 6),
(5, 1, 'teste', 'teste@gmail0c', 'teste@gmail0,.c,', 25.00, NULL, 10),
(6, 1, 'Iago', 'Iagopinto@gmail.com', 'cKm2rA9#zLNT', 50.00, NULL, 6),
(7, 1, 'Davi', 'Davipinto@gmail.com', 'z9T@ZxXiOato', 50.00, NULL, 6),
(8, 1, 'Miguel', 'Miguelpinto@gmail.com', '!5dysbFoghKz', 50.00, NULL, 6),
(9, 1, 'Douglas', 'douglaspinto@gmail.com', '9657424pdP!', 0.00, NULL, 6),
(12, 1, 'sacola', 'sacola@gmail.com', 'U7V#fTrvw9CB', 25.00, NULL, 11),
(13, 1, 'viado', 'gay@bosta124', 'gay@bosta124', 25.00, NULL, 10),
(16, 1, 'Teste Doda', 'testedoda@gmail.com', '9657424pdP!', 25.00, NULL, 15),
(17, 1, 'Teste ger', 'testerdds@gmail.com', 'XPYTWicDR3H9', 40.00, NULL, 17),
(18, 1, 'Judas', 'judas@tes.com', '147852369', 25.00, NULL, 17),
(19, 5, 'torvic', 'torvic@gmail.com', 'torvic7', 30.00, NULL, 19);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `acao_tipo` (`acao_tipo`),
  ADD KEY `id_usuario_acao` (`id_usuario_acao`),
  ADD KEY `fk_org_log` (`org_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`),
  ADD KEY `fk_org_super_admin` (`super_admin_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `platform_settings`
--
ALTER TABLE `platform_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `relatorios`
--
ALTER TABLE `relatorios`
  ADD PRIMARY KEY (`id_relatorio`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_org_relatorio` (`org_id`);

--
-- Indexes for table `saved_reports`
--
ALTER TABLE `saved_reports`
  ADD PRIMARY KEY (`id_report_salvo`),
  ADD KEY `id_salvo_por` (`id_salvo_por`),
  ADD KEY `fk_org_savedreport` (`org_id`);

--
-- Indexes for table `sub_administradores`
--
ALTER TABLE `sub_administradores`
  ADD PRIMARY KEY (`id_sub_adm`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_org_subadmin` (`org_id`),
  ADD KEY `fk_parent_admin` (`parent_admin_id`);

--
-- Indexes for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id_transacao`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_org_transacao` (`org_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_org_usuario` (`org_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `platform_settings`
--
ALTER TABLE `platform_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `relatorios`
--
ALTER TABLE `relatorios`
  MODIFY `id_relatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `saved_reports`
--
ALTER TABLE `saved_reports`
  MODIFY `id_report_salvo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sub_administradores`
--
ALTER TABLE `sub_administradores`
  MODIFY `id_sub_adm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id_transacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_org_log` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `fk_org_super_admin` FOREIGN KEY (`super_admin_id`) REFERENCES `sub_administradores` (`id_sub_adm`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `relatorios`
--
ALTER TABLE `relatorios`
  ADD CONSTRAINT `fk_org_relatorio` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `relatorios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Constraints for table `saved_reports`
--
ALTER TABLE `saved_reports`
  ADD CONSTRAINT `fk_org_savedreport` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_reports_ibfk_1` FOREIGN KEY (`id_salvo_por`) REFERENCES `sub_administradores` (`id_sub_adm`) ON DELETE SET NULL;

--
-- Constraints for table `sub_administradores`
--
ALTER TABLE `sub_administradores`
  ADD CONSTRAINT `fk_org_subadmin` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_admin` FOREIGN KEY (`parent_admin_id`) REFERENCES `sub_administradores` (`id_sub_adm`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `fk_org_transacao` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transacoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_org_usuario` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
