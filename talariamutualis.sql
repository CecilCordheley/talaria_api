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
-- Base de données : `talariamutualis`
--

-- --------------------------------------------------------

--
-- Structure de la table `changeetat`
--

DROP TABLE IF EXISTS `changeetat`;
CREATE TABLE IF NOT EXISTS `changeetat` (
  `EtatTicket` int(11) NOT NULL,
  `Ticket` int(11) NOT NULL,
  `dateEtatTicket` datetime NOT NULL,
  `comment` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`EtatTicket`,`Ticket`,`dateEtatTicket`),
  KEY `fk_etatTicket_has_ticket_ticket1_idx` (`Ticket`),
  KEY `fk_etatTicket_has_ticket_etatTicket1_idx` (`EtatTicket`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `idEntreprise` int(11) NOT NULL AUTO_INCREMENT,
  `nomEntreprise` varchar(100) NOT NULL,
  `siretEntreprise` varchar(14) DEFAULT NULL,
  `adresseEntrerprise` varchar(150) NOT NULL,
  `cpEntreprise` char(5) NOT NULL,
  `villeEntreprise` varchar(45) NOT NULL,
  `telEntreprise` char(10) DEFAULT NULL,
  `mailEntreprise` varchar(45) DEFAULT NULL,
  `typeEntreprise` enum('client','prestataire','autre') NOT NULL DEFAULT 'client',
  `dataEntreprise` json DEFAULT NULL,
  `created_At` datetime NOT NULL,
  `update_At` datetime DEFAULT NULL,
  PRIMARY KEY (`idEntreprise`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`idEntreprise`, `nomEntreprise`, `siretEntreprise`, `adresseEntrerprise`, `cpEntreprise`, `villeEntreprise`, `telEntreprise`, `mailEntreprise`, `typeEntreprise`, `dataEntreprise`, `created_At`, `update_At`) VALUES
(6, 'Mutualis', '95982348653255', '165 rue des faux', '77000', 'Melun', '0102030405', 'contact@mutualis.fr', 'client', NULL, '2026-04-23 08:17:06', NULL),
(7, 'PrestaCall', '13901935748320', '123 rue des bidons', '59290', 'Wasquehal', '0102030405', 'contact@prestacall.com', 'prestataire', NULL, '2026-04-23 08:17:06', NULL),
(9, 'test', '12345678912345', 'bidon', '00000', 'bidon', 'NULL', 'NULL', 'prestataire', '{}', '2026-04-28 10:42:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `etatticket`
--

DROP TABLE IF EXISTS `etatticket`;
CREATE TABLE IF NOT EXISTS `etatticket` (
  `idEtatTicket` int(11) NOT NULL AUTO_INCREMENT,
  `libEtatTicket` varchar(45) NOT NULL,
  `refEtatTicket` char(10) DEFAULT NULL,
  PRIMARY KEY (`idEtatTicket`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `etatticket`
--

INSERT INTO `etatticket` (`idEtatTicket`, `libEtatTicket`, `refEtatTicket`) VALUES
(1, 'Created', 'CREA-G73FF'),
(2, 'Validated', 'VALI-D83EF'),
(3, 'Pending', 'PEND-8IO36'),
(4, 'Rejected', 'REJE-K1E32'),
(5, 'Processed', 'PROC-F356T');

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `idService` int(11) NOT NULL AUTO_INCREMENT,
  `nomService` varchar(45) NOT NULL,
  `descService` varchar(255) NOT NULL,
  `createAt` date NOT NULL,
  `isActiv` tinyint(4) NOT NULL DEFAULT '1',
  `Entreprise` int(11) DEFAULT NULL,
  `uuidService` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idService`),
  KEY `fk_service_Entreprise1_idx` (`Entreprise`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`idService`, `nomService`, `descService`, `createAt`, `isActiv`, `Entreprise`, `uuidService`) VALUES
(2, 'Prestation', 'Gère les remboursements', '2026-04-24', 1, 6, '69eb2ac1d37b1'),
(3, 'Relation adhérent', 'Gère les appels entrants ', '2026-04-24', 1, 7, '69eb3442c3d07');

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE IF NOT EXISTS `ticket` (
  `idTicket` int(11) NOT NULL AUTO_INCREMENT,
  `uuidTicket` varchar(45) NOT NULL,
  `contentTicket` text NOT NULL,
  `dateTicket` date NOT NULL,
  `objetTicket` varchar(25) NOT NULL,
  `prioriteTicket` enum('basse','normal','haute') NOT NULL DEFAULT 'normal',
  `dataticket` json NOT NULL,
  `responsable` int(11) DEFAULT NULL,
  `Auteur` int(11) NOT NULL,
  `service` int(11) NOT NULL COMMENT 'service destinataire',
  `typeTicket` int(11) NOT NULL,
  `entreprise_source` int(11) NOT NULL,
  `entreprise_cible` int(11) NOT NULL,
  PRIMARY KEY (`idTicket`),
  KEY `fk_ticket_user2_idx` (`responsable`),
  KEY `fk_ticket_user1_idx` (`Auteur`),
  KEY `fk_ticket_service1_idx` (`service`),
  KEY `fk_ticket_typeticket1_idx` (`typeTicket`),
  KEY `fk_ticket_entreprise1_idx` (`entreprise_source`),
  KEY `fk_ticket_entreprise2_idx` (`entreprise_cible`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `typeticket`
--

DROP TABLE IF EXISTS `typeticket`;
CREATE TABLE IF NOT EXISTS `typeticket` (
  `idTypeTicket` int(11) NOT NULL AUTO_INCREMENT,
  `libTypeTicket` varchar(45) NOT NULL,
  `descTypeTicket` varchar(125) DEFAULT NULL,
  `refTypeTicket` char(10) DEFAULT NULL,
  PRIMARY KEY (`idTypeTicket`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `typeticket`
--

INSERT INTO `typeticket` (`idTypeTicket`, `libTypeTicket`, `descTypeTicket`, `refTypeTicket`) VALUES
(1, 'Réclamation', 'concerne les réclamations ou les potentiels situations conflictuelles', 'RECL-E5ABD'),
(2, 'Information', 'Information sur les dossiers', 'INFO-G6VF5'),
(3, 'Intervention', 'Demande d\'action sur un dossier', 'INT-2F6G3'),
(4, 'Documentation', 'Demande de documentation sur un contrat', 'DOC-5G3T6'),
(5, 'Incident IT', 'relatif au problème logiciel ou espace adhérents', 'IT-B3H6F');

-- --------------------------------------------------------

--
-- Structure de la table `type_user`
--

DROP TABLE IF EXISTS `type_user`;
CREATE TABLE IF NOT EXISTS `type_user` (
  `idTypeUser` int(11) NOT NULL AUTO_INCREMENT,
  `libTypeUser` varchar(45) NOT NULL,
  `refTypeUser` char(10) DEFAULT NULL COMMENT 'Reférence pour exportation\n4 premiers caractères du libelle suivi de valeur aléatoire',
  PRIMARY KEY (`idTypeUser`),
  UNIQUE KEY `refTypeUser` (`refTypeUser`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `type_user`
--

INSERT INTO `type_user` (`idTypeUser`, `libTypeUser`, `refTypeUser`) VALUES
(1, 'Admin', 'ADMI-BEA3'),
(2, 'Agent', 'AGEN-A7E6'),
(3, 'Dev', 'DEV-BR316'),
(4, 'manager', 'MANA-D23F8'),
(5, 'TRANSITIONAL', 'TRANSI-456');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `nomUser` varchar(45) NOT NULL,
  `prenomUser` varchar(45) NOT NULL,
  `mailUser` varchar(125) NOT NULL,
  `mdpUser` char(64) NOT NULL,
  `validiteMdp` date NOT NULL,
  `uuidUser` varchar(45) NOT NULL,
  `dataAgent` json NOT NULL,
  `service_idService` int(11) DEFAULT NULL,
  `typeUser` int(11) NOT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE KEY `mailUser_UNIQUE` (`mailUser`),
  KEY `fk_user_service1_idx` (`service_idService`),
  KEY `fk_user_type_user1_idx` (`typeUser`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `nomUser`, `prenomUser`, `mailUser`, `mdpUser`, `validiteMdp`, `uuidUser`, `dataAgent`, `service_idService`, `typeUser`) VALUES
(1, 'Alain', 'Dapuis', 'a.dapuis@mutualis.fr', '6ca22e07e5cc2ca87eb69f66947653a063273b07ce0a0e4828eb2c86925fa8a0', '2026-06-23', '17112162-e1db-4eee', '{}', NULL, 1),
(2, 'test', 'test', 'pop@mail.fr', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2026-05-27', '69ef627a197ea', '[]', NULL, 4);

-- --------------------------------------------------------

--
-- Structure de la table `user_entreprise`
--

DROP TABLE IF EXISTS `user_entreprise`;
CREATE TABLE IF NOT EXISTS `user_entreprise` (
  `user_idUser` int(11) NOT NULL,
  `entreprise_idEntreprise` int(11) NOT NULL,
  PRIMARY KEY (`user_idUser`,`entreprise_idEntreprise`),
  KEY `fk_user_has_entreprise_entreprise1_idx` (`entreprise_idEntreprise`),
  KEY `fk_user_has_entreprise_user1_idx` (`user_idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user_entreprise`
--

INSERT INTO `user_entreprise` (`user_idUser`, `entreprise_idEntreprise`) VALUES
(1, 6);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `changeetat`
--
ALTER TABLE `changeetat`
  ADD CONSTRAINT `fk_etatTicket_has_ticket_etatTicket1` FOREIGN KEY (`EtatTicket`) REFERENCES `etatticket` (`idEtatTicket`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_etatTicket_has_ticket_ticket1` FOREIGN KEY (`Ticket`) REFERENCES `ticket` (`idTicket`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `fk_service_Entreprise1` FOREIGN KEY (`Entreprise`) REFERENCES `entreprise` (`idEntreprise`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_user1` FOREIGN KEY (`Auteur`) REFERENCES `user` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_user2` FOREIGN KEY (`responsable`) REFERENCES `user` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_service1` FOREIGN KEY (`service_idService`) REFERENCES `service` (`idService`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_type_user1` FOREIGN KEY (`typeUser`) REFERENCES `type_user` (`idTypeUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `user_entreprise`
--
ALTER TABLE `user_entreprise`
  ADD CONSTRAINT `fk_user_entreprise_entreprise1` FOREIGN KEY (`entreprise_idEntreprise`) REFERENCES `entreprise` (`idEntreprise`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_entreprise_user1` FOREIGN KEY (`user_idUser`) REFERENCES `user` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
