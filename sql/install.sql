-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 07 sep. 2023 à 16:32
-- Version du serveur : 10.6.15-MariaDB
-- Version de PHP : 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `rlvq6885_projet5`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `content`, `author_id`, `created_at`, `parent_id`, `post_id`, `is_approved`) VALUES
(18, '<p>Premier <strong>parent</strong> commentaire</p>', 9, '2023-07-16 19:25:28', NULL, 13, 1),
(19, '<p><strong>Deuxi&egrave;me</strong> parent commentaire</p>', 9, '2023-07-18 22:35:48', NULL, 13, 1),
(20, '<p>Test r&eacute;ponse premier parent</p>', 9, '2023-07-18 22:44:42', 18, 13, 1),
(24, 'test', 9, '2023-07-18 22:55:51', NULL, 13, 1),
(25, 'test', 9, '2023-07-18 22:56:20', 19, 13, 1),
(26, 'testtttt', 9, '2023-07-18 22:56:26', NULL, 13, 1),
(32, '<p>Test r&eacute;ponse premier parent encore</p>', 9, '2023-07-21 14:07:03', 18, 13, 1),
(33, '<p>test</p>', 12, '2023-07-23 16:30:38', NULL, 13, 1),
(34, '<p>tezsrsfds</p>', 12, '2023-07-23 16:30:47', 18, 13, 1),
(35, '<p>Tets</p>', 15, '2023-07-28 08:48:15', NULL, 14, 1),
(36, '<p>test</p>', 15, '2023-08-02 23:24:48', NULL, 13, 1);

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `hat` text NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `title`, `slug`, `thumbnail`, `hat`, `content`, `created_at`, `updated_at`, `is_active`, `author_id`) VALUES
(13, 'Au Cameroun, Kylian Mbappé de retour sur la terre paternelle test', 'au-cameroun-kylian-mbapp-de-retour-sur-la-terre-paternelle', 'https://img.lemde.fr/2023/07/07/0/0/5472/3648/556/0/75/0/3eff518_1688718284701-000-33mz3rh.jpg', '<p>Le footballeur fran&ccedil;ais effectue une visite de trois jours dans le pays. Au programme : visite d&rsquo;&eacute;coles, rencontre avec le premier ministre et match de gala..</p>', '<p>Kylian Mbapp&eacute; est arriv&eacute; jeudi 6 juillet au Cameroon, pays d&rsquo;origine de son p&egrave;re, Wilfrid Mbapp&eacute;. D&egrave;s sa sortie du jet priv&eacute; qui l&rsquo;a men&eacute; des Etats-Unis &agrave; Yaound&eacute;, le capitaine de l&rsquo;&eacute;quipe de France de football a &eacute;t&eacute; accueilli par un public en joie. Personnel de l&rsquo;a&eacute;roport, hommes, femmes, groupes de danse, membres de sa famille&hellip; criant son nom &agrave; tue-t&ecirc;te, chacun voulant le toucher ou prendre un selfie avec lui..</p>\r\n<p class=\"article__paragraph \">&nbsp;</p>\r\n<p class=\"article__paragraph \"><strong>Souriant, le joueur de 24&nbsp;ans les a approch&eacute;s, salu&eacute;s. La police et ses gardes du corps ont eu du mal &agrave; l&rsquo;extraire de la foule. Son cort&egrave;ge s&rsquo;est ensuite &eacute;branl&eacute; vers une &eacute;cole qui accueille des enfants atteints d&rsquo;une d&eacute;ficience auditive et qui a re&ccedil;u des financements de la fondation de l&rsquo;attaquant du Paris-Saint-Germain (PSG). Le champion du monde 2018 a partag&eacute; une partie de foot avec des enfants visiblement heureux de sa visite.</strong></p>\r\n<div id=\"inread_top-3\" class=\"dfp-slot dfp__slot dfp__inread dfp-unloaded\" data-format=\"inread_top\" aria-hidden=\"true\"></div>\r\n<p class=\"article__paragraph \">Loin du tumulte parisien, Kylian Mbapp&eacute; passera trois jours sur le sol camerounais, log&eacute; au Village Noah, le complexe h&ocirc;telier de l&rsquo;ancien tennisman Yannick Noah. Vendredi, son programme inclut notamment une rencontre avec le premier ministre. Samedi, il se rendra &agrave; Douala pour visiter une autre &eacute;cole financ&eacute;e par sa fondation, avant de se rendre sur l&rsquo;&icirc;le de Dj&eacute;bal&egrave;, d&rsquo;o&ugrave; son p&egrave;re est originaire. <em>&laquo;&nbsp;Parce que voil&agrave;, le circuit normal de la vie, c&rsquo;est qu&rsquo;on part des racines jusqu&rsquo;au fruit, et un fruit qui tombe, cela va toujours regermer &agrave; travers les racines&nbsp;&raquo;</em>, a expliqu&eacute; Philippe Mbapp&eacute;, son grand-p&egrave;re, aux journalistes.</p>\r\n<h2 class=\"article__sub-title\">&laquo;&nbsp;Je ne tiens plus en place&nbsp;&raquo;</h2>\r\n<p class=\"article__paragraph \">Pour de nombreux Camerounais, l&rsquo;arriv&eacute;e du sportif est une<em> &laquo;&nbsp;reconnaissance pour le pays de ses anc&ecirc;tres&nbsp;&raquo;</em>. <em>&laquo;&nbsp;M&ecirc;me s&rsquo;il joue pour la France, il a une moiti&eacute; de sang camerounais dans son corps. Venir sur la terre de ses anc&ecirc;tres est toujours une b&eacute;n&eacute;diction. Il est venu recevoir la b&eacute;n&eacute;diction de ses a&iuml;eux, la b&eacute;n&eacute;diction de son pays&nbsp;&raquo;, </em>explique Roger Sylvain Tagne, menuisier &agrave; Douala. Pour ce fan du FC Barcelone, la venue du champion permettra aussi &agrave; des jeunes de<em> &laquo;&nbsp;r&ecirc;ver grand&nbsp;&raquo;. &laquo;&nbsp;Mbapp&eacute; est un joueur extraordinaire et j&rsquo;esp&egrave;re qu&rsquo;il quittera pour de bon le PSG. Tous les jeunes que je connais sont inspir&eacute;s par lui. Il s&rsquo;exprime bien, il n&rsquo;a pas de scandale, c&rsquo;est un mod&egrave;le pour la jeunesse&nbsp;&raquo;</em>, assure Sonny, apprenti menuisier.</p>\r\n<p class=\"article__paragraph \">Au Vent d&rsquo;Etoudi, club de football de deuxi&egrave;me division de Yaound&eacute;, les joueurs et l&rsquo;encadrement n&rsquo;ont qu&rsquo;un objectif&nbsp;: tirer le maximum de conseils et de motivation de Kylian Mbapp&eacute;. Ils joueront un match de gala avec le prodige fran&ccedil;ais ce vendredi. Passy Atangana, milieu de terrain du club, a h&acirc;te de passer du temps avec ce<em> &laquo;&nbsp;meneur d&rsquo;hommes&nbsp;&raquo;</em> dont il regarde presque tous les matchs de championnat de France ou de Ligue des champions. <em>&laquo;&nbsp;Je ne tiens plus en place&nbsp;&raquo;</em>, confie-t-il. Pour lui, jouer avec le meilleur buteur du PSG est une <em>&laquo;&nbsp;offrande&nbsp;&raquo;, </em>mais aussi l&rsquo;opportunit&eacute; de glaner quelques conseils pour sa future carri&egrave;re<em>.</em></p>\r\n<div id=\"inread-8\" class=\"dfp-slot dfp__slot dfp__inread dfp-unloaded\" data-format=\"inread\" aria-hidden=\"true\"></div>\r\n<p class=\"article__paragraph \"><em>&laquo;&nbsp;Voir Mbapp&eacute; c&rsquo;est un r&ecirc;ve, et quand on dit r&ecirc;ver, ils esp&egrave;rent qu&rsquo;un jour ils pourront &ecirc;tre comme lui&nbsp;&raquo;, </em>souligne le colonel&nbsp;&agrave; la retraite Beno&icirc;t Akini, administrateur du Vent d&rsquo;Etoudi, qui assure &ecirc;tre<em> &laquo;&nbsp;tr&egrave;s heureux&nbsp;et honor&eacute;&nbsp;&raquo; </em>de la visite de Kylian Mbapp&eacute;.&nbsp;Une joie partag&eacute;e. Dans une vid&eacute;o publi&eacute;e sur les r&eacute;seaux sociaux, on entend le joueur r&eacute;pondre &agrave; une personne qui lui demande si c&rsquo;est sa premi&egrave;re visite au Cameroun&nbsp;: <em>&laquo;&nbsp;Ce n&rsquo;est pas la premi&egrave;re fois, mais je suis tr&egrave;s content.&nbsp;&raquo;</em></p>', '2023-07-07 14:43:03', '2023-08-02 23:19:54', 1, 9),
(14, 'Pas Lorem Ipsum que ce soit, mais votre Lorem Ipsum!!!', 'pas-lorem-ipsum-que-ce-soit-mais-votre-lorem-ipsum', 'https://images.unsplash.com/photo-1688670565149-d1e7c8ea70a9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80', '<p>Avec le&nbsp;<strong>g&eacute;n&eacute;rateur de texte online</strong>&nbsp;vous pouvez composer votre&nbsp;<strong>Lorem Ipsum</strong>&nbsp;personnel, en l&rsquo;enrichissant avec des &eacute;l&eacute;ments html qui en d&eacute;finissent la structure, avec la possibilit&eacute; d&rsquo;ins&eacute;rer des links externes, mais pas seulement.</p>', '<p>Maintenant, composer un&nbsp;<strong>texte Lorem Ipsum</strong>&nbsp;est beaucoup plus amusant!</p>\r\n<p>En fait, en ins&eacute;rant n&rsquo;importe quel texte invent&eacute; ou c&eacute;l&egrave;bre, que ce soit une po&eacute;sie, un discours, un passage litt&eacute;raire, un texte de chanson, etc., notre g&egrave;n&eacute;rateur de texte choisira de fa&ccedil;on al&eacute;atoire les mots et les passages qui iront composer votre unique Lorem Ipsum.</p>\r\n<p>Soyez-vous original, mettez votre imagination &agrave; l\'&eacute;preuve&hellip; notre g&eacute;n&eacute;rateur de Lorem Ipsum vous surprendra. Prouvez-le maintenant!</p>', '2023-07-07 15:47:56', '2023-07-07 15:52:05', 1, 9),
(18, 'Votre Lorem Ipsum!!!', 'votre-lorem-ipsum', 'https://images.unsplash.com/photo-1693845886349-54208259fe49?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=3028&q=80', '<p>Avec le&nbsp;<strong>g&eacute;n&eacute;rateur de texte online</strong>&nbsp;vous pouvez composer votre&nbsp;<strong>Lorem Ipsum</strong>&nbsp;personnel, en l&rsquo;enrichissant avec des &eacute;l&eacute;ments html qui en d&eacute;finissent la structure, avec la possibilit&eacute; d&rsquo;ins&eacute;rer des links externes, mais pas seulement.</p>', '<p>Maintenant, composer un&nbsp;<strong>texte Lorem Ipsum</strong>&nbsp;est beaucoup plus amusant!</p>\r\n<p>En fait, en ins&eacute;rant n&rsquo;importe quel texte invent&eacute; ou c&eacute;l&egrave;bre, que ce soit une po&eacute;sie, un discours, un passage litt&eacute;raire, un texte de chanson, etc., notre g&egrave;n&eacute;rateur de texte choisira de fa&ccedil;on al&eacute;atoire les mots et les passages qui iront composer votre unique Lorem Ipsum.</p>\r\n<p>Soyez-vous original, mettez votre imagination &agrave; l\'&eacute;preuve&hellip; notre g&eacute;n&eacute;rateur de Lorem Ipsum vous surprendra. Prouvez-le maintenant!</p>', '2023-09-07 09:48:18', '2023-09-07 09:48:18', 1, 9),
(19, 'Votre Lorem Ipsum!!! V2', 'votre-lorem-ipsum-v2', 'https://images.unsplash.com/photo-1693745200812-38873ef7478f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2970&q=80', '<p>Avec le&nbsp;<strong>g&eacute;n&eacute;rateur de texte online</strong>&nbsp;vous pouvez composer votre&nbsp;<strong>Lorem Ipsum</strong>&nbsp;personnel, en l&rsquo;enrichissant avec des &eacute;l&eacute;ments html qui en d&eacute;finissent la structure, avec la possibilit&eacute; d&rsquo;ins&eacute;rer des links externes, mais pas seulement.</p>', '<p>Maintenant, composer un&nbsp;<strong>texte Lorem Ipsum</strong>&nbsp;est beaucoup plus amusant!</p>\r\n<p>En fait, en ins&eacute;rant n&rsquo;importe quel texte invent&eacute; ou c&eacute;l&egrave;bre, que ce soit une po&eacute;sie, un discours, un passage litt&eacute;raire, un texte de chanson, etc., notre g&egrave;n&eacute;rateur de texte choisira de fa&ccedil;on al&eacute;atoire les mots et les passages qui iront composer votre unique Lorem Ipsum.</p>\r\n<p>Soyez-vous original, mettez votre imagination &agrave; l\'&eacute;preuve&hellip; notre g&eacute;n&eacute;rateur de Lorem Ipsum vous surprendra. Prouvez-le maintenant!</p>', '2023-09-07 09:50:42', '2023-09-07 09:50:42', 1, 9);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `reset_token`, `role`, `created_at`, `is_active`) VALUES
(9, 'Mattéo', 'Groult', 'mattgroult10@gmail.com', 'matt14100', '$2y$10$FBngpyw73W9osk4UWaw0.udqu1jCxP5Lr21HaIFilni6BnRdN5oLy', NULL, 'ROLE_ADMIN', '2021-10-11 22:16:35', 1),
(10, 'Maureen', 'Lemaignent', 'lemaignent.m@gmail.com', 'mauleml', '$2y$10$yV/5/rdY6NVWv7wsxZjux.BCx6Hd7Ip2xbpqUzZjaJiCav3gv9O8a', NULL, 'ROLE_ADMIN', '2023-07-11 23:07:24', 1),
(12, 'Mattéo', 'Groult', 'matteo@agence-88.fr', 'agence88', '$2y$10$.5sYDrilDfuJSe6h.X4yq.FT/VLiZYLtaHEtGyPBobE6vCbDix1aG', NULL, 'ROLE_USER', '2023-06-27 00:49:19', 1),
(15, 'Xavier', 'test', 'xavier@xavier.fr', 'xavier1234', '$2y$10$ZNeWHATcK2ZBX7qeUqmTquTJWrvswKlkkW.htqfO4Pzob0i6cg4CS', NULL, 'ROLE_ADMIN', '2023-07-27 16:57:09', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_author_id` (`author_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
