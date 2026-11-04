-- ========================================================
-- Database Dump for: bangkiang_jaran
-- Generated: 2026-10-28 21:48:01
-- E-Tourism Bangkiang Jaran Waterfall Deliverable
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Struktur Tabel `activity_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id_log` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_log`),
  KEY `activity_logs_id_user_foreign` (`id_user`),
  KEY `activity_logs_aktivitas_index` (`aktivitas`),
  CONSTRAINT `activity_logs_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `detail_pemesanan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detail_pemesanan`;
CREATE TABLE `detail_pemesanan` (
  `id_detail` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pemesanan` bigint(20) unsigned NOT NULL,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `kode_tiket` varchar(20) NOT NULL,
  `nama_pengunjung` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `plat_kendaraan` varchar(20) DEFAULT NULL,
  `nama_tiket` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `status_tiket` enum('aktif','digunakan','kadaluarsa') NOT NULL DEFAULT 'aktif',
  `check_in_at` timestamp NULL DEFAULT NULL,
  `check_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  UNIQUE KEY `detail_pemesanan_kode_tiket_unique` (`kode_tiket`),
  KEY `detail_pemesanan_id_tiket_foreign` (`id_tiket`),
  KEY `detail_pemesanan_id_pemesanan_index` (`id_pemesanan`),
  CONSTRAINT `detail_pemesanan_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE,
  CONSTRAINT `detail_pemesanan_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id_tiket`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `failed_jobs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `galeri`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `galeri`;
CREATE TABLE `galeri` (
  `id_galeri` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL,
  `tipe` enum('wisata','restoran') NOT NULL DEFAULT 'wisata',
  `keterangan` text DEFAULT NULL,
  `keterangan_en` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_galeri`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Isi Data Tabel `galeri`
INSERT INTO `galeri` VALUES 
('1', 'galeri/galeri-01.jpg', 'wisata', 'Air terjun Bangkiang Jaran dari kejauhan', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 20:28:30'),
('2', 'galeri/galeri-02.jpg', 'wisata', 'Suasana hutan tropis sekitar air terjun', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('3', 'galeri/galeri-03.jpg', 'wisata', 'Kolam alami yang jernih dan segar', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('4', 'galeri/galeri-04.jpg', 'wisata', 'Spot foto instagramable di area wisata', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('5', 'galeri/galeri-05.jpg', 'wisata', 'Gazebo untuk bersantai menikmati alam', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('6', 'galeri/galeri-06.jpg', 'wisata', 'Jalur trekking menuju air terjun', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('7', 'galeri/galeri-07.jpg', 'wisata', 'Pemandangan matahari terbenam dari atas tebing', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('8', 'galeri/galeri-08.jpg', 'wisata', 'Area parkir yang luas dan nyaman', NULL, NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55');

-- --------------------------------------------------------
-- Struktur Tabel `konten`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `konten`;
CREATE TABLE `konten` (
  `id_konten` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `isi` text NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_konten`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Isi Data Tabel `konten`
INSERT INTO `konten` VALUES 
('1', 'Sejarah Bangkiang Jaran Waterfall', 'Bangkiang Jaran Waterfall merupakan salah satu air terjun tersembunyi yang terletak di Desa Bakbakan, Kecamatan Gianyar, Kabupaten Gianyar, Bali.\n\nNama \"Bangkiang Jaran\" berasal dari bahasa lokal yang menggambarkan keunikan air terjun ini — \"Bangkiang\" berarti tebing curam, dan \"Jaran\" berarti kuda. Konon, tebing di sekitar air terjun ini memiliki bentuk yang menyerupai kuda.\n\nAir terjun ini memiliki ketinggian sekitar 15 meter dengan kolam alami yang jernih dan segar. Dikelilingi oleh hutan tropis yang masih asri, tempat ini menjadi destinasi favorit bagi wisatawan yang mencari ketenangan dan kesegaran alam.\n\nSejak tahun 2020, pengelolaan objek wisata ini dilakukan secara profesional oleh Pemerintah Desa Bakbakan bersama kelompok sadar wisata (Pokdarwis) setempat untuk memberikan pengalaman terbaik bagi pengunjung.', 'sejarah', NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55'),
('2', 'Informasi Umum', 'Jam Operasional:\n• Senin - Minggu: 07.00 - 18.00 WITA\n\nFasilitas:\n• Area parkir luas (motor & mobil)\n• Toilet umum\n• Warung makan & minuman\n• Gazebo untuk bersantai\n• Spot foto instagramable\n• Guide lokal (opsional)\n\nTips Berkunjung:\n• Gunakan pakaian dan alas kaki yang nyaman\n• Bawa pakaian ganti jika ingin berenang\n• Jaga kebersihan dengan tidak membuang sampah sembarangan\n• Patuhi arahan petugas untuk keselamatan', 'info', NULL, '2026-08-03 15:56:55', '2026-08-03 15:56:55');

-- --------------------------------------------------------
-- Struktur Tabel `log_login_gagal`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_login_gagal`;
CREATE TABLE `log_login_gagal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `input_login` varchar(150) NOT NULL,
  `alasan` varchar(255) NOT NULL DEFAULT 'Kredensial tidak cocok',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_login_gagal_input_login_index` (`input_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `log_login_sukses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_login_sukses`;
CREATE TABLE `log_login_sukses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `metode` varchar(50) NOT NULL DEFAULT 'manual',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_login_sukses_id_user_index` (`id_user`),
  KEY `log_login_sukses_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Isi Data Tabel `migrations`
INSERT INTO `migrations` VALUES 
('1', '2014_10_12_000000_create_users_table', '1'),
('2', '2014_10_12_100000_create_password_resets_table', '1'),
('3', '2019_08_19_000000_create_failed_jobs_table', '1'),
('4', '2019_12_14_000001_create_personal_access_tokens_table', '1'),
('5', '2024_01_01_000001_update_users_table', '1'),
('6', '2024_01_01_000002_create_tikets_table', '1'),
('7', '2024_01_01_000003_create_pemesanans_table', '1'),
('8', '2024_01_01_000004_create_pembayarans_table', '1'),
('9', '2024_01_01_000005_create_galeris_table', '1'),
('10', '2024_01_01_000006_create_kontens_table', '1'),
('11', '2024_01_01_000007_create_laporans_table', '1'),
('12', '2024_01_01_000008_create_activity_logs_table', '1'),
('13', '2024_01_01_000009_drop_kuota_from_tiket_table', '1'),
('14', '2024_01_01_000011_remove_tipe_from_galeri_table', '1'),
('15', '2026_07_21_025346_add_oauth_and_role_to_users_table', '1'),
('16', '2026_07_21_032413_add_is_verified_to_users_table', '1'),
('17', '2026_07_21_032413_create_otp_codes_table', '1'),
('18', '2026_07_21_142335_remove_otp_and_is_verified_from_database', '1'),
('19', '2026_07_21_144000_add_detail_to_pemesanan_table', '1'),
('20', '2026_07_24_141622_create_detail_pemesanan_table', '1'),
('21', '2026_07_24_202411_add_data_pengunjung_to_detail_pemesanan_table', '1'),
('22', '2026_07_24_202411_add_kategori_to_tiket_table', '1'),
('23', '2026_07_25_195027_add_gender_and_check_in_out_to_detail_pemesanan_table', '1'),
('24', '2026_07_25_201723_add_tipe_to_galeri_table', '1'),
('25', '2026_07_25_210000_remove_paket_keluarga_from_tiket', '1'),
('26', '2026_08_03_000000_null_password_google_users', '1'),
('27', '2026_08_03_010000_add_login_tracking_to_users_table', '1'),
('28', '2026_08_03_020000_cleanup_parkir_and_laporan', '1'),
('29', '2026_08_03_030000_add_en_columns_for_translation', '2'),
('30', '2026_10_17_000000_improve_default_ticket_names', '3'),
('31', '2026_10_20_000000_add_auth_payment_unique_indexes', '4'),
('32', '2026_10_28_000000_add_registration_methods_to_users_table', '5'),
('33', '2026_10_29_060000_create_separated_login_logs_tables', '6');

-- --------------------------------------------------------
-- Struktur Tabel `password_resets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `pembayaran`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran` (
  `id_bayar` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pemesanan` bigint(20) unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status` enum('pending','valid','ditolak') NOT NULL DEFAULT 'pending',
  `tgl_bayar` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_bayar`),
  UNIQUE KEY `pembayaran_pemesanan_unique` (`id_pemesanan`),
  KEY `pembayaran_status_index` (`status`),
  CONSTRAINT `pembayaran_id_pemesanan_foreign` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `pemesanan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pemesanan`;
CREATE TABLE `pemesanan` (
  `id_pemesanan` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned NOT NULL,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `tgl_kunjungan` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail`)),
  `status` enum('pending','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `kode_booking` varchar(20) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pemesanan`),
  UNIQUE KEY `pemesanan_kode_booking_unique` (`kode_booking`),
  KEY `pemesanan_id_user_foreign` (`id_user`),
  KEY `pemesanan_id_tiket_foreign` (`id_tiket`),
  KEY `pemesanan_kode_booking_index` (`kode_booking`),
  KEY `pemesanan_status_index` (`status`),
  KEY `pemesanan_tgl_kunjungan_index` (`tgl_kunjungan`),
  CONSTRAINT `pemesanan_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id_tiket`) ON DELETE CASCADE,
  CONSTRAINT `pemesanan_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `personal_access_tokens`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Struktur Tabel `tiket`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tiket`;
CREATE TABLE `tiket` (
  `id_tiket` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_tiket` varchar(100) NOT NULL,
  `nama_tiket_en` varchar(255) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori` enum('perorangan','kendaraan') NOT NULL DEFAULT 'perorangan',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tiket`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Isi Data Tabel `tiket`
INSERT INTO `tiket` VALUES 
('1', 'Tiket Dewasa', 'Adult Admission', '20000.00', 'perorangan', 'aktif', NULL, '2026-08-03 15:56:55', '2026-10-17 00:48:35'),
('2', 'Tiket Anak', 'Child Admission', '15000.00', 'perorangan', 'aktif', NULL, '2026-08-03 15:56:55', '2026-10-17 00:48:35'),
('3', 'Tiket Wisatawan Mancanegara', 'International Visitor', '30000.00', 'perorangan', 'aktif', NULL, '2026-08-03 15:56:55', '2026-10-17 00:48:35');

-- --------------------------------------------------------
-- Struktur Tabel `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('wisatawan','pengelola') NOT NULL DEFAULT 'wisatawan',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_method` varchar(10) DEFAULT NULL,
  `login_count` int(10) unsigned NOT NULL DEFAULT 0,
  `registration_method` varchar(16) NOT NULL DEFAULT 'email',
  `email_verification_method` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  KEY `users_email_index` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_registration_method_index` (`registration_method`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Isi Data Tabel `users`
INSERT INTO `users` VALUES 
('2', 'Pengelola Bangkiang Jaran', 'pengelola', 'pengelola@bangkiangjaran.com', '081234567890', '2026-08-03 15:56:54', '$2y$12$lHWc4tKuWMPp8PUscwNN9.IlKqyKxc0txsM6.5H16n81uZhOd9DEK', 'pengelola', NULL, '2026-08-03 15:56:54', '2026-10-24 22:51:04', NULL, NULL, 'manual', '0', 'email', NULL),
('3', 'Wisatawan Demo', 'wisatawan', 'wisatawan@demo.com', '081234567891', '2026-08-03 15:56:55', '$2y$12$FNkPIDlSLJOmloDhKlnzF.K9d7rmGmmV.eaI31tvG6CRTqQFkGif.', 'wisatawan', NULL, '2026-08-03 15:56:55', '2026-10-24 22:51:33', NULL, NULL, 'manual', '0', 'email', NULL);

SET FOREIGN_KEY_CHECKS = 1;
