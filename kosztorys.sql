-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 01 Sie 2018, 21:23
-- Wersja serwera: 10.1.21-MariaDB
-- Wersja PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `kosztorys`
--
  CREATE DATABASE IF NOT EXISTS `kosztorys`;
-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typy`
--

CREATE TABLE `typy` (
  `id` int(11) NOT NULL,
  `typ` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `id_uzytkownika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `typy`
--

INSERT INTO `typy` (`id`, `typ`, `id_uzytkownika`) VALUES
(2, 'Inne', 2),
(5, 'Urządzenia elektroniczne', 2),
(9, 'Przybory szkolne', 2),
(12, 'Jedzenie', 1),
(14, 'Jedzenie', 2),
(15, 'Słodycze', 2),
(23, 'Bielizna', 2),
(24, 'Ozdoby', 2),
(27, 'Podarunki', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `login` varchar(30) COLLATE utf8mb4_polish_ci NOT NULL,
  `haslo` varchar(64) COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `login`, `haslo`) VALUES
(1, 'user', '$2y$10$O2kDha313RBnhkKQqJQKZepfhkrIEoi38VHoBkqaKDzYv/h90.YLq'),
(2, 'michal', '$2y$10$IHgANexgSKE0Xj2YCg/3ku7V10hkLu3EF3T/qMHh7.C9skFUp7M7.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wydatki`
--

CREATE TABLE `wydatki` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `wydatek` text COLLATE utf8mb4_polish_ci NOT NULL,
  `typ` varchar(50) COLLATE utf8mb4_polish_ci NOT NULL,
  `cena` float NOT NULL,
  `sztuk` int(11) NOT NULL,
  `data_wydatku` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `wydatki`
--

INSERT INTO `wydatki` (`id`, `id_uzytkownika`, `wydatek`, `typ`, `cena`, `sztuk`, `data_wydatku`) VALUES
(11, 2, 'Kamerka internetowa', 'Urządzenia elektroniczne', 69.99, 1, '2018-07-24'),
(14, 1, 'Batonik', 'Jedzenie', 1.49, 2, '2018-07-24'),
(15, 2, 'Słuchawki', 'Urządzenia elektroniczne', 29.99, 1, '2018-07-25'),
(16, 2, 'Ładowarka', 'Urządzenia elektroniczne', 8.99, 1, '2018-07-25'),
(21, 2, 'Podarunek bratu', 'Podarunki', 5, 1, '2018-07-25'),
(22, 2, 'Podarunek bratu', 'Inne', 5, 1, '2018-07-25'),
(24, 2, 'Zeszyt', 'Przybory szkolne', 1.69, 1, '2018-07-25'),
(25, 2, 'Zeszyt', 'Przybory szkolne', 1.59, 2, '2018-07-25'),
(26, 2, 'Milky Way', 'Jedzenie', 0.89, 2, '2018-07-25'),
(27, 2, 'Spinka do włosów', 'Ozdoby', 0.99, 2, '2018-08-01');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `typy`
--
ALTER TABLE `typy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `typ` (`typ`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wydatki`
--
ALTER TABLE `wydatki`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `typ` (`typ`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `typy`
--
ALTER TABLE `typy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `wydatki`
--
ALTER TABLE `wydatki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
