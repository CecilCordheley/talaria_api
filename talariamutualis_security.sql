-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 28 avr. 2026 à 12:02
-- Version du serveur : 5.7.40
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `talariamutualis_security`
--

-- --------------------------------------------------------

--
-- Structure de la table `api_key`
--

DROP TABLE IF EXISTS `api_key`;
CREATE TABLE IF NOT EXISTS `api_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(60) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `scopes` json DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `last_used` datetime DEFAULT NULL,
  `revoked` tinyint(4) DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `api_key`
--

INSERT INTO `api_key` (`id`, `user_id`, `token_hash`, `label`, `scopes`, `expires_at`, `last_used`, `revoked`, `created_at`) VALUES
(1, '6904b3a67216a', '47374798ba751e5fe08c674623b4ad59c548886112b975a5ad3852bc07614d42', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-09 11:55:41', NULL, 0, '2026-04-09 11:40:41'),
(2, '6904b3a67216a', '7a5e794545228da5f7c59c9371e4c67c3d250cffab8948f58229adc2f9207c4e', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-09 11:56:38', '2026-04-09 11:46:38', 0, '2026-04-09 11:41:38'),
(3, '6904b3a67216a', '8f9d4783568ad06c7bf14c8934e1bd3fbbb40d02383930a47530a7ad0c9c8284', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-20 10:04:21', '2026-04-20 10:04:02', 1, '2026-04-20 09:49:21'),
(4, '6904b3a67216a', '5cb14f262e3b06d0b68a2b4a0046a7729bba356336a0287d588d534b17f32cf6', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-20 10:23:17', '2026-04-20 10:19:55', 1, '2026-04-20 10:08:17'),
(5, '6904b3a67216a', '76219be1975b65a5f4bd712d02707d57176242152d0815ae1142332cf9698b8e', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-21 09:38:22', '2026-04-21 09:33:22', 1, '2026-04-21 09:23:22'),
(6, '6904b3a67216a', '87f7c8291327f02ffac4724c2258753726ba18340bb107296b767b2e247e4d26', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-21 12:23:04', '2026-04-21 12:18:04', 1, '2026-04-21 12:08:04'),
(7, '6904b3a67216a', '01c992fe00e4dbcd24d1a56fa4b7c84e330152494056c12f95e65c1395e6fa09', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-21 13:25:48', '2026-04-21 13:22:10', 1, '2026-04-21 13:10:48'),
(8, '6904b3a67216a', '320d4d17799e1342b21d7d77427c0e9cdfd209a3f0499605cbe24848845fed57', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-21 13:43:19', '2026-04-21 13:41:31', 1, '2026-04-21 13:28:19'),
(9, '17112162-e1db-4eee', '2d67f1cdfee1e037809df3abb4dbae89f1d1e32c991ed9a2b2766ec2e3a5c37b', NULL, '{\"role\": \"\\\"admin\\\"\", \"scopes\": []}', '2026-04-28 10:32:16', NULL, 0, '2026-04-28 10:17:41'),
(10, '\"17112162-e1db-4eee\"', '70df3546d4ff276c34b876190197fc7a73ff898852a2865ec826b504cbabf685', NULL, '{\"role\": \"\\\"admin\\\"\", \"scopes\": []}', '2026-04-28 10:39:40', NULL, 0, '2026-04-28 10:24:40'),
(11, '\"17112162-e1db-4eee\"', '8c2846b707012feac63cd990a2d7cf6e3959fa89a682a3b5344ec59e2d4be0f5', NULL, '{\"role\": \"\\\"admin\\\"\", \"scopes\": []}', '2026-04-28 10:40:01', NULL, 0, '2026-04-28 10:25:01'),
(12, '\"17112162-e1db-4eee\"', '0d08fb6ef5464f306a59fc1dd286079f37601daa6798fe677a1229ebfbe48a85', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-28 10:40:34', '2026-04-28 10:33:41', 0, '2026-04-28 10:25:34'),
(13, '\"17112162-e1db-4eee\"', 'b3862c3471bf882d3dfbcf8a87d1b3ea38d1e8fbfc75bac03775e285ad258580', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-28 10:49:36', '2026-04-28 10:38:28', 0, '2026-04-28 10:34:36'),
(14, '17112162-e1db-4eee', 'c5c904be34174e060493743e94617278a76677de8bab4789fbfec45a4fbc755e', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-28 10:57:22', '2026-04-28 10:55:43', 1, '2026-04-28 10:39:14'),
(15, '17112162-e1db-4eee', '390d0bc8a7dfd653d6df72ab2badb12dbcb6723e14c971a5ec548b5c28aeefab', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-28 13:49:51', '2026-04-28 13:48:20', 1, '2026-04-28 13:34:51'),
(16, '17112162-e1db-4eee', 'cf2d44d4e9f0e44a26799999613f23c24ab7435f89ce5b0e3ab44f041d866fb4', NULL, '{\"role\": \"admin\", \"scopes\": []}', '2026-04-28 14:08:25', '2026-04-28 13:59:27', 0, '2026-04-28 13:53:25');

-- --------------------------------------------------------

--
-- Structure de la table `api_log`
--

DROP TABLE IF EXISTS `api_log`;
CREATE TABLE IF NOT EXISTS `api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key_id` int(11) DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `response_time_ms` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `api_key_id` (`api_key_id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `api_log`
--

INSERT INTO `api_log` (`id`, `api_key_id`, `user_id`, `endpoint`, `method`, `status_code`, `response_time_ms`, `ip`, `created_at`) VALUES
(1, 3, '6904b3a67216a', '/Talaria_API/async/users_changePassWord?idUser=6904b3a67216a&newPassWord=kb0di64vu', 'GET', 200, 14, '127.0.0.1', '2026-04-20 09:57:21'),
(2, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 34, '127.0.0.1', '2026-04-20 09:58:22'),
(3, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 31, '127.0.0.1', '2026-04-20 09:58:33'),
(4, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 13, '127.0.0.1', '2026-04-20 10:00:52'),
(5, 3, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 15, '127.0.0.1', '2026-04-20 10:01:00'),
(6, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 31, '127.0.0.1', '2026-04-20 10:02:45'),
(7, 3, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 30, '127.0.0.1', '2026-04-20 10:02:45'),
(8, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 44, '127.0.0.1', '2026-04-20 10:03:57'),
(9, 3, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 32, '127.0.0.1', '2026-04-20 10:03:58'),
(10, 3, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 30, '127.0.0.1', '2026-04-20 10:04:01'),
(11, 3, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 30, '127.0.0.1', '2026-04-20 10:04:02'),
(12, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 24, '127.0.0.1', '2026-04-20 10:08:19'),
(13, 4, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 13, '127.0.0.1', '2026-04-20 10:08:19'),
(14, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 19, '127.0.0.1', '2026-04-20 10:08:24'),
(15, 4, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 31, '127.0.0.1', '2026-04-20 10:08:25'),
(16, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 12, '127.0.0.1', '2026-04-20 10:08:49'),
(17, 4, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 11, '127.0.0.1', '2026-04-20 10:08:50'),
(18, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 21, '127.0.0.1', '2026-04-20 10:08:58'),
(19, 4, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 18, '127.0.0.1', '2026-04-20 10:08:59'),
(20, 4, '6904b3a67216a', '/Talaria_API/async/entreprise_getUsers&id=1', 'GET', 200, 13, '127.0.0.1', '2026-04-20 10:09:14'),
(21, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 29, '127.0.0.1', '2026-04-20 10:09:52'),
(22, 4, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 31, '127.0.0.1', '2026-04-20 10:09:56'),
(23, 4, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 30, '127.0.0.1', '2026-04-20 10:14:55'),
(24, 4, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 14, '127.0.0.1', '2026-04-20 10:19:55'),
(25, 5, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 21, '127.0.0.1', '2026-04-21 09:23:23'),
(26, 5, '6904b3a67216a', '/Talaria_API/async/entreprise_getUsers&id=1', 'GET', 200, 14, '127.0.0.1', '2026-04-21 09:23:25'),
(27, 5, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 13, '127.0.0.1', '2026-04-21 09:23:27'),
(28, 5, '6904b3a67216a', '/Talaria_API/async/service_getServices&idService=RELACLT_SERVICE1', 'GET', 200, 32, '127.0.0.1', '2026-04-21 09:23:34'),
(29, 5, '6904b3a67216a', '/Talaria_API/async/service_getUsers&idService=1', 'GET', 200, 46, '127.0.0.1', '2026-04-21 09:23:34'),
(30, 5, '6904b3a67216a', '/Talaria_API/async/service_getServices&idService=69a948201d462', 'GET', 200, 14, '127.0.0.1', '2026-04-21 09:23:39'),
(31, 5, '6904b3a67216a', '/Talaria_API/async/service_getUsers&idService=8', 'GET', 200, 11, '127.0.0.1', '2026-04-21 09:23:39'),
(32, 5, '6904b3a67216a', '/Talaria_API/async/service_getServices&idService=PRESTA_SERVICE2', 'GET', 200, 30, '127.0.0.1', '2026-04-21 09:23:42'),
(33, 5, '6904b3a67216a', '/Talaria_API/async/service_getUsers&idService=2', 'GET', 200, 19, '127.0.0.1', '2026-04-21 09:23:42'),
(34, 5, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 23, '127.0.0.1', '2026-04-21 09:28:22'),
(35, 5, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 31, '127.0.0.1', '2026-04-21 09:33:22'),
(36, 6, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 30, '127.0.0.1', '2026-04-21 12:08:05'),
(37, 6, '6904b3a67216a', '/Talaria_API/async/entreprise_getUsers&id=1', 'GET', 200, 31, '127.0.0.1', '2026-04-21 12:08:10'),
(38, 6, '6904b3a67216a', '/Talaria_API/async/service_getServices', 'GET', 200, 29, '127.0.0.1', '2026-04-21 12:08:14'),
(39, 6, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 15, '127.0.0.1', '2026-04-21 12:13:04'),
(40, 6, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 30, '127.0.0.1', '2026-04-21 12:18:04'),
(41, 7, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 31, '127.0.0.1', '2026-04-21 13:10:49'),
(42, 7, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 22, '127.0.0.1', '2026-04-21 13:11:49'),
(43, 7, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 31, '127.0.0.1', '2026-04-21 13:13:38'),
(44, 7, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 14, '127.0.0.1', '2026-04-21 13:13:57'),
(45, 7, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 16, '127.0.0.1', '2026-04-21 13:15:07'),
(46, 7, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 16, '127.0.0.1', '2026-04-21 13:15:10'),
(47, 7, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 14, '127.0.0.1', '2026-04-21 13:17:11'),
(48, 7, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 20, '127.0.0.1', '2026-04-21 13:17:26'),
(49, 7, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 17, '127.0.0.1', '2026-04-21 13:22:10'),
(50, 8, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 30, '127.0.0.1', '2026-04-21 13:28:56'),
(51, 8, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 16, '127.0.0.1', '2026-04-21 13:28:57'),
(52, 8, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 13, '127.0.0.1', '2026-04-21 13:31:37'),
(53, 8, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 19, '127.0.0.1', '2026-04-21 13:31:38'),
(54, 8, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 36, '127.0.0.1', '2026-04-21 13:31:50'),
(55, 8, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 11, '127.0.0.1', '2026-04-21 13:31:51'),
(56, 8, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 30, '127.0.0.1', '2026-04-21 13:33:46'),
(57, 8, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 13, '127.0.0.1', '2026-04-21 13:33:47'),
(58, 8, '6904b3a67216a', '/Talaria_API/async/users_checkToken', 'GET', 200, 13, '127.0.0.1', '2026-04-21 13:38:45'),
(59, 8, '6904b3a67216a', '/Talaria_API/async/users_getUser&id=6904b3a67216a', 'GET', 200, 35, '127.0.0.1', '2026-04-21 13:41:30'),
(60, 8, '6904b3a67216a', '/Talaria_API/async/ticket_filter', 'POST', 200, 32, '127.0.0.1', '2026-04-21 13:41:31'),
(61, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 36, '127.0.0.1', '2026-04-28 10:26:17'),
(62, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 32, '127.0.0.1', '2026-04-28 10:26:20'),
(63, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 30, '127.0.0.1', '2026-04-28 10:26:22'),
(64, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 17, '127.0.0.1', '2026-04-28 10:27:34'),
(65, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/service_getServices', 'GET', 200, 18, '127.0.0.1', '2026-04-28 10:27:34'),
(66, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 31, '127.0.0.1', '2026-04-28 10:27:35'),
(67, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 32, '127.0.0.1', '2026-04-28 10:30:06'),
(68, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 22, '127.0.0.1', '2026-04-28 10:30:07'),
(69, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 12, '127.0.0.1', '2026-04-28 10:31:15'),
(70, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 13, '127.0.0.1', '2026-04-28 10:32:00'),
(71, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 13, '127.0.0.1', '2026-04-28 10:33:18'),
(72, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/entreprise_get', 'GET', 200, 28, '127.0.0.1', '2026-04-28 10:33:34'),
(73, 12, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 38, '127.0.0.1', '2026-04-28 10:33:41'),
(74, 13, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 31, '127.0.0.1', '2026-04-28 10:34:37'),
(75, 13, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 33, '127.0.0.1', '2026-04-28 10:35:07'),
(76, 13, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 20, '127.0.0.1', '2026-04-28 10:36:15'),
(77, 13, '\"17112162-e1db-4eee\"', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 32, '127.0.0.1', '2026-04-28 10:38:28'),
(78, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 32, '127.0.0.1', '2026-04-28 10:39:15'),
(79, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 11, '127.0.0.1', '2026-04-28 10:39:35'),
(80, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 50, '127.0.0.1', '2026-04-28 10:39:37'),
(81, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_del?siret=12345678912345', 'GET', 200, 20, '127.0.0.1', '2026-04-28 10:41:38'),
(82, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_del?siret=12345678912345', 'GET', 200, 13, '127.0.0.1', '2026-04-28 10:41:38'),
(83, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 12, '127.0.0.1', '2026-04-28 10:41:41'),
(84, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_create', 'POST', 200, 13, '127.0.0.1', '2026-04-28 10:42:22'),
(85, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_create', 'POST', 200, 30, '127.0.0.1', '2026-04-28 10:42:22'),
(86, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 21, '127.0.0.1', '2026-04-28 10:42:54'),
(87, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=undefined', 'GET', 200, 11, '127.0.0.1', '2026-04-28 10:43:25'),
(88, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 38, '127.0.0.1', '2026-04-28 10:43:31'),
(89, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=undefined', 'GET', 200, 31, '127.0.0.1', '2026-04-28 10:43:33'),
(90, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 10, '127.0.0.1', '2026-04-28 10:45:26'),
(91, 14, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 32, '127.0.0.1', '2026-04-28 10:45:27'),
(92, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_checkToken', 'GET', 200, 33, '127.0.0.1', '2026-04-28 10:50:34'),
(93, 14, '17112162-e1db-4eee', '/Talaria_API/async/users_checkToken', 'GET', 200, 12, '127.0.0.1', '2026-04-28 10:55:43'),
(94, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 45, '127.0.0.1', '2026-04-28 13:34:53'),
(95, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 27, '127.0.0.1', '2026-04-28 13:34:54'),
(96, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:35:10'),
(97, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:35:17'),
(98, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 35, '127.0.0.1', '2026-04-28 13:37:30'),
(99, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:37:31'),
(100, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 34, '127.0.0.1', '2026-04-28 13:37:46'),
(101, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:37:48'),
(102, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:41:26'),
(103, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_checkToken', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:42:45'),
(104, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:42:48'),
(105, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 20, '127.0.0.1', '2026-04-28 13:42:51'),
(106, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 35, '127.0.0.1', '2026-04-28 13:43:24'),
(107, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 36, '127.0.0.1', '2026-04-28 13:45:13'),
(108, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:45:14'),
(109, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:45:15'),
(110, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 13, '127.0.0.1', '2026-04-28 13:46:06'),
(111, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 16, '127.0.0.1', '2026-04-28 13:46:08'),
(112, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 32, '127.0.0.1', '2026-04-28 13:46:08'),
(113, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 28, '127.0.0.1', '2026-04-28 13:46:38'),
(114, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:46:40'),
(115, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 33, '127.0.0.1', '2026-04-28 13:46:40'),
(116, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:46:45'),
(117, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 36, '127.0.0.1', '2026-04-28 13:46:46'),
(118, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 29, '127.0.0.1', '2026-04-28 13:47:45'),
(119, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 35, '127.0.0.1', '2026-04-28 13:47:46'),
(120, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:47:47'),
(121, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:47:56'),
(122, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:47:56'),
(123, 15, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:48:13'),
(124, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 11, '127.0.0.1', '2026-04-28 13:48:15'),
(125, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 35, '127.0.0.1', '2026-04-28 13:48:15'),
(126, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:48:16'),
(127, 15, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 33, '127.0.0.1', '2026-04-28 13:48:20'),
(128, 15, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 34, '127.0.0.1', '2026-04-28 13:48:20'),
(129, 16, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 28, '127.0.0.1', '2026-04-28 13:53:26'),
(130, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:53:27'),
(131, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_get', 'GET', 200, 35, '127.0.0.1', '2026-04-28 13:53:27'),
(132, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 14, '127.0.0.1', '2026-04-28 13:53:53'),
(133, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 17, '127.0.0.1', '2026-04-28 13:53:54'),
(134, 16, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 13, '127.0.0.1', '2026-04-28 13:54:13'),
(135, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 13, '127.0.0.1', '2026-04-28 13:54:14'),
(136, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 31, '127.0.0.1', '2026-04-28 13:54:15'),
(137, 16, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:54:39'),
(138, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 16, '127.0.0.1', '2026-04-28 13:54:39'),
(139, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 32, '127.0.0.1', '2026-04-28 13:54:40'),
(140, 16, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:55:35'),
(141, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 15, '127.0.0.1', '2026-04-28 13:55:35'),
(142, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 12, '127.0.0.1', '2026-04-28 13:55:36'),
(143, 16, '17112162-e1db-4eee', '/Talaria_API/async/users_getUser&id=17112162-e1db-4eee', 'GET', 200, 33, '127.0.0.1', '2026-04-28 13:59:26'),
(144, 16, '17112162-e1db-4eee', '/Talaria_API/async/entreprise_getUsers&id=6', 'GET', 200, 11, '127.0.0.1', '2026-04-28 13:59:27'),
(145, 16, '17112162-e1db-4eee', '/Talaria_API/async/service_getServices', 'GET', 200, 28, '127.0.0.1', '2026-04-28 13:59:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
