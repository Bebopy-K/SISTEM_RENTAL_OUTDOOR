-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2026 at 09:46 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oltp_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id_cabang` bigint UNSIGNED NOT NULL,
  `kode_cabang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id_cabang`, `kode_cabang`, `nama_kota`, `created_at`, `updated_at`) VALUES
(1, 'CBG_PLU', 'Palu', NULL, NULL),
(2, 'CBG_SIG', 'Sigi', NULL, NULL),
(3, 'CBG_MKS', 'Makassar', NULL, NULL),
(4, 'CBG_MDO', 'Manado', NULL, NULL),
(5, 'CBG_KDI', 'Kendari', NULL, NULL),
(6, 'CBG_GTO', 'Gorontalo', NULL, NULL),
(7, 'CBG_BTG', 'Bitung', NULL, NULL),
(8, 'CBG_TLT', 'Tolitoli', NULL, NULL),
(9, 'CBG_BUO', 'Buol', NULL, NULL),
(10, 'CBG_MRW', 'Morowali', NULL, NULL),
(11, 'CBG_BGI', 'Banggai', NULL, NULL),
(12, 'CBG_PAR', 'Parigi', NULL, NULL),
(13, 'CBG_DGL', 'Donggala', NULL, NULL),
(14, 'CBG_LWK', 'Luwuk', NULL, NULL),
(15, 'CBG_PSO', 'Poso', NULL, NULL),
(16, 'CBG_PLM', 'Palembang', NULL, NULL),
(17, 'CBG_PDG', 'Padang', NULL, NULL),
(18, 'CBG_BTM', 'Batam', NULL, NULL),
(19, 'CBG_PKU', 'Pekanbaru', NULL, NULL),
(20, 'CBG_BKS', 'Bekasi', NULL, NULL),
(21, 'CBG_SBY', 'Surabaya', NULL, NULL),
(22, 'CBG_BGR', 'Bogor', NULL, NULL),
(23, 'CBG_TGR', 'Tangerang', NULL, NULL),
(24, 'CBG_BDG', 'Bandung', NULL, NULL),
(25, 'CBG_SMG', 'Semarang', NULL, NULL),
(26, 'CBG_YOG', 'Yogyakarta', NULL, NULL),
(27, 'CBG_DPS', 'Denpasar', NULL, NULL),
(28, 'CBG_LOM', 'Mataram', NULL, NULL),
(29, 'CBG_KPG', 'Kupang', NULL, NULL),
(30, 'CBG_BJN', 'Banjarmasin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_05_25_101037_create_cabang_table', 1),
(3, '2026_05_25_101046_create_produk_table', 1),
(4, '2026_05_25_101054_create_users_table', 1),
(5, '2026_05_25_101102_create_transaksi_table', 1),
(6, '2026_06_04_123529_add_google_id_to_users_table', 2),
(7, '2025_08_25_015421_create_two_fa_codes_table', 3),
(8, '2025_08_25_015421_create_two_fa_logs_table', 3),
(9, '2025_08_25_015421_create_two_fa_settings_table', 3),
(10, '2026_06_05_103559_add_email_to_users_table', 4),
(11, '2026_06_05_133444_add_email_to_users_table', 5),
(12, '2026_06_05_134915_create_two_factor_codes_table', 6),
(13, '2026_06_17_100817_create_password_reset_tokens_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` bigint UNSIGNED NOT NULL,
  `kode_produk` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_sewa` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `kode_produk`, `nama_produk`, `harga_sewa`, `created_at`, `updated_at`) VALUES
(1, 'TND01', 'Tenda Kap 2-3 Non Flysheet', 25000, NULL, NULL),
(2, 'TND02', 'Tenda Kap 3-4 Non Flysheet', 35000, NULL, NULL),
(3, 'TND03', 'Tenda Kap 4-5 + Flysheet', 45000, NULL, NULL),
(4, 'TND04', 'Tenda Kap 6-7 + Flysheet', 60000, NULL, NULL),
(5, 'TND05', 'Tenda Kap 8-9 + Flysheet', 90000, NULL, NULL),
(6, 'TND06', 'Tenda Kap 10-12 + Flysheet', 120000, NULL, NULL),
(7, 'TND07', 'Tenda Kap 20 + Flysheet + 5 Matras', 250000, NULL, NULL),
(8, 'MAT01', 'Matras', 5000, NULL, NULL),
(9, 'SBT01', 'Sleeping Bag Tipis', 10000, NULL, NULL),
(10, 'SBB01', 'Sleeping Bag Tebal', 15000, NULL, NULL),
(11, 'ABD01', 'Air Bed', 25000, NULL, NULL),
(12, 'FLY01', 'Flysheet', 10000, NULL, NULL),
(13, 'KRL01', 'Keril', 20000, NULL, NULL),
(14, 'HMK01', 'Hamok', 10000, NULL, NULL),
(15, 'GTR01', 'Gitar', 45000, NULL, NULL),
(16, 'CJB01', 'Cajon Box', 45000, NULL, NULL),
(17, 'CJT01', 'Cajon Travel', 20000, NULL, NULL),
(18, 'LPT01', 'Lampu Tenda', 10000, NULL, NULL),
(19, 'HDL01', 'Head Lamp', 10000, NULL, NULL),
(20, 'MKS01', 'Meja + Kursi Set', 50000, NULL, NULL),
(21, 'NST01', 'Nesting', 15000, NULL, NULL),
(22, 'GAS01', 'Gas', 10000, NULL, NULL),
(23, 'KMP01', 'Kompor Kecil', 15000, NULL, NULL),
(24, 'KMP02', 'Kompor Besar', 30000, NULL, NULL),
(25, 'BBQ01', 'Barbeque Grill Set', 30000, NULL, NULL),
(26, 'CAM01', 'Canon 1100D', 80000, NULL, NULL),
(27, 'CAM02', 'Canon 660D', 100000, NULL, NULL),
(28, 'CAM03', 'Canon 700D', 120000, NULL, NULL),
(29, 'CAM04', 'Canon 60D', 130000, NULL, NULL),
(30, 'CAM05', 'Nikon D3000', 65000, NULL, NULL),
(31, 'CAM06', 'Xiaomi Yi Set', 50000, NULL, NULL),
(32, 'CAM07', 'Fujifilm X A10', 150000, NULL, NULL),
(33, 'TRP01', 'Tripod Stand', 20000, NULL, NULL),
(34, 'TRP02', 'Tripod Gorilla', 10000, NULL, NULL),
(35, 'SPK01', 'Speaker Travel', 10000, NULL, NULL),
(36, 'PKT01', 'Paket 1 (2-3 Tenda + Kompor Kecil + Nesting + Matras)', 55000, NULL, NULL),
(37, 'PKT02', 'Paket 2 (Tenda 4-5 + Flysheet + Kompor Besar + Nesting + 2 Matras)', 125000, NULL, NULL),
(38, 'PKT03', 'Paket Komplit (Tenda 6-7 + Flysheet + Kompor Besar + Nesting + Gas + 4 Matras + 4 Sleeping Bag Tebal + Lampu + Headlamp)', 250000, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `jumlah` int NOT NULL,
  `durasi` int NOT NULL,
  `cabang_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `total_harga` int NOT NULL,
  `denda` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synced` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `produk_id`, `jumlah`, `durasi`, `cabang_id`, `user_id`, `total_harga`, `denda`, `created_at`, `updated_at`, `synced`) VALUES
(201, '2026-06-01', 1, 2, 3, 1, 2, 300000, 0, NULL, NULL, 1),
(202, '2026-06-01', 12, 2, 1, 3, 4, 20000, 0, '2026-06-01 08:07:37', '2026-06-01 08:07:37', 1),
(203, '2026-06-01', 2, 2, 1, 3, 4, 70000, 0, '2026-06-01 08:07:37', '2026-06-01 08:07:37', 1),
(204, '2026-06-01', 2, 3, 2, 6, 7, 210000, 0, '2026-06-01 09:26:10', '2026-06-01 09:26:10', 1),
(205, '2026-06-01', 9, 4, 1, 6, 7, 40000, 20000, '2026-06-01 09:26:10', '2026-06-10 03:53:43', 1),
(211, '2026-06-22', 38, 1, 2, 1, 2, 500000, 0, '2026-06-22 00:21:18', '2026-06-22 00:21:18', 1),
(212, '2026-06-22', 32, 1, 2, 3, 4, 300000, 0, '2026-06-22 00:29:54', '2026-06-22 00:29:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `two_factor_codes`
--

INSERT INTO `two_factor_codes` (`id`, `user_id`, `code`, `expires_at`, `used`, `created_at`, `updated_at`) VALUES
(3, 1, '858095', '2026-06-21 10:22:17', 1, '2026-06-21 10:12:17', '2026-06-21 10:12:34'),
(4, 1, '954197', '2026-06-21 10:23:01', 1, '2026-06-21 10:13:01', '2026-06-21 10:13:52'),
(5, 2, '297364', '2026-06-21 10:26:25', 1, '2026-06-21 10:16:25', '2026-06-21 10:16:52'),
(6, 1, '624633', '2026-06-22 00:25:16', 1, '2026-06-22 00:15:16', '2026-06-22 00:17:13'),
(7, 2, '872387', '2026-06-22 00:29:12', 1, '2026-06-22 00:19:12', '2026-06-22 00:20:45'),
(8, 4, '569809', '2026-06-22 00:36:22', 1, '2026-06-22 00:26:22', '2026-06-22 00:29:15'),
(9, 1, '907848', '2026-06-22 00:40:29', 1, '2026-06-22 00:30:29', '2026-06-22 00:30:53'),
(11, 1, '454471', '2026-06-22 04:29:58', 1, '2026-06-22 04:19:58', '2026-06-22 04:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` bigint UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `role`, `cabang_id`, `remember_token`, `created_at`, `updated_at`, `google_id`) VALUES
(1, 'supercantik', 'siicantik404@gmail.com', '$2y$12$VsyC7/bN4aAa0P0zH1pt/erWB.crzaaj6E6oXJzj5YtDLinm1DQaa', 'superadmin', NULL, 'TkHfpkEsaMTbBxMqKQgJdfndpAB2DxrpRHjARxs43vrxXVKA635ZB9pUFX6T', NULL, '2026-06-21 09:16:41', '110441651385606747699'),
(2, 'ADM_PLU', 'belajarbalalala@gmail.com', '$2y$12$IpO/iUJz1M6FcJvLrEOSrOGQBfTlNXaCvZZgPkGAQCX0m.rlk.B5.', 'admin_cabang', 1, 'bOyRA22ADiMlpcHX7KV2Z83LK4ycuUgFnI53LZhxzcBFqijpM7xRKlM4UadS', NULL, '2026-06-20 07:32:08', '115192656765648728715'),
(3, 'ADM_SIG', NULL, '$2y$12$qdLGPvSd0So1taVKRrmG6.otcvonyLJMFKESksPx/Ij7.lmJYrGke', 'admin_cabang', 2, NULL, NULL, NULL, NULL),
(4, 'ADM_MKS', 'kimjju381@gmail.com', '$2y$12$fzfc5iFoYpXKImDB/6r.C.fEgp.uFNWOodVhoh3c5i56rBsa2C.kO', 'admin_cabang', 3, NULL, NULL, NULL, NULL),
(5, 'ADM_MDO', 'alliyainnayah@gmail.com', '$2y$12$a6dmzUS2C54SP6HtShzVtO9kjs5eULiHLPg49zYmoR/t/1hLNW./y', 'admin_cabang', 4, NULL, NULL, NULL, NULL),
(6, 'ADM_KDI', NULL, '$2y$12$eCAMGGcLFQJSig6M7ifWLOez.02u35ECYhbgxsXDk1pt/fYlTp.Be', 'admin_cabang', 5, NULL, NULL, NULL, NULL),
(7, 'ADM_GTO', NULL, '$2y$12$yh.RdkLjVLF4ReOvJVchp.ZUPjlgIpHPMIdSn4sUSGRv46XJfHt0C', 'admin_cabang', 6, NULL, NULL, NULL, NULL),
(8, 'ADM_BTG', NULL, '$2y$12$5Uw7ExY3v44RRvzRzXnHYu1UuBf.WAhmohjwsQG9jCeKLURdbqQke', 'admin_cabang', 7, NULL, NULL, NULL, NULL),
(9, 'ADM_TLT', NULL, '$2y$12$VM.Em0xfcfqwCDMGYp2BXuvqydtuLCI8gnOQCPUnjoZPXssJpIQOG', 'admin_cabang', 8, NULL, NULL, NULL, NULL),
(10, 'ADM_BUO', NULL, '$2y$12$eKoK38hoSrZELumgy4xZ1e/jqxUWJpF4prwtZ/LYGs5m7ibj4dcFK', 'admin_cabang', 9, NULL, NULL, NULL, NULL),
(11, 'ADM_MRW', NULL, '$2y$12$d2Huxt1B1MPzrn8Az.aTI.ASHFz.F2dVPmzr5G2nCSMphNy229p3q', 'admin_cabang', 10, NULL, NULL, NULL, NULL),
(12, 'ADM_BGI', NULL, '$2y$12$5suddAgBMLWcJlXHO6EeyuhHhc8wOX1MEA567UqEvoSPVSXGF/Oga', 'admin_cabang', 11, NULL, NULL, NULL, NULL),
(13, 'ADM_PAR', NULL, '$2y$12$KId7CgvOuilnH3tB5wY4BuE4XNyo2S5lPTLbt5ELE0KR1sPNivPFm', 'admin_cabang', 12, NULL, NULL, NULL, NULL),
(14, 'ADM_DGL', NULL, '$2y$12$ZVTxLLMn5nc6gXndvJ2JSudo31somXET1YsYzi4tMOf5hgDnxjxe.', 'admin_cabang', 13, NULL, NULL, NULL, NULL),
(15, 'ADM_LWK', NULL, '$2y$12$VVAoy8j6EE/DIOd0FF4pTe811WjS1rzExZ6MX3RlAzSkvZxsGJ6/W', 'admin_cabang', 14, NULL, NULL, NULL, NULL),
(16, 'ADM_PSO', NULL, '$2y$12$b2gBPUnbGXDtzx2py08uAuELtgMfyuVh8vTX85eaMNu2kQaaHCo3.', 'admin_cabang', 15, NULL, NULL, NULL, NULL),
(17, 'ADM_PLM', NULL, '$2y$12$EHJS4c90wbMVMuymJgytAu7WRwLRD/Xs4QW4mga9BzlfJLUWlPVVa', 'admin_cabang', 16, NULL, NULL, NULL, NULL),
(18, 'ADM_PDG', NULL, '$2y$12$16W36B4Ug3cCQR1nDuQftuGtX0bP.pTzDXo3ie43wZxetvgTKjd8a', 'admin_cabang', 17, NULL, NULL, NULL, NULL),
(19, 'ADM_BTM', NULL, '$2y$12$upN5A7RfZw9zl8dEScWh5Od/V5W1BIiJbBWm6U/LdAmADF4xQJe/m', 'admin_cabang', 18, NULL, NULL, NULL, NULL),
(20, 'ADM_PKU', NULL, '$2y$12$xPAQlKV8VllBffqfc775dugWWe/y0eJYmoUjADVbSqIuE7pp.8nV.', 'admin_cabang', 19, NULL, NULL, NULL, NULL),
(21, 'ADM_BKS', NULL, '$2y$12$qE.8T3M8bkhmbW48gMs4AO9XnTqIubx7IzVxsLd3Ll7eYcBM1vQaK', 'admin_cabang', 20, NULL, NULL, NULL, NULL),
(22, 'ADM_SBY', NULL, '$2y$12$9JQ8qhD6CinnAac7LMMpD.tpT5Bsu2TjCsqtGzF3Uxr5OPMnzj8NO', 'admin_cabang', 21, NULL, NULL, NULL, NULL),
(23, 'ADM_BGR', NULL, '$2y$12$HiWMxuZB.92E8xnFfswbuOFk07UlASg0xRniuTuLPlZ0ROmZrWwDS', 'admin_cabang', 22, NULL, NULL, NULL, NULL),
(24, 'ADM_TGR', NULL, '$2y$12$O7Ht6ek/jra0DC/TfesX3ulPNw7na09UCdFU3VJwb7iQ46riE9IQ2', 'admin_cabang', 23, NULL, NULL, NULL, NULL),
(25, 'ADM_BDG', NULL, '$2y$12$fJ1W2Y3LF4l3ICanGtYvCuT2hFpKFZOq34BrdbbdOSZMvKJgIBmDu', 'admin_cabang', 24, NULL, NULL, NULL, NULL),
(26, 'ADM_SMG', NULL, '$2y$12$vdhpA9zSQ3mJxH9ALRdSnuXb.vyPsf.HVQRHPA38qOnN9WMvG0H7a', 'admin_cabang', 25, NULL, NULL, NULL, NULL),
(27, 'ADM_YOG', NULL, '$2y$12$oMNIiX9XQrxTBl0yaZAI..yATdcYYCKR9RWRCocTPODgDW/xUYhYS', 'admin_cabang', 26, NULL, NULL, NULL, NULL),
(28, 'ADM_DPS', NULL, '$2y$12$Saez/wvw/WXuIZJipIOgkO0CLg8DQVv0V0SUlr2VGrrMP6LmRKdau', 'admin_cabang', 27, NULL, NULL, NULL, NULL),
(29, 'ADM_LOM', NULL, '$2y$12$nGZ3zWJrW7V/AaryKtKPieLDR0qBmUgplxiTcmxTDAoYUeQ7S/6HG', 'admin_cabang', 28, NULL, NULL, NULL, NULL),
(30, 'ADM_KPG', NULL, '$2y$12$f.tbLaCaMTwGbEf9N6q4nelvCQkmMSR9NeuIjKK9QqLX9e6M3jRBS', 'admin_cabang', 29, NULL, NULL, NULL, NULL),
(31, 'ADM_BJN', NULL, '$2y$12$L9Bm7yFdqUFpfCkOLkLTkeEtaPUTd/5ASz6TPZiGIgJ2yzBfItzYK', 'admin_cabang', 30, NULL, NULL, NULL, NULL),
(32, 'innayahalliya@gmail.com', 'innayahalliya@gmail.com', '$2y$12$Tpmt4QovedlbXGWpXpCe1.QnLws38wNTU8GZp7cTRRs8wwb6Wa.7y', 'admin_cabang', NULL, 'HkjWm7DHjcvlS7i0A1v0YwUL9Tg2LgclUIZgUS9Q1Aunoiu0nnPcWwC6eh6k', '2026-06-05 06:56:00', '2026-06-05 06:56:00', '106122765931872820521'),
(34, 'superadmin', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id_cabang`),
  ADD UNIQUE KEY `cabang_kode_cabang_unique` (`kode_cabang`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `transaksi_produk_id_foreign` (`produk_id`),
  ADD KEY `transaksi_cabang_id_foreign` (`cabang_id`),
  ADD KEY `transaksi_user_id_foreign` (`user_id`);

--
-- Indexes for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `two_factor_codes_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_cabang_id_foreign` (`cabang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id_cabang` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id_cabang`),
  ADD CONSTRAINT `transaksi_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `transaksi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id_cabang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
