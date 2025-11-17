-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 10:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appkuis`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori_agent`
--

CREATE TABLE `kategori_agent` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_agent`
--

INSERT INTO `kategori_agent` (`id_kategori`, `nama_kategori`, `created_at`, `is_active`, `updated_at`, `deleted_at`) VALUES
(3, 'Agent Voice', '2025-09-09 02:29:09', 1, '2025-10-10 16:08:28', NULL),
(12, 'Agent Video Call', '2025-09-23 02:08:36', 1, '2025-09-28 19:30:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kuis`
--

CREATE TABLE `kuis` (
  `id_kuis` int(11) NOT NULL,
  `nama_kuis` varchar(150) NOT NULL,
  `topik` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `durasi_menit` int(11) DEFAULT 0,
  `nilai_minimum` int(11) NOT NULL,
  `batas_pengulangan` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` datetime DEFAULT NULL,
  `status` enum('draft','active','inactive') NOT NULL DEFAULT 'draft',
  `file_excel` varchar(255) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuis`
--

INSERT INTO `kuis` (`id_kuis`, `nama_kuis`, `topik`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `durasi_menit`, `nilai_minimum`, `batas_pengulangan`, `created_at`, `updated_at`, `published_at`, `status`, `file_excel`, `start_at`, `end_at`) VALUES
(136, 'Pertamina 1', 'BBm 1', '2025-10-10', '16:17:00', '16:26:00', 0, 80, 2, '2025-10-10 09:14:22', '2025-10-10 09:26:01', NULL, 'inactive', '1760087662_5e6b727cf555ad7047cc.xlsx', '2025-10-10 16:17:00', '2025-10-10 16:26:00'),
(137, 'Sistem Informasi', 'DDST', '2025-10-10', '16:44:00', '16:53:00', 0, 67, 2, '2025-10-10 09:43:20', '2025-10-15 17:49:55', NULL, 'inactive', '1760089400_73dffac02ac8a78ecc0a.xlsx', '2025-10-10 16:44:00', '2025-10-10 16:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `kuis_hasil`
--

CREATE TABLE `kuis_hasil` (
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kuis` int(11) NOT NULL,
  `jumlah_soal` int(11) NOT NULL,
  `jawaban` text DEFAULT NULL,
  `jawaban_benar` int(11) NOT NULL,
  `jawaban_salah` int(11) NOT NULL,
  `total_skor` int(11) NOT NULL,
  `status` enum('in_progress','finished','abandoned') DEFAULT 'in_progress',
  `tanggal_pengerjaan` timestamp NOT NULL DEFAULT current_timestamp(),
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `jumlah_pengerjaan` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuis_hasil`
--

INSERT INTO `kuis_hasil` (`id_hasil`, `id_user`, `id_kuis`, `jumlah_soal`, `jawaban`, `jawaban_benar`, `jawaban_salah`, `total_skor`, `status`, `tanggal_pengerjaan`, `started_at`, `finished_at`, `jumlah_pengerjaan`) VALUES
(100, 75, 136, 0, NULL, 1, 2, 33, 'finished', '2025-10-10 09:19:17', '2025-10-10 16:19:17', '2025-10-10 16:21:03', 1),
(101, 75, 136, 0, NULL, 3, 0, 100, 'finished', '2025-10-10 09:21:30', '2025-10-10 16:21:30', '2025-10-10 16:23:08', 2),
(102, 75, 137, 0, NULL, 0, 0, 0, 'in_progress', '2025-10-10 09:45:10', '2025-10-10 16:45:10', NULL, 1),
(103, 76, 137, 0, NULL, 0, 0, 0, 'in_progress', '2025-10-10 09:49:55', '2025-10-10 16:49:55', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kuis_jawaban`
--

CREATE TABLE `kuis_jawaban` (
  `id_jawaban` int(11) NOT NULL,
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_soal` int(11) NOT NULL,
  `jawaban_user` varchar(255) DEFAULT NULL,
  `jawaban_benar` varchar(255) DEFAULT NULL,
  `status` enum('Benar','Salah') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuis_jawaban`
--

INSERT INTO `kuis_jawaban` (`id_jawaban`, `id_hasil`, `id_soal`, `jawaban_user`, `jawaban_benar`, `status`, `created_at`) VALUES
(154, 100, 322, 'Sistem ERP (Enterprise Resource Planning)', 'Sistem ERP (Enterprise Resource Planning)', 'Benar', '2025-10-10 09:21:03'),
(155, 100, 323, 'Google Drive', 'Mouse', 'Salah', '2025-10-10 09:21:03'),
(156, 100, 324, 'Input', 'Informasi', 'Salah', '2025-10-10 09:21:03'),
(157, 101, 322, 'Sistem ERP (Enterprise Resource Planning)', 'Sistem ERP (Enterprise Resource Planning)', 'Benar', '2025-10-10 09:23:08'),
(158, 101, 323, 'Mouse', 'Mouse', 'Benar', '2025-10-10 09:23:08'),
(159, 101, 324, 'Informasi', 'Informasi', 'Benar', '2025-10-10 09:23:08');

-- --------------------------------------------------------

--
-- Table structure for table `kuis_kategori`
--

CREATE TABLE `kuis_kategori` (
  `id` int(11) NOT NULL,
  `id_kuis` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuis_kategori`
--

INSERT INTO `kuis_kategori` (`id`, `id_kuis`, `id_kategori`) VALUES
(420, 135, 3),
(421, 135, 12),
(423, 136, 3),
(424, 137, 3),
(425, 137, 12);

-- --------------------------------------------------------

--
-- Table structure for table `soal_kuis`
--

CREATE TABLE `soal_kuis` (
  `id_soal` int(11) NOT NULL,
  `id_kuis` int(11) NOT NULL,
  `soal` text NOT NULL,
  `pilihan_a` varchar(255) NOT NULL,
  `pilihan_b` varchar(255) NOT NULL,
  `pilihan_c` varchar(255) NOT NULL,
  `pilihan_d` varchar(255) NOT NULL,
  `pilihan_e` varchar(255) NOT NULL,
  `jawaban` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_kuis`
--

INSERT INTO `soal_kuis` (`id_soal`, `id_kuis`, `soal`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `pilihan_e`, `jawaban`) VALUES
(319, 135, 'Manakah yang termasuk contoh sistem informasi manajemen dalam sebuah perusahaan?', 'Google Chrome', 'Microsoft Word', 'Sistem ERP (Enterprise Resource Planning)', 'WhatsApp Messenger', 'WhatsApp Messenger', 'Sistem ERP (Enterprise Resource Planning)'),
(320, 135, 'Contoh perangkat keras adalah...', 'Word', 'Excel', 'Mouse', 'Google Drive', 'Google Drive', 'Mouse'),
(321, 135, 'Data yang sudah diolah menjadi..', 'File', 'Informasi', 'Input', 'Program', 'Program', 'Informasi'),
(322, 136, 'Manakah yang termasuk contoh sistem informasi manajemen dalam sebuah perusahaan?', 'Google Chrome', 'Microsoft Word', 'Sistem ERP (Enterprise Resource Planning)', 'WhatsApp Messenger', 'WhatsApp Messenger', 'Sistem ERP (Enterprise Resource Planning)'),
(323, 136, 'Contoh perangkat keras adalah...', 'Word', 'Excel', 'Mouse', 'Google Drive', 'Google Drive', 'Mouse'),
(324, 136, 'Data yang sudah diolah menjadi..', 'File', 'Informasi', 'Input', 'Program', 'Program', 'Informasi'),
(325, 137, 'Manakah yang termasuk contoh sistem informasi manajemen dalam sebuah perusahaan?', 'Google Chrome', 'Microsoft Word', 'Sistem ERP (Enterprise Resource Planning)', 'WhatsApp Messenger', 'WhatsApp Messenger', 'Sistem ERP (Enterprise Resource Planning)'),
(326, 137, 'Contoh perangkat keras adalah...', 'Word', 'Excel', 'Mouse', 'Google Drive', 'Google Drive', 'Mouse'),
(327, 137, 'Data yang sudah diolah menjadi..', 'File', 'Informasi', 'Input', 'Program', 'Program', 'Informasi');

-- --------------------------------------------------------

--
-- Table structure for table `team_leader`
--

CREATE TABLE `team_leader` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_leader`
--

INSERT INTO `team_leader` (`id`, `nama`, `created_at`, `is_active`, `updated_at`, `deleted_at`) VALUES
(1, 'Ahmad', '2025-09-12 18:30:54', 0, NULL, NULL),
(2, 'Widya', '2025-09-12 09:05:01', 0, NULL, NULL),
(3, 'Aura', '2025-09-13 12:47:21', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `nik` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','reviewer','agent') NOT NULL,
  `kategori_agent_id` int(11) DEFAULT NULL,
  `team_leader_id` int(11) DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 1,
  `last_password_change` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nik`, `nama`, `password`, `role`, `kategori_agent_id`, `team_leader_id`, `must_change_password`, `last_password_change`, `created_at`, `updated_at`, `is_active`) VALUES
(2, '22574018', 'Riska Permata', '$2y$10$M8.K4PDIj/AfNwCWNWvAC.zHmZH1q3YTi/aUK9gWonnvhoirgbizm', 'admin', NULL, NULL, 0, '2025-09-29 01:46:32', NULL, '2025-09-29 01:46:32', 1),
(72, '11223344', 'Cici', '$2y$10$mfurNOdDb5WWjdID0G0obOOQL67OAG9geMy.K4HX4ND0y2xGOlMf6', 'reviewer', NULL, NULL, 0, '2025-10-10 16:29:46', '2025-10-10 16:00:07', '2025-10-10 16:29:46', 1),
(74, '112233', 'nina', '$2y$10$DjdGG4XxaoX37XszhB53qeyuGhJc325W/T9FuIkGn3PL7Y20hsAbK', 'agent', 3, 1, 1, NULL, '2025-10-10 16:06:24', '2025-10-10 16:06:24', 1),
(75, '1122334455', 'dada', '$2y$10$vxAifLoaSl2woxbYlOzzH.Q918YnJP5Ftai/yw/goxzHb6K/Lmpm2', 'agent', 3, 3, 0, '2025-10-10 16:18:14', '2025-10-10 16:17:08', '2025-10-10 16:18:14', 1),
(76, '225760059', 'rara', '$2y$10$CSwv4HDIcpCRFsOFB9gFuu2Z7XVONTW4.WCjg5JfSbaWejZ81o00S', 'agent', 12, 3, 0, '2025-10-10 16:49:11', '2025-10-10 16:46:31', '2025-10-10 16:49:11', 1),
(77, '22576005', 'Caca', '$2y$10$cqSXWnXlbGCaFf0SyXA.ZO8RornQnqxp2iHJttKWkQ1xf7cYpwFa.', 'reviewer', NULL, NULL, 0, '2025-10-16 01:32:40', '2025-10-16 01:32:07', '2025-10-16 01:32:40', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori_agent`
--
ALTER TABLE `kategori_agent`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `kuis`
--
ALTER TABLE `kuis`
  ADD PRIMARY KEY (`id_kuis`);

--
-- Indexes for table `kuis_hasil`
--
ALTER TABLE `kuis_hasil`
  ADD PRIMARY KEY (`id_hasil`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kuis` (`id_kuis`);

--
-- Indexes for table `kuis_jawaban`
--
ALTER TABLE `kuis_jawaban`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD KEY `id_hasil` (`id_hasil`),
  ADD KEY `id_soal` (`id_soal`);

--
-- Indexes for table `kuis_kategori`
--
ALTER TABLE `kuis_kategori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kuis` (`id_kuis`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `soal_kuis`
--
ALTER TABLE `soal_kuis`
  ADD PRIMARY KEY (`id_soal`);

--
-- Indexes for table `team_leader`
--
ALTER TABLE `team_leader`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`nik`),
  ADD KEY `fk_users_kategori` (`kategori_agent_id`),
  ADD KEY `fk_users_teamleader` (`team_leader_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori_agent`
--
ALTER TABLE `kategori_agent`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kuis`
--
ALTER TABLE `kuis`
  MODIFY `id_kuis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `kuis_hasil`
--
ALTER TABLE `kuis_hasil`
  MODIFY `id_hasil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `kuis_jawaban`
--
ALTER TABLE `kuis_jawaban`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `kuis_kategori`
--
ALTER TABLE `kuis_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT for table `soal_kuis`
--
ALTER TABLE `soal_kuis`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=328;

--
-- AUTO_INCREMENT for table `team_leader`
--
ALTER TABLE `team_leader`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kuis_hasil`
--
ALTER TABLE `kuis_hasil`
  ADD CONSTRAINT `kuis_hasil_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kuis_hasil_ibfk_2` FOREIGN KEY (`id_kuis`) REFERENCES `kuis` (`id_kuis`) ON DELETE CASCADE;

--
-- Constraints for table `kuis_jawaban`
--
ALTER TABLE `kuis_jawaban`
  ADD CONSTRAINT `fk_hasil` FOREIGN KEY (`id_hasil`) REFERENCES `kuis_hasil` (`id_hasil`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_kategori` FOREIGN KEY (`kategori_agent_id`) REFERENCES `kategori_agent` (`id_kategori`),
  ADD CONSTRAINT `fk_users_teamleader` FOREIGN KEY (`team_leader_id`) REFERENCES `team_leader` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
