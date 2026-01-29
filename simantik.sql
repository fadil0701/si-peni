-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for simantik
CREATE DATABASE IF NOT EXISTS `simantik` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `simantik`;

-- Dumping structure for table simantik.approval_flow_definition
CREATE TABLE IF NOT EXISTS `approval_flow_definition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `modul_approval` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `step_order` int NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `nama_step` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('MENUNGGU','DIKETAHUI','DIVERIFIKASI','DISETUJUI','DITOLAK','DIDISPOSISIKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU',
  `status_text` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `can_reject` tinyint(1) NOT NULL DEFAULT '0',
  `can_approve` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_flow_unique` (`modul_approval`,`step_order`,`role_id`),
  KEY `approval_flow_definition_role_id_foreign` (`role_id`),
  CONSTRAINT `approval_flow_definition_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.approval_flow_definition: ~8 rows (approximately)
REPLACE INTO `approval_flow_definition` (`id`, `modul_approval`, `step_order`, `role_id`, `nama_step`, `status`, `status_text`, `is_required`, `can_reject`, `can_approve`, `created_at`, `updated_at`) VALUES
	(1, 'PERMINTAAN_BARANG', 1, 4, 'Diajukan', 'MENUNGGU', 'Permintaan telah diajukan oleh pegawai', 1, 0, 0, NULL, NULL),
	(2, 'PERMINTAAN_BARANG', 2, 5, 'Diketahui Unit', 'MENUNGGU', 'Kepala Unit telah mengetahui permintaan', 1, 0, 0, NULL, NULL),
	(3, 'PERMINTAAN_BARANG', 3, 6, 'Diketahui TU', 'MENUNGGU', 'Kasubbag TU telah memverifikasi administrasi permintaan', 1, 1, 0, NULL, NULL),
	(4, 'PERMINTAAN_BARANG', 4, 7, 'Disetujui Pimpinan', 'MENUNGGU', 'Kepala Pusat telah menyetujui permintaan', 1, 1, 1, NULL, NULL),
	(5, 'PERMINTAAN_BARANG', 5, 2, 'Didisposisikan', 'MENUNGGU', 'Permintaan telah didisposisikan ke Admin Gudang / Unit Terkait', 0, 0, 0, NULL, NULL),
	(6, 'PERMINTAAN_BARANG', 6, 2, 'Diproses', 'MENUNGGU', 'Admin Gudang sedang memproses distribusi barang', 1, 0, 0, NULL, NULL),
	(8, 'PERMINTAAN_BARANG', 5, 11, 'Didisposisikan - ASET', 'MENUNGGU', 'Permintaan telah didisposisikan ke Admin Gudang ASET', 1, 0, 0, '2026-01-22 03:22:59', '2026-01-22 03:22:59'),
	(9, 'PERMINTAAN_BARANG', 5, 13, 'Didisposisikan - FARMASI', 'MENUNGGU', 'Permintaan telah didisposisikan ke Admin Gudang FARMASI', 1, 0, 0, '2026-01-22 03:46:31', '2026-01-22 03:46:31');

-- Dumping structure for table simantik.approval_log
CREATE TABLE IF NOT EXISTS `approval_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `modul_approval` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_referensi` bigint unsigned NOT NULL,
  `id_approval_flow` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned NOT NULL,
  `status` enum('MENUNGGU','DIKETAHUI','DIVERIFIKASI','DISETUJUI','DITOLAK','DIDISPOSISIKAN','DIPROSES') COLLATE utf8mb4_unicode_ci DEFAULT 'MENUNGGU',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_log_id_approval_flow_foreign` (`id_approval_flow`),
  KEY `approval_log_user_id_foreign` (`user_id`),
  KEY `approval_log_role_id_foreign` (`role_id`),
  KEY `approval_log_modul_approval_id_referensi_index` (`modul_approval`,`id_referensi`),
  CONSTRAINT `approval_log_id_approval_flow_foreign` FOREIGN KEY (`id_approval_flow`) REFERENCES `approval_flow_definition` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_log_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approval_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.approval_log: ~0 rows (approximately)

-- Dumping structure for table simantik.approval_permintaan
CREATE TABLE IF NOT EXISTS `approval_permintaan` (
  `id_approval` bigint unsigned NOT NULL AUTO_INCREMENT,
  `modul_approval` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_referensi` bigint unsigned NOT NULL,
  `id_approver` bigint unsigned NOT NULL,
  `status_approval` enum('MENUNGGU','DISETUJUI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_approval`),
  KEY `approval_permintaan_id_approver_foreign` (`id_approver`),
  CONSTRAINT `approval_permintaan_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.approval_permintaan: ~0 rows (approximately)

-- Dumping structure for table simantik.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.cache: ~0 rows (approximately)

-- Dumping structure for table simantik.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.cache_locks: ~0 rows (approximately)

-- Dumping structure for table simantik.data_inventory
CREATE TABLE IF NOT EXISTS `data_inventory` (
  `id_inventory` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_data_barang` bigint unsigned NOT NULL,
  `id_gudang` bigint unsigned NOT NULL,
  `id_anggaran` bigint unsigned NOT NULL,
  `id_sub_kegiatan` bigint unsigned NOT NULL,
  `jenis_inventory` enum('ASET','PERSEDIAAN','FARMASI') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_anggaran` int NOT NULL,
  `qty_input` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `merk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spesifikasi` text COLLATE utf8mb4_unicode_ci,
  `tahun_produksi` int DEFAULT NULL,
  `nama_penyedia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_seri` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_batch` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_kedaluwarsa` date DEFAULT NULL,
  `status_inventory` enum('DRAFT','AKTIF','DISTRIBUSI','HABIS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `upload_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_qr_code` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_inventory`),
  KEY `data_inventory_id_data_barang_foreign` (`id_data_barang`),
  KEY `data_inventory_id_gudang_foreign` (`id_gudang`),
  KEY `data_inventory_id_anggaran_foreign` (`id_anggaran`),
  KEY `data_inventory_id_sub_kegiatan_foreign` (`id_sub_kegiatan`),
  KEY `data_inventory_id_satuan_foreign` (`id_satuan`),
  KEY `data_inventory_created_by_foreign` (`created_by`),
  CONSTRAINT `data_inventory_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `data_inventory_id_anggaran_foreign` FOREIGN KEY (`id_anggaran`) REFERENCES `master_sumber_anggaran` (`id_anggaran`) ON DELETE CASCADE,
  CONSTRAINT `data_inventory_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `data_inventory_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `data_inventory_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE,
  CONSTRAINT `data_inventory_id_sub_kegiatan_foreign` FOREIGN KEY (`id_sub_kegiatan`) REFERENCES `master_sub_kegiatan` (`id_sub_kegiatan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.data_inventory: ~5 rows (approximately)
REPLACE INTO `data_inventory` (`id_inventory`, `id_data_barang`, `id_gudang`, `id_anggaran`, `id_sub_kegiatan`, `jenis_inventory`, `tahun_anggaran`, `qty_input`, `id_satuan`, `harga_satuan`, `total_harga`, `merk`, `tipe`, `spesifikasi`, `tahun_produksi`, `nama_penyedia`, `no_seri`, `no_batch`, `tanggal_kedaluwarsa`, `status_inventory`, `upload_foto`, `upload_dokumen`, `auto_qr_code`, `created_by`, `created_at`, `updated_at`) VALUES
	(2, 2, 3, 1, 1, 'FARMASI', 2026, 90.00, 2, 25000.00, 2250000.00, 'Panadol', NULL, 'Ga tau', 2026, 'PT Kimia Aja', NULL, '1205AS2030', '2027-01-19', 'AKTIF', 'foto-inventory/UuAoyEmeXqGqx6fwb8WCe5KTliXcbvR06oGZUgyb.png', NULL, NULL, 1, '2026-01-19 08:06:30', '2026-01-22 04:05:37'),
	(4, 2, 5, 1, 1, 'FARMASI', 2026, 10.00, 2, 25000.00, 250000.00, 'Panadol', NULL, 'Ga tau', 2026, 'PT Kimia Aja', NULL, '1205AS2030', '2027-01-19', 'AKTIF', 'foto-inventory/UuAoyEmeXqGqx6fwb8WCe5KTliXcbvR06oGZUgyb.png', NULL, NULL, 1, '2026-01-22 04:05:37', '2026-01-22 04:05:37'),
	(5, 3, 3, 2, 1, 'FARMASI', 2026, 190.00, 2, 50000.00, 9500000.00, 'Tes', NULL, NULL, 2026, 'PT Tes', NULL, '092741239', '2026-12-31', 'AKTIF', 'foto-inventory/RY9oxTZD0W1LSJlS51OLhZhIPRS7twwrTMxZgjL2.png', NULL, NULL, 1, '2026-01-22 06:52:52', '2026-01-23 08:34:43'),
	(6, 2, 3, 1, 1, 'FARMASI', 2026, 50.00, 2, 25000.00, 1250000.00, 'Panadol', NULL, NULL, 2026, 'PT Tes 2', NULL, '8094-190294-1293', '2027-12-31', 'AKTIF', 'foto-inventory/HAJcA43daxYzPnaCefXz93JEwqtbTY0TT4DuhHJN.png', NULL, NULL, 1, '2026-01-22 06:59:40', '2026-01-22 06:59:40'),
	(7, 3, 8, 2, 1, 'FARMASI', 2026, 10.00, 2, 50000.00, 500000.00, 'Tes', NULL, NULL, 2026, 'PT Tes', NULL, '092741239', '2026-12-31', 'AKTIF', 'foto-inventory/RY9oxTZD0W1LSJlS51OLhZhIPRS7twwrTMxZgjL2.png', NULL, NULL, 1, '2026-01-23 08:34:43', '2026-01-23 08:34:43');

-- Dumping structure for table simantik.data_stock
CREATE TABLE IF NOT EXISTS `data_stock` (
  `id_stock` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_data_barang` bigint unsigned NOT NULL,
  `id_gudang` bigint unsigned NOT NULL,
  `qty_awal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `qty_masuk` decimal(15,2) NOT NULL DEFAULT '0.00',
  `qty_keluar` decimal(15,2) NOT NULL DEFAULT '0.00',
  `qty_akhir` decimal(15,2) NOT NULL DEFAULT '0.00',
  `id_satuan` bigint unsigned NOT NULL,
  `last_updated` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_stock`),
  UNIQUE KEY `data_stock_id_data_barang_id_gudang_unique` (`id_data_barang`,`id_gudang`),
  KEY `data_stock_id_gudang_foreign` (`id_gudang`),
  KEY `data_stock_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `data_stock_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `data_stock_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `data_stock_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.data_stock: ~6 rows (approximately)
REPLACE INTO `data_stock` (`id_stock`, `id_data_barang`, `id_gudang`, `qty_awal`, `qty_masuk`, `qty_keluar`, `qty_akhir`, `id_satuan`, `last_updated`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 0.00, 17.00, 0.00, 17.00, 1, '2026-01-29 04:13:10', '2026-01-19 07:39:29', '2026-01-29 04:13:10'),
	(2, 2, 3, 0.00, 140.00, 0.00, 140.00, 2, '2026-01-29 04:30:10', '2026-01-19 08:06:30', '2026-01-29 04:30:10'),
	(3, 1, 5, 0.00, 0.00, 0.00, 0.00, 1, '2026-01-23 08:18:32', '2026-01-20 07:02:48', '2026-01-22 04:05:37'),
	(4, 2, 5, 0.00, 10.00, 0.00, 10.00, 2, '2026-01-29 04:30:10', '2026-01-22 04:05:37', '2026-01-29 04:30:10'),
	(5, 3, 3, 0.00, 190.00, 0.00, 190.00, 2, '2026-01-29 04:30:10', '2026-01-22 06:52:52', '2026-01-29 04:30:10'),
	(6, 3, 8, 0.00, 10.00, 0.00, 10.00, 2, '2026-01-29 04:30:10', '2026-01-23 08:34:43', '2026-01-29 04:30:10');

-- Dumping structure for table simantik.data_stock_opname
CREATE TABLE IF NOT EXISTS `data_stock_opname` (
  `id_opname` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_data_barang` bigint unsigned NOT NULL,
  `id_gudang` bigint unsigned NOT NULL,
  `tanggal_opname` date NOT NULL,
  `qty_sistem` decimal(15,2) NOT NULL,
  `qty_fisik` decimal(15,2) NOT NULL,
  `selisih` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `id_petugas` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_opname`),
  KEY `data_stock_opname_id_data_barang_foreign` (`id_data_barang`),
  KEY `data_stock_opname_id_gudang_foreign` (`id_gudang`),
  KEY `data_stock_opname_id_petugas_foreign` (`id_petugas`),
  CONSTRAINT `data_stock_opname_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `data_stock_opname_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `data_stock_opname_id_petugas_foreign` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.data_stock_opname: ~0 rows (approximately)

-- Dumping structure for table simantik.detail_distribusi
CREATE TABLE IF NOT EXISTS `detail_distribusi` (
  `id_detail_distribusi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_distribusi` bigint unsigned NOT NULL,
  `id_inventory` bigint unsigned NOT NULL,
  `qty_distribusi` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_distribusi`),
  KEY `detail_distribusi_id_distribusi_foreign` (`id_distribusi`),
  KEY `detail_distribusi_id_inventory_foreign` (`id_inventory`),
  KEY `detail_distribusi_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `detail_distribusi_id_distribusi_foreign` FOREIGN KEY (`id_distribusi`) REFERENCES `transaksi_distribusi` (`id_distribusi`) ON DELETE CASCADE,
  CONSTRAINT `detail_distribusi_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `detail_distribusi_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.detail_distribusi: ~0 rows (approximately)

-- Dumping structure for table simantik.detail_pemakaian_barang
CREATE TABLE IF NOT EXISTS `detail_pemakaian_barang` (
  `id_detail_pemakaian` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pemakaian` bigint unsigned NOT NULL,
  `id_inventory` bigint unsigned NOT NULL,
  `qty_pemakaian` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `alasan_pemakaian_item` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_pemakaian`),
  KEY `detail_pemakaian_barang_id_pemakaian_foreign` (`id_pemakaian`),
  KEY `detail_pemakaian_barang_id_inventory_foreign` (`id_inventory`),
  KEY `detail_pemakaian_barang_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `detail_pemakaian_barang_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `detail_pemakaian_barang_id_pemakaian_foreign` FOREIGN KEY (`id_pemakaian`) REFERENCES `pemakaian_barang` (`id_pemakaian`) ON DELETE CASCADE,
  CONSTRAINT `detail_pemakaian_barang_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.detail_pemakaian_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.detail_penerimaan_barang
CREATE TABLE IF NOT EXISTS `detail_penerimaan_barang` (
  `id_detail_penerimaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_penerimaan` bigint unsigned NOT NULL,
  `id_inventory` bigint unsigned NOT NULL,
  `qty_diterima` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_penerimaan`),
  KEY `detail_penerimaan_barang_id_penerimaan_foreign` (`id_penerimaan`),
  KEY `detail_penerimaan_barang_id_inventory_foreign` (`id_inventory`),
  KEY `detail_penerimaan_barang_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `detail_penerimaan_barang_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `detail_penerimaan_barang_id_penerimaan_foreign` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_barang` (`id_penerimaan`) ON DELETE CASCADE,
  CONSTRAINT `detail_penerimaan_barang_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.detail_penerimaan_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.detail_permintaan_barang
CREATE TABLE IF NOT EXISTS `detail_permintaan_barang` (
  `id_detail_permintaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_permintaan` bigint unsigned NOT NULL,
  `id_data_barang` bigint unsigned NOT NULL,
  `qty_diminta` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_permintaan`),
  KEY `detail_permintaan_barang_id_permintaan_foreign` (`id_permintaan`),
  KEY `detail_permintaan_barang_id_data_barang_foreign` (`id_data_barang`),
  KEY `detail_permintaan_barang_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `detail_permintaan_barang_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `detail_permintaan_barang_id_permintaan_foreign` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_barang` (`id_permintaan`) ON DELETE CASCADE,
  CONSTRAINT `detail_permintaan_barang_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.detail_permintaan_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.detail_retur_barang
CREATE TABLE IF NOT EXISTS `detail_retur_barang` (
  `id_detail_retur` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_retur` bigint unsigned NOT NULL,
  `id_inventory` bigint unsigned NOT NULL,
  `qty_retur` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `alasan_retur_item` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_retur`),
  KEY `detail_retur_barang_id_retur_foreign` (`id_retur`),
  KEY `detail_retur_barang_id_inventory_foreign` (`id_inventory`),
  KEY `detail_retur_barang_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `detail_retur_barang_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `detail_retur_barang_id_retur_foreign` FOREIGN KEY (`id_retur`) REFERENCES `retur_barang` (`id_retur`) ON DELETE CASCADE,
  CONSTRAINT `detail_retur_barang_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.detail_retur_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.draft_detail_distribusi
CREATE TABLE IF NOT EXISTS `draft_detail_distribusi` (
  `id_draft_detail` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_permintaan` bigint unsigned NOT NULL,
  `id_inventory` bigint unsigned NOT NULL,
  `id_gudang_asal` bigint unsigned NOT NULL,
  `qty_distribusi` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kategori_gudang` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `status` enum('DRAFT','READY','COMPILED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_draft_detail`),
  KEY `draft_detail_distribusi_id_inventory_foreign` (`id_inventory`),
  KEY `draft_detail_distribusi_id_gudang_asal_foreign` (`id_gudang_asal`),
  KEY `draft_detail_distribusi_id_satuan_foreign` (`id_satuan`),
  KEY `draft_detail_distribusi_created_by_foreign` (`created_by`),
  KEY `idx_draft_detail_permintaan` (`id_permintaan`,`kategori_gudang`,`status`),
  CONSTRAINT `draft_detail_distribusi_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `draft_detail_distribusi_id_gudang_asal_foreign` FOREIGN KEY (`id_gudang_asal`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `draft_detail_distribusi_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `draft_detail_distribusi_id_permintaan_foreign` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_barang` (`id_permintaan`) ON DELETE CASCADE,
  CONSTRAINT `draft_detail_distribusi_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.draft_detail_distribusi: ~0 rows (approximately)

-- Dumping structure for table simantik.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table simantik.history_lokasi
CREATE TABLE IF NOT EXISTS `history_lokasi` (
  `id_history` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_inventory` bigint unsigned NOT NULL,
  `id_gudang_asal` bigint unsigned DEFAULT NULL,
  `id_gudang_tujuan` bigint unsigned DEFAULT NULL,
  `id_transaksi` bigint unsigned DEFAULT NULL,
  `jenis_transaksi` enum('DISTRIBUSI','PENERIMAAN','MUTASI') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `qty` decimal(15,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_history`),
  KEY `history_lokasi_id_inventory_foreign` (`id_inventory`),
  KEY `history_lokasi_id_gudang_asal_foreign` (`id_gudang_asal`),
  KEY `history_lokasi_id_gudang_tujuan_foreign` (`id_gudang_tujuan`),
  KEY `history_lokasi_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `history_lokasi_id_gudang_asal_foreign` FOREIGN KEY (`id_gudang_asal`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE SET NULL,
  CONSTRAINT `history_lokasi_id_gudang_tujuan_foreign` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE SET NULL,
  CONSTRAINT `history_lokasi_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `history_lokasi_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.history_lokasi: ~0 rows (approximately)

-- Dumping structure for table simantik.inventory_item
CREATE TABLE IF NOT EXISTS `inventory_item` (
  `id_item` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_inventory` bigint unsigned NOT NULL,
  `kode_register` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_seri` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_item` enum('BAIK','RUSAK_RINGAN','RUSAK_BERAT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BAIK',
  `status_item` enum('AKTIF','DISTRIBUSI','NONAKTIF') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AKTIF',
  `id_gudang` bigint unsigned DEFAULT NULL,
  `id_ruangan` bigint unsigned DEFAULT NULL,
  `qr_code` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  UNIQUE KEY `inventory_item_kode_register_unique` (`kode_register`),
  KEY `inventory_item_id_inventory_foreign` (`id_inventory`),
  KEY `inventory_item_id_gudang_foreign` (`id_gudang`),
  KEY `inventory_item_id_ruangan_foreign` (`id_ruangan`),
  CONSTRAINT `inventory_item_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE SET NULL,
  CONSTRAINT `inventory_item_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `inventory_item_id_ruangan_foreign` FOREIGN KEY (`id_ruangan`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.inventory_item: ~0 rows (approximately)

-- Dumping structure for table simantik.jadwal_maintenance
CREATE TABLE IF NOT EXISTS `jadwal_maintenance` (
  `id_jadwal` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_register_aset` bigint unsigned NOT NULL,
  `jenis_maintenance` enum('RUTIN','KALIBRASI','PERBAIKAN','PENGGANTIAN_SPAREPART') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RUTIN',
  `periode` enum('HARIAN','MINGGUAN','BULANAN','3_BULAN','6_BULAN','TAHUNAN','CUSTOM') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BULANAN',
  `interval_hari` int DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selanjutnya` date DEFAULT NULL,
  `tanggal_terakhir` date DEFAULT NULL,
  `status` enum('AKTIF','NONAKTIF','SELESAI') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AKTIF',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `jadwal_maintenance_id_register_aset_foreign` (`id_register_aset`),
  KEY `jadwal_maintenance_created_by_foreign` (`created_by`),
  CONSTRAINT `jadwal_maintenance_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_maintenance_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.jadwal_maintenance: ~0 rows (approximately)

-- Dumping structure for table simantik.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.jobs: ~0 rows (approximately)

-- Dumping structure for table simantik.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.job_batches: ~0 rows (approximately)

-- Dumping structure for table simantik.kalibrasi_aset
CREATE TABLE IF NOT EXISTS `kalibrasi_aset` (
  `id_kalibrasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_kalibrasi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_register_aset` bigint unsigned NOT NULL,
  `id_permintaan_pemeliharaan` bigint unsigned DEFAULT NULL,
  `tanggal_kalibrasi` date NOT NULL,
  `tanggal_berlaku` date NOT NULL,
  `tanggal_kadaluarsa` date NOT NULL,
  `lembaga_kalibrasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_sertifikat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kalibrasi` enum('VALID','KADALUARSA','MENUNGGU','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VALID',
  `biaya_kalibrasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `file_sertifikat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kalibrasi`),
  UNIQUE KEY `kalibrasi_aset_no_kalibrasi_unique` (`no_kalibrasi`),
  KEY `kalibrasi_aset_id_register_aset_foreign` (`id_register_aset`),
  KEY `kalibrasi_aset_id_permintaan_pemeliharaan_foreign` (`id_permintaan_pemeliharaan`),
  KEY `kalibrasi_aset_created_by_foreign` (`created_by`),
  CONSTRAINT `kalibrasi_aset_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kalibrasi_aset_id_permintaan_pemeliharaan_foreign` FOREIGN KEY (`id_permintaan_pemeliharaan`) REFERENCES `permintaan_pemeliharaan` (`id_permintaan_pemeliharaan`) ON DELETE SET NULL,
  CONSTRAINT `kalibrasi_aset_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.kalibrasi_aset: ~0 rows (approximately)

-- Dumping structure for table simantik.kartu_inventaris_ruangan
CREATE TABLE IF NOT EXISTS `kartu_inventaris_ruangan` (
  `id_kir` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_register_aset` bigint unsigned NOT NULL,
  `id_ruangan` bigint unsigned NOT NULL,
  `id_penanggung_jawab` bigint unsigned DEFAULT NULL,
  `tanggal_penempatan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kir`),
  KEY `kartu_inventaris_ruangan_id_register_aset_foreign` (`id_register_aset`),
  KEY `kartu_inventaris_ruangan_id_ruangan_foreign` (`id_ruangan`),
  KEY `kartu_inventaris_ruangan_id_penanggung_jawab_foreign` (`id_penanggung_jawab`),
  CONSTRAINT `kartu_inventaris_ruangan_id_penanggung_jawab_foreign` FOREIGN KEY (`id_penanggung_jawab`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kartu_inventaris_ruangan_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE,
  CONSTRAINT `kartu_inventaris_ruangan_id_ruangan_foreign` FOREIGN KEY (`id_ruangan`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.kartu_inventaris_ruangan: ~0 rows (approximately)

-- Dumping structure for table simantik.kontrak
CREATE TABLE IF NOT EXISTS `kontrak` (
  `id_kontrak` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_paket` bigint unsigned NOT NULL,
  `no_kontrak` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_sp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_po` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_vendor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `npwp_vendor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_vendor` text COLLATE utf8mb4_unicode_ci,
  `nilai_kontrak` decimal(15,2) NOT NULL,
  `tanggal_kontrak` date NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis_pembayaran` enum('TUNAI','UANG_MUKA','TERMIN','PELUNASAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TERMIN',
  `jumlah_termin` int NOT NULL DEFAULT '1',
  `status_kontrak` enum('DRAFT','AKTIF','SELESAI','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `upload_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kontrak`),
  UNIQUE KEY `kontrak_no_kontrak_unique` (`no_kontrak`),
  UNIQUE KEY `kontrak_no_sp_unique` (`no_sp`),
  UNIQUE KEY `kontrak_no_po_unique` (`no_po`),
  KEY `kontrak_id_paket_foreign` (`id_paket`),
  CONSTRAINT `kontrak_id_paket_foreign` FOREIGN KEY (`id_paket`) REFERENCES `pengadaan_paket` (`id_paket`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.kontrak: ~0 rows (approximately)

-- Dumping structure for table simantik.master_aset
CREATE TABLE IF NOT EXISTS `master_aset` (
  `id_aset` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_aset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_aset`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_aset: ~2 rows (approximately)
REPLACE INTO `master_aset` (`id_aset`, `nama_aset`, `created_at`, `updated_at`) VALUES
	(1, 'Aset Tetap', '2026-01-19 06:51:22', '2026-01-19 06:51:22'),
	(2, 'Aset Lancar', '2026-01-19 07:48:42', '2026-01-19 07:48:42');

-- Dumping structure for table simantik.master_data_barang
CREATE TABLE IF NOT EXISTS `master_data_barang` (
  `id_data_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_subjenis_barang` bigint unsigned NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `kode_data_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `upload_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_data_barang`),
  UNIQUE KEY `master_data_barang_kode_data_barang_unique` (`kode_data_barang`),
  KEY `master_data_barang_id_subjenis_barang_foreign` (`id_subjenis_barang`),
  KEY `master_data_barang_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `master_data_barang_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE,
  CONSTRAINT `master_data_barang_id_subjenis_barang_foreign` FOREIGN KEY (`id_subjenis_barang`) REFERENCES `master_subjenis_barang` (`id_subjenis_barang`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_data_barang: ~3 rows (approximately)
REPLACE INTO `master_data_barang` (`id_data_barang`, `id_subjenis_barang`, `id_satuan`, `kode_data_barang`, `nama_barang`, `deskripsi`, `upload_foto`, `foto_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '01.01.01.01.01', 'Komputer PC', 'Core i7', 'foto-barang/vitVT02pcbd5SCpATNoe3TeUjWkQ6nOlKT3awPhG.png', NULL, '2026-01-19 07:14:06', '2026-01-19 07:14:06'),
	(2, 2, 2, '02.01.01.01.01', 'Paracetamol', 'Penurun Panas', 'foto-barang/AeZ6ywtlGEgTpmH8EvGAHDcS69CEZGOWO5OWgi03.png', NULL, '2026-01-19 07:52:45', '2026-01-19 07:52:45'),
	(3, 2, 2, '02.01.01.01.02', 'Vitamin C', NULL, 'foto-barang/nCuYSL3yi6LYLJFUAf9JGAATyEATuuG0FIig27dh.png', NULL, '2026-01-22 06:51:41', '2026-01-22 06:51:41');

-- Dumping structure for table simantik.master_gudang
CREATE TABLE IF NOT EXISTS `master_gudang` (
  `id_gudang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `nama_gudang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_gudang` enum('PUSAT','UNIT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_gudang` enum('ASET','PERSEDIAAN','FARMASI') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_gudang`),
  KEY `master_gudang_id_unit_kerja_foreign` (`id_unit_kerja`),
  CONSTRAINT `master_gudang_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_gudang: ~6 rows (approximately)
REPLACE INTO `master_gudang` (`id_gudang`, `id_unit_kerja`, `nama_gudang`, `jenis_gudang`, `kategori_gudang`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Gudang Aset', 'PUSAT', 'ASET', '2026-01-19 07:25:21', '2026-01-19 07:25:21'),
	(2, 1, 'Gudang Persediaan', 'PUSAT', 'PERSEDIAAN', '2026-01-19 07:25:42', '2026-01-19 07:25:42'),
	(3, 1, 'Gudang Farmasi', 'PUSAT', 'FARMASI', '2026-01-19 07:25:58', '2026-01-19 07:25:58'),
	(5, 3, 'Klinik Pratama Balaikota', 'UNIT', NULL, '2026-01-19 07:26:28', '2026-01-23 02:16:58'),
	(6, 2, 'Klinik Utama Balaikota', 'UNIT', NULL, '2026-01-19 07:46:40', '2026-01-23 02:16:50'),
	(8, 4, 'Klinik Pratama DPRD', 'UNIT', NULL, '2026-01-19 07:47:03', '2026-01-23 02:17:09');

-- Dumping structure for table simantik.master_jabatan
CREATE TABLE IF NOT EXISTS `master_jabatan` (
  `id_jabatan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `role_id` bigint unsigned DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jabatan`),
  KEY `master_jabatan_role_id_foreign` (`role_id`),
  CONSTRAINT `master_jabatan_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_jabatan: ~13 rows (approximately)
REPLACE INTO `master_jabatan` (`id_jabatan`, `nama_jabatan`, `urutan`, `role_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
	(1, 'Admin IT / Pengelola Aplikasi', 1, 1, 'Admin Sistem - Kelola user, role, master data, konfigurasi sistem', '2026-01-20 03:05:12', '2026-01-20 03:05:12'),
	(6, 'Kasubbag TU', 4, 6, 'Kasubbag TU - Verifikasi administrasi permintaan, cek kelengkapan', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(7, 'Kepala Pusat', 5, 7, 'Kepala Pusat / Kepala UPT (Pimpinan) - Approve/Reject permintaan, memberikan disposisi', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(9, 'Pengurus Barang', 6, 2, 'Admin Gudang / Pengurus Barang - Kelola stok, proses distribusi, cetak SBBK', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(10, 'Admin Gudang', 7, 2, 'Admin Gudang / Pengurus Barang - Kelola stok, proses distribusi, cetak SBBK', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(11, 'Perencanaan', 8, 8, 'Unit Perencanaan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(12, 'Pengadaan Barang', 9, 9, 'Unit Pengadaan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(13, 'Keuangan/Bendahara', 10, 10, 'Unit Keuangan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:05:13', '2026-01-20 03:09:43'),
	(15, 'Admin Unit', 2, 4, 'Admin Unit / Staf Unit Kerja / Pelaksana Teknis - Membuat permintaan barang, melihat status, menerima barang', '2026-01-20 03:09:43', '2026-01-20 03:09:43'),
	(16, 'Kepala Unit', 3, 5, 'Kepala Unit / Kepala Seksi / Kepala Sub Unit - Melihat permintaan dari unitnya, memberi status "Mengetahui"', '2026-01-20 03:09:43', '2026-01-20 03:09:43'),
	(17, 'Admin Gudang Aset', 11, 11, NULL, '2026-01-20 06:39:35', '2026-01-20 06:39:43'),
	(18, 'Admin Gudang Farmasi', 12, 13, NULL, '2026-01-20 06:40:04', '2026-01-20 08:02:53'),
	(19, 'Admin Gudang Persediaan', 13, 12, NULL, '2026-01-20 06:40:29', '2026-01-20 08:03:15');

-- Dumping structure for table simantik.master_jenis_barang
CREATE TABLE IF NOT EXISTS `master_jenis_barang` (
  `id_jenis_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kategori_barang` bigint unsigned NOT NULL,
  `kode_jenis_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jenis_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jenis_barang`),
  KEY `master_jenis_barang_id_kategori_barang_foreign` (`id_kategori_barang`),
  CONSTRAINT `master_jenis_barang_id_kategori_barang_foreign` FOREIGN KEY (`id_kategori_barang`) REFERENCES `master_kategori_barang` (`id_kategori_barang`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_jenis_barang: ~2 rows (approximately)
REPLACE INTO `master_jenis_barang` (`id_jenis_barang`, `id_kategori_barang`, `kode_jenis_barang`, `nama_jenis_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, '01.01.01', 'Komputer PC', '2026-01-19 07:04:25', '2026-01-19 07:04:25'),
	(2, 2, '02.01.01', 'Obat-obatan Umum Spesialis', '2026-01-19 07:50:47', '2026-01-19 07:50:47');

-- Dumping structure for table simantik.master_kategori_barang
CREATE TABLE IF NOT EXISTS `master_kategori_barang` (
  `id_kategori_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kode_barang` bigint unsigned NOT NULL,
  `kode_kategori_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kategori_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori_barang`),
  KEY `master_kategori_barang_id_kode_barang_foreign` (`id_kode_barang`),
  CONSTRAINT `master_kategori_barang_id_kode_barang_foreign` FOREIGN KEY (`id_kode_barang`) REFERENCES `master_kode_barang` (`id_kode_barang`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_kategori_barang: ~2 rows (approximately)
REPLACE INTO `master_kategori_barang` (`id_kategori_barang`, `id_kode_barang`, `kode_kategori_barang`, `nama_kategori_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, '01.01', 'Komputer', '2026-01-19 07:03:37', '2026-01-19 07:03:37'),
	(2, 2, '02.01', 'Obat-obatan Umum', '2026-01-19 07:50:17', '2026-01-19 07:50:17');

-- Dumping structure for table simantik.master_kegiatan
CREATE TABLE IF NOT EXISTS `master_kegiatan` (
  `id_kegiatan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_program` bigint unsigned NOT NULL,
  `nama_kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kegiatan`),
  KEY `master_kegiatan_id_program_foreign` (`id_program`),
  CONSTRAINT `master_kegiatan_id_program_foreign` FOREIGN KEY (`id_program`) REFERENCES `master_program` (`id_program`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_kegiatan: ~1 rows (approximately)
REPLACE INTO `master_kegiatan` (`id_kegiatan`, `id_program`, `nama_kegiatan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Kegiatan Pelayanan Kesehatan', '2026-01-19 07:30:33', '2026-01-19 07:30:33');

-- Dumping structure for table simantik.master_kode_barang
CREATE TABLE IF NOT EXISTS `master_kode_barang` (
  `id_kode_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_aset` bigint unsigned NOT NULL,
  `kode_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kode_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kode_barang`),
  UNIQUE KEY `master_kode_barang_kode_barang_unique` (`kode_barang`),
  KEY `master_kode_barang_id_aset_foreign` (`id_aset`),
  CONSTRAINT `master_kode_barang_id_aset_foreign` FOREIGN KEY (`id_aset`) REFERENCES `master_aset` (`id_aset`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_kode_barang: ~3 rows (approximately)
REPLACE INTO `master_kode_barang` (`id_kode_barang`, `id_aset`, `kode_barang`, `nama_kode_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, '01', 'Komputer-Laptop-Printer', '2026-01-19 07:02:50', '2026-01-19 07:03:55'),
	(2, 2, '02', 'Obat-Obatan', '2026-01-19 07:49:31', '2026-01-19 07:49:39'),
	(3, 2, '03', 'ATK', '2026-01-19 07:49:51', '2026-01-19 07:49:51');

-- Dumping structure for table simantik.master_pegawai
CREATE TABLE IF NOT EXISTS `master_pegawai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nip_pegawai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_jabatan` bigint unsigned NOT NULL,
  `email_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_pegawai_nip_pegawai_unique` (`nip_pegawai`),
  UNIQUE KEY `master_pegawai_email_pegawai_unique` (`email_pegawai`),
  KEY `master_pegawai_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `master_pegawai_id_jabatan_foreign` (`id_jabatan`),
  KEY `master_pegawai_user_id_foreign` (`user_id`),
  CONSTRAINT `master_pegawai_id_jabatan_foreign` FOREIGN KEY (`id_jabatan`) REFERENCES `master_jabatan` (`id_jabatan`) ON DELETE CASCADE,
  CONSTRAINT `master_pegawai_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE,
  CONSTRAINT `master_pegawai_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_pegawai: ~8 rows (approximately)
REPLACE INTO `master_pegawai` (`id`, `user_id`, `nip_pegawai`, `nama_pegawai`, `id_unit_kerja`, `id_jabatan`, `email_pegawai`, `no_telp`, `created_at`, `updated_at`) VALUES
	(1, 28, 'A-001', 'dr. Dwian Andhika', 1, 7, 'kepala@gmail.com', '081111111111111', '2026-01-20 03:14:03', '2026-01-23 07:54:50'),
	(2, 27, 'A-002', 'Dara Indir Yunita', 1, 6, 'kasubbag@gmail.com', '081111111111112', '2026-01-20 03:17:13', '2026-01-23 07:54:39'),
	(3, 24, 'A-003', 'Assifha Setiawati', 1, 9, 'pengurusbarang@gmail.com', '081111111111113', '2026-01-20 03:18:48', '2026-01-23 07:54:32'),
	(4, 25, 'A-004', 'Syaiful', 1, 10, 'syaiful@gmail.com', '01111', '2026-01-20 03:22:22', '2026-01-23 07:54:24'),
	(5, 26, 'A-005', 'dr. Lintang', 3, 16, 'lintang@gmail.com', '000000', '2026-01-20 03:23:37', '2026-01-23 07:54:16'),
	(6, 23, 'A-006', 'ruka', 3, 15, 'ruka@gmail.com', '00011111111', '2026-01-20 03:24:24', '2026-01-23 07:53:49'),
	(7, 29, 'A-007', 'Dhila', 1, 18, 'dhila@gmail.com', '0111111', '2026-01-22 03:42:47', '2026-01-23 07:54:07'),
	(8, 30, 'A-009', 'Annas', 4, 15, 'annas@gmail.com', '00101021201', '2026-01-23 08:30:18', '2026-01-23 08:31:51');

-- Dumping structure for table simantik.master_program
CREATE TABLE IF NOT EXISTS `master_program` (
  `id_program` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_program`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_program: ~1 rows (approximately)
REPLACE INTO `master_program` (`id_program`, `nama_program`, `created_at`, `updated_at`) VALUES
	(1, 'Peningkatan Program Pelayanan Kesehatan', '2026-01-19 07:29:17', '2026-01-19 07:29:17');

-- Dumping structure for table simantik.master_ruangan
CREATE TABLE IF NOT EXISTS `master_ruangan` (
  `id_ruangan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `kode_ruangan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_ruangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ruangan`),
  KEY `master_ruangan_id_unit_kerja_foreign` (`id_unit_kerja`),
  CONSTRAINT `master_ruangan_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_ruangan: ~4 rows (approximately)
REPLACE INTO `master_ruangan` (`id_ruangan`, `id_unit_kerja`, `kode_ruangan`, `nama_ruangan`, `created_at`, `updated_at`) VALUES
	(1, 3, 'Poli-001', 'Poli Umum', '2026-01-19 07:48:00', '2026-01-19 07:48:00'),
	(2, 3, 'Poli-002', 'Poli Gigi', '2026-01-19 07:48:15', '2026-01-19 07:48:15'),
	(3, 4, 'Poli-001', 'Poli Umum', '2026-01-29 02:13:28', '2026-01-29 02:13:28'),
	(4, 4, 'Poli-002', 'Poli Gigi', '2026-01-29 02:13:39', '2026-01-29 02:13:39');

-- Dumping structure for table simantik.master_satuan
CREATE TABLE IF NOT EXISTS `master_satuan` (
  `id_satuan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_satuan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_satuan: ~5 rows (approximately)
REPLACE INTO `master_satuan` (`id_satuan`, `nama_satuan`, `created_at`, `updated_at`) VALUES
	(1, 'Unit', '2026-01-19 07:05:13', '2026-01-19 07:05:13'),
	(2, 'Box', '2026-01-19 07:51:40', '2026-01-19 07:51:40'),
	(3, 'Tablet', '2026-01-19 07:51:47', '2026-01-19 07:51:47'),
	(4, 'Botol', '2026-01-19 07:51:53', '2026-01-19 07:51:53'),
	(5, 'Karton', '2026-01-19 07:52:02', '2026-01-19 07:52:02');

-- Dumping structure for table simantik.master_subjenis_barang
CREATE TABLE IF NOT EXISTS `master_subjenis_barang` (
  `id_subjenis_barang` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_jenis_barang` bigint unsigned NOT NULL,
  `kode_subjenis_barang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_subjenis_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_subjenis_barang`),
  KEY `master_subjenis_barang_id_jenis_barang_foreign` (`id_jenis_barang`),
  CONSTRAINT `master_subjenis_barang_id_jenis_barang_foreign` FOREIGN KEY (`id_jenis_barang`) REFERENCES `master_jenis_barang` (`id_jenis_barang`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_subjenis_barang: ~2 rows (approximately)
REPLACE INTO `master_subjenis_barang` (`id_subjenis_barang`, `id_jenis_barang`, `kode_subjenis_barang`, `nama_subjenis_barang`, `created_at`, `updated_at`) VALUES
	(1, 1, '01.01.01.01', 'Komputer PC', '2026-01-19 07:05:00', '2026-01-19 07:05:00'),
	(2, 2, '02.01.01.01', 'Obat Dokter Spesialis', '2026-01-19 07:51:15', '2026-01-19 07:51:15');

-- Dumping structure for table simantik.master_sub_kegiatan
CREATE TABLE IF NOT EXISTS `master_sub_kegiatan` (
  `id_sub_kegiatan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kegiatan` bigint unsigned NOT NULL,
  `nama_sub_kegiatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_sub_kegiatan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_sub_kegiatan`),
  UNIQUE KEY `master_sub_kegiatan_kode_sub_kegiatan_unique` (`kode_sub_kegiatan`),
  KEY `master_sub_kegiatan_id_kegiatan_foreign` (`id_kegiatan`),
  CONSTRAINT `master_sub_kegiatan_id_kegiatan_foreign` FOREIGN KEY (`id_kegiatan`) REFERENCES `master_kegiatan` (`id_kegiatan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_sub_kegiatan: ~1 rows (approximately)
REPLACE INTO `master_sub_kegiatan` (`id_sub_kegiatan`, `id_kegiatan`, `nama_sub_kegiatan`, `kode_sub_kegiatan`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Peningkatan pelayanan kesehatan', '01.01', '2026-01-19 07:30:59', '2026-01-19 07:30:59');

-- Dumping structure for table simantik.master_sumber_anggaran
CREATE TABLE IF NOT EXISTS `master_sumber_anggaran` (
  `id_anggaran` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_anggaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_anggaran`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_sumber_anggaran: ~2 rows (approximately)
REPLACE INTO `master_sumber_anggaran` (`id_anggaran`, `nama_anggaran`, `created_at`, `updated_at`) VALUES
	(1, 'APBD', '2026-01-19 07:05:29', '2026-01-19 07:05:29'),
	(2, 'BLUD', '2026-01-19 07:05:38', '2026-01-19 07:05:38');

-- Dumping structure for table simantik.master_unit_kerja
CREATE TABLE IF NOT EXISTS `master_unit_kerja` (
  `id_unit_kerja` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_unit_kerja` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_unit_kerja` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_unit_kerja`),
  UNIQUE KEY `master_unit_kerja_kode_unit_kerja_unique` (`kode_unit_kerja`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.master_unit_kerja: ~4 rows (approximately)
REPLACE INTO `master_unit_kerja` (`id_unit_kerja`, `kode_unit_kerja`, `nama_unit_kerja`, `created_at`, `updated_at`) VALUES
	(1, '001', 'Manajemen', '2026-01-19 03:34:31', '2026-01-19 03:34:31'),
	(2, '002', 'Klinik Utama', '2026-01-19 04:00:28', '2026-01-19 04:00:28'),
	(3, '003', 'Klinik Pratama Balaikota', '2026-01-19 04:00:44', '2026-01-19 04:00:44'),
	(4, '004', 'Klinik Pratama DPRD', '2026-01-19 04:01:05', '2026-01-19 04:01:05');

-- Dumping structure for table simantik.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.migrations: ~76 rows (approximately)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_01_15_081604_create_master_unit_kerja_table', 1),
	(5, '2026_01_15_081605_create_master_jabatan_table', 1),
	(6, '2026_01_15_081606_create_master_ruangan_table', 1),
	(7, '2026_01_15_081607_create_master_gudang_table', 1),
	(8, '2026_01_15_081607_create_master_program_table', 1),
	(9, '2026_01_15_081608_create_master_kegiatan_table', 1),
	(10, '2026_01_15_081609_create_master_sub_kegiatan_table', 1),
	(11, '2026_01_15_082344_create_master_pegawai_table', 1),
	(12, '2026_01_15_082346_create_master_aset_table', 1),
	(13, '2026_01_15_082346_create_master_satuan_table', 1),
	(14, '2026_01_15_082348_create_master_sumber_anggaran_table', 1),
	(15, '2026_01_15_082350_create_master_kode_barang_table', 1),
	(16, '2026_01_15_082351_create_master_kategori_barang_table', 1),
	(17, '2026_01_15_082352_create_master_jenis_barang_table', 1),
	(18, '2026_01_15_082353_create_master_subjenis_barang_table', 1),
	(19, '2026_01_15_082354_create_master_data_barang_table', 1),
	(20, '2026_01_15_083552_create_data_inventory_table', 1),
	(21, '2026_01_15_083553_create_inventory_item_table', 1),
	(22, '2026_01_15_083554_create_data_stock_table', 1),
	(23, '2026_01_15_083555_create_data_stock_opname_table', 1),
	(24, '2026_01_15_083642_create_permintaan_barang_table', 1),
	(25, '2026_01_15_083643_create_detail_permintaan_barang_table', 1),
	(26, '2026_01_15_083644_create_approval_permintaan_table', 1),
	(27, '2026_01_15_083644_create_transaksi_distribusi_table', 1),
	(28, '2026_01_15_083645_create_detail_distribusi_table', 1),
	(29, '2026_01_15_083646_create_penerimaan_barang_table', 1),
	(30, '2026_01_15_083647_create_detail_penerimaan_barang_table', 1),
	(31, '2026_01_15_084003_create_register_aset_table', 1),
	(32, '2026_01_15_084004_create_kartu_inventaris_ruangan_table', 1),
	(33, '2026_01_15_084005_create_mutasi_aset_table', 1),
	(34, '2026_01_15_084006_create_history_lokasi_table', 1),
	(35, '2026_01_15_085252_create_pemeliharaan_aset_table', 1),
	(36, '2026_01_15_112320_create_rku_header_table', 1),
	(37, '2026_01_15_112321_create_rku_detail_table', 1),
	(38, '2026_01_15_112322_create_pengadaan_paket_table', 1),
	(39, '2026_01_15_112323_create_kontrak_table', 1),
	(40, '2026_01_15_112324_create_pembayaran_table', 1),
	(41, '2026_01_19_141036_remove_extra_fields_from_master_data_barang_table', 1),
	(42, '2026_01_19_141124_remove_unused_fields_from_master_data_barang_table', 2),
	(43, '2026_01_19_142902_add_upload_foto_to_data_inventory_table', 3),
	(44, '2026_01_19_145939_add_nama_penyedia_to_data_inventory_table', 3),
	(45, '2026_01_19_152739_create_roles_table', 3),
	(46, '2026_01_19_152740_create_role_user_table', 3),
	(47, '2026_01_19_160006_add_user_id_to_master_pegawai_table', 3),
	(48, '2026_01_20_090457_add_fields_to_master_jabatan_table', 3),
	(49, '2026_01_20_095537_update_status_permintaan_barang_for_multilevel_approval', 3),
	(50, '2026_01_20_095539_create_approval_flow_definition_table', 3),
	(51, '2026_01_20_095541_create_approval_log_table', 3),
	(52, '2026_01_20_101556_create_permissions_table', 3),
	(53, '2026_01_20_101558_create_permission_role_table', 3),
	(54, '2026_01_20_111152_update_jenis_permintaan_to_support_multichoice', 3),
	(55, '2026_01_20_125741_make_user_id_nullable_in_approval_log_table', 3),
	(56, '2026_01_20_140000_update_approval_flow_definition_unique_constraint', 3),
	(57, '2026_01_20_141103_add_distribusi_workflow_fields_to_transaksi_distribusi_table', 3),
	(58, '2026_01_20_141207_create_draft_detail_distribusi_table', 4),
	(59, '2026_01_20_141544_add_diproses_status_to_approval_log_table', 4),
	(60, '2026_01_21_083428_create_retur_barang_table', 4),
	(61, '2026_01_21_083430_create_detail_retur_barang_table', 4),
	(62, '2026_01_22_100000_create_permintaan_pemeliharaan_table', 4),
	(63, '2026_01_22_100001_create_jadwal_maintenance_table', 4),
	(64, '2026_01_22_100002_create_kalibrasi_aset_table', 4),
	(65, '2026_01_22_100003_create_service_report_table', 4),
	(66, '2026_01_22_100004_create_riwayat_pemeliharaan_table', 4),
	(67, '2026_01_22_103713_add_tipe_permintaan_to_permintaan_barang_table', 4),
	(68, '2026_01_22_110401_fix_approval_log_status_for_sqlite', 4),
	(69, '2026_01_22_145613_create_modules_table', 4),
	(70, '2026_01_22_145616_create_user_modules_table', 4),
	(71, '2026_01_28_114224_create_stock_adjustment_table', 5),
	(72, '2026_01_28_115017_create_pemakaian_barang_table', 5),
	(73, '2026_01_29_094657_add_id_ruangan_to_register_aset_table', 6),
	(74, '2026_01_29_095605_make_id_penanggung_jawab_nullable_in_kartu_inventaris_ruangan_table', 7),
	(75, '2026_01_29_111701_change_tipe_permintaan_tahunan_to_cito_in_permintaan_barang_table', 8),
	(76, '2026_01_29_112051_fix_tipe_permintaan_enum_to_cito', 9);

-- Dumping structure for table simantik.modules
CREATE TABLE IF NOT EXISTS `modules` (
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.modules: ~10 rows (approximately)
REPLACE INTO `modules` (`name`, `display_name`, `description`, `icon`, `sort_order`, `created_at`, `updated_at`) VALUES
	('asset', 'Aset & KIR', 'Pengelolaan register aset dan kartu inventaris ruangan', 'document-duplicate', 300, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('finance', 'Keuangan', 'Pengelolaan keuangan dan pembayaran', 'currency-dollar', 700, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('inventory', 'Inventory', 'Pengelolaan inventory dan stock gudang', 'archive-box', 100, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('maintenance', 'Pemeliharaan', 'Pengelolaan pemeliharaan dan kalibrasi aset', 'wrench-screwdriver', 400, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('master-data', 'Master Data', 'Pengelolaan master data barang (Aset, Kode Barang, Kategori, Jenis, Sub Jenis, Data Barang, Satuan, Sumber Anggaran)', 'database', 20, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('master-manajemen', 'Master Manajemen', 'Pengelolaan master data manajemen (Unit Kerja, Lokasi, Ruangan, Gudang, Program, Kegiatan, Sub Kegiatan)', 'building-office', 10, '2026-01-22 07:59:39', '2026-01-22 07:59:39'),
	('planning', 'Perencanaan', 'Pengelolaan perencanaan kebutuhan unit (RKU)', 'calendar', 500, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('procurement', 'Pengadaan', 'Pengelolaan pengadaan barang dan jasa', 'shopping-cart', 600, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('reports', 'Laporan', 'Laporan dan monitoring', 'chart-bar', 800, '2026-01-22 07:59:40', '2026-01-22 07:59:40'),
	('transaction', 'Transaksi', 'Pengelolaan transaksi (Permintaan, Approval, Distribusi, Penerimaan, Retur)', 'arrow-path', 200, '2026-01-22 07:59:40', '2026-01-22 07:59:40');

-- Dumping structure for table simantik.mutasi_aset
CREATE TABLE IF NOT EXISTS `mutasi_aset` (
  `id_mutasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_register_aset` bigint unsigned NOT NULL,
  `id_ruangan_asal` bigint unsigned NOT NULL,
  `id_ruangan_tujuan` bigint unsigned NOT NULL,
  `tanggal_mutasi` date NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_mutasi`),
  KEY `mutasi_aset_id_register_aset_foreign` (`id_register_aset`),
  KEY `mutasi_aset_id_ruangan_asal_foreign` (`id_ruangan_asal`),
  KEY `mutasi_aset_id_ruangan_tujuan_foreign` (`id_ruangan_tujuan`),
  CONSTRAINT `mutasi_aset_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE,
  CONSTRAINT `mutasi_aset_id_ruangan_asal_foreign` FOREIGN KEY (`id_ruangan_asal`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE CASCADE,
  CONSTRAINT `mutasi_aset_id_ruangan_tujuan_foreign` FOREIGN KEY (`id_ruangan_tujuan`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.mutasi_aset: ~0 rows (approximately)

-- Dumping structure for table simantik.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table simantik.pemakaian_barang
CREATE TABLE IF NOT EXISTS `pemakaian_barang` (
  `id_pemakaian` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_pemakaian` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_gudang` bigint unsigned NOT NULL,
  `id_pegawai_pemakai` bigint unsigned NOT NULL,
  `tanggal_pemakaian` date NOT NULL,
  `status_pemakaian` enum('DRAFT','DIAJUKAN','DISETUJUI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `alasan_pemakaian` text COLLATE utf8mb4_unicode_ci,
  `id_approver` bigint unsigned DEFAULT NULL,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `catatan_approval` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemakaian`),
  UNIQUE KEY `pemakaian_barang_no_pemakaian_unique` (`no_pemakaian`),
  KEY `pemakaian_barang_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `pemakaian_barang_id_gudang_foreign` (`id_gudang`),
  KEY `pemakaian_barang_id_pegawai_pemakai_foreign` (`id_pegawai_pemakai`),
  KEY `pemakaian_barang_id_approver_foreign` (`id_approver`),
  CONSTRAINT `pemakaian_barang_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pemakaian_barang_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `pemakaian_barang_id_pegawai_pemakai_foreign` FOREIGN KEY (`id_pegawai_pemakai`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemakaian_barang_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.pemakaian_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_kontrak` bigint unsigned NOT NULL,
  `no_pembayaran` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_pembayaran` enum('UANG_MUKA','TERMIN','PELUNASAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TERMIN',
  `termin_ke` int DEFAULT NULL,
  `nilai_pembayaran` decimal(15,2) NOT NULL,
  `ppn` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pph` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_pembayaran` decimal(15,2) NOT NULL,
  `tanggal_pembayaran` date NOT NULL,
  `status_pembayaran` enum('DRAFT','DIAJUKAN','DIVERIFIKASI','DIBAYAR','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `id_verifikator` bigint unsigned DEFAULT NULL,
  `tanggal_verifikasi` date DEFAULT NULL,
  `catatan_verifikasi` text COLLATE utf8mb4_unicode_ci,
  `no_bukti_bayar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  UNIQUE KEY `pembayaran_no_pembayaran_unique` (`no_pembayaran`),
  KEY `pembayaran_id_kontrak_foreign` (`id_kontrak`),
  KEY `pembayaran_id_verifikator_foreign` (`id_verifikator`),
  CONSTRAINT `pembayaran_id_kontrak_foreign` FOREIGN KEY (`id_kontrak`) REFERENCES `kontrak` (`id_kontrak`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_id_verifikator_foreign` FOREIGN KEY (`id_verifikator`) REFERENCES `master_pegawai` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.pembayaran: ~0 rows (approximately)

-- Dumping structure for table simantik.pemeliharaan_aset
CREATE TABLE IF NOT EXISTS `pemeliharaan_aset` (
  `id_pemeliharaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_item` bigint unsigned NOT NULL,
  `jenis_pemeliharaan` enum('RUTIN','KALIBRASI','PERBAIKAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `laporan_service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemeliharaan`),
  KEY `pemeliharaan_aset_id_item_foreign` (`id_item`),
  KEY `pemeliharaan_aset_created_by_foreign` (`created_by`),
  CONSTRAINT `pemeliharaan_aset_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemeliharaan_aset_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `inventory_item` (`id_item`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.pemeliharaan_aset: ~0 rows (approximately)

-- Dumping structure for table simantik.penerimaan_barang
CREATE TABLE IF NOT EXISTS `penerimaan_barang` (
  `id_penerimaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_penerimaan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_distribusi` bigint unsigned NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_pegawai_penerima` bigint unsigned NOT NULL,
  `tanggal_penerimaan` date NOT NULL,
  `status_penerimaan` enum('DITERIMA','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DITERIMA',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_penerimaan`),
  UNIQUE KEY `penerimaan_barang_no_penerimaan_unique` (`no_penerimaan`),
  KEY `penerimaan_barang_id_distribusi_foreign` (`id_distribusi`),
  KEY `penerimaan_barang_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `penerimaan_barang_id_pegawai_penerima_foreign` (`id_pegawai_penerima`),
  CONSTRAINT `penerimaan_barang_id_distribusi_foreign` FOREIGN KEY (`id_distribusi`) REFERENCES `transaksi_distribusi` (`id_distribusi`) ON DELETE CASCADE,
  CONSTRAINT `penerimaan_barang_id_pegawai_penerima_foreign` FOREIGN KEY (`id_pegawai_penerima`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penerimaan_barang_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.penerimaan_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.pengadaan_paket
CREATE TABLE IF NOT EXISTS `pengadaan_paket` (
  `id_paket` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_sub_kegiatan` bigint unsigned NOT NULL,
  `id_rku` bigint unsigned DEFAULT NULL,
  `no_paket` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_paket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_paket` text COLLATE utf8mb4_unicode_ci,
  `metode_pengadaan` enum('PEMILIHAN_LANGSUNG','PENUNJUKAN_LANGSUNG','TENDER','SWAKELOLA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEMILIHAN_LANGSUNG',
  `nilai_paket` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_paket` enum('DRAFT','DIAJUKAN','DIPROSES','SELESAI','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_paket`),
  UNIQUE KEY `pengadaan_paket_no_paket_unique` (`no_paket`),
  KEY `pengadaan_paket_id_sub_kegiatan_foreign` (`id_sub_kegiatan`),
  KEY `pengadaan_paket_id_rku_foreign` (`id_rku`),
  CONSTRAINT `pengadaan_paket_id_rku_foreign` FOREIGN KEY (`id_rku`) REFERENCES `rku_header` (`id_rku`) ON DELETE SET NULL,
  CONSTRAINT `pengadaan_paket_id_sub_kegiatan_foreign` FOREIGN KEY (`id_sub_kegiatan`) REFERENCES `master_sub_kegiatan` (`id_sub_kegiatan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.pengadaan_paket: ~0 rows (approximately)

-- Dumping structure for table simantik.permintaan_barang
CREATE TABLE IF NOT EXISTS `permintaan_barang` (
  `id_permintaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_permintaan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_pemohon` bigint unsigned NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `tipe_permintaan` enum('RUTIN','CITO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_permintaan` json DEFAULT NULL,
  `status_permintaan` enum('DRAFT','DIAJUKAN','DIKETAHUI_UNIT','DIKETAHUI_TU','DISETUJUI_PIMPINAN','DITOLAK','DIDISPOSISIKAN','DIPROSES','SELESAI') COLLATE utf8mb4_unicode_ci DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_permintaan`),
  UNIQUE KEY `permintaan_barang_no_permintaan_unique` (`no_permintaan`),
  KEY `permintaan_barang_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `permintaan_barang_id_pemohon_foreign` (`id_pemohon`),
  CONSTRAINT `permintaan_barang_id_pemohon_foreign` FOREIGN KEY (`id_pemohon`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_barang_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.permintaan_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.permintaan_pemeliharaan
CREATE TABLE IF NOT EXISTS `permintaan_pemeliharaan` (
  `id_permintaan_pemeliharaan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_permintaan_pemeliharaan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_register_aset` bigint unsigned NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_pemohon` bigint unsigned NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `jenis_pemeliharaan` enum('RUTIN','KALIBRASI','PERBAIKAN','PENGGANTIAN_SPAREPART') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RUTIN',
  `prioritas` enum('RENDAH','SEDANG','TINGGI','DARURAT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SEDANG',
  `status_permintaan` enum('DRAFT','DIAJUKAN','DISETUJUI','DITOLAK','DIPROSES','SELESAI','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `deskripsi_kerusakan` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_permintaan_pemeliharaan`),
  UNIQUE KEY `permintaan_pemeliharaan_no_permintaan_pemeliharaan_unique` (`no_permintaan_pemeliharaan`),
  KEY `permintaan_pemeliharaan_id_register_aset_foreign` (`id_register_aset`),
  KEY `permintaan_pemeliharaan_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `permintaan_pemeliharaan_id_pemohon_foreign` (`id_pemohon`),
  CONSTRAINT `permintaan_pemeliharaan_id_pemohon_foreign` FOREIGN KEY (`id_pemohon`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_pemeliharaan_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_pemeliharaan_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.permintaan_pemeliharaan: ~0 rows (approximately)

-- Dumping structure for table simantik.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=473 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.permissions: ~229 rows (approximately)
REPLACE INTO `permissions` (`id`, `name`, `display_name`, `module`, `group`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, 'user.dashboard', 'View Dashboard', 'dashboard', 'dashboard', 'Akses ke dashboard user', 1, '2026-01-20 03:20:00', '2026-01-20 03:20:00'),
	(2, 'master-manajemen.master-pegawai.index', 'View Master Pegawai', 'master-manajemen', 'master-manajemen.master-pegawai', 'Melihat daftar master pegawai', 10, '2026-01-20 03:20:00', '2026-01-20 03:20:00'),
	(6, 'master-manajemen.master-jabatan.index', 'View Master Jabatan', 'master-manajemen', 'master-manajemen.master-jabatan', 'Melihat daftar master jabatan', 20, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(10, 'master.unit-kerja.*', 'Master Unit Kerja (All)', 'master-manajemen', 'master.unit-kerja', 'Akses penuh ke master unit kerja', 30, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(11, 'master.gudang.*', 'Master Gudang (All)', 'master-manajemen', 'master.gudang', 'Akses penuh ke master gudang', 40, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(12, 'master.ruangan.*', 'Master Ruangan (All)', 'master-manajemen', 'master.ruangan', 'Akses penuh ke master ruangan', 50, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(13, 'inventory.data-stock.index', 'View Data Stock', 'inventory', 'inventory.data-stock', 'Melihat data stock gudang', 100, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(14, 'inventory.data-inventory.index', 'View Data Inventory', 'inventory', 'inventory.data-inventory', 'Melihat daftar data inventory', 110, '2026-01-20 03:20:01', '2026-01-20 03:20:01'),
	(18, 'inventory.inventory-item.*', 'Inventory Item (All)', 'inventory', 'inventory.inventory-item', 'Akses penuh ke inventory item', 120, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(19, 'transaction.permintaan-barang.index', 'View Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Melihat daftar permintaan barang', 200, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(22, 'transaction.permintaan-barang.show', 'View Detail Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Melihat detail permintaan barang', 203, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(23, 'transaction.approval.index', 'View Approval', 'transaction', 'transaction.approval', 'Melihat daftar approval', 210, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(24, 'transaction.approval.show', 'View Detail Approval', 'transaction', 'transaction.approval', 'Melihat detail approval', 211, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(25, 'transaction.approval.mengetahui', 'Mengetahui Approval', 'transaction', 'transaction.approval', 'Memberi status mengetahui pada approval', 212, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(26, 'transaction.approval.verifikasi', 'Verifikasi Approval', 'transaction', 'transaction.approval', 'Memverifikasi approval', 213, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(27, 'transaction.approval.approve', 'Approve Request', 'transaction', 'transaction.approval', 'Menyetujui permintaan', 214, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(28, 'transaction.approval.reject', 'Reject Request', 'transaction', 'transaction.approval', 'Menolak permintaan', 215, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(29, 'transaction.distribusi.*', 'Distribusi Barang (All)', 'transaction', 'transaction.distribusi', 'Akses penuh ke distribusi barang', 220, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(30, 'transaction.penerimaan-barang.*', 'Penerimaan Barang (All)', 'transaction', 'transaction.penerimaan-barang', 'Akses penuh ke penerimaan barang', 230, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(31, 'transaction.retur.*', 'Retur Barang (All)', 'transaction', 'transaction.retur', 'Akses penuh ke retur barang', 240, '2026-01-20 03:20:02', '2026-01-20 03:20:02'),
	(32, 'asset.register-aset.*', 'Register Aset (All)', 'asset', 'asset.register-aset', 'Akses penuh ke register aset', 300, '2026-01-20 03:20:03', '2026-01-20 03:20:03'),
	(33, 'reports.*', 'Reports (All)', 'reports', 'reports', 'Akses penuh ke semua laporan', 400, '2026-01-20 03:20:03', '2026-01-20 03:20:03'),
	(34, 'reports.stock-gudang', 'View Stock Gudang Report', 'reports', 'reports', 'Melihat laporan stock gudang', 401, '2026-01-20 03:20:03', '2026-01-20 03:20:03'),
	(35, 'admin.roles.*', 'Role Management (All)', 'admin', 'admin.roles', 'Akses penuh ke manajemen role', 500, '2026-01-20 03:20:03', '2026-01-20 03:20:03'),
	(36, 'admin.users.*', 'User Management (All)', 'admin', 'admin.users', 'Akses penuh ke manajemen user', 510, '2026-01-20 03:20:03', '2026-01-20 03:20:03'),
	(42, 'user.assets', 'Assets User', 'dashboard', 'user.assets', 'Assets user', 1, '2026-01-22 02:21:32', '2026-01-22 02:21:32'),
	(43, 'user.assets.show', 'View Detail Assets', 'dashboard', 'user.assets', 'Melihat detail assets', 3, '2026-01-22 02:21:32', '2026-01-22 02:21:32'),
	(44, 'user.requests', 'Requests User', 'dashboard', 'user.requests', 'Requests user', 1, '2026-01-22 02:21:32', '2026-01-22 02:21:32'),
	(47, 'user.requests.show', 'View Detail Requests', 'dashboard', 'user.requests', 'Melihat detail requests', 3, '2026-01-22 02:21:33', '2026-01-22 02:21:33'),
	(49, 'master-manajemen.master-pegawai.show', 'View Detail Master Pegawai', 'master-manajemen', 'master-manajemen.master-pegawai', 'Melihat detail master pegawai', 12, '2026-01-22 02:21:33', '2026-01-22 02:21:33'),
	(53, 'master-manajemen.master-jabatan.show', 'View Detail Master Jabatan', 'master-manajemen', 'master-manajemen.master-jabatan', 'Melihat detail master jabatan', 12, '2026-01-22 02:21:33', '2026-01-22 02:21:33'),
	(56, 'master.unit-kerja.index', 'View Unit Kerja', 'master-manajemen', 'master.unit-kerja', 'Melihat daftar unit kerja', 10, '2026-01-22 02:21:33', '2026-01-22 02:21:33'),
	(59, 'master.unit-kerja.show', 'View Detail Unit Kerja', 'master-manajemen', 'master.unit-kerja', 'Melihat detail unit kerja', 12, '2026-01-22 02:21:34', '2026-01-22 02:21:34'),
	(63, 'master.gudang.index', 'View Gudang', 'master-manajemen', 'master.gudang', 'Melihat daftar gudang', 10, '2026-01-22 02:21:34', '2026-01-22 02:21:34'),
	(66, 'master.gudang.show', 'View Detail Gudang', 'master-manajemen', 'master.gudang', 'Melihat detail gudang', 12, '2026-01-22 02:21:34', '2026-01-22 02:21:34'),
	(70, 'master.ruangan.index', 'View Ruangan', 'master-manajemen', 'master.ruangan', 'Melihat daftar ruangan', 10, '2026-01-22 02:21:34', '2026-01-22 02:21:34'),
	(73, 'master.ruangan.show', 'View Detail Ruangan', 'master-manajemen', 'master.ruangan', 'Melihat detail ruangan', 12, '2026-01-22 02:21:35', '2026-01-22 02:21:35'),
	(77, 'master.program.index', 'View Program', 'master-manajemen', 'master.program', 'Melihat daftar program', 10, '2026-01-22 02:21:35', '2026-01-22 02:21:35'),
	(80, 'master.program.show', 'View Detail Program', 'master-manajemen', 'master.program', 'Melihat detail program', 12, '2026-01-22 02:21:35', '2026-01-22 02:21:35'),
	(84, 'master.kegiatan.index', 'View Kegiatan', 'master-manajemen', 'master.kegiatan', 'Melihat daftar kegiatan', 10, '2026-01-22 02:21:35', '2026-01-22 02:21:35'),
	(87, 'master.kegiatan.show', 'View Detail Kegiatan', 'master-manajemen', 'master.kegiatan', 'Melihat detail kegiatan', 12, '2026-01-22 02:21:36', '2026-01-22 02:21:36'),
	(91, 'master.sub-kegiatan.index', 'View Sub Kegiatan', 'master-manajemen', 'master.sub-kegiatan', 'Melihat daftar sub kegiatan', 10, '2026-01-22 02:21:36', '2026-01-22 02:21:36'),
	(94, 'master.sub-kegiatan.show', 'View Detail Sub Kegiatan', 'master-manajemen', 'master.sub-kegiatan', 'Melihat detail sub kegiatan', 12, '2026-01-22 02:21:36', '2026-01-22 02:21:36'),
	(98, 'master-data.aset.index', 'View Aset', 'master-data', 'master-data.aset', 'Melihat daftar aset', 20, '2026-01-22 02:21:36', '2026-01-22 02:21:36'),
	(101, 'master-data.aset.show', 'View Detail Aset', 'master-data', 'master-data.aset', 'Melihat detail aset', 22, '2026-01-22 02:21:36', '2026-01-22 02:21:36'),
	(105, 'master-data.kode-barang.index', 'View Kode Barang', 'master-data', 'master-data.kode-barang', 'Melihat daftar kode barang', 20, '2026-01-22 02:21:37', '2026-01-22 02:21:37'),
	(108, 'master-data.kode-barang.show', 'View Detail Kode Barang', 'master-data', 'master-data.kode-barang', 'Melihat detail kode barang', 22, '2026-01-22 02:21:37', '2026-01-22 02:21:37'),
	(112, 'master-data.kategori-barang.index', 'View Kategori Barang', 'master-data', 'master-data.kategori-barang', 'Melihat daftar kategori barang', 20, '2026-01-22 02:21:37', '2026-01-22 02:21:37'),
	(115, 'master-data.kategori-barang.show', 'View Detail Kategori Barang', 'master-data', 'master-data.kategori-barang', 'Melihat detail kategori barang', 22, '2026-01-22 02:21:37', '2026-01-22 02:21:37'),
	(119, 'master-data.jenis-barang.index', 'View Jenis Barang', 'master-data', 'master-data.jenis-barang', 'Melihat daftar jenis barang', 20, '2026-01-22 02:21:38', '2026-01-22 02:21:38'),
	(122, 'master-data.jenis-barang.show', 'View Detail Jenis Barang', 'master-data', 'master-data.jenis-barang', 'Melihat detail jenis barang', 22, '2026-01-22 02:21:38', '2026-01-22 02:21:38'),
	(126, 'master-data.subjenis-barang.index', 'View Subjenis Barang', 'master-data', 'master-data.subjenis-barang', 'Melihat daftar subjenis barang', 20, '2026-01-22 02:21:38', '2026-01-22 02:21:38'),
	(129, 'master-data.subjenis-barang.show', 'View Detail Subjenis Barang', 'master-data', 'master-data.subjenis-barang', 'Melihat detail subjenis barang', 22, '2026-01-22 02:21:38', '2026-01-22 02:21:38'),
	(133, 'master-data.data-barang.index', 'View Data Barang', 'master-data', 'master-data.data-barang', 'Melihat daftar data barang', 20, '2026-01-22 02:21:39', '2026-01-22 02:21:39'),
	(136, 'master-data.data-barang.show', 'View Detail Data Barang', 'master-data', 'master-data.data-barang', 'Melihat detail data barang', 22, '2026-01-22 02:21:39', '2026-01-22 02:21:39'),
	(140, 'master-data.satuan.index', 'View Satuan', 'master-data', 'master-data.satuan', 'Melihat daftar satuan', 20, '2026-01-22 02:21:39', '2026-01-22 02:21:39'),
	(143, 'master-data.satuan.show', 'View Detail Satuan', 'master-data', 'master-data.satuan', 'Melihat detail satuan', 22, '2026-01-22 02:21:39', '2026-01-22 02:21:39'),
	(147, 'master-data.sumber-anggaran.index', 'View Sumber Anggaran', 'master-data', 'master-data.sumber-anggaran', 'Melihat daftar sumber anggaran', 20, '2026-01-22 02:21:40', '2026-01-22 02:21:40'),
	(150, 'master-data.sumber-anggaran.show', 'View Detail Sumber Anggaran', 'master-data', 'master-data.sumber-anggaran', 'Melihat detail sumber anggaran', 22, '2026-01-22 02:21:40', '2026-01-22 02:21:40'),
	(155, 'inventory.data-inventory.show', 'View Detail Data Inventory', 'inventory', 'inventory.data-inventory', 'Melihat detail data inventory', 102, '2026-01-22 02:21:40', '2026-01-22 02:21:40'),
	(158, 'inventory.inventory-item.index', 'View Inventory Item', 'inventory', 'inventory.inventory-item', 'Melihat daftar inventory item', 100, '2026-01-22 02:21:40', '2026-01-22 02:21:40'),
	(161, 'inventory.inventory-item.show', 'View Detail Inventory Item', 'inventory', 'inventory.inventory-item', 'Melihat detail inventory item', 102, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(166, 'api.gudang.inventory', 'Inventory Gudang', 'api', 'api.gudang', 'Inventory gudang', 900, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(167, 'api.permintaan.detail', 'Detail Permintaan', 'api', 'api.permintaan', 'Detail permintaan', 900, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(168, 'api.distribusi.detail', 'Detail Distribusi', 'api', 'api.distribusi', 'Detail distribusi', 940, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(172, 'transaction.permintaan-barang.ajukan', 'Ajukan Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Mengajukan permintaan barang', 205, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(173, 'transaction.approval.diagram', 'View Diagram Approval', 'transaction', 'transaction.approval', 'Melihat diagram approval', 223, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(174, 'transaction.approval.kembalikan', 'Kembalikan Approval', 'transaction', 'transaction.approval', 'Mengembalikan approval', 218, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(175, 'transaction.approval.disposisi', 'Disposisi Approval', 'transaction', 'transaction.approval', 'Melakukan disposisi approval', 221, '2026-01-22 02:21:41', '2026-01-22 02:21:41'),
	(176, 'transaction.draft-distribusi.index', 'View Draft Distribusi', 'transaction', 'transaction.draft-distribusi', 'Melihat daftar draft distribusi', 220, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(179, 'transaction.draft-distribusi.show', 'View Detail Draft Distribusi', 'transaction', 'transaction.draft-distribusi', 'Melihat detail draft distribusi', 222, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(180, 'transaction.compile-distribusi.index', 'View Compile Distribusi', 'transaction', 'transaction.compile-distribusi', 'Melihat daftar compile distribusi', 230, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(183, 'transaction.distribusi.index', 'View Distribusi', 'transaction', 'transaction.distribusi', 'Melihat daftar distribusi', 240, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(186, 'transaction.distribusi.show', 'View Detail Distribusi', 'transaction', 'transaction.distribusi', 'Melihat detail distribusi', 242, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(190, 'transaction.distribusi.kirim', 'Kirim Distribusi', 'transaction', 'transaction.distribusi', 'Mengirim distribusi', 252, '2026-01-22 02:21:42', '2026-01-22 02:21:42'),
	(191, 'transaction.distribusi.api.gudang-tujuan', 'Gudang-tujuan Api', 'transaction', 'transaction.distribusi', 'Gudang-tujuan api', 240, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(192, 'transaction.penerimaan-barang.index', 'View Penerimaan Barang', 'transaction', 'transaction.penerimaan-barang', 'Melihat daftar penerimaan barang', 250, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(195, 'transaction.penerimaan-barang.show', 'View Detail Penerimaan Barang', 'transaction', 'transaction.penerimaan-barang', 'Melihat detail penerimaan barang', 252, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(199, 'transaction.retur-barang.index', 'View Retur Barang', 'transaction', 'transaction.retur-barang', 'Melihat daftar retur barang', 260, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(202, 'transaction.retur-barang.show', 'View Detail Retur Barang', 'transaction', 'transaction.retur-barang', 'Melihat detail retur barang', 262, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(206, 'transaction.retur-barang.penerimaan.detail', 'Detail Penerimaan', 'transaction', 'transaction.retur-barang', 'Detail penerimaan', 260, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(207, 'asset.register-aset.index', 'View Register Aset', 'asset', 'asset.register-aset', 'Melihat daftar register aset', 300, '2026-01-22 02:21:43', '2026-01-22 02:21:43'),
	(210, 'asset.register-aset.show', 'View Detail Register Aset', 'asset', 'asset.register-aset', 'Melihat detail register aset', 302, '2026-01-22 02:21:44', '2026-01-22 02:21:44'),
	(214, 'planning.rku.index', 'View Rku', 'planning', 'planning.rku', 'Melihat daftar rku', 400, '2026-01-22 02:21:44', '2026-01-22 02:21:44'),
	(217, 'planning.rku.show', 'View Detail Rku', 'planning', 'planning.rku', 'Melihat detail rku', 402, '2026-01-22 02:21:44', '2026-01-22 02:21:44'),
	(221, 'procurement.paket-pengadaan.index', 'View Paket Pengadaan', 'procurement', 'procurement.paket-pengadaan', 'Melihat daftar paket pengadaan', 500, '2026-01-22 02:21:44', '2026-01-22 02:21:44'),
	(224, 'procurement.paket-pengadaan.show', 'View Detail Paket Pengadaan', 'procurement', 'procurement.paket-pengadaan', 'Melihat detail paket pengadaan', 502, '2026-01-22 02:21:45', '2026-01-22 02:21:45'),
	(228, 'finance.pembayaran.index', 'View Pembayaran', 'finance', 'finance.pembayaran', 'Melihat daftar pembayaran', 600, '2026-01-22 02:21:45', '2026-01-22 02:21:45'),
	(231, 'finance.pembayaran.show', 'View Detail Pembayaran', 'finance', 'finance.pembayaran', 'Melihat detail pembayaran', 602, '2026-01-22 02:21:45', '2026-01-22 02:21:45'),
	(235, 'admin.roles.index', 'View Roles', 'admin', 'admin.roles', 'Melihat daftar roles', 700, '2026-01-22 02:21:45', '2026-01-22 02:21:45'),
	(238, 'admin.roles.show', 'View Detail Roles', 'admin', 'admin.roles', 'Melihat detail roles', 702, '2026-01-22 02:21:46', '2026-01-22 02:21:46'),
	(242, 'admin.users.index', 'View Users', 'admin', 'admin.users', 'Melihat daftar users', 700, '2026-01-22 02:21:46', '2026-01-22 02:21:46'),
	(245, 'admin.users.show', 'View Detail Users', 'admin', 'admin.users', 'Melihat detail users', 702, '2026-01-22 02:21:46', '2026-01-22 02:21:46'),
	(249, 'reports.index', 'View Reports', 'reports', 'reports.index', 'Melihat daftar reports', 800, '2026-01-22 02:21:46', '2026-01-22 02:21:46'),
	(250, 'reports.stock-gudang.export', 'Export Stock Gudang', 'reports', 'reports.stock-gudang', 'Export stock gudang', 800, '2026-01-22 02:21:46', '2026-01-22 02:21:46'),
	(252, 'maintenance.permintaan-pemeliharaan.index', 'View Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Melihat daftar permintaan pemeliharaan', 900, '2026-01-22 02:32:10', '2026-01-22 02:32:10'),
	(255, 'maintenance.permintaan-pemeliharaan.show', 'View Detail Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Melihat detail permintaan pemeliharaan', 902, '2026-01-22 02:32:10', '2026-01-22 02:32:10'),
	(259, 'maintenance.permintaan-pemeliharaan.ajukan', 'Ajukan Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Mengajukan permintaan pemeliharaan', 905, '2026-01-22 02:32:11', '2026-01-22 02:32:11'),
	(260, 'maintenance.jadwal-maintenance.index', 'View Jadwal Maintenance', 'maintenance', 'maintenance.jadwal-maintenance', 'Melihat daftar jadwal maintenance', 900, '2026-01-22 02:32:11', '2026-01-22 02:32:11'),
	(263, 'maintenance.jadwal-maintenance.show', 'View Detail Jadwal Maintenance', 'maintenance', 'maintenance.jadwal-maintenance', 'Melihat detail jadwal maintenance', 902, '2026-01-22 02:32:12', '2026-01-22 02:32:12'),
	(267, 'maintenance.kalibrasi-aset.index', 'View Kalibrasi Aset', 'maintenance', 'maintenance.kalibrasi-aset', 'Melihat daftar kalibrasi aset', 900, '2026-01-22 02:32:12', '2026-01-22 02:32:12'),
	(270, 'maintenance.kalibrasi-aset.show', 'View Detail Kalibrasi Aset', 'maintenance', 'maintenance.kalibrasi-aset', 'Melihat detail kalibrasi aset', 902, '2026-01-22 02:32:13', '2026-01-22 02:32:13'),
	(274, 'maintenance.service-report.index', 'View Service Report', 'maintenance', 'maintenance.service-report', 'Melihat daftar service report', 900, '2026-01-22 02:32:13', '2026-01-22 02:32:13'),
	(277, 'maintenance.service-report.show', 'View Detail Service Report', 'maintenance', 'maintenance.service-report', 'Melihat detail service report', 902, '2026-01-22 02:32:14', '2026-01-22 02:32:14'),
	(281, 'master-manajemen.master-pegawai.*', 'Master Manajemen Master Pegawai (All)', 'master-manajemen', 'master-manajemen.master-pegawai', 'Akses penuh ke master-manajemen master-pegawai', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(282, 'master-manajemen.master-jabatan.*', 'Master Manajemen Master Jabatan (All)', 'master-manajemen', 'master-manajemen.master-jabatan', 'Akses penuh ke master-manajemen master-jabatan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(283, 'inventory.data-inventory.*', 'Inventory Data Inventory (All)', 'inventory', 'inventory.data-inventory', 'Akses penuh ke inventory data-inventory', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(284, 'transaction.permintaan-barang.*', 'Transaction Permintaan Barang (All)', 'transaction', 'transaction.permintaan-barang', 'Akses penuh ke transaction permintaan-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(285, 'user.requests.*', 'User Requests (All)', 'user', 'user.requests', 'Akses penuh ke user requests', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(286, 'master.program.*', 'Master Program (All)', 'master', 'master.program', 'Akses penuh ke master program', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(287, 'master.kegiatan.*', 'Master Kegiatan (All)', 'master', 'master.kegiatan', 'Akses penuh ke master kegiatan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(288, 'master.sub-kegiatan.*', 'Master Sub Kegiatan (All)', 'master', 'master.sub-kegiatan', 'Akses penuh ke master sub-kegiatan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(289, 'master-data.aset.*', 'Master Data Aset (All)', 'master-data', 'master-data.aset', 'Akses penuh ke master-data aset', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(290, 'master-data.kode-barang.*', 'Master Data Kode Barang (All)', 'master-data', 'master-data.kode-barang', 'Akses penuh ke master-data kode-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(291, 'master-data.kategori-barang.*', 'Master Data Kategori Barang (All)', 'master-data', 'master-data.kategori-barang', 'Akses penuh ke master-data kategori-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(292, 'master-data.jenis-barang.*', 'Master Data Jenis Barang (All)', 'master-data', 'master-data.jenis-barang', 'Akses penuh ke master-data jenis-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(293, 'master-data.subjenis-barang.*', 'Master Data Subjenis Barang (All)', 'master-data', 'master-data.subjenis-barang', 'Akses penuh ke master-data subjenis-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(294, 'master-data.data-barang.*', 'Master Data Data Barang (All)', 'master-data', 'master-data.data-barang', 'Akses penuh ke master-data data-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(295, 'master-data.satuan.*', 'Master Data Satuan (All)', 'master-data', 'master-data.satuan', 'Akses penuh ke master-data satuan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(296, 'master-data.sumber-anggaran.*', 'Master Data Sumber Anggaran (All)', 'master-data', 'master-data.sumber-anggaran', 'Akses penuh ke master-data sumber-anggaran', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(297, 'transaction.draft-distribusi.*', 'Transaction Draft Distribusi (All)', 'transaction', 'transaction.draft-distribusi', 'Akses penuh ke transaction draft-distribusi', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(298, 'transaction.compile-distribusi.*', 'Transaction Compile Distribusi (All)', 'transaction', 'transaction.compile-distribusi', 'Akses penuh ke transaction compile-distribusi', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(299, 'transaction.retur-barang.*', 'Transaction Retur Barang (All)', 'transaction', 'transaction.retur-barang', 'Akses penuh ke transaction retur-barang', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(300, 'planning.rku.*', 'Planning Rku (All)', 'planning', 'planning.rku', 'Akses penuh ke planning rku', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(301, 'procurement.paket-pengadaan.*', 'Procurement Paket Pengadaan (All)', 'procurement', 'procurement.paket-pengadaan', 'Akses penuh ke procurement paket-pengadaan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(302, 'finance.pembayaran.*', 'Finance Pembayaran (All)', 'finance', 'finance.pembayaran', 'Akses penuh ke finance pembayaran', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(303, 'maintenance.permintaan-pemeliharaan.*', 'Maintenance Permintaan Pemeliharaan (All)', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Akses penuh ke maintenance permintaan-pemeliharaan', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(304, 'maintenance.jadwal-maintenance.*', 'Maintenance Jadwal Maintenance (All)', 'maintenance', 'maintenance.jadwal-maintenance', 'Akses penuh ke maintenance jadwal-maintenance', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(305, 'maintenance.kalibrasi-aset.*', 'Maintenance Kalibrasi Aset (All)', 'maintenance', 'maintenance.kalibrasi-aset', 'Akses penuh ke maintenance kalibrasi-aset', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(306, 'maintenance.service-report.*', 'Maintenance Service Report (All)', 'maintenance', 'maintenance.service-report', 'Akses penuh ke maintenance service-report', 999, '2026-01-22 02:39:28', '2026-01-22 02:39:28'),
	(307, 'user.requests.create', 'Create Requests', 'dashboard', 'user.requests', 'Membuat requests', 2, '2026-01-22 03:06:32', '2026-01-22 03:06:32'),
	(309, 'master-manajemen.master-pegawai.create', 'Create Master Pegawai', 'master-manajemen', 'master-manajemen.master-pegawai', 'Membuat master pegawai', 11, '2026-01-22 03:06:32', '2026-01-22 03:06:32'),
	(311, 'master-manajemen.master-pegawai.edit', 'Edit Master Pegawai', 'master-manajemen', 'master-manajemen.master-pegawai', 'Mengedit master pegawai', 13, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(313, 'master-manajemen.master-pegawai.destroy', 'Delete Master Pegawai', 'master-manajemen', 'master-manajemen.master-pegawai', 'Menghapus master pegawai', 14, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(314, 'master-manajemen.master-jabatan.create', 'Create Master Jabatan', 'master-manajemen', 'master-manajemen.master-jabatan', 'Membuat master jabatan', 11, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(316, 'master-manajemen.master-jabatan.edit', 'Edit Master Jabatan', 'master-manajemen', 'master-manajemen.master-jabatan', 'Mengedit master jabatan', 13, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(318, 'master-manajemen.master-jabatan.destroy', 'Delete Master Jabatan', 'master-manajemen', 'master-manajemen.master-jabatan', 'Menghapus master jabatan', 14, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(319, 'master.unit-kerja.create', 'Create Unit Kerja', 'master-manajemen', 'master.unit-kerja', 'Membuat unit kerja', 11, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(321, 'master.unit-kerja.edit', 'Edit Unit Kerja', 'master-manajemen', 'master.unit-kerja', 'Mengedit unit kerja', 13, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(323, 'master.unit-kerja.destroy', 'Delete Unit Kerja', 'master-manajemen', 'master.unit-kerja', 'Menghapus unit kerja', 14, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(324, 'master.gudang.create', 'Create Gudang', 'master-manajemen', 'master.gudang', 'Membuat gudang', 11, '2026-01-22 03:06:33', '2026-01-22 03:06:33'),
	(326, 'master.gudang.edit', 'Edit Gudang', 'master-manajemen', 'master.gudang', 'Mengedit gudang', 13, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(328, 'master.gudang.destroy', 'Delete Gudang', 'master-manajemen', 'master.gudang', 'Menghapus gudang', 14, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(329, 'master.ruangan.create', 'Create Ruangan', 'master-manajemen', 'master.ruangan', 'Membuat ruangan', 11, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(331, 'master.ruangan.edit', 'Edit Ruangan', 'master-manajemen', 'master.ruangan', 'Mengedit ruangan', 13, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(333, 'master.ruangan.destroy', 'Delete Ruangan', 'master-manajemen', 'master.ruangan', 'Menghapus ruangan', 14, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(334, 'master.program.create', 'Create Program', 'master-manajemen', 'master.program', 'Membuat program', 11, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(336, 'master.program.edit', 'Edit Program', 'master-manajemen', 'master.program', 'Mengedit program', 13, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(338, 'master.program.destroy', 'Delete Program', 'master-manajemen', 'master.program', 'Menghapus program', 14, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(339, 'master.kegiatan.create', 'Create Kegiatan', 'master-manajemen', 'master.kegiatan', 'Membuat kegiatan', 11, '2026-01-22 03:06:34', '2026-01-22 03:06:34'),
	(341, 'master.kegiatan.edit', 'Edit Kegiatan', 'master-manajemen', 'master.kegiatan', 'Mengedit kegiatan', 13, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(343, 'master.kegiatan.destroy', 'Delete Kegiatan', 'master-manajemen', 'master.kegiatan', 'Menghapus kegiatan', 14, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(344, 'master.sub-kegiatan.create', 'Create Sub Kegiatan', 'master-manajemen', 'master.sub-kegiatan', 'Membuat sub kegiatan', 11, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(346, 'master.sub-kegiatan.edit', 'Edit Sub Kegiatan', 'master-manajemen', 'master.sub-kegiatan', 'Mengedit sub kegiatan', 13, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(348, 'master.sub-kegiatan.destroy', 'Delete Sub Kegiatan', 'master-manajemen', 'master.sub-kegiatan', 'Menghapus sub kegiatan', 14, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(349, 'master-data.aset.create', 'Create Aset', 'master-data', 'master-data.aset', 'Membuat aset', 21, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(351, 'master-data.aset.edit', 'Edit Aset', 'master-data', 'master-data.aset', 'Mengedit aset', 23, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(353, 'master-data.aset.destroy', 'Delete Aset', 'master-data', 'master-data.aset', 'Menghapus aset', 24, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(354, 'master-data.kode-barang.create', 'Create Kode Barang', 'master-data', 'master-data.kode-barang', 'Membuat kode barang', 21, '2026-01-22 03:06:35', '2026-01-22 03:06:35'),
	(356, 'master-data.kode-barang.edit', 'Edit Kode Barang', 'master-data', 'master-data.kode-barang', 'Mengedit kode barang', 23, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(358, 'master-data.kode-barang.destroy', 'Delete Kode Barang', 'master-data', 'master-data.kode-barang', 'Menghapus kode barang', 24, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(359, 'master-data.kategori-barang.create', 'Create Kategori Barang', 'master-data', 'master-data.kategori-barang', 'Membuat kategori barang', 21, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(361, 'master-data.kategori-barang.edit', 'Edit Kategori Barang', 'master-data', 'master-data.kategori-barang', 'Mengedit kategori barang', 23, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(363, 'master-data.kategori-barang.destroy', 'Delete Kategori Barang', 'master-data', 'master-data.kategori-barang', 'Menghapus kategori barang', 24, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(364, 'master-data.jenis-barang.create', 'Create Jenis Barang', 'master-data', 'master-data.jenis-barang', 'Membuat jenis barang', 21, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(366, 'master-data.jenis-barang.edit', 'Edit Jenis Barang', 'master-data', 'master-data.jenis-barang', 'Mengedit jenis barang', 23, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(368, 'master-data.jenis-barang.destroy', 'Delete Jenis Barang', 'master-data', 'master-data.jenis-barang', 'Menghapus jenis barang', 24, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(369, 'master-data.subjenis-barang.create', 'Create Subjenis Barang', 'master-data', 'master-data.subjenis-barang', 'Membuat subjenis barang', 21, '2026-01-22 03:06:36', '2026-01-22 03:06:36'),
	(371, 'master-data.subjenis-barang.edit', 'Edit Subjenis Barang', 'master-data', 'master-data.subjenis-barang', 'Mengedit subjenis barang', 23, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(373, 'master-data.subjenis-barang.destroy', 'Delete Subjenis Barang', 'master-data', 'master-data.subjenis-barang', 'Menghapus subjenis barang', 24, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(374, 'master-data.data-barang.create', 'Create Data Barang', 'master-data', 'master-data.data-barang', 'Membuat data barang', 21, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(376, 'master-data.data-barang.edit', 'Edit Data Barang', 'master-data', 'master-data.data-barang', 'Mengedit data barang', 23, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(378, 'master-data.data-barang.destroy', 'Delete Data Barang', 'master-data', 'master-data.data-barang', 'Menghapus data barang', 24, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(379, 'master-data.satuan.create', 'Create Satuan', 'master-data', 'master-data.satuan', 'Membuat satuan', 21, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(381, 'master-data.satuan.edit', 'Edit Satuan', 'master-data', 'master-data.satuan', 'Mengedit satuan', 23, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(383, 'master-data.satuan.destroy', 'Delete Satuan', 'master-data', 'master-data.satuan', 'Menghapus satuan', 24, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(384, 'master-data.sumber-anggaran.create', 'Create Sumber Anggaran', 'master-data', 'master-data.sumber-anggaran', 'Membuat sumber anggaran', 21, '2026-01-22 03:06:37', '2026-01-22 03:06:37'),
	(386, 'master-data.sumber-anggaran.edit', 'Edit Sumber Anggaran', 'master-data', 'master-data.sumber-anggaran', 'Mengedit sumber anggaran', 23, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(388, 'master-data.sumber-anggaran.destroy', 'Delete Sumber Anggaran', 'master-data', 'master-data.sumber-anggaran', 'Menghapus sumber anggaran', 24, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(389, 'inventory.data-inventory.create', 'Create Data Inventory', 'inventory', 'inventory.data-inventory', 'Membuat data inventory', 101, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(391, 'inventory.data-inventory.edit', 'Edit Data Inventory', 'inventory', 'inventory.data-inventory', 'Mengedit data inventory', 103, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(393, 'inventory.data-inventory.destroy', 'Delete Data Inventory', 'inventory', 'inventory.data-inventory', 'Menghapus data inventory', 104, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(394, 'inventory.inventory-item.create', 'Create Inventory Item', 'inventory', 'inventory.inventory-item', 'Membuat inventory item', 101, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(396, 'inventory.inventory-item.edit', 'Edit Inventory Item', 'inventory', 'inventory.inventory-item', 'Mengedit inventory item', 103, '2026-01-22 03:06:38', '2026-01-22 03:06:38'),
	(398, 'inventory.inventory-item.destroy', 'Delete Inventory Item', 'inventory', 'inventory.inventory-item', 'Menghapus inventory item', 104, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(399, 'transaction.permintaan-barang.create', 'Create Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Membuat permintaan barang', 201, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(401, 'transaction.permintaan-barang.edit', 'Edit Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Mengedit permintaan barang', 203, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(403, 'transaction.permintaan-barang.destroy', 'Delete Permintaan Barang', 'transaction', 'transaction.permintaan-barang', 'Menghapus permintaan barang', 204, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(404, 'transaction.draft-distribusi.create', 'Create Draft Distribusi', 'transaction', 'transaction.draft-distribusi', 'Membuat draft distribusi', 221, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(406, 'transaction.compile-distribusi.create', 'Create Compile Distribusi', 'transaction', 'transaction.compile-distribusi', 'Membuat compile distribusi', 231, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(408, 'transaction.distribusi.create', 'Create Distribusi', 'transaction', 'transaction.distribusi', 'Membuat distribusi', 241, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(410, 'transaction.distribusi.edit', 'Edit Distribusi', 'transaction', 'transaction.distribusi', 'Mengedit distribusi', 243, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(412, 'transaction.distribusi.destroy', 'Delete Distribusi', 'transaction', 'transaction.distribusi', 'Menghapus distribusi', 244, '2026-01-22 03:06:39', '2026-01-22 03:06:39'),
	(413, 'transaction.penerimaan-barang.create', 'Create Penerimaan Barang', 'transaction', 'transaction.penerimaan-barang', 'Membuat penerimaan barang', 251, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(415, 'transaction.penerimaan-barang.edit', 'Edit Penerimaan Barang', 'transaction', 'transaction.penerimaan-barang', 'Mengedit penerimaan barang', 253, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(417, 'transaction.penerimaan-barang.destroy', 'Delete Penerimaan Barang', 'transaction', 'transaction.penerimaan-barang', 'Menghapus penerimaan barang', 254, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(418, 'transaction.retur-barang.create', 'Create Retur Barang', 'transaction', 'transaction.retur-barang', 'Membuat retur barang', 261, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(420, 'transaction.retur-barang.edit', 'Edit Retur Barang', 'transaction', 'transaction.retur-barang', 'Mengedit retur barang', 263, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(422, 'transaction.retur-barang.destroy', 'Delete Retur Barang', 'transaction', 'transaction.retur-barang', 'Menghapus retur barang', 264, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(423, 'asset.register-aset.create', 'Create Register Aset', 'asset', 'asset.register-aset', 'Membuat register aset', 301, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(425, 'asset.register-aset.edit', 'Edit Register Aset', 'asset', 'asset.register-aset', 'Mengedit register aset', 303, '2026-01-22 03:06:40', '2026-01-22 03:06:40'),
	(427, 'asset.register-aset.destroy', 'Delete Register Aset', 'asset', 'asset.register-aset', 'Menghapus register aset', 304, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(428, 'maintenance.permintaan-pemeliharaan.create', 'Create Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Membuat permintaan pemeliharaan', 901, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(430, 'maintenance.permintaan-pemeliharaan.edit', 'Edit Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Mengedit permintaan pemeliharaan', 903, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(432, 'maintenance.permintaan-pemeliharaan.destroy', 'Delete Permintaan Pemeliharaan', 'maintenance', 'maintenance.permintaan-pemeliharaan', 'Menghapus permintaan pemeliharaan', 904, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(433, 'maintenance.jadwal-maintenance.create', 'Create Jadwal Maintenance', 'maintenance', 'maintenance.jadwal-maintenance', 'Membuat jadwal maintenance', 901, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(435, 'maintenance.jadwal-maintenance.edit', 'Edit Jadwal Maintenance', 'maintenance', 'maintenance.jadwal-maintenance', 'Mengedit jadwal maintenance', 903, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(437, 'maintenance.jadwal-maintenance.destroy', 'Delete Jadwal Maintenance', 'maintenance', 'maintenance.jadwal-maintenance', 'Menghapus jadwal maintenance', 904, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(438, 'maintenance.kalibrasi-aset.create', 'Create Kalibrasi Aset', 'maintenance', 'maintenance.kalibrasi-aset', 'Membuat kalibrasi aset', 901, '2026-01-22 03:06:41', '2026-01-22 03:06:41'),
	(440, 'maintenance.kalibrasi-aset.edit', 'Edit Kalibrasi Aset', 'maintenance', 'maintenance.kalibrasi-aset', 'Mengedit kalibrasi aset', 903, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(442, 'maintenance.kalibrasi-aset.destroy', 'Delete Kalibrasi Aset', 'maintenance', 'maintenance.kalibrasi-aset', 'Menghapus kalibrasi aset', 904, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(443, 'maintenance.service-report.create', 'Create Service Report', 'maintenance', 'maintenance.service-report', 'Membuat service report', 901, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(445, 'maintenance.service-report.edit', 'Edit Service Report', 'maintenance', 'maintenance.service-report', 'Mengedit service report', 903, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(447, 'maintenance.service-report.destroy', 'Delete Service Report', 'maintenance', 'maintenance.service-report', 'Menghapus service report', 904, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(448, 'planning.rku.create', 'Create Rku', 'planning', 'planning.rku', 'Membuat rku', 401, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(450, 'planning.rku.edit', 'Edit Rku', 'planning', 'planning.rku', 'Mengedit rku', 403, '2026-01-22 03:06:42', '2026-01-22 03:06:42'),
	(452, 'planning.rku.destroy', 'Delete Rku', 'planning', 'planning.rku', 'Menghapus rku', 404, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(453, 'procurement.paket-pengadaan.create', 'Create Paket Pengadaan', 'procurement', 'procurement.paket-pengadaan', 'Membuat paket pengadaan', 501, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(455, 'procurement.paket-pengadaan.edit', 'Edit Paket Pengadaan', 'procurement', 'procurement.paket-pengadaan', 'Mengedit paket pengadaan', 503, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(457, 'procurement.paket-pengadaan.destroy', 'Delete Paket Pengadaan', 'procurement', 'procurement.paket-pengadaan', 'Menghapus paket pengadaan', 504, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(458, 'finance.pembayaran.create', 'Create Pembayaran', 'finance', 'finance.pembayaran', 'Membuat pembayaran', 601, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(460, 'finance.pembayaran.edit', 'Edit Pembayaran', 'finance', 'finance.pembayaran', 'Mengedit pembayaran', 603, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(462, 'finance.pembayaran.destroy', 'Delete Pembayaran', 'finance', 'finance.pembayaran', 'Menghapus pembayaran', 604, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(463, 'admin.roles.create', 'Create Roles', 'admin', 'admin.roles', 'Membuat roles', 701, '2026-01-22 03:06:43', '2026-01-22 03:06:43'),
	(465, 'admin.roles.edit', 'Edit Roles', 'admin', 'admin.roles', 'Mengedit roles', 703, '2026-01-22 03:06:44', '2026-01-22 03:06:44'),
	(467, 'admin.roles.destroy', 'Delete Roles', 'admin', 'admin.roles', 'Menghapus roles', 704, '2026-01-22 03:06:44', '2026-01-22 03:06:44'),
	(468, 'admin.users.create', 'Create Users', 'admin', 'admin.users', 'Membuat users', 701, '2026-01-22 03:06:44', '2026-01-22 03:06:44'),
	(470, 'admin.users.edit', 'Edit Users', 'admin', 'admin.users', 'Mengedit users', 703, '2026-01-22 03:06:44', '2026-01-22 03:06:44'),
	(472, 'admin.users.destroy', 'Delete Users', 'admin', 'admin.users', 'Menghapus users', 704, '2026-01-22 03:06:44', '2026-01-22 03:06:44');

-- Dumping structure for table simantik.permission_role
CREATE TABLE IF NOT EXISTS `permission_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_role_permission_id_role_id_unique` (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=649 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.permission_role: ~590 rows (approximately)
REPLACE INTO `permission_role` (`id`, `permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(1, 19, 11, NULL, NULL),
	(4, 22, 11, NULL, NULL),
	(11, 29, 11, NULL, NULL),
	(13, 31, 11, NULL, NULL),
	(14, 19, 13, NULL, NULL),
	(17, 29, 13, NULL, NULL),
	(18, 30, 13, NULL, NULL),
	(19, 31, 13, NULL, NULL),
	(20, 30, 11, NULL, NULL),
	(23, 32, 1, NULL, NULL),
	(24, 13, 1, NULL, NULL),
	(25, 14, 1, NULL, NULL),
	(29, 18, 1, NULL, NULL),
	(30, 2, 1, NULL, NULL),
	(34, 6, 1, NULL, NULL),
	(38, 10, 1, NULL, NULL),
	(39, 11, 1, NULL, NULL),
	(40, 12, 1, NULL, NULL),
	(41, 33, 1, NULL, NULL),
	(42, 34, 1, NULL, NULL),
	(43, 19, 1, NULL, NULL),
	(46, 22, 1, NULL, NULL),
	(47, 23, 1, NULL, NULL),
	(48, 24, 1, NULL, NULL),
	(49, 25, 1, NULL, NULL),
	(50, 26, 1, NULL, NULL),
	(51, 27, 1, NULL, NULL),
	(52, 28, 1, NULL, NULL),
	(53, 29, 1, NULL, NULL),
	(54, 30, 1, NULL, NULL),
	(55, 31, 1, NULL, NULL),
	(57, 19, 7, NULL, NULL),
	(60, 22, 7, NULL, NULL),
	(61, 23, 7, NULL, NULL),
	(62, 24, 7, NULL, NULL),
	(63, 25, 7, NULL, NULL),
	(64, 26, 7, NULL, NULL),
	(65, 27, 7, NULL, NULL),
	(66, 28, 7, NULL, NULL),
	(70, 19, 6, NULL, NULL),
	(73, 22, 6, NULL, NULL),
	(74, 23, 6, NULL, NULL),
	(75, 24, 6, NULL, NULL),
	(76, 25, 6, NULL, NULL),
	(77, 26, 6, NULL, NULL),
	(78, 27, 6, NULL, NULL),
	(79, 28, 6, NULL, NULL),
	(80, 29, 6, NULL, NULL),
	(81, 30, 6, NULL, NULL),
	(82, 31, 6, NULL, NULL),
	(85, 13, 7, NULL, NULL),
	(86, 14, 7, NULL, NULL),
	(87, 18, 7, NULL, NULL),
	(88, 33, 7, NULL, NULL),
	(89, 34, 7, NULL, NULL),
	(90, 32, 6, NULL, NULL),
	(91, 1, 6, NULL, NULL),
	(92, 13, 6, NULL, NULL),
	(93, 14, 6, NULL, NULL),
	(94, 18, 6, NULL, NULL),
	(95, 33, 6, NULL, NULL),
	(96, 34, 6, NULL, NULL),
	(97, 281, 1, NULL, NULL),
	(98, 282, 1, NULL, NULL),
	(99, 283, 1, NULL, NULL),
	(100, 284, 1, NULL, NULL),
	(101, 283, 6, NULL, NULL),
	(102, 284, 6, NULL, NULL),
	(103, 283, 7, NULL, NULL),
	(104, 284, 7, NULL, NULL),
	(105, 284, 11, NULL, NULL),
	(106, 284, 13, NULL, NULL),
	(111, 158, 7, NULL, NULL),
	(112, 155, 7, NULL, NULL),
	(113, 161, 7, NULL, NULL),
	(114, 214, 7, NULL, NULL),
	(115, 217, 7, NULL, NULL),
	(116, 221, 7, NULL, NULL),
	(117, 224, 7, NULL, NULL),
	(118, 249, 7, NULL, NULL),
	(119, 250, 7, NULL, NULL),
	(120, 172, 7, NULL, NULL),
	(121, 175, 7, NULL, NULL),
	(122, 183, 7, NULL, NULL),
	(123, 186, 7, NULL, NULL),
	(124, 192, 7, NULL, NULL),
	(125, 195, 7, NULL, NULL),
	(126, 199, 7, NULL, NULL),
	(127, 174, 6, NULL, NULL),
	(128, 176, 6, NULL, NULL),
	(129, 175, 6, NULL, NULL),
	(130, 183, 6, NULL, NULL),
	(131, 195, 6, NULL, NULL),
	(132, 199, 6, NULL, NULL),
	(133, 206, 6, NULL, NULL),
	(134, 202, 6, NULL, NULL),
	(135, 174, 7, NULL, NULL),
	(136, 176, 7, NULL, NULL),
	(137, 179, 7, NULL, NULL),
	(138, 401, 7, NULL, NULL),
	(140, 206, 7, NULL, NULL),
	(141, 202, 7, NULL, NULL),
	(142, 175, 2, NULL, NULL),
	(143, 175, 1, NULL, NULL),
	(144, 166, 14, NULL, NULL),
	(145, 167, 14, NULL, NULL),
	(146, 168, 14, NULL, NULL),
	(147, 32, 14, NULL, NULL),
	(148, 1, 14, NULL, NULL),
	(149, 42, 14, NULL, NULL),
	(150, 44, 14, NULL, NULL),
	(151, 307, 14, NULL, NULL),
	(153, 43, 14, NULL, NULL),
	(154, 47, 14, NULL, NULL),
	(155, 13, 14, NULL, NULL),
	(156, 158, 14, NULL, NULL),
	(157, 389, 14, NULL, NULL),
	(159, 394, 14, NULL, NULL),
	(161, 155, 14, NULL, NULL),
	(162, 161, 14, NULL, NULL),
	(163, 391, 14, NULL, NULL),
	(165, 396, 14, NULL, NULL),
	(167, 393, 14, NULL, NULL),
	(168, 398, 14, NULL, NULL),
	(169, 14, 14, NULL, NULL),
	(170, 18, 14, NULL, NULL),
	(171, 283, 14, NULL, NULL),
	(172, 252, 14, NULL, NULL),
	(173, 260, 14, NULL, NULL),
	(174, 267, 14, NULL, NULL),
	(175, 274, 14, NULL, NULL),
	(176, 428, 14, NULL, NULL),
	(178, 433, 14, NULL, NULL),
	(180, 438, 14, NULL, NULL),
	(182, 443, 14, NULL, NULL),
	(184, 255, 14, NULL, NULL),
	(185, 263, 14, NULL, NULL),
	(186, 270, 14, NULL, NULL),
	(187, 277, 14, NULL, NULL),
	(188, 430, 14, NULL, NULL),
	(190, 435, 14, NULL, NULL),
	(192, 440, 14, NULL, NULL),
	(194, 445, 14, NULL, NULL),
	(196, 432, 14, NULL, NULL),
	(197, 437, 14, NULL, NULL),
	(198, 442, 14, NULL, NULL),
	(199, 447, 14, NULL, NULL),
	(200, 259, 14, NULL, NULL),
	(201, 303, 14, NULL, NULL),
	(202, 304, 14, NULL, NULL),
	(203, 305, 14, NULL, NULL),
	(204, 306, 14, NULL, NULL),
	(205, 33, 14, NULL, NULL),
	(206, 34, 14, NULL, NULL),
	(207, 249, 14, NULL, NULL),
	(208, 250, 14, NULL, NULL),
	(209, 19, 14, NULL, NULL),
	(210, 399, 14, NULL, NULL),
	(212, 22, 14, NULL, NULL),
	(213, 401, 14, NULL, NULL),
	(215, 403, 14, NULL, NULL),
	(216, 172, 14, NULL, NULL),
	(217, 30, 14, NULL, NULL),
	(218, 180, 14, NULL, NULL),
	(219, 406, 14, NULL, NULL),
	(221, 31, 14, NULL, NULL),
	(222, 183, 14, NULL, NULL),
	(223, 191, 14, NULL, NULL),
	(224, 408, 14, NULL, NULL),
	(226, 186, 14, NULL, NULL),
	(227, 195, 14, NULL, NULL),
	(228, 415, 14, NULL, NULL),
	(229, 228, 7, NULL, NULL),
	(230, 231, 7, NULL, NULL),
	(231, 252, 7, NULL, NULL),
	(232, 260, 7, NULL, NULL),
	(233, 255, 7, NULL, NULL),
	(234, 263, 7, NULL, NULL),
	(235, 270, 7, NULL, NULL),
	(236, 277, 7, NULL, NULL),
	(237, 259, 7, NULL, NULL),
	(238, 304, 7, NULL, NULL),
	(239, 305, 7, NULL, NULL),
	(240, 306, 7, NULL, NULL),
	(241, 300, 7, NULL, NULL),
	(242, 297, 7, NULL, NULL),
	(243, 298, 7, NULL, NULL),
	(244, 299, 7, NULL, NULL),
	(245, 32, 7, NULL, NULL),
	(246, 207, 7, NULL, NULL),
	(247, 210, 7, NULL, NULL),
	(248, 207, 1, NULL, NULL),
	(249, 423, 1, NULL, NULL),
	(250, 210, 1, NULL, NULL),
	(251, 425, 1, NULL, NULL),
	(252, 427, 1, NULL, NULL),
	(253, 228, 1, NULL, NULL),
	(254, 458, 1, NULL, NULL),
	(255, 231, 1, NULL, NULL),
	(256, 460, 1, NULL, NULL),
	(257, 462, 1, NULL, NULL),
	(258, 302, 1, NULL, NULL),
	(259, 158, 1, NULL, NULL),
	(260, 389, 1, NULL, NULL),
	(261, 394, 1, NULL, NULL),
	(262, 155, 1, NULL, NULL),
	(263, 161, 1, NULL, NULL),
	(264, 391, 1, NULL, NULL),
	(265, 396, 1, NULL, NULL),
	(266, 393, 1, NULL, NULL),
	(267, 398, 1, NULL, NULL),
	(268, 252, 1, NULL, NULL),
	(269, 260, 1, NULL, NULL),
	(270, 267, 1, NULL, NULL),
	(271, 274, 1, NULL, NULL),
	(272, 428, 1, NULL, NULL),
	(273, 433, 1, NULL, NULL),
	(274, 438, 1, NULL, NULL),
	(275, 443, 1, NULL, NULL),
	(276, 255, 1, NULL, NULL),
	(277, 263, 1, NULL, NULL),
	(278, 270, 1, NULL, NULL),
	(279, 277, 1, NULL, NULL),
	(280, 430, 1, NULL, NULL),
	(281, 435, 1, NULL, NULL),
	(282, 440, 1, NULL, NULL),
	(283, 445, 1, NULL, NULL),
	(284, 432, 1, NULL, NULL),
	(285, 437, 1, NULL, NULL),
	(286, 442, 1, NULL, NULL),
	(287, 447, 1, NULL, NULL),
	(288, 259, 1, NULL, NULL),
	(289, 303, 1, NULL, NULL),
	(290, 304, 1, NULL, NULL),
	(291, 305, 1, NULL, NULL),
	(292, 306, 1, NULL, NULL),
	(293, 98, 1, NULL, NULL),
	(294, 105, 1, NULL, NULL),
	(295, 112, 1, NULL, NULL),
	(296, 119, 1, NULL, NULL),
	(297, 126, 1, NULL, NULL),
	(298, 133, 1, NULL, NULL),
	(299, 140, 1, NULL, NULL),
	(300, 147, 1, NULL, NULL),
	(301, 349, 1, NULL, NULL),
	(302, 354, 1, NULL, NULL),
	(303, 359, 1, NULL, NULL),
	(304, 364, 1, NULL, NULL),
	(305, 369, 1, NULL, NULL),
	(306, 374, 1, NULL, NULL),
	(307, 379, 1, NULL, NULL),
	(308, 384, 1, NULL, NULL),
	(309, 101, 1, NULL, NULL),
	(310, 108, 1, NULL, NULL),
	(311, 115, 1, NULL, NULL),
	(312, 122, 1, NULL, NULL),
	(313, 129, 1, NULL, NULL),
	(314, 136, 1, NULL, NULL),
	(315, 143, 1, NULL, NULL),
	(316, 150, 1, NULL, NULL),
	(317, 351, 1, NULL, NULL),
	(318, 356, 1, NULL, NULL),
	(319, 361, 1, NULL, NULL),
	(320, 366, 1, NULL, NULL),
	(321, 371, 1, NULL, NULL),
	(322, 376, 1, NULL, NULL),
	(323, 381, 1, NULL, NULL),
	(324, 386, 1, NULL, NULL),
	(325, 353, 1, NULL, NULL),
	(326, 358, 1, NULL, NULL),
	(327, 363, 1, NULL, NULL),
	(328, 368, 1, NULL, NULL),
	(329, 373, 1, NULL, NULL),
	(330, 378, 1, NULL, NULL),
	(331, 383, 1, NULL, NULL),
	(332, 388, 1, NULL, NULL),
	(333, 289, 1, NULL, NULL),
	(334, 290, 1, NULL, NULL),
	(335, 291, 1, NULL, NULL),
	(336, 292, 1, NULL, NULL),
	(337, 293, 1, NULL, NULL),
	(338, 294, 1, NULL, NULL),
	(339, 295, 1, NULL, NULL),
	(340, 296, 1, NULL, NULL),
	(341, 56, 1, NULL, NULL),
	(342, 63, 1, NULL, NULL),
	(343, 70, 1, NULL, NULL),
	(344, 77, 1, NULL, NULL),
	(345, 84, 1, NULL, NULL),
	(346, 91, 1, NULL, NULL),
	(347, 309, 1, NULL, NULL),
	(348, 314, 1, NULL, NULL),
	(349, 319, 1, NULL, NULL),
	(350, 324, 1, NULL, NULL),
	(351, 329, 1, NULL, NULL),
	(352, 334, 1, NULL, NULL),
	(353, 339, 1, NULL, NULL),
	(354, 344, 1, NULL, NULL),
	(355, 49, 1, NULL, NULL),
	(356, 53, 1, NULL, NULL),
	(357, 59, 1, NULL, NULL),
	(358, 66, 1, NULL, NULL),
	(359, 73, 1, NULL, NULL),
	(360, 80, 1, NULL, NULL),
	(361, 87, 1, NULL, NULL),
	(362, 94, 1, NULL, NULL),
	(363, 311, 1, NULL, NULL),
	(364, 316, 1, NULL, NULL),
	(365, 321, 1, NULL, NULL),
	(366, 326, 1, NULL, NULL),
	(367, 331, 1, NULL, NULL),
	(368, 336, 1, NULL, NULL),
	(369, 341, 1, NULL, NULL),
	(370, 346, 1, NULL, NULL),
	(371, 313, 1, NULL, NULL),
	(372, 318, 1, NULL, NULL),
	(373, 323, 1, NULL, NULL),
	(374, 328, 1, NULL, NULL),
	(375, 333, 1, NULL, NULL),
	(376, 338, 1, NULL, NULL),
	(377, 343, 1, NULL, NULL),
	(378, 348, 1, NULL, NULL),
	(379, 214, 1, NULL, NULL),
	(380, 448, 1, NULL, NULL),
	(381, 217, 1, NULL, NULL),
	(382, 450, 1, NULL, NULL),
	(383, 452, 1, NULL, NULL),
	(384, 300, 1, NULL, NULL),
	(385, 221, 1, NULL, NULL),
	(386, 453, 1, NULL, NULL),
	(387, 224, 1, NULL, NULL),
	(388, 455, 1, NULL, NULL),
	(389, 457, 1, NULL, NULL),
	(390, 301, 1, NULL, NULL),
	(391, 249, 1, NULL, NULL),
	(392, 250, 1, NULL, NULL),
	(393, 399, 1, NULL, NULL),
	(394, 401, 1, NULL, NULL),
	(395, 403, 1, NULL, NULL),
	(396, 172, 1, NULL, NULL),
	(397, 174, 1, NULL, NULL),
	(398, 176, 1, NULL, NULL),
	(399, 404, 1, NULL, NULL),
	(400, 179, 1, NULL, NULL),
	(401, 173, 1, NULL, NULL),
	(402, 180, 1, NULL, NULL),
	(403, 406, 1, NULL, NULL),
	(404, 183, 1, NULL, NULL),
	(405, 191, 1, NULL, NULL),
	(406, 408, 1, NULL, NULL),
	(407, 186, 1, NULL, NULL),
	(408, 410, 1, NULL, NULL),
	(409, 412, 1, NULL, NULL),
	(410, 192, 1, NULL, NULL),
	(411, 413, 1, NULL, NULL),
	(412, 190, 1, NULL, NULL),
	(413, 195, 1, NULL, NULL),
	(414, 415, 1, NULL, NULL),
	(415, 417, 1, NULL, NULL),
	(416, 199, 1, NULL, NULL),
	(417, 206, 1, NULL, NULL),
	(418, 418, 1, NULL, NULL),
	(419, 202, 1, NULL, NULL),
	(420, 420, 1, NULL, NULL),
	(421, 422, 1, NULL, NULL),
	(422, 297, 1, NULL, NULL),
	(423, 298, 1, NULL, NULL),
	(424, 299, 1, NULL, NULL),
	(425, 252, 2, NULL, NULL),
	(426, 260, 2, NULL, NULL),
	(427, 267, 2, NULL, NULL),
	(428, 274, 2, NULL, NULL),
	(429, 428, 2, NULL, NULL),
	(430, 433, 2, NULL, NULL),
	(431, 438, 2, NULL, NULL),
	(432, 443, 2, NULL, NULL),
	(433, 255, 2, NULL, NULL),
	(434, 263, 2, NULL, NULL),
	(435, 270, 2, NULL, NULL),
	(436, 277, 2, NULL, NULL),
	(437, 430, 2, NULL, NULL),
	(438, 435, 2, NULL, NULL),
	(439, 440, 2, NULL, NULL),
	(440, 445, 2, NULL, NULL),
	(441, 432, 2, NULL, NULL),
	(442, 437, 2, NULL, NULL),
	(443, 442, 2, NULL, NULL),
	(444, 447, 2, NULL, NULL),
	(445, 259, 2, NULL, NULL),
	(446, 303, 2, NULL, NULL),
	(447, 304, 2, NULL, NULL),
	(448, 305, 2, NULL, NULL),
	(449, 306, 2, NULL, NULL),
	(450, 286, 2, NULL, NULL),
	(451, 287, 2, NULL, NULL),
	(452, 288, 2, NULL, NULL),
	(453, 98, 2, NULL, NULL),
	(454, 105, 2, NULL, NULL),
	(455, 112, 2, NULL, NULL),
	(456, 119, 2, NULL, NULL),
	(457, 126, 2, NULL, NULL),
	(458, 133, 2, NULL, NULL),
	(459, 140, 2, NULL, NULL),
	(460, 147, 2, NULL, NULL),
	(461, 349, 2, NULL, NULL),
	(462, 354, 2, NULL, NULL),
	(463, 359, 2, NULL, NULL),
	(464, 364, 2, NULL, NULL),
	(465, 369, 2, NULL, NULL),
	(466, 374, 2, NULL, NULL),
	(467, 379, 2, NULL, NULL),
	(468, 384, 2, NULL, NULL),
	(469, 101, 2, NULL, NULL),
	(470, 108, 2, NULL, NULL),
	(471, 115, 2, NULL, NULL),
	(472, 122, 2, NULL, NULL),
	(473, 129, 2, NULL, NULL),
	(474, 136, 2, NULL, NULL),
	(475, 143, 2, NULL, NULL),
	(476, 150, 2, NULL, NULL),
	(477, 351, 2, NULL, NULL),
	(478, 356, 2, NULL, NULL),
	(479, 361, 2, NULL, NULL),
	(480, 366, 2, NULL, NULL),
	(481, 371, 2, NULL, NULL),
	(482, 376, 2, NULL, NULL),
	(483, 381, 2, NULL, NULL),
	(484, 386, 2, NULL, NULL),
	(485, 353, 2, NULL, NULL),
	(486, 358, 2, NULL, NULL),
	(487, 363, 2, NULL, NULL),
	(488, 368, 2, NULL, NULL),
	(489, 373, 2, NULL, NULL),
	(490, 378, 2, NULL, NULL),
	(491, 383, 2, NULL, NULL),
	(492, 388, 2, NULL, NULL),
	(493, 289, 2, NULL, NULL),
	(494, 290, 2, NULL, NULL),
	(495, 291, 2, NULL, NULL),
	(496, 292, 2, NULL, NULL),
	(497, 293, 2, NULL, NULL),
	(498, 294, 2, NULL, NULL),
	(499, 295, 2, NULL, NULL),
	(500, 296, 2, NULL, NULL),
	(501, 2, 2, NULL, NULL),
	(502, 56, 2, NULL, NULL),
	(503, 63, 2, NULL, NULL),
	(504, 70, 2, NULL, NULL),
	(505, 77, 2, NULL, NULL),
	(506, 84, 2, NULL, NULL),
	(507, 91, 2, NULL, NULL),
	(508, 309, 2, NULL, NULL),
	(509, 314, 2, NULL, NULL),
	(510, 319, 2, NULL, NULL),
	(511, 324, 2, NULL, NULL),
	(512, 329, 2, NULL, NULL),
	(513, 334, 2, NULL, NULL),
	(514, 339, 2, NULL, NULL),
	(515, 344, 2, NULL, NULL),
	(516, 49, 2, NULL, NULL),
	(517, 53, 2, NULL, NULL),
	(518, 59, 2, NULL, NULL),
	(519, 66, 2, NULL, NULL),
	(520, 73, 2, NULL, NULL),
	(521, 80, 2, NULL, NULL),
	(522, 87, 2, NULL, NULL),
	(523, 94, 2, NULL, NULL),
	(524, 311, 2, NULL, NULL),
	(525, 316, 2, NULL, NULL),
	(526, 321, 2, NULL, NULL),
	(527, 326, 2, NULL, NULL),
	(528, 331, 2, NULL, NULL),
	(529, 336, 2, NULL, NULL),
	(530, 341, 2, NULL, NULL),
	(531, 346, 2, NULL, NULL),
	(532, 313, 2, NULL, NULL),
	(533, 318, 2, NULL, NULL),
	(534, 323, 2, NULL, NULL),
	(535, 328, 2, NULL, NULL),
	(536, 333, 2, NULL, NULL),
	(537, 338, 2, NULL, NULL),
	(538, 343, 2, NULL, NULL),
	(539, 348, 2, NULL, NULL),
	(540, 6, 2, NULL, NULL),
	(541, 10, 2, NULL, NULL),
	(542, 11, 2, NULL, NULL),
	(543, 12, 2, NULL, NULL),
	(544, 281, 2, NULL, NULL),
	(545, 282, 2, NULL, NULL),
	(546, 214, 2, NULL, NULL),
	(547, 448, 2, NULL, NULL),
	(548, 217, 2, NULL, NULL),
	(549, 450, 2, NULL, NULL),
	(550, 452, 2, NULL, NULL),
	(551, 300, 2, NULL, NULL),
	(552, 221, 2, NULL, NULL),
	(553, 453, 2, NULL, NULL),
	(554, 224, 2, NULL, NULL),
	(555, 455, 2, NULL, NULL),
	(556, 457, 2, NULL, NULL),
	(557, 301, 2, NULL, NULL),
	(558, 33, 2, NULL, NULL),
	(559, 34, 2, NULL, NULL),
	(560, 249, 2, NULL, NULL),
	(561, 250, 2, NULL, NULL),
	(562, 19, 2, NULL, NULL),
	(563, 399, 2, NULL, NULL),
	(564, 22, 2, NULL, NULL),
	(565, 401, 2, NULL, NULL),
	(566, 403, 2, NULL, NULL),
	(567, 172, 2, NULL, NULL),
	(568, 23, 2, NULL, NULL),
	(569, 24, 2, NULL, NULL),
	(570, 25, 2, NULL, NULL),
	(571, 26, 2, NULL, NULL),
	(572, 27, 2, NULL, NULL),
	(573, 28, 2, NULL, NULL),
	(574, 174, 2, NULL, NULL),
	(575, 29, 2, NULL, NULL),
	(576, 176, 2, NULL, NULL),
	(577, 404, 2, NULL, NULL),
	(578, 179, 2, NULL, NULL),
	(579, 173, 2, NULL, NULL),
	(580, 30, 2, NULL, NULL),
	(581, 180, 2, NULL, NULL),
	(582, 406, 2, NULL, NULL),
	(583, 31, 2, NULL, NULL),
	(584, 183, 2, NULL, NULL),
	(585, 191, 2, NULL, NULL),
	(586, 408, 2, NULL, NULL),
	(587, 186, 2, NULL, NULL),
	(588, 410, 2, NULL, NULL),
	(589, 412, 2, NULL, NULL),
	(590, 192, 2, NULL, NULL),
	(591, 413, 2, NULL, NULL),
	(592, 190, 2, NULL, NULL),
	(593, 195, 2, NULL, NULL),
	(594, 415, 2, NULL, NULL),
	(595, 417, 2, NULL, NULL),
	(596, 199, 2, NULL, NULL),
	(597, 206, 2, NULL, NULL),
	(598, 418, 2, NULL, NULL),
	(599, 202, 2, NULL, NULL),
	(600, 420, 2, NULL, NULL),
	(601, 422, 2, NULL, NULL),
	(602, 284, 2, NULL, NULL),
	(603, 297, 2, NULL, NULL),
	(604, 298, 2, NULL, NULL),
	(605, 299, 2, NULL, NULL),
	(606, 285, 2, NULL, NULL),
	(607, 399, 7, NULL, NULL),
	(608, 191, 7, NULL, NULL),
	(609, 418, 7, NULL, NULL),
	(610, 207, 4, NULL, NULL),
	(611, 210, 4, NULL, NULL),
	(612, 252, 4, NULL, NULL),
	(613, 274, 4, NULL, NULL),
	(614, 260, 4, NULL, NULL),
	(615, 267, 4, NULL, NULL),
	(616, 428, 4, NULL, NULL),
	(617, 255, 4, NULL, NULL),
	(618, 263, 4, NULL, NULL),
	(619, 270, 4, NULL, NULL),
	(620, 277, 4, NULL, NULL),
	(621, 430, 4, NULL, NULL),
	(622, 259, 4, NULL, NULL),
	(623, 448, 4, NULL, NULL),
	(624, 33, 4, NULL, NULL),
	(625, 34, 4, NULL, NULL),
	(626, 250, 4, NULL, NULL),
	(627, 249, 4, NULL, NULL),
	(628, 19, 4, NULL, NULL),
	(629, 399, 4, NULL, NULL),
	(630, 22, 4, NULL, NULL),
	(631, 401, 4, NULL, NULL),
	(632, 403, 4, NULL, NULL),
	(633, 172, 4, NULL, NULL),
	(634, 23, 4, NULL, NULL),
	(635, 24, 4, NULL, NULL),
	(638, 31, 4, NULL, NULL),
	(639, 186, 4, NULL, NULL),
	(640, 192, 4, NULL, NULL),
	(641, 413, 4, NULL, NULL),
	(642, 195, 4, NULL, NULL),
	(643, 199, 4, NULL, NULL),
	(644, 206, 4, NULL, NULL),
	(645, 418, 4, NULL, NULL),
	(646, 202, 4, NULL, NULL),
	(647, 191, 4, NULL, NULL),
	(648, 415, 4, NULL, NULL);

-- Dumping structure for table simantik.register_aset
CREATE TABLE IF NOT EXISTS `register_aset` (
  `id_register_aset` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_inventory` bigint unsigned NOT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_ruangan` bigint unsigned DEFAULT NULL,
  `nomor_register` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_aset` enum('BAIK','RUSAK_RINGAN','RUSAK_BERAT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BAIK',
  `tanggal_perolehan` date NOT NULL,
  `status_aset` enum('AKTIF','NONAKTIF') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AKTIF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_register_aset`),
  UNIQUE KEY `register_aset_nomor_register_unique` (`nomor_register`),
  KEY `register_aset_id_inventory_foreign` (`id_inventory`),
  KEY `register_aset_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `register_aset_id_ruangan_foreign` (`id_ruangan`),
  CONSTRAINT `register_aset_id_inventory_foreign` FOREIGN KEY (`id_inventory`) REFERENCES `data_inventory` (`id_inventory`) ON DELETE CASCADE,
  CONSTRAINT `register_aset_id_ruangan_foreign` FOREIGN KEY (`id_ruangan`) REFERENCES `master_ruangan` (`id_ruangan`) ON DELETE SET NULL,
  CONSTRAINT `register_aset_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.register_aset: ~0 rows (approximately)

-- Dumping structure for table simantik.retur_barang
CREATE TABLE IF NOT EXISTS `retur_barang` (
  `id_retur` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_retur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_penerimaan` bigint unsigned DEFAULT NULL,
  `id_distribusi` bigint unsigned DEFAULT NULL,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_gudang_asal` bigint unsigned NOT NULL,
  `id_gudang_tujuan` bigint unsigned NOT NULL,
  `id_pegawai_pengirim` bigint unsigned NOT NULL,
  `tanggal_retur` date NOT NULL,
  `status_retur` enum('DRAFT','DIAJUKAN','DITERIMA','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `alasan_retur` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_retur`),
  UNIQUE KEY `retur_barang_no_retur_unique` (`no_retur`),
  KEY `retur_barang_id_penerimaan_foreign` (`id_penerimaan`),
  KEY `retur_barang_id_distribusi_foreign` (`id_distribusi`),
  KEY `retur_barang_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `retur_barang_id_gudang_asal_foreign` (`id_gudang_asal`),
  KEY `retur_barang_id_gudang_tujuan_foreign` (`id_gudang_tujuan`),
  KEY `retur_barang_id_pegawai_pengirim_foreign` (`id_pegawai_pengirim`),
  CONSTRAINT `retur_barang_id_distribusi_foreign` FOREIGN KEY (`id_distribusi`) REFERENCES `transaksi_distribusi` (`id_distribusi`) ON DELETE CASCADE,
  CONSTRAINT `retur_barang_id_gudang_asal_foreign` FOREIGN KEY (`id_gudang_asal`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `retur_barang_id_gudang_tujuan_foreign` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `retur_barang_id_pegawai_pengirim_foreign` FOREIGN KEY (`id_pegawai_pengirim`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `retur_barang_id_penerimaan_foreign` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_barang` (`id_penerimaan`) ON DELETE CASCADE,
  CONSTRAINT `retur_barang_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.retur_barang: ~0 rows (approximately)

-- Dumping structure for table simantik.riwayat_pemeliharaan
CREATE TABLE IF NOT EXISTS `riwayat_pemeliharaan` (
  `id_riwayat` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_register_aset` bigint unsigned NOT NULL,
  `id_permintaan_pemeliharaan` bigint unsigned DEFAULT NULL,
  `id_service_report` bigint unsigned DEFAULT NULL,
  `id_kalibrasi` bigint unsigned DEFAULT NULL,
  `tanggal_pemeliharaan` date NOT NULL,
  `jenis_pemeliharaan` enum('RUTIN','KALIBRASI','PERBAIKAN','PENGGANTIAN_SPAREPART') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RUTIN',
  `status` enum('SELESAI','GAGAL','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SELESAI',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_riwayat`),
  KEY `riwayat_pemeliharaan_id_register_aset_foreign` (`id_register_aset`),
  KEY `riwayat_pemeliharaan_id_permintaan_pemeliharaan_foreign` (`id_permintaan_pemeliharaan`),
  KEY `riwayat_pemeliharaan_id_service_report_foreign` (`id_service_report`),
  KEY `riwayat_pemeliharaan_id_kalibrasi_foreign` (`id_kalibrasi`),
  CONSTRAINT `riwayat_pemeliharaan_id_kalibrasi_foreign` FOREIGN KEY (`id_kalibrasi`) REFERENCES `kalibrasi_aset` (`id_kalibrasi`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_pemeliharaan_id_permintaan_pemeliharaan_foreign` FOREIGN KEY (`id_permintaan_pemeliharaan`) REFERENCES `permintaan_pemeliharaan` (`id_permintaan_pemeliharaan`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_pemeliharaan_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_pemeliharaan_id_service_report_foreign` FOREIGN KEY (`id_service_report`) REFERENCES `service_report` (`id_service_report`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.riwayat_pemeliharaan: ~0 rows (approximately)

-- Dumping structure for table simantik.rku_detail
CREATE TABLE IF NOT EXISTS `rku_detail` (
  `id_rku_detail` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rku` bigint unsigned NOT NULL,
  `id_data_barang` bigint unsigned NOT NULL,
  `qty_rencana` decimal(10,2) NOT NULL,
  `id_satuan` bigint unsigned NOT NULL,
  `harga_satuan_rencana` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal_rencana` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rku_detail`),
  KEY `rku_detail_id_rku_foreign` (`id_rku`),
  KEY `rku_detail_id_data_barang_foreign` (`id_data_barang`),
  KEY `rku_detail_id_satuan_foreign` (`id_satuan`),
  CONSTRAINT `rku_detail_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `rku_detail_id_rku_foreign` FOREIGN KEY (`id_rku`) REFERENCES `rku_header` (`id_rku`) ON DELETE CASCADE,
  CONSTRAINT `rku_detail_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `master_satuan` (`id_satuan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.rku_detail: ~0 rows (approximately)

-- Dumping structure for table simantik.rku_header
CREATE TABLE IF NOT EXISTS `rku_header` (
  `id_rku` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_unit_kerja` bigint unsigned NOT NULL,
  `id_sub_kegiatan` bigint unsigned NOT NULL,
  `no_rku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_anggaran` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `jenis_rku` enum('BARANG','ASET') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BARANG',
  `status_rku` enum('DRAFT','DIAJUKAN','DISETUJUI','DITOLAK','DIPROSES') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `id_pengaju` bigint unsigned DEFAULT NULL,
  `id_approver` bigint unsigned DEFAULT NULL,
  `tanggal_approval` date DEFAULT NULL,
  `catatan_approval` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total_anggaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rku`),
  UNIQUE KEY `rku_header_no_rku_unique` (`no_rku`),
  KEY `rku_header_id_unit_kerja_foreign` (`id_unit_kerja`),
  KEY `rku_header_id_sub_kegiatan_foreign` (`id_sub_kegiatan`),
  KEY `rku_header_id_pengaju_foreign` (`id_pengaju`),
  KEY `rku_header_id_approver_foreign` (`id_approver`),
  CONSTRAINT `rku_header_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `master_pegawai` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rku_header_id_pengaju_foreign` FOREIGN KEY (`id_pengaju`) REFERENCES `master_pegawai` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rku_header_id_sub_kegiatan_foreign` FOREIGN KEY (`id_sub_kegiatan`) REFERENCES `master_sub_kegiatan` (`id_sub_kegiatan`) ON DELETE CASCADE,
  CONSTRAINT `rku_header_id_unit_kerja_foreign` FOREIGN KEY (`id_unit_kerja`) REFERENCES `master_unit_kerja` (`id_unit_kerja`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.rku_header: ~0 rows (approximately)

-- Dumping structure for table simantik.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.roles: ~13 rows (approximately)
REPLACE INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Admin Sistem', 'Admin IT / Pengelola Aplikasi - Kelola user, role, master data, konfigurasi sistem', '2026-01-19 08:30:09', '2026-01-20 03:00:15'),
	(2, 'admin_gudang', 'Admin Gudang / Pengurus Barang', 'Pengurus Barang / Admin Gudang - Kelola stok, proses distribusi, cetak SBBK, register aset', '2026-01-19 08:30:09', '2026-01-20 03:00:16'),
	(4, 'pegawai', 'Pegawai (Pemohon)', 'Staf Unit Kerja / Pelaksana Teknis - Membuat permintaan barang, melihat status, menerima barang', '2026-01-19 08:30:09', '2026-01-20 03:00:16'),
	(5, 'kepala_unit', 'Kepala Unit', 'Kepala Seksi / Kepala Sub Unit - Melihat permintaan dari unitnya, memberi status "Mengetahui"', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(6, 'kasubbag_tu', 'Kasubbag TU', 'Kepala Sub Bagian Tata Usaha - Verifikasi administrasi permintaan, cek kelengkapan', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(7, 'kepala_pusat', 'Kepala Pusat (Pimpinan)', 'Kepala Pusat / Kepala UPT - Approve/Reject permintaan, memberikan disposisi', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(8, 'perencanaan', 'Perencanaan', 'Unit Perencanaan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(9, 'pengadaan', 'Pengadaan', 'Unit Pengadaan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(10, 'keuangan', 'Keuangan', 'Unit Keuangan - Menindaklanjuti disposisi pimpinan', '2026-01-20 03:00:16', '2026-01-20 03:00:16'),
	(11, 'admin_gudang_aset', 'Admin Gudang Aset', 'Admin Gudang Pusat Kategori Aset - Kelola stok aset, proses distribusi aset, cetak SBBK aset', '2026-01-20 06:36:53', '2026-01-20 06:36:53'),
	(12, 'admin_gudang_persediaan', 'Admin Gudang Persediaan', 'Admin Gudang Pusat Kategori Persediaan - Kelola stok persediaan, proses distribusi persediaan, cetak SBBK persediaan', '2026-01-20 06:36:54', '2026-01-20 06:36:54'),
	(13, 'admin_gudang_farmasi', 'Admin Gudang Farmasi', 'Admin Gudang Pusat Kategori Farmasi - Kelola stok farmasi, proses distribusi farmasi, cetak SBBK farmasi', '2026-01-20 06:36:54', '2026-01-20 06:36:54'),
	(14, 'pengurus_barang', 'Admin Pengurus Barang', NULL, '2026-01-22 07:31:55', '2026-01-22 07:31:55');

-- Dumping structure for table simantik.role_user
CREATE TABLE IF NOT EXISTS `role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.role_user: ~9 rows (approximately)
REPLACE INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
	(2, 1, 1, NULL, NULL),
	(24, 23, 4, NULL, NULL),
	(25, 24, 2, NULL, NULL),
	(27, 26, 5, NULL, NULL),
	(28, 27, 6, NULL, NULL),
	(29, 28, 7, NULL, NULL),
	(30, 29, 13, NULL, NULL),
	(31, 25, 2, NULL, NULL),
	(32, 30, 4, NULL, NULL);

-- Dumping structure for table simantik.service_report
CREATE TABLE IF NOT EXISTS `service_report` (
  `id_service_report` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_service_report` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_permintaan_pemeliharaan` bigint unsigned NOT NULL,
  `id_register_aset` bigint unsigned NOT NULL,
  `tanggal_service` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jenis_service` enum('RUTIN','KALIBRASI','PERBAIKAN','PENGGANTIAN_SPAREPART') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RUTIN',
  `status_service` enum('MENUNGGU','DIPROSES','SELESAI','DITOLAK','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU',
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teknisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_kerja` text COLLATE utf8mb4_unicode_ci,
  `tindakan_yang_dilakukan` text COLLATE utf8mb4_unicode_ci,
  `sparepart_yang_diganti` text COLLATE utf8mb4_unicode_ci,
  `biaya_service` decimal(15,2) NOT NULL DEFAULT '0.00',
  `biaya_sparepart` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kondisi_setelah_service` enum('BAIK','RUSAK_RINGAN','RUSAK_BERAT','TIDAK_BISA_DIPERBAIKI') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_laporan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_service_report`),
  UNIQUE KEY `service_report_no_service_report_unique` (`no_service_report`),
  KEY `service_report_id_permintaan_pemeliharaan_foreign` (`id_permintaan_pemeliharaan`),
  KEY `service_report_id_register_aset_foreign` (`id_register_aset`),
  KEY `service_report_created_by_foreign` (`created_by`),
  CONSTRAINT `service_report_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_report_id_permintaan_pemeliharaan_foreign` FOREIGN KEY (`id_permintaan_pemeliharaan`) REFERENCES `permintaan_pemeliharaan` (`id_permintaan_pemeliharaan`) ON DELETE CASCADE,
  CONSTRAINT `service_report_id_register_aset_foreign` FOREIGN KEY (`id_register_aset`) REFERENCES `register_aset` (`id_register_aset`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.service_report: ~0 rows (approximately)

-- Dumping structure for table simantik.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.sessions: ~1 rows (approximately)
REPLACE INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('rND2VzpIQ6Jag5JiQIdjqow4AWtz4iz4KANw1jag', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic3ZkNmFXdWVkMmlleXRYU0YwSDVTc0tUd1BZS25KTXhQMUpRdzQxUyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvaW52ZW50b3J5L2RhdGEtc3RvY2siO3M6NToicm91dGUiO3M6MjY6ImludmVudG9yeS5kYXRhLXN0b2NrLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1769661010);

-- Dumping structure for table simantik.stock_adjustment
CREATE TABLE IF NOT EXISTS `stock_adjustment` (
  `id_adjustment` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_stock` bigint unsigned NOT NULL,
  `id_data_barang` bigint unsigned NOT NULL,
  `id_gudang` bigint unsigned NOT NULL,
  `tanggal_adjustment` date NOT NULL,
  `qty_sebelum` decimal(15,2) NOT NULL,
  `qty_sesudah` decimal(15,2) NOT NULL,
  `qty_selisih` decimal(15,2) NOT NULL,
  `jenis_adjustment` enum('PENAMBAHAN','PENGURANGAN','KOREKSI','OPNAME') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'KOREKSI',
  `alasan` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `id_petugas` bigint unsigned NOT NULL,
  `status` enum('DRAFT','DIAJUKAN','DISETUJUI','DITOLAK') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `id_approver` bigint unsigned DEFAULT NULL,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `catatan_approval` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_adjustment`),
  KEY `stock_adjustment_id_stock_foreign` (`id_stock`),
  KEY `stock_adjustment_id_data_barang_foreign` (`id_data_barang`),
  KEY `stock_adjustment_id_gudang_foreign` (`id_gudang`),
  KEY `stock_adjustment_id_petugas_foreign` (`id_petugas`),
  KEY `stock_adjustment_id_approver_foreign` (`id_approver`),
  CONSTRAINT `stock_adjustment_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_adjustment_id_data_barang_foreign` FOREIGN KEY (`id_data_barang`) REFERENCES `master_data_barang` (`id_data_barang`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_id_petugas_foreign` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_id_stock_foreign` FOREIGN KEY (`id_stock`) REFERENCES `data_stock` (`id_stock`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.stock_adjustment: ~0 rows (approximately)

-- Dumping structure for table simantik.transaksi_distribusi
CREATE TABLE IF NOT EXISTS `transaksi_distribusi` (
  `id_distribusi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_sbbk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_permintaan` bigint unsigned DEFAULT NULL,
  `tanggal_distribusi` datetime NOT NULL,
  `id_gudang_asal` bigint unsigned NOT NULL,
  `id_gudang_tujuan` bigint unsigned NOT NULL,
  `id_pegawai_pengirim` bigint unsigned NOT NULL,
  `status_distribusi` enum('DRAFT','DIKIRIM','SELESAI') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_distribusi`),
  UNIQUE KEY `transaksi_distribusi_no_sbbk_unique` (`no_sbbk`),
  KEY `transaksi_distribusi_id_permintaan_foreign` (`id_permintaan`),
  KEY `transaksi_distribusi_id_gudang_asal_foreign` (`id_gudang_asal`),
  KEY `transaksi_distribusi_id_gudang_tujuan_foreign` (`id_gudang_tujuan`),
  KEY `transaksi_distribusi_id_pegawai_pengirim_foreign` (`id_pegawai_pengirim`),
  CONSTRAINT `transaksi_distribusi_id_gudang_asal_foreign` FOREIGN KEY (`id_gudang_asal`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_distribusi_id_gudang_tujuan_foreign` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `master_gudang` (`id_gudang`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_distribusi_id_pegawai_pengirim_foreign` FOREIGN KEY (`id_pegawai_pengirim`) REFERENCES `master_pegawai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_distribusi_id_permintaan_foreign` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_barang` (`id_permintaan`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.transaksi_distribusi: ~0 rows (approximately)

-- Dumping structure for table simantik.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.users: ~9 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrator', 'pusdatinppkp@gmail.com', '2026-01-15 04:20:48', '$2y$12$DMSuQ5RIu3RBdRwdALTLoOpVPT/kJAmYRq8LYSq8rTFybfGaay97q', '5txY4fJIhkkNt99Sgk1jVmm0hBN5yP8jnYvwiROHqudWeCye4nRHcTOFFgGc', '2026-01-15 04:20:48', '2026-01-15 04:20:48'),
	(23, 'ruka', 'ruka@gmail.com', NULL, '$2y$12$SEbjG9fSyu5rD1lnH/ZXtOv2sCRejxV3PSstS8Uw0DlYOg0lhgxua', NULL, '2026-01-22 03:02:01', '2026-01-22 03:02:01'),
	(24, 'Assifha Setiawati', 'pengurusbarang@gmail.com', NULL, '$2y$12$9GZ3t43WR346/9DCTB90WusYGK91ujC14el7emzpwl7dnISP.Kr/S', NULL, '2026-01-22 03:02:19', '2026-01-22 03:02:19'),
	(25, 'Syaiful', 'syaiful@gmail.com', NULL, '$2y$12$HMTrgVGLyI8baPFODDUcXuk34rah7O.jq8Ub4VFiO2phuRquKa7Ie', NULL, '2026-01-22 03:02:41', '2026-01-22 03:02:41'),
	(26, 'dr. Lintang', 'lintang@gmail.com', NULL, '$2y$12$beYMzPJoB51H3C/KMFkBCOkAyhjqhzL7eqwTJm4k/8f5qY0kVs2CS', NULL, '2026-01-22 03:03:02', '2026-01-22 03:03:02'),
	(27, 'Dara Indir Yunita', 'kasubbag@gmail.com', NULL, '$2y$12$Cj2Ac1gF/OUEPD43w2dkL.9rmzghz7QpAF8lSLyodvM9R7R3fIx7K', NULL, '2026-01-22 03:03:21', '2026-01-22 03:03:21'),
	(28, 'dr. Dwian Andhika', 'kepala@gmail.com', NULL, '$2y$12$qLp1zSEA22QrCY4NGhVG2O.H58lvFtrQ36ARITIXaP9AkDsuLyd/O', NULL, '2026-01-22 03:03:35', '2026-01-22 03:03:35'),
	(29, 'Dhila', 'dhila@gmail.com', NULL, '$2y$12$whryvZw/pk3zgmYSS27b3eoUG7p9ioq9ZSd3EPIvB8Pa4Ym.fa47O', NULL, '2026-01-22 03:43:48', '2026-01-22 03:43:48'),
	(30, 'Annas', 'annas@gmail.com', NULL, '$2y$12$aA.LDjg06Zz141cDlNgRK.Fa4etsZh6Q2x1w2ZEXqpYgkjtWvNNCe', NULL, '2026-01-23 08:31:14', '2026-01-23 08:31:14');

-- Dumping structure for table simantik.user_modules
CREATE TABLE IF NOT EXISTS `user_modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_modules_user_id_module_unique` (`user_id`,`module`),
  KEY `user_modules_module_foreign` (`module`),
  CONSTRAINT `user_modules_module_foreign` FOREIGN KEY (`module`) REFERENCES `modules` (`name`) ON DELETE CASCADE,
  CONSTRAINT `user_modules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table simantik.user_modules: ~37 rows (approximately)
REPLACE INTO `user_modules` (`id`, `user_id`, `module`, `created_at`, `updated_at`) VALUES
	(1, 28, 'inventory', '2026-01-23 01:10:34', '2026-01-23 01:10:34'),
	(2, 28, 'transaction', '2026-01-23 01:10:34', '2026-01-23 01:10:34'),
	(3, 28, 'maintenance', '2026-01-23 01:10:34', '2026-01-23 01:10:34'),
	(4, 28, 'planning', '2026-01-23 01:10:34', '2026-01-23 01:10:34'),
	(5, 28, 'procurement', '2026-01-23 01:10:34', '2026-01-23 01:10:34'),
	(6, 28, 'finance', '2026-01-23 01:10:35', '2026-01-23 01:10:35'),
	(7, 28, 'reports', '2026-01-23 01:10:35', '2026-01-23 01:10:35'),
	(8, 28, 'asset', '2026-01-23 01:14:18', '2026-01-23 01:14:18'),
	(9, 1, 'master-manajemen', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(10, 1, 'master-data', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(11, 1, 'inventory', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(12, 1, 'transaction', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(13, 1, 'asset', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(14, 1, 'maintenance', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(15, 1, 'planning', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(16, 1, 'procurement', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(17, 1, 'finance', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(18, 1, 'reports', '2026-01-23 02:35:20', '2026-01-23 02:35:20'),
	(19, 23, 'transaction', '2026-01-23 03:44:30', '2026-01-23 03:44:30'),
	(20, 23, 'asset', '2026-01-23 03:44:30', '2026-01-23 03:44:30'),
	(21, 29, 'inventory', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(22, 29, 'transaction', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(23, 29, 'asset', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(24, 29, 'maintenance', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(25, 29, 'planning', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(26, 29, 'procurement', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(27, 29, 'finance', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(28, 29, 'reports', '2026-01-23 07:24:37', '2026-01-23 07:24:37'),
	(29, 23, 'maintenance', '2026-01-23 07:26:29', '2026-01-23 07:26:29'),
	(30, 23, 'planning', '2026-01-23 07:26:29', '2026-01-23 07:26:29'),
	(31, 23, 'reports', '2026-01-23 07:26:29', '2026-01-23 07:26:29'),
	(32, 30, 'inventory', '2026-01-23 08:31:14', '2026-01-23 08:31:14'),
	(33, 30, 'transaction', '2026-01-23 08:31:14', '2026-01-23 08:31:14'),
	(34, 30, 'asset', '2026-01-23 08:31:14', '2026-01-23 08:31:14'),
	(35, 30, 'maintenance', '2026-01-23 08:31:14', '2026-01-23 08:31:14'),
	(36, 30, 'planning', '2026-01-23 08:31:15', '2026-01-23 08:31:15'),
	(37, 30, 'reports', '2026-01-23 08:31:15', '2026-01-23 08:31:15');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
simantik