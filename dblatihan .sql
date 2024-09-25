-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2024 at 08:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dblatihan`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_nilai`
--

CREATE TABLE `data_nilai` (
  `id` int(11) NOT NULL,
  `no_absen` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kelas` text NOT NULL,
  `push_up` int(11) DEFAULT 0,
  `pull_up` int(11) DEFAULT 0,
  `lari_12menit` int(11) DEFAULT NULL,
  `sit_up` int(11) DEFAULT 0,
  `instruktur_id` int(11) NOT NULL,
  `tanggal` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_nilai`
--

INSERT INTO `data_nilai` (`id`, `no_absen`, `nama`, `kelas`, `push_up`, `pull_up`, `lari_12menit`, `sit_up`, `instruktur_id`, `tanggal`) VALUES
(136, 1, 'Rangga Pasha', 'XI TJ', 15, 15, 15, 15, 29, '2024-09-10'),
(139, 1, 'Rangga Pasha Cucu Wibisono', 'X TJ', 15, 1, 1, 1, 29, '2024-09-10'),
(140, 1, 'Laud jika', 'X DP1', 1, 1, 1, 1, 29, '2024-09-10'),
(142, 2, 'Rudi', 'X TL1', 50, 50, 50, 50, 29, '2024-09-10'),
(144, 2, 'rgggg', 'X TJ', 4, 5, 6, 5, 29, '2024-09-25'),
(145, 5, 'RANGGA PASHA CW ', 'X TJ', 10, 50, 6, 4, 29, '2024-09-25');

-- --------------------------------------------------------

--
-- Table structure for table `tuser`
--

CREATE TABLE `tuser` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('Siswa','Instruktur') NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuser`
--

INSERT INTO `tuser` (`id`, `nama_lengkap`, `username`, `password`, `level`, `profile_picture`) VALUES
(12, 'nama saya budi', 'budi', '$2y$10$YuvoxlPg4EaePrQu178biOt4f/UgJ9O4FrRpPPf9kTzNoyZ64lm5i', 'Instruktur', NULL),
(20, 'Yoga ', 'yogi', '$2y$10$zhCR6AJ5ty5cEUMss7tslODmyW5LkUL3kxUYG5yRAV1/qPlmEZti6', 'Siswa', NULL),
(21, 'test', 'test', '$2y$10$YBK1GX7PW2KavCjYhkDVZeCwMEuiI1CxIV1Rd5GTYoopnJdaWbK8u', 'Instruktur', NULL),
(23, 'Rangga Pasha Cucu Wibisono', 'royal', '$2y$10$rWNeHQpjO3lcZ4jW5KSrQe/uSc9a8eLgWCY90z9x08byoJxCp8By.', 'Siswa', NULL),
(24, 'NAMA LENGKAP', 'usernamesiswa', '$2y$10$3K4GAEUeDwUCpKuiinioIeWYkKwHYOlcRYkSAPHvTybql88hA7rX2', 'Siswa', NULL),
(26, 'Instruktur Rangga', 'rangga', '12345', 'Instruktur', NULL),
(27, 'Rangga Pasha Cucu Wibisono', 'instruktur', '$2y$10$2o2x3u3RRajTpNaAtUrP.eUM9ClSND2FQlmwOvJUSON3PGJl/wCz6', 'Instruktur', NULL),
(28, 'Rangga Pasha Cucu Wibisono', 'user123', '$2y$10$TsjRjG1A3FpukzjvsafDmuyld9/wFuFVsYEHLYExP/zJ/ezILd/8m', 'Siswa', NULL),
(29, 'Rangga Pasha Cucu Wibisono', 'admin', '$2y$10$YzoaKNrQJk.bk7oAcKXqm.3biYf/KrENHlr9kCzYx8QW.16SI.z0e', 'Instruktur', 'jisoo-blackpink-kim-ji-soo-hd-wallpaper-preview.jpg'),
(30, 'Budiono Siregar', 'kapal laud', '$2y$10$4iH.Kdm77Mp/IeB6oARz5OwX964exi8rvevmnsl49GmeUN3qP/nRq', 'Siswa', NULL),
(31, 'siswa', 'siswa', '$2y$10$PMeyXXXS1i/kyojRaZqwvePxV604EpBvx9LdY3QUfbIbHZ2EllHua', 'Siswa', NULL),
(32, 'Rangga Pasha Cucu Wibisono', '12345', '$2y$10$5qwr23QW.ois1n/IcDAaleoyhsstvk9QcKjpGnGqGNI82uJnPvhsW', 'Siswa', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_nilai`
--
ALTER TABLE `data_nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instruktur_id` (`instruktur_id`);

--
-- Indexes for table `tuser`
--
ALTER TABLE `tuser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_nilai`
--
ALTER TABLE `data_nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `tuser`
--
ALTER TABLE `tuser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_nilai`
--
ALTER TABLE `data_nilai`
  ADD CONSTRAINT `data_nilai_ibfk_1` FOREIGN KEY (`instruktur_id`) REFERENCES `tuser` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
