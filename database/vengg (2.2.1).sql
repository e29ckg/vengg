-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2024 at 09:07 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vengg`
--

-- --------------------------------------------------------

--
-- Table structure for table `dep`
--

CREATE TABLE `dep` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dep`
--

INSERT INTO `dep` (`id`, `name`, `date_create`) VALUES
(1, 'พนักงานคอมพิวเตอร์', '2019-08-10 17:45:49'),
(2, 'นักวิชาการคอมพิวเตอร์', '2019-08-10 17:45:49'),
(3, 'เจ้าหน้าที่ศาลยุติธรรมปฏิบัติงาน', '2019-08-10 17:45:49'),
(4, 'เจ้าหน้าที่ศาลยุติธรรมชำนาญงาน', '2019-08-10 17:45:49'),
(5, 'นักจิตวิทยาปฏิบัติการ', '2019-08-10 17:45:49'),
(6, 'พนักงานสถานที่', '2019-08-10 17:45:49'),
(7, 'พนักงานขับรถยนต์', '2019-08-10 17:45:49'),
(8, 'เจ้าหน้าที่ศาลยุติธรรม', '2019-08-10 17:45:49'),
(9, 'เจ้าพนักงานศาลยุติธรรมปฏิบัติการ', '2019-08-10 17:45:49'),
(10, 'นิติกรชำนาญการ', '2019-08-10 17:45:49'),
(11, 'เจ้าพนักงานศาลยุติธรรมชำนาญการ', '2019-08-10 17:45:49'),
(12, 'นักวิชาการเงินและบัญชีปฏิบัติการ', '2019-08-10 17:45:49'),
(13, 'เจ้าพนักงานศาลยุติธรรมชำนาญการพิเศษ', '2019-08-10 17:45:49'),
(14, 'นิติกร', '2019-08-10 17:45:49'),
(15, 'ผู้อำนวยการฯ', '2019-08-10 17:45:49'),
(17, 'พนักงานขับรถยนต์(จ้างเหมา)', NULL),
(18, 'ผู้พิพากษา', NULL),
(19, 'นิติกรชำนาญการพิเศษ', NULL),
(20, 'เจ้าพนักงานการเงินและบัญชีปฏิบัติงาน', NULL),
(21, 'นักจิตวิทยาชำนาญการ', NULL),
(22, 'เจ้าพนักงานศาลยุติธรรม', NULL),
(23, 'นิติกรปฏิบัติการ', NULL),
(24, 'นักวิชาการเงินและบัญชีชำนาญการพิเศษ', NULL),
(25, 'เจ้าพนักงานตำรวจศาลปฏิบัติการ', NULL),
(28, 'ผู้พิพากษาสมทบ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fname`
--

CREATE TABLE `fname` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fname`
--

INSERT INTO `fname` (`id`, `name`, `date_create`) VALUES
(1, 'นาย', '2019-08-10 17:45:49'),
(2, 'นาง', '2019-08-10 17:45:50'),
(3, 'นางสาว', '2019-08-10 17:45:50'),
(4, 'พันจ่าเอก', NULL),
(5, 'พ.ต.อ.', NULL),
(6, 'พท.', NULL),
(7, 'สิบตำรวจเอก', NULL),
(8, 'หม่อมหลวง', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_create` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`id`, `name`, `date_create`) VALUES
(1, 'ผู้อำนวยการฯ', '2019-10-06 18:49:32'),
(2, 'กลุ่มช่วยอำนวยการ', '2019-10-06 18:49:32'),
(3, 'กลุ่มงานช่วยพิจารณาคดี', '2019-10-06 18:49:32'),
(4, 'กลุ่มงานคดี', '2019-10-06 18:49:32'),
(5, 'กลุ่มงานคลัง', '2019-10-06 18:49:32'),
(6, 'กลุ่มงานปริการประชาชนและประชาสัมพันธ์', '2019-10-06 18:49:32'),
(7, 'กลุ่มงานไกล่เกลี่ยและประนอมข้อพิพาท', '2019-10-06 18:49:32'),
(8, 'ผู้พิพากษา', NULL),
(9, 'ส่วนมาตรการพิเศษ', NULL),
(10, 'เจ้าพนักงานตำรวจศาล', NULL),
(11, 'ส่วนส่งเสริมและวิชาการ', NULL),
(12, 'ส่วนเทคโนโลยีสารสนเทศ', NULL),
(14, 'ผู้พิพากษาสมทบ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `line`
--

CREATE TABLE `line` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `line`
--

INSERT INTO `line` (`id`, `name`, `token`, `status`) VALUES
(1, 'admin', 'StzWTl6iwQfwKKZPqsHxLrx6Ie6g4GPiTnVaXaJzIKa ', 0),
(2, 'ven', 'StzWTl6iwQfwKKZPqsHxLrx6Ie6g4GPiTnVaXaJzIKa', 0),
(3, 'ven_admin', 'StzWTl6iwQfwKKZPqsHxLrx6Ie6g4GPiTnVaXaJzIKa', 0);

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `id_card` varchar(255) DEFAULT NULL,
  `fname` varchar(25) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sname` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `bloodtype` varchar(255) DEFAULT NULL,
  `dep` varchar(255) DEFAULT NULL,
  `workgroup` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `bank_comment` varchar(200) DEFAULT NULL,
  `status` smallint(6) DEFAULT 10,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `st` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `user_id`, `id_card`, `fname`, `name`, `sname`, `img`, `birthday`, `bloodtype`, `dep`, `workgroup`, `address`, `phone`, `bank_account`, `bank_comment`, `status`, `created_at`, `updated_at`, `st`) VALUES
(1, '1', NULL, 'นาย', 'ผู้ดูแลระบบ', 'ทดสอบ', NULL, NULL, NULL, 'พนักงานสถานที่', 'กลุ่มช่วยอำนวยการ', NULL, '9999', '', '', 10, '2023-12-14 09:54:13', '2024-05-16 06:58:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sign_name`
--

CREATE TABLE `sign_name` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dep` varchar(255) NOT NULL,
  `dep2` varchar(255) NOT NULL,
  `dep3` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `st` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sign_name`
--

INSERT INTO `sign_name` (`id`, `name`, `dep`, `dep2`, `dep3`, `role`, `st`) VALUES
(6, 'ศาลเยาวชนและครอบครัวกลาง1', 'สำนักอำนวยการประจำศาลเยาวชนและครอบครัวกลาง1', '', '', 'Court_Name', 1),
(7, 'นายเผดิม เพ็ชรกูล', 'อธิบดีผู้พิพากษาศาลเยาวชนและครอบครัวกลาง', '', '', 'Chief_Judge', 1),
(8, 'นางสาวสุดาทิพย์ อำนวยพันธ์วิไล', 'ผู้อำนวยการสำนักอำนวยการประจำศาลเยาวชนและครอบครัวกลาง', '', '', 'Director', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` smallint(6) NOT NULL DEFAULT 1,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, '$2y$10$SjrUtSxJ8dcOU2cnkltWbOFNzXpCKhd0.5McR3qskS0nIVsOLZrT2', NULL, NULL, 9, 10, '2023-12-14 09:54:13', '2024-04-27 18:13:27');

-- --------------------------------------------------------

--
-- Table structure for table `ven`
--

CREATE TABLE `ven` (
  `id` int(11) NOT NULL,
  `user_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ven_com_id` varchar(255) DEFAULT NULL,
  `ven_com_idb` varchar(255) DEFAULT NULL,
  `ven_date` date NOT NULL,
  `ven_time` varchar(255) NOT NULL,
  `vn_id` int(11) DEFAULT NULL,
  `vns_id` int(11) DEFAULT NULL,
  `gcal_id` varchar(255) DEFAULT NULL,
  `ref1` varchar(255) DEFAULT NULL,
  `ref2` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `create_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `ven_change`
--

CREATE TABLE `ven_change` (
  `id` varchar(255) NOT NULL,
  `ven_month` varchar(255) DEFAULT NULL,
  `ven_date1` varchar(255) DEFAULT NULL,
  `ven_date2` varchar(255) DEFAULT NULL,
  `ven_com_id` varchar(255) DEFAULT NULL,
  `ven_com_idb` int(11) NOT NULL,
  `vn_id` int(11) NOT NULL,
  `vns_id` int(11) NOT NULL,
  `ven_id1` int(11) DEFAULT NULL,
  `ven_id2` int(11) DEFAULT NULL,
  `ven_id1_old` int(11) DEFAULT NULL,
  `ven_id2_old` int(11) DEFAULT NULL,
  `user_id1` int(11) DEFAULT NULL,
  `user_id2` int(11) DEFAULT NULL,
  `s_po` int(11) DEFAULT NULL,
  `s_bb` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ref1` varchar(255) DEFAULT NULL,
  `ref2` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `ven_com`
--

CREATE TABLE `ven_com` (
  `id` int(11) NOT NULL,
  `ven_com_num` varchar(255) DEFAULT NULL,
  `ven_com_date` varchar(255) DEFAULT NULL,
  `ven_month` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `vn_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `ven_name`
--

CREATE TABLE `ven_name` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_full` text DEFAULT NULL,
  `DN` varchar(255) DEFAULT NULL,
  `srt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ven_name`
--

INSERT INTO `ven_name` (`id`, `name`, `name_full`, `DN`, `srt`) VALUES
(24, 'เวรเปิดทำการศาลนอกเวลาราชการ (เวรตรวจสอบการจับ)', 'ให้ข้าราชการฝ่ายตุลาการศาลยุติธรรม พนักงานราชการศาลยุติธรรม และลูกจ้างในศาลเยาวชนและครอบครัวกลาง อยู่ปฏิบัติหน้าที่โดยเปิดทำการศาลนอกเวลาราชการในวันหยุดราชการ', 'กลางวัน', 0),
(25, 'เวรปฏิบัติหน้าที่ออกหมายจับและหมายค้นนอกเวลาราชการ (เวรกลางคืน)', 'ให้ข้าราชการฝ่ายตุลาการศาลยุติธรรม พนักงานราชการศาลยุติธรรม และลูกจ้างในศาลเยาวชนและครอบครัวกลางอยู่ปฏิบัติหน้าที่ออกหมายจับและหมายค้นนอกเวลาราชการ (เวรกลางคืน) ', 'กลางคืน', 5),
(27, 'เวรปฏิบัติงานนอกเวลาราชการในวันทำการปกติตามโครงการเปิดทำการศาลนอกเวลาราชการฯ 16.30-20.30 น.', 'ให้ข้าราชการตุลาการ ข้าราชการศาลยุติธรรม ลูกจ้าง และพนักงานราชการ ปฏิบัติงานในวันหยุดราชการ เวลา 16.30 – 20.30 นาฬิกา ตามโครงการเปิดทำการศาลนอกเวลาราชการเพื่อเร่งรัดการพิจารณาพิพากษาคดี หรือเพื่ออำนวยความสะดวกแก่ประชาชน ประจำปีงบประมาณ พ.ศ. ๒๕๖๗ ', 'nightCourt', 3),
(28, 'เวรปฏิบัติงานในวันหยุดราชการตามโครงการเปิดทำการศาลนอกเวลาราชการฯ 8.30-16.30 น.', 'ให้ข้าราชการตุลาการ ข้าราชการศาลยุติธรรม ลูกจ้าง และพนักงานราชการ ปฏิบัติงานในวันหยุดราชการ เวลา 08.30 – 16.30 นาฬิกา ตามโครงการเปิดทำการศาลนอกเวลาราชการเพื่อเร่งรัดการพิจารณาพิพากษาคดี หรือเพื่ออำนวยความสะดวกแก่ประชาชน ประจำปีงบประมาณ พ.ศ. ๒๕๖๗ ', 'กลางวัน', 1),
(29, 'เวรปฏิบัติงานนอกเวลาราชการในวันทำการปกติตามโครงการเปิดทำการศาลนอกเวลาราชการฯ 16.30-20.30 น. ( ผู้พิพากษาสมทบ)', NULL, 'nightCourt', 4),
(30, 'เวรปฏิบัติงานในวันหยุดราชการตามโครงการเปิดทำการศาลนอกเวลาราชการฯ 8.30-16.30 น. (ผู้พิพากษาสมทบ)', NULL, 'กลางวัน', 6);

-- --------------------------------------------------------

--
-- Table structure for table `ven_name_sub`
--

CREATE TABLE `ven_name_sub` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ven_name_id` int(11) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `srt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ven_name_sub`
--

INSERT INTO `ven_name_sub` (`id`, `name`, `ven_name_id`, `price`, `color`, `srt`) VALUES
(109, 'ผู้พิพากษา', 25, 2500, 'Violet', 0),
(110, 'จนท', 25, 1200, 'Violet', 1),
(113, 'ผู้พิพากษา', 27, 2000, 'Green', 0),
(115, 'ผู้พิพากษา', 28, 3000, 'Brown', 0),
(116, 'รับฟ้อง+ปชส ', 28, 1500, 'Brown', 1),
(117, 'งานรับฟ้อง', 24, 1500, 'BlueViolet', 1),
(118, 'งานหน้าบัลลังก์', 24, 1500, 'BlueViolet', 2),
(119, 'งานหมาย', 24, 1500, 'BlueViolet', 3),
(120, 'งานประชาสัมพันธ์', 24, 1500, 'BlueViolet', 4),
(121, 'งานการเงิน', 24, 1500, 'BlueViolet', 5),
(123, 'รับฟ้อง+ปชส', 27, 1000, 'Green', 1),
(124, 'การเงิน+ปล่อยตัวชั่วคราว', 27, 1000, 'Green', 2),
(125, 'หน้าบัลลังก์', 27, 1000, 'Green', 3),
(128, 'การเงิน+ปล่อยตัวชั่วคราว ', 28, 1500, 'Brown', 2),
(129, 'หน้าบัลลังก์', 28, 1500, 'Brown', 3),
(130, 'ผู้พิพากษา', 24, 3000, 'BlueViolet', 0),
(133, 'ผู้พิพากษาสมทบ', 29, 1000, 'Magenta', 0),
(134, 'ผู้พิพากษาสมทบ', 30, 1000, 'DarkCyan', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ven_user`
--

CREATE TABLE `ven_user` (
  `vu_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order` int(2) DEFAULT NULL,
  `vn_id` int(11) NOT NULL,
  `vns_id` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `dep`
--
ALTER TABLE `dep`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fname`
--
ALTER TABLE `fname`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `line`
--
ALTER TABLE `line`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `sign_name`
--
ALTER TABLE `sign_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `ven`
--
ALTER TABLE `ven`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ven_change`
--
ALTER TABLE `ven_change`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ven_com`
--
ALTER TABLE `ven_com`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ven_name`
--
ALTER TABLE `ven_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ven_name_sub`
--
ALTER TABLE `ven_name_sub`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ven_user`
--
ALTER TABLE `ven_user`
  ADD PRIMARY KEY (`vu_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dep`
--
ALTER TABLE `dep`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `fname`
--
ALTER TABLE `fname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `line`
--
ALTER TABLE `line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `sign_name`
--
ALTER TABLE `sign_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1680162049;

--
-- AUTO_INCREMENT for table `ven`
--
ALTER TABLE `ven`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1715843175;

--
-- AUTO_INCREMENT for table `ven_com`
--
ALTER TABLE `ven_com`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1715826182;

--
-- AUTO_INCREMENT for table `ven_name`
--
ALTER TABLE `ven_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `ven_name_sub`
--
ALTER TABLE `ven_name_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `ven_user`
--
ALTER TABLE `ven_user`
  MODIFY `vu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=719;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
