-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Ven 13 Mars 2020 à 10:29
-- Version du serveur :  10.3.22-MariaDB-0+deb10u1
-- Version de PHP :  7.3.14-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `Projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `Eau`
--

CREATE TABLE `Eau` (
  `Id` int(11) NOT NULL,
  `Conso` int(11) NOT NULL,
  `Debit` float NOT NULL,
  `Electrovanne` tinyint(1) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `Eau`
--

INSERT INTO `Eau` (`Id`, `Conso`, `Debit`, `Electrovanne`, `Date`) VALUES
(1, 170, 0, 0, '2020-03-11 14:09:35'),
(2, 170, 0, 0, '2020-03-11 14:09:45'),
(3, 176, 0.6, 0, '2020-03-11 14:09:55'),
(4, 192, 1.6, 0, '2020-03-11 14:10:05');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `Eau`
--
ALTER TABLE `Eau`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `Eau`
--
ALTER TABLE `Eau`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
