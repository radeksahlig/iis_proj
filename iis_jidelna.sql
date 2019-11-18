-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `alergeny`;
CREATE TABLE `alergeny` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `alergeny` (`id`, `nazev`) VALUES
(1,	'Lepek'),
(2,	'Korýši'),
(3,	'Vejce'),
(4,	'Ryby'),
(5,	'Arašídy'),
(6,	'Sója'),
(7,	'Mléko'),
(8,	'Ořechy'),
(9,	'Celer'),
(10,	'Hořčice'),
(11,	'Sezam'),
(12,	'Oxid siřičitý'),
(13,	'Vlčí bob'),
(14,	'Měkkýší');

DROP TABLE IF EXISTS `alergeny_v_jidle`;
CREATE TABLE `alergeny_v_jidle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alergen` int(11) NOT NULL,
  `jidlo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alergen` (`alergen`),
  KEY `jidlo` (`jidlo`),
  CONSTRAINT `alergeny_v_jidle_ibfk_3` FOREIGN KEY (`alergen`) REFERENCES `alergeny` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alergeny_v_jidle_ibfk_4` FOREIGN KEY (`jidlo`) REFERENCES `jidlo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `alergeny_v_jidle` (`id`, `alergen`, `jidlo`) VALUES
(1,	1,	1),
(2,	3,	1),
(3,	7,	1),
(4,	9,	1),
(5,	10,	1),
(6,	11,	1),
(7,	3,	2),
(8,	7,	2),
(9,	9,	2),
(10,	10,	2),
(11,	11,	2),
(12,	1,	3),
(13,	3,	3),
(14,	7,	3),
(15,	9,	3),
(16,	12,	3),
(17,	13,	3),
(18,	1,	4),
(19,	3,	4),
(20,	6,	4),
(21,	7,	4),
(22,	9,	4),
(23,	10,	4),
(24,	13,	4),
(26,	1,	5),
(27,	3,	5),
(28,	6,	5),
(29,	7,	5),
(30,	9,	5),
(31,	13,	5),
(32,	1,	6),
(33,	9,	6),
(34,	1,	7),
(35,	3,	7),
(36,	6,	7),
(37,	7,	7),
(38,	9,	7),
(39,	10,	7),
(40,	13,	7),
(41,	1,	8),
(42,	3,	8),
(43,	6,	8),
(44,	7,	8),
(45,	9,	8),
(46,	13,	8);

DROP TABLE IF EXISTS `jidelna`;
CREATE TABLE `jidelna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `adresa` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `operator` int(11) DEFAULT NULL,
  `stav` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  KEY `operator` (`operator`),
  KEY `mesto` (`mesto`),
  CONSTRAINT `jidelna_ibfk_2` FOREIGN KEY (`operator`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jidelna_ibfk_3` FOREIGN KEY (`mesto`) REFERENCES `mesta` (`Nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `jidelna` (`id`, `nazev`, `mesto`, `adresa`, `operator`, `stav`) VALUES
(1,	'Netu',	'Brno',	'Jelení 1',	3,	CONV('1', 2, 10) + 0),
(2,	'Jídelna1',	'Brno',	'Jelení 1985',	3,	CONV('0', 2, 10) + 0);

DROP TABLE IF EXISTS `jidla_v_nabidce`;
CREATE TABLE `jidla_v_nabidce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nabidka` int(11) NOT NULL,
  `jidlo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jidlo` (`jidlo`),
  KEY `nabidka` (`nabidka`),
  CONSTRAINT `jidla_v_nabidce_ibfk_1` FOREIGN KEY (`jidlo`) REFERENCES `jidlo` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jidla_v_nabidce_ibfk_2` FOREIGN KEY (`nabidka`) REFERENCES `nabidka` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `jidlo`;
CREATE TABLE `jidlo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  `typ` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `ob` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `jidlo` (`id`, `nazev`, `popis`, `typ`, `ob`) VALUES
(1,	'Kotleta steak v orientálním koření s jasmínovou rýží',	'Krkovice vepřová 150g, olej, sůl, steak 7 pepřů-koření, máslo, kari koření, Solamyl-bramborový škrob, cibule, rýže jasmínová',	'hlavni',	NULL),
(2,	'Kuřecí steak s hranolkami',	'Kuřecí řízky, olej, grilovací koření, máslo, Solamyl-bramborový škrob, vejce, hranolky, sůl',	'hlavni',	NULL),
(3,	'Těstoviny s krůtím masem a sýrem',	'Krůtí řízky, těstoviny, cibule, eidam sýr, kečup, rajčatový protlak, olej, mouka hladká, anglická slanina, sůl, vývar slepičí, sušený česnek-plátky, bazalka-koření',	'hlavni',	NULL),
(4,	'Sýrová polévka',	'Mouka hladká, poesie sýrová, mr. žampiony, anglická slanina, sůl, vývar zeleninový, muškátový květ',	'polevka',	NULL),
(5,	'Kuřecí chilli medové paličky 4ks',	'Kuřecí štylka, st. rajčata krájená, rajčatový protlak, olej, sojová omáčka, česneková pasta, med, paprika mletá, petržel nať, chilli papričky čerstvé',	'hlavni',	NULL),
(6,	'Kuřecí nudličky na pivě',	'Kuřecí řízky, cibule, mr. francouzská směs zelenina, cuketa, slanina, olej, solamyl-bramborový škrob, paprika mletá, sůl, ocet krém balsamico/pyré/, pepř mletý, pilsner 0,33',	'hlavni',	NULL),
(7,	'Fazolová polévka s uzeninou',	'Fazole, párky, mouka hladká, olej, mrkev, cibule, česneková pasta, celer, petržel, sůl, šunka, polévkové koření, vývar zeleninový, gothaj salám, majoránka, pepř mletý',	'polevka',	NULL),
(8,	'Kapustová polévka',	'Mr. kapusta, brambory, mouka hladká, cibule tuk, česneková pasta, polévkové koření, sůl, vývar zeleninový',	'polevka',	NULL),
(18,	'aasdadsa',	'asdassd',	'hlavni',	'uuuu.jpg');

DROP TABLE IF EXISTS `mesta`;
CREATE TABLE `mesta` (
  `Nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`Nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `mesta` (`Nazev`) VALUES
('Brno'),
('Černá hora'),
('Dolní Dobrouč'),
('Hnátnice'),
('Letohrad'),
('Lipůvka'),
('Písečná'),
('Sebranice');

DROP TABLE IF EXISTS `mesta_dovozu`;
CREATE TABLE `mesta_dovozu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mesto` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `jidelna` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mesto` (`mesto`),
  KEY `jidelna` (`jidelna`),
  CONSTRAINT `mesta_dovozu_ibfk_2` FOREIGN KEY (`mesto`) REFERENCES `mesta` (`Nazev`),
  CONSTRAINT `mesta_dovozu_ibfk_3` FOREIGN KEY (`jidelna`) REFERENCES `jidelna` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `mesta_dovozu` (`id`, `mesto`, `jidelna`) VALUES
(1,	'Brno',	1),
(2,	'Lipůvka',	1),
(3,	'Sebranice',	1),
(4,	'Černá hora',	1);

DROP TABLE IF EXISTS `nabidka`;
CREATE TABLE `nabidka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jidelna` int(11) NOT NULL,
  `den` date DEFAULT NULL,
  `stav` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jidelna` (`jidelna`),
  CONSTRAINT `nabidka_ibfk_2` FOREIGN KEY (`jidelna`) REFERENCES `jidelna` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `objednana_jidla`;
CREATE TABLE `objednana_jidla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objednavka` int(11) NOT NULL,
  `jidlo` int(11) NOT NULL,
  `pocet` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `objednavka` (`objednavka`),
  KEY `jidlo` (`jidlo`),
  CONSTRAINT `objednana_jidla_ibfk_3` FOREIGN KEY (`objednavka`) REFERENCES `objednavka` (`id`) ON DELETE CASCADE,
  CONSTRAINT `objednana_jidla_ibfk_4` FOREIGN KEY (`jidlo`) REFERENCES `jidlo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `objednavka`;
CREATE TABLE `objednavka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `stav` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `ridic` int(11) DEFAULT NULL,
  `cena` int(11) NOT NULL,
  `cas_objednani` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `cas_dodani` timestamp NOT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `adresa` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `ridic` (`ridic`),
  KEY `mesto` (`mesto`),
  CONSTRAINT `objednavka_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  CONSTRAINT `objednavka_ibfk_2` FOREIGN KEY (`ridic`) REFERENCES `user` (`id`),
  CONSTRAINT `objednavka_ibfk_3` FOREIGN KEY (`mesto`) REFERENCES `mesta` (`Nazev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `prava`;
CREATE TABLE `prava` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `prava` (`id`, `nazev`, `popis`) VALUES
(1,	'Administrátor',	'Může vše'),
(2,	'Operátor',	'Spravuje jídelny a jejich nabídky, přiřazuje řidičům objednávky.'),
(3,	'Řidič',	'Dostává objednávky, vyzvedává objednaná jídla a rozváží je.'),
(4,	'Strávník',	'Může objednávat více jídel.'),
(5,	'Pleb',	'Nemůže nic');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `adresa` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `telefon` int(11) DEFAULT NULL,
  `prava` int(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`),
  KEY `prava` (`prava`),
  KEY `mesto` (`mesto`),
  CONSTRAINT `user_ibfk_4` FOREIGN KEY (`prava`) REFERENCES `prava` (`id`),
  CONSTRAINT `user_ibfk_5` FOREIGN KEY (`mesto`) REFERENCES `mesta` (`Nazev`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `jmeno`, `prijmeni`, `email`, `password`, `mesto`, `adresa`, `telefon`, `prava`) VALUES
(1,	'Admin',	'Admin',	'admin@jidelna.cz',	'$2y$10$Aklkl1KEk4tFEqX9apIWbuCq2SrPrCd5Qqe0yodi5.eX5WjBruuLy',	NULL,	NULL,	NULL,	1),
(2,	'Jan',	'Novák',	'novak@jidelna.cz',	'$2y$10$1aVc/0Us2dJ0XgiOz8jdMewFvCaJpV0ywwpKEQFPqW8bErm3GnoKK',	NULL,	NULL,	NULL,	4),
(3,	'Ladislav',	'Novák',	'LadNov@jidelna.cz',	'$2y$10$WXIRJZz0kTHqwOEYkE4U9Ocy/Rd0cp3SYDMBiGxMTBif7.7L9czX2',	NULL,	NULL,	NULL,	2),
(4,	'Michal',	'Jansa',	'Mich@seznam.cz',	'$2y$10$ozjtWuZcxX.liIsuMAW0xenUdcviSZWpmf.Y3g2bck6DM5OOuI2Pu',	NULL,	NULL,	NULL,	5),
(5,	'Radek',	'Pospíšil',	'pos@seznam.cz',	'$2y$10$.48n3E/4ydIUsKMbgULhqegl2UNLycsQ9Ltoal/toWmXVgcLfaeEi',	NULL,	NULL,	NULL,	5),
(6,	'Jaroslav',	'Jireš',	'jires@gmail.cz',	'$2y$10$UExpnYpmVQ/E8IkQyxFQ1OotJBHaaV3VeUfuDNmEfTavOcAsPOFci',	NULL,	NULL,	NULL,	5),
(7,	'Anotnína',	'Nováková',	'Annov@seznam.cz',	'$2y$10$mbBbkos8uGWoAhcUW9AdSO1I9TznGeJul6Z99ax1Y9FxlX6532H1u',	NULL,	NULL,	NULL,	5),
(8,	'Adam',	'Jíl',	'jil@gmail.com',	'$2y$10$oEjtsPZBjOV.0q.DeecEJ.YAdKbnUrqEbFtuuuyMgtwaCo6UcqTbe',	NULL,	NULL,	NULL,	5),
(9,	'Tereza',	'Mandlová',	'Tereza@email.cz',	'$2y$10$dYI1uvX1iHVVjke8hK9uqug8v//aOhGy0rrXb8S.8O4hGGRMIDYKC',	NULL,	NULL,	NULL,	5),
(10,	'Karina',	'Jedlá',	'kari@seznam.cz',	'$2y$10$rHB3Cb5vEaq1/.QC4J7SHukDsifF9juL.wVg3j2dN705KlYeQSQxC',	NULL,	NULL,	NULL,	5),
(11,	'Diana',	'Jelínková',	'Dijel@gmail.com',	'$2y$10$MumZEVqIIaFpqgW9yVRq3.L7vgLJdaN3jG0YUpCP8ovv4DcZKYkKS',	NULL,	NULL,	NULL,	5),
(12,	'Melichar',	'Pošeptný',	'Meli@centrum.cz',	'$2y$10$aiwIlqJmhRQeiVG1MFsdu.6lTsj2pIQ.DB3rZrqApuVCwLirXQmx.',	NULL,	NULL,	NULL,	5),
(13,	'Vilma',	'Špatná',	'Vili@centrum.cz',	'$2y$10$zRx4vPZNIiNLqsHwYESvuu1s6WiVqqY3lUCbeVed2SFal.IA3QRYi',	NULL,	NULL,	NULL,	5),
(14,	'Čestmír',	'Nový',	'cest@gmail.com',	'$2y$10$dBTYfqZwgf.S.YVK5gVQgO3KmcZLqb.tzXrZuvySdE2BGfgb2JKmu',	NULL,	NULL,	NULL,	5),
(15,	'Ctirad',	'Hruška',	'hrus@gmail.com',	'$2y$10$Zm4qqBtnLSJ4uJkeu37DnesrTc392byqTelWb5ZyTNEzm0R9c2kUK',	NULL,	NULL,	NULL,	5),
(16,	'Edita',	'Pražná',	'EditPra@gmail.com',	'$2y$10$FXn.lQChgnU/jO8YtpRYje2GjVKzw2yLlfTCFLfBBPfaG.DUY/qfa',	NULL,	NULL,	NULL,	5),
(17,	'Apolína',	'Poštolová',	'appos@gmail.com',	'$2y$10$bsmAGcqXbZDDHDdoakgjKuVKolxm8RKqXpHMWiG3vktHsd3Vu.MnK',	NULL,	NULL,	NULL,	5),
(18,	'Ladislav',	'Jmelí',	'ladi@gmail.com',	'$2y$10$BPoUSaayw70jVso0YKt5sOY7t5uvf5LCk30UOQObFbzgq513U6cCy',	NULL,	NULL,	NULL,	5),
(19,	'Jan',	'Perník',	'prna@seznam.cz',	'$2y$10$PH4kIBTwbS9PTnv/QKRgU.jifZZ3l43Y8yyD59cZhP9RURYnt6Mxq',	NULL,	NULL,	NULL,	5),
(20,	'Michal',	'Adam',	'mic@gmail.com',	'$2y$10$ilmDfjHyd6qXVy0xgUcglec6ujaKfyDQORcGKsV8io9gyS9MAcG1C',	NULL,	NULL,	NULL,	5),
(21,	'Melichar',	'Jansa',	'Milichar@jidelna.cz',	'$2y$10$YqUY4xzCV5Is4JyKPhHssunbHnHrcqg.VDDmL.RKvDP.fa3/YZxla',	NULL,	NULL,	NULL,	5);

-- 2019-11-02 18:33:52
