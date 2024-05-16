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

-- Dumping structure for table pemweb.daily_expenses
CREATE TABLE IF NOT EXISTS `daily_expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `payment_method` varchar(50) DEFAULT NULL,
  `account` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  CONSTRAINT `daily_expenses_chk_1` CHECK ((`type` in (_utf8mb4'pemasukan',_utf8mb4'pengeluaran')))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pemweb.daily_expenses: ~15 rows (approximately)
INSERT INTO `daily_expenses` (`id`, `amount`, `type`, `category`, `date`, `description`, `payment_method`, `account`, `created_at`, `updated_at`) VALUES
	(1, 500000.00, 'pemasukan', 'gaji', '2024-05-01', 'Gaji bulanan dari perusahaan', 'transfer bank', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(2, 150000.00, 'pengeluaran', 'belanja', '2024-05-02', 'Belanja bulanan di supermarket', 'kartu kredit', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(3, 75000.00, 'pengeluaran', 'hiburan', '2024-05-03', 'Menonton film di bioskop', 'tunai', 'dompet', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(4, 20000.00, 'pengeluaran', 'transportasi', '2024-05-04', 'Ongkos ojek online', 'dompet digital', 'OVO', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(5, 300000.00, 'pemasukan', 'gaji', '2024-05-05', 'Penjualan barang bekas', 'tunai', 'dompet', '2024-05-16 13:41:11', '2024-05-16 13:42:10'),
	(6, 100000.00, 'pengeluaran', 'makan', '2024-05-06', 'Makan malam di restoran', 'kartu debit', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(7, 50000.00, 'pengeluaran', 'kesehatan', '2024-05-07', 'Beli obat di apotek', 'tunai', 'dompet', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(8, 450000.00, 'pemasukan', 'gaji', '2024-05-08', 'Pembayaran proyek freelance', 'transfer bank', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:42:16'),
	(9, 120000.00, 'pengeluaran', 'belanja', '2024-05-09', 'Beli pakaian baru', 'kartu kredit', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(10, 80000.00, 'pengeluaran', 'hiburan', '2024-05-10', 'Berlangganan layanan streaming', 'dompet digital', 'GoPay', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(11, 25000.00, 'pengeluaran', 'transportasi', '2024-05-11', 'Tiket bus kota', 'tunai', 'dompet', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(12, 200000.00, 'pengeluaran', 'belanja', '2024-05-12', 'Beli bahan makanan', 'kartu debit', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(13, 150000.00, 'pengeluaran', 'makan', '2024-05-13', 'Makan siang di kafe', 'kartu kredit', 'rekening bank', '2024-05-16 13:41:11', '2024-05-16 13:41:11'),
	(14, 100000.00, 'pemasukan', 'gaji', '2024-05-14', 'Hadiah ulang tahun dari teman', 'tunai', 'dompet', '2024-05-16 13:41:11', '2024-05-16 13:42:23'),
	(15, 30000.00, 'pengeluaran', 'kesehatan', '2024-05-15', 'Konsultasi dokter online', 'dompet digital', 'DANA', '2024-05-16 13:41:11', '2024-05-16 13:41:11');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
