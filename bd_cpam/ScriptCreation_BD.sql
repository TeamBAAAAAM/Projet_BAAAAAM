-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 25 juin 2020 à 14:30
-- Version du serveur :  5.7.26
-- Version de PHP :  5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `bd_cpam`
--

-- --------------------------------------------------------

--
-- Structure de la table `assure`
--

DROP TABLE IF EXISTS `assure`;
CREATE TABLE IF NOT EXISTS `assure` (
  `CodeA` int(11) NOT NULL AUTO_INCREMENT,
  `NirA` char(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NomA` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PrenomA` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TelA` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MailA` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`CodeA`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `CodeC` int(11) NOT NULL,
  `NomC` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DesignationC` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `StatutC` enum('Actif','Inactif') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Actif',
  PRIMARY KEY (`CodeC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `concerner`
--

DROP TABLE IF EXISTS `concerner`;
CREATE TABLE IF NOT EXISTS `concerner` (
  `CodeC` int(11) NOT NULL,
  `CodeM` int(11) NOT NULL,
  `Label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`CodeC`,`CodeM`,`Label`),
  UNIQUE KEY `CodeC` (`CodeC`,`CodeM`),
  KEY `FK_PK2_Lstnmé_Cat` (`CodeM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossier`
--

DROP TABLE IF EXISTS `dossier`;
CREATE TABLE IF NOT EXISTS `dossier` (
  `CodeD` int(11) NOT NULL AUTO_INCREMENT,
  `StatutD` enum('À traiter','En cours','Terminé','Classé sans suite') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'À traiter',
  `DateD` datetime DEFAULT CURRENT_TIMESTAMP,
  `RefD` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DateAM` date NOT NULL,
  `CodeA` int(11) NOT NULL,
  PRIMARY KEY (`CodeD`),
  KEY `fk_dossier_assure` (`CodeA`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `justificatif`
--

DROP TABLE IF EXISTS `justificatif`;
CREATE TABLE IF NOT EXISTS `justificatif` (
  `CodeJ` int(11) NOT NULL AUTO_INCREMENT,
  `CheminJ` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `StatutJ` enum('Valide','Invalide') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CodeD` int(11) NOT NULL,
  `CodeM` int(11) NOT NULL,
  `CodeT` int(11) DEFAULT NULL,
  PRIMARY KEY (`CodeJ`),
  KEY `fk_justificatif_dossier` (`CodeD`) USING BTREE,
  KEY `fk_justificatif_listemnemonique` (`CodeM`) USING BTREE,
  KEY `fk_Justificatif_Technicien` (`CodeT`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `listemnemonique`
--

DROP TABLE IF EXISTS `listemnemonique`;
CREATE TABLE IF NOT EXISTS `listemnemonique` (
  `CodeM` int(11) NOT NULL AUTO_INCREMENT,
  `Mnemonique` char(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Designation` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`CodeM`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `CodeA` int(11) NOT NULL,
  `CodeT` int(11) NOT NULL,
  `DateEnvoiM` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Contenu` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`CodeA`,`CodeT`,`DateEnvoiM`),
  KEY `fk_message_assure` (`CodeA`),
  KEY `fk_message_technicien` (`CodeT`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `technicien`
--

DROP TABLE IF EXISTS `technicien`;
CREATE TABLE IF NOT EXISTS `technicien` (
  `CodeT` int(11) NOT NULL AUTO_INCREMENT,
  `Matricule` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NomT` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PrenomT` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MdpT` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`CodeT`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `traiter`
--

DROP TABLE IF EXISTS `traiter`;
CREATE TABLE IF NOT EXISTS `traiter` (
  `CodeT` int(11) NOT NULL,
  `CodeD` int(11) NOT NULL,
  `DateTraiterD` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CodeT`,`CodeD`,`DateTraiterD`),
  KEY `fk_traiter_dossier` (`CodeD`) USING BTREE,
  KEY `fk_traiter_technicien` (`CodeT`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `concerner`
--
ALTER TABLE `concerner`
  ADD CONSTRAINT `FK_PK2_Lstnmé_Cat` FOREIGN KEY (`CodeM`) REFERENCES `listemnemonique` (`CodeM`),
  ADD CONSTRAINT `FK_PK_Lstnmé_Cat` FOREIGN KEY (`CodeC`) REFERENCES `categorie` (`CodeC`);

--
-- Contraintes pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD CONSTRAINT `fk_dossier_assure` FOREIGN KEY (`CodeA`) REFERENCES `assure` (`CodeA`);

--
-- Contraintes pour la table `justificatif`
--
ALTER TABLE `justificatif`
  ADD CONSTRAINT `fk_Justificatif_Technicien` FOREIGN KEY (`CodeT`) REFERENCES `technicien` (`CodeT`),
  ADD CONSTRAINT `fk_justificatif_dossier` FOREIGN KEY (`CodeD`) REFERENCES `dossier` (`CodeD`),
  ADD CONSTRAINT `fk_justificatif_listemnemonique` FOREIGN KEY (`CodeM`) REFERENCES `listemnemonique` (`CodeM`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_assure` FOREIGN KEY (`CodeA`) REFERENCES `assure` (`CodeA`),
  ADD CONSTRAINT `fk_message_technicien` FOREIGN KEY (`CodeT`) REFERENCES `technicien` (`CodeT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
