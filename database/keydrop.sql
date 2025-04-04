-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 08:06 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `keydrop`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ads`
--

CREATE TABLE `ads` (
  `ad_token` varchar(256) NOT NULL,
  `id_user` int(11) NOT NULL,
  `ad_start` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bans`
--

CREATE TABLE `bans` (
  `id_ban` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `ban_date` datetime NOT NULL DEFAULT current_timestamp(),
  `ban_end` datetime NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cases`
--

CREATE TABLE `cases` (
  `id_case` int(11) NOT NULL,
  `case_name` varchar(50) NOT NULL,
  `case_value` decimal(8,2) NOT NULL,
  `image` text DEFAULT NULL,
  `for_ad` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`id_case`, `case_name`, `case_value`, `image`, `for_ad`, `hidden`) VALUES
(1, 'Chroma 2 Case', 4.18, 'Chroma_2_Case.webp', 0, 0),
(2, 'Chroma 3 Case', 6.48, 'Chroma_3_Case.webp', 0, 0),
(3, 'Chroma Case', 6.48, 'Chroma_Case.webp', 0, 0),
(4, 'Clutch Case', 10.00, 'Clutch_Case.webp', 0, 0),
(5, 'CS:GO Weapon Case', 10.00, 'CS_GO_Weapon_Case.webp', 0, 0),
(6, 'CS:GO Weapon Case 2', 10.00, 'CS_GO_Weapon_Case_2.webp', 0, 0),
(7, 'CS:GO Weapon Case 3', 10.00, 'CS_GO_Weapon_Case_3.webp', 0, 0),
(8, 'CS20 Case', 10.00, 'CS20_Case.webp', 0, 0),
(9, 'Danger Zone Case', 10.00, 'Danger_Zone_Case.webp', 0, 0),
(10, 'Dreams & Nightmares Case', 10.00, 'Dreams_&_Nightmares_Case.webp', 0, 0),
(11, 'Falchion Case', 10.00, 'Falchion_Case.webp', 0, 0),
(12, 'Fracture Case', 10.00, 'Fracture_Case.webp', 0, 0),
(13, 'Gallery Case', 10.00, 'Gallery_Case.webp', 0, 0),
(14, 'Gamma 2 Case', 10.00, 'Gamma_2_Case.webp', 0, 0),
(15, 'Gamma Case', 10.00, 'Gamma_Case.webp', 0, 0),
(16, 'Glove Case', 10.00, 'Glove_Case.webp', 0, 0),
(17, 'Horizon Case', 10.00, 'Horizon_Case.webp', 0, 0),
(18, 'Huntsman Weapon Case', 10.00, 'Huntsman_Weapon_Case.webp', 0, 0),
(19, 'Kilowatt Case', 10.00, 'Kilowatt_Case.webp', 0, 0),
(20, 'Operation Bravo Case', 10.00, 'Operation_Bravo_Case.webp', 0, 0),
(21, 'Operation Breakout Weapon Case', 10.00, 'Operation_Breakout_Weapon_Case.webp', 0, 0),
(22, 'Operation Broken Fang Case', 10.00, 'Operation_Broken_Fang_Case.webp', 0, 0),
(23, 'Operation Hydra Case', 10.00, 'Operation_Hydra_Case.webp', 0, 0),
(24, 'Operation Phoenix Weapon Case', 10.00, 'Operation_Phoenix_Weapon_Case.webp', 0, 0),
(25, 'Operation Riptide Case', 10.00, 'Operation_Riptide_Case.webp', 0, 0),
(26, 'Operation Vanguard Weapon Case', 10.00, 'Operation_Vanguard_Weapon_Case.webp', 0, 0),
(27, 'Operation Wildfire Case', 10.00, 'Operation_Wildfire_Case.webp', 0, 0),
(28, 'Prisma 2 Case', 10.00, 'Prisma_2_Case.webp', 0, 0),
(29, 'Prisma Case', 10.00, 'Prisma_Case.webp', 0, 0),
(30, 'Recoil Case', 10.00, 'Recoil_Case.webp', 0, 0),
(31, 'Revolution Case', 10.00, 'Revolution_Case.webp', 0, 0),
(32, 'Revolver Case', 10.00, 'Revolver_Case.webp', 0, 0),
(33, 'Shadow Case', 10.00, 'Shadow_Case.webp', 0, 0),
(34, 'Shattered Web Case', 10.00, 'Shattered_Web_Case.webp', 0, 0),
(35, 'Snakebite Case', 10.00, 'Snakebite_Case.webp', 0, 0),
(36, 'Spectrum 2 Case', 10.00, 'Spectrum_2_Case.webp', 0, 0),
(37, 'Spectrum Case', 10.00, 'Spectrum_Case.webp', 0, 0),
(38, 'Winter Offensive Weapon Case', 10.00, 'Winter_Offensive_Weapon_Case.webp', 0, 0),
(39, 'Szymon Linek\'s Case', 100.00, 'Szymon_Linek_Case.webp', 1, 0),
(40, 'Szucka\'s Coffin', 0.00, 'Szucka_s_Coffin', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `case_contents`
--

CREATE TABLE `case_contents` (
  `id` int(11) NOT NULL,
  `id_case` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `chance` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_contents`
--

INSERT INTO `case_contents` (`id`, `id_case`, `id_item`, `chance`) VALUES
(2, 39, 1, 0.20),
(3, 39, 2, 0.20),
(4, 39, 4, 99.00),
(5, 39, 6, 0.20),
(6, 39, 7, 0.20),
(7, 39, 8, 0.20),
(8, 40, 9, 16.00),
(9, 40, 10, 20.00),
(10, 40, 11, 18.00),
(11, 40, 12, 12.00),
(13, 40, 13, 3.00),
(14, 40, 14, 10.00),
(15, 40, 15, 14.00),
(16, 40, 17, 7.00),
(18, 1, 18, 0.25),
(19, 1, 19, 0.25),
(20, 1, 20, 2.50),
(21, 1, 21, 2.50),
(22, 1, 22, 2.50),
(23, 1, 23, 5.00),
(24, 1, 24, 5.00),
(25, 1, 25, 5.00),
(26, 1, 26, 5.00),
(27, 1, 27, 12.00),
(28, 1, 28, 12.00),
(29, 1, 29, 12.00),
(30, 1, 30, 12.00),
(31, 1, 4, 12.00),
(32, 1, 31, 12.00),
(35, 3, 10, 12.00),
(36, 3, 32, 12.00),
(37, 3, 33, 12.00),
(38, 3, 34, 12.00),
(39, 3, 35, 12.00),
(40, 3, 36, 12.00),
(41, 3, 37, 5.00),
(42, 3, 38, 5.00),
(43, 3, 39, 5.00),
(44, 3, 40, 3.00),
(45, 3, 41, 3.00),
(46, 3, 42, 3.00),
(47, 3, 43, 2.00),
(48, 3, 44, 2.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `changes`
--

CREATE TABLE `changes` (
  `id_change` varchar(256) NOT NULL,
  `id_user` int(11) NOT NULL,
  `old_username` varchar(25) DEFAULT NULL,
  `old_email` varchar(100) DEFAULT NULL,
  `old_password` varchar(255) DEFAULT NULL,
  `change_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `delete_requests`
--

CREATE TABLE `delete_requests` (
  `id_request` varchar(256) NOT NULL,
  `id_user` int(11) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `drops`
--

CREATE TABLE `drops` (
  `id_drop` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `id_case` int(11) DEFAULT NULL,
  `drop_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `items`
--

CREATE TABLE `items` (
  `id_item` int(11) NOT NULL,
  `item_name` varchar(50) NOT NULL,
  `id_item_type` int(11) NOT NULL,
  `id_item_rarity` int(11) NOT NULL,
  `id_item_wear` int(11) NOT NULL,
  `item_value` decimal(9,2) DEFAULT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id_item`, `item_name`, `id_item_type`, `id_item_rarity`, `id_item_wear`, `item_value`, `image`) VALUES
(1, 'M9 Bayonet | Doppler Ruby', 1, 8, 1, 14465.27, 'item_images/M9_Bayonet___Doppler_Ruby.png'),
(2, 'M9 Bayonet | Doppler Sapphire', 1, 8, 1, 15381.99, 'item_images/M9_Bayonet___Doppler_Sapphire.png'),
(4, 'Negev | Man-o\'-war', 8, 2, 3, 0.10, 'item_images/Negev___Man-o\'-war.png'),
(6, 'Karambit | Doppler Ruby', 1, 8, 1, 10888.90, 'item_images/Karambit___Doppler_Ruby.png'),
(7, 'M9 Bayonet | Doppler Black Pearl', 1, 8, 1, 10252.90, 'item_images/M9_Bayonet___Doppler_Black_Pearl.png'),
(8, 'Karambit | Doppler Sapphire', 1, 8, 1, 9379.62, 'item_images/Karambit___Doppler_Sapphire.png'),
(9, 'FAMAS | Macabre', 5, 3, 1, 4.00, 'item_images/FAMAS___Macabre.png'),
(10, 'Glock-18 | Catacombs', 3, 3, 1, 0.75, 'item_images/Glock-18___Catacombs.png'),
(11, 'P250 | Contamination', 3, 2, 1, 2.87, 'item_images/P250___Contamination.png'),
(12, 'Tec-9 | Toxic', 3, 3, 1, 10.61, 'item_images/Tec-9___Toxic.png'),
(13, 'USP-S | Kill Confirmed', 3, 6, 1, 165.39, 'item_images/USP-S___Kill_Confirmed.png'),
(14, 'MP7 | Skulls', 11, 3, 1, 23.22, 'item_images/MP7___Skulls.png'),
(15, 'UMP-45 | Bone Pile', 4, 3, 1, 10.44, 'item_images/UMP-45___Bone_Pile.png'),
(16, 'P90 | Shallow Grave', 4, 5, 1, 6.83, 'item_images/P90___Shallow_Grave.png'),
(17, 'XM-10 | Bone Machine', 7, 5, 1, 8.82, 'item_images/XM-10_Bone_Machine.png'),
(18, 'M4A1-S | Hyper Beast', 5, 6, 1, 131.47, 'item_images/M4A1-S___Hyper_Beast.png'),
(19, 'MAC-10 | Neon Rider', 4, 6, 1, 12.05, 'item_images/MAC-10___Neon_Rider.png'),
(20, 'Galil AR | Eco', 5, 5, 2, 47.29, 'item_images/Galil_AR___Eco.png'),
(21, 'Five-SeveN | Monkey Business', 3, 5, 2, 11.61, 'item_images/Five-SeveN___Monkey_Business.png'),
(22, 'FAMAS | Djinn', 5, 5, 1, 10.89, 'item_images/FAMAS___Djinn.png'),
(23, 'AWP | Worm God', 5, 4, 1, 10.89, 'item_images/AWP___Worm_God.png'),
(24, 'MAG-7 | Heat', 7, 4, 1, 3.39, 'item_images/MAG-7___Heat.png'),
(25, 'CZ75-Auto | Pole Position', 3, 4, 1, 2.56, 'item_images/CZ75-Auto___Pole_Position.png'),
(26, 'UMP-45 | Grand Prix', 4, 4, 3, 0.46, 'item_images/UMP-45___Grand_Prix.png'),
(27, 'AK-47 | Elite Build', 5, 3, 1, 6.08, 'item_images/AK-47___Elite_Build.png'),
(28, 'Desert Eagle | Bronze Deco', 3, 3, 1, 0.37, 'item_images/Desert_Eagle___Bronze_Deco.png'),
(29, 'P250 | Valence', 3, 3, 1, 0.79, 'item_images/P250___Valence.png'),
(30, 'MP7 | Armor Core', 4, 3, 1, 0.27, 'item_images/MP7___Armor_Core.png'),
(31, 'Sawed-Off | Origami', 7, 3, 1, 0.25, 'item_images/Sawed-Off___Origami.png'),
(32, 'M249 | Impact Drill', 8, 1, 1, 4.55, 'item_images/M249___Impact_Drill.png'),
(33, 'MP9 | Deadly Poison', 4, 3, 1, 2.51, 'item_images/MP9___Deadly_Poison.png'),
(34, 'SCAR-20 | Grotto', 6, 3, 1, 0.43, 'item_images/SCAR-20___Grotto.png'),
(35, 'XM1014 | Quicksilver', 7, 3, 1, 0.41, 'item_images/XM1014___Quicksilver.png'),
(36, 'Dual Berettas | Urban Shock', 3, 4, 1, 3.05, 'item_images/Dual_Berettas___Urban_Shock.png'),
(37, 'Desert Eagle | Naga', 3, 4, 1, 5.56, 'item_images/Desert_Eagle___Naga.png'),
(38, 'MAC-10 | Malachite', 4, 4, 1, 3.69, 'item_images/MAC-10___Malachite.png'),
(39, 'Sawed-Off | Serenity', 7, 4, 1, 3.24, 'item_images/Sawed-Off___Serenity.png'),
(40, 'AK-47 | Cartel', 5, 5, 1, 35.78, 'item_images/AK-47___Cartel.png'),
(41, 'M4A4 | 龍王 (Dragon King)', 5, 5, 1, 31.53, 'item_images/M4A4______(Dragon_King).png'),
(42, 'P250 | Muertos', 3, 5, 1, 9.52, 'item_images/P250___Muertos.png'),
(43, 'AWP | Man-o\'-war', 6, 6, 2, 40.60, 'item_images/AWP___Man-o\'-war.png'),
(44, 'Galil AR | Chatterbox', 5, 6, 3, 73.21, 'item_images/Galil_AR___Chatterbox.png');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_inventory`
--

CREATE TABLE `item_inventory` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_item` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_rarities`
--

CREATE TABLE `item_rarities` (
  `id_item_rarity` int(11) NOT NULL,
  `rarity_name` varchar(50) NOT NULL,
  `rarity_color` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_rarities`
--

INSERT INTO `item_rarities` (`id_item_rarity`, `rarity_name`, `rarity_color`) VALUES
(1, 'Consumer Grade', 'rgb(176, 195, 217)'),
(2, 'Industrial Grade', 'rgb(94, 152, 217)'),
(3, 'Mil-Spec', 'rgb(75, 105, 255)'),
(4, 'Restricted', 'rgb(136, 71, 211)'),
(5, 'Classified', 'rgb(211, 44, 230)'),
(6, 'Covert', 'rgb(235, 75, 75)'),
(7, 'Contraband', 'rgb(228, 174, 57)'),
(8, 'Extraordinary', 'rgb(255, 215, 0)');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_types`
--

CREATE TABLE `item_types` (
  `id_item_type` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_types`
--

INSERT INTO `item_types` (`id_item_type`, `type_name`) VALUES
(1, 'Knife'),
(2, 'Gloves'),
(3, 'Pistol'),
(4, 'SMG'),
(5, 'Rifle'),
(6, 'Sniper Rifle'),
(7, 'Shotgun'),
(8, 'Machine Gun'),
(9, 'Sticker'),
(10, 'Graffiti'),
(11, 'Patch'),
(12, 'Case'),
(13, 'Key'),
(14, 'Agent'),
(15, 'Music Kit');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_wears`
--

CREATE TABLE `item_wears` (
  `id_item_wear` int(11) NOT NULL,
  `wear_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_wears`
--

INSERT INTO `item_wears` (`id_item_wear`, `wear_name`) VALUES
(1, 'Factory New'),
(2, 'Minimal Wear'),
(3, 'Field-Tested'),
(4, 'Well-Worn'),
(5, 'Battle-Scarred');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logs`
--

CREATE TABLE `logs` (
  `id_log` int(11) NOT NULL,
  `log_type` varchar(100) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role_name` varchar(25) NOT NULL,
  `default_luck` decimal(7,2) NOT NULL DEFAULT 100.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `role_name`, `default_luck`) VALUES
(1, 'User', 100.00),
(2, 'Admin', 1000.00),
(3, 'Youtuber', 500.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `transactions`
--

CREATE TABLE `transactions` (
  `id_transaction` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `money` decimal(8,2) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `upgrades`
--

CREATE TABLE `upgrades` (
  `id_upgrade` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_item_old` int(11) NOT NULL,
  `id_item_new` int(11) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL DEFAULT 1,
  `money` decimal(12,2) NOT NULL DEFAULT 0.00,
  `luck` decimal(7,2) NOT NULL DEFAULT 100.00,
  `dedicated_ad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id_session` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `last_activity` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`ad_token`),
  ADD KEY `ads_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id_ban`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeksy dla tabeli `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id_case`);

--
-- Indeksy dla tabeli `case_contents`
--
ALTER TABLE `case_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_contents_ibfk_1` (`id_case`),
  ADD KEY `case_contents_ibfk_2` (`id_item`);

--
-- Indeksy dla tabeli `changes`
--
ALTER TABLE `changes`
  ADD PRIMARY KEY (`id_change`),
  ADD KEY `changes_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `delete_requests`
--
ALTER TABLE `delete_requests`
  ADD PRIMARY KEY (`id_request`),
  ADD KEY `changes_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `drops`
--
ALTER TABLE `drops`
  ADD PRIMARY KEY (`id_drop`),
  ADD KEY `drops_ibfk_1` (`id_user`),
  ADD KEY `drops_ibfk_2` (`id_item`),
  ADD KEY `drops_ibfk_3` (`id_case`);

--
-- Indeksy dla tabeli `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `items_ibfk_1` (`id_item_type`),
  ADD KEY `items_ibfk_2` (`id_item_rarity`),
  ADD KEY `items_ibfk_3` (`id_item_wear`);

--
-- Indeksy dla tabeli `item_inventory`
--
ALTER TABLE `item_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_inventory_ibfk_1` (`id_user`),
  ADD KEY `item_inventory_ibfk_2` (`id_item`);

--
-- Indeksy dla tabeli `item_rarities`
--
ALTER TABLE `item_rarities`
  ADD PRIMARY KEY (`id_item_rarity`),
  ADD UNIQUE KEY `rarity_name` (`rarity_name`);

--
-- Indeksy dla tabeli `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id_item_type`);

--
-- Indeksy dla tabeli `item_wears`
--
ALTER TABLE `item_wears`
  ADD PRIMARY KEY (`id_item_wear`);

--
-- Indeksy dla tabeli `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `logs_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeksy dla tabeli `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `transactions_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `upgrades`
--
ALTER TABLE `upgrades`
  ADD PRIMARY KEY (`id_upgrade`),
  ADD KEY `upgrades_ibfk_2` (`id_item_old`),
  ADD KEY `upgrades_ibfk_3` (`id_item_new`),
  ADD KEY `upgrades_ibfk_1` (`id_user`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `users_ibfk_1` (`id_role`);

--
-- Indeksy dla tabeli `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id_session`),
  ADD KEY `fk_sessions_users` (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bans`
--
ALTER TABLE `bans`
  MODIFY `id_ban` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id_case` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `case_contents`
--
ALTER TABLE `case_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `drops`
--
ALTER TABLE `drops`
  MODIFY `id_drop` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `item_inventory`
--
ALTER TABLE `item_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_rarities`
--
ALTER TABLE `item_rarities`
  MODIFY `id_item_rarity` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `id_item_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `item_wears`
--
ALTER TABLE `item_wears`
  MODIFY `id_item_wear` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `upgrades`
--
ALTER TABLE `upgrades`
  MODIFY `id_upgrade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bans`
--
ALTER TABLE `bans`
  ADD CONSTRAINT `bans_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `case_contents`
--
ALTER TABLE `case_contents`
  ADD CONSTRAINT `case_contents_ibfk_1` FOREIGN KEY (`id_case`) REFERENCES `cases` (`id_case`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_contents_ibfk_2` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`);

--
-- Constraints for table `changes`
--
ALTER TABLE `changes`
  ADD CONSTRAINT `changes_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `delete_requests`
--
ALTER TABLE `delete_requests`
  ADD CONSTRAINT `fk_users_delete_requests` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `drops`
--
ALTER TABLE `drops`
  ADD CONSTRAINT `drops_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_drops_cases` FOREIGN KEY (`id_case`) REFERENCES `cases` (`id_case`),
  ADD CONSTRAINT `fk_drops_items` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`id_item_type`) REFERENCES `item_types` (`id_item_type`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`id_item_rarity`) REFERENCES `item_rarities` (`id_item_rarity`),
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`id_item_wear`) REFERENCES `item_wears` (`id_item_wear`);

--
-- Constraints for table `item_inventory`
--
ALTER TABLE `item_inventory`
  ADD CONSTRAINT `item_inventory_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_inventory_ibfk_2` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `upgrades`
--
ALTER TABLE `upgrades`
  ADD CONSTRAINT `upgrades_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `upgrades_ibfk_2` FOREIGN KEY (`id_item_old`) REFERENCES `items` (`id_item`) ON DELETE CASCADE,
  ADD CONSTRAINT `upgrades_ibfk_3` FOREIGN KEY (`id_item_new`) REFERENCES `items` (`id_item`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_sessions_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
