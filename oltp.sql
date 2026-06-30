-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2026 at 07:27 PM
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

--
-- Dumping data for table `migrations`
--

INSERT IGNORE INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2026_05_25_101037_create_cabang_table', 2),
(3, '2026_05_25_101046_create_produk_table', 3),
(4, '2026_05_25_101054_create_users_table', 4),
(5, '2026_05_25_101102_create_transaksi_table', 5),
(6, '2026_06_04_123529_add_google_id_to_users_table', 6),
(7, '2025_08_25_015421_create_two_fa_codes_table', 7),
(8, '2025_08_25_015421_create_two_fa_logs_table', 8),
(9, '2025_08_25_015421_create_two_fa_settings_table', 9),
(10, '2026_06_05_103559_add_email_to_users_table', 10),
(12, '2026_06_05_134915_create_two_factor_codes_table', 11),
(13, '2026_06_17_100817_create_password_reset_tokens_table', 12);

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

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `produk_id`, `jumlah`, `durasi`, `cabang_id`, `user_id`, `total_harga`, `denda`, `created_at`, `updated_at`) VALUES
(201, '2026-06-01', 1, 2, 3, 1, 2, 300000, 0, NULL, NULL),
(202, '2026-06-01', 12, 2, 1, 3, 4, 20000, 0, '2026-06-01 08:07:37', '2026-06-01 08:07:37'),
(203, '2026-06-01', 2, 2, 1, 3, 4, 70000, 0, '2026-06-01 08:07:37', '2026-06-01 08:07:37'),
(204, '2026-06-01', 2, 3, 2, 6, 7, 210000, 0, '2026-06-01 09:26:10', '2026-06-01 09:26:10'),
(205, '2026-06-01', 9, 4, 1, 6, 7, 40000, 20000, '2026-06-01 09:26:10', '2026-06-10 03:53:43'),
(211, '2026-06-22', 38, 1, 2, 1, 2, 500000, 0, '2026-06-22 00:21:18', '2026-06-22 00:21:18'),
(212, '2026-06-22', 32, 1, 2, 3, 4, 300000, 0, '2026-06-22 00:29:54', '2026-06-22 00:29:54');

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
(10, 'ADM_BUO', NULL, '$2y$12$eKoK38hoSrZELumgy4xZ1e/jqxUWJpF4prwtZ/LYGs5m7ibj4dcFK', 'admin_cabang', 9, NULL, NULL