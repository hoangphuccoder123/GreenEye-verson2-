-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 20, 2025 lúc 02:37 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `greeneye`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_applications`
--

CREATE TABLE `admin_applications` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `organization` varchar(255) DEFAULT 'chưa có',
  `activities` text DEFAULT 'chưa có',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_applications`
--

INSERT INTO `admin_applications` (`id`, `username`, `email`, `dob`, `password`, `organization`, `activities`, `reason`, `status`, `created_at`) VALUES
(5, 'hai anh', 'nalana9569@gmai.com', '2008-02-01', '$2y$10$WHy4sWOwfqC88ugoFkAJiOKdEJMLYzi1.TYDaAB9uHoe5dwG6.xh2', 'không có', 'không có', 'tôi muốn đóng góp sức lực giúp cho môi trường thêm xanh', 'approved', '2025-06-23 09:04:01'),
(6, 'hai anh', 'nalana9569@gmai.com', '2008-02-01', '$2y$10$WHy4sWOwfqC88ugoFkAJiOKdEJMLYzi1.TYDaAB9uHoe5dwG6.xh2', 'không có', 'không có', 'tôi muốn đóng góp sức lực giúp cho môi trường thêm xanh', 'approved', '2025-06-23 09:04:01'),
(7, 'Hai Anh', 'nalana9569@gmai.com', '2000-12-28', '$2y$10$HbCsgxysOj3e2G7vlH9Q6eXwPtRoP8glb5wTE1Y8AyyH0kz9lCaIy', '...', '...', '.....', 'approved', '2025-06-29 01:16:37'),
(8, 'Hai Anh', 'nalana9569@gmai.com', '2000-12-28', '$2y$10$HbCsgxysOj3e2G7vlH9Q6eXwPtRoP8glb5wTE1Y8AyyH0kz9lCaIy', '...', '...', '.....', 'rejected', '2025-06-29 01:16:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `su_kien`
--

CREATE TABLE `su_kien` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `thoigian` datetime NOT NULL,
  `soluong` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `diadiem` varchar(255) DEFAULT NULL,
  `mota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `su_kien`
--

INSERT INTO `su_kien` (`id`, `ten`, `thoigian`, `soluong`, `created_at`, `diadiem`, `mota`) VALUES
(11, 'Sự kiện dọn rác ở công viên', '2026-11-12 10:00:00', 30, '2025-06-29 01:50:05', 'Công viên Máy Tơ, Ngô Quyền, Hải Phòng', 'có nhiều rác thải, gây ảnh hưởng đến môi trường mọi người xung quanh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tham_gia_su_kien`
--

CREATE TABLE `tham_gia_su_kien` (
  `user_id` int(11) NOT NULL,
  `su_kien_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tham_gia_su_kien`
--

INSERT INTO `tham_gia_su_kien` (`user_id`, `su_kien_id`) VALUES
(9, 11);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trash_points`
--

CREATE TABLE `trash_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_hash` varchar(64) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `ai_analysis` text DEFAULT NULL,
  `ai_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `trash_points`
--

INSERT INTO `trash_points` (`id`, `user_id`, `latitude`, `longitude`, `description`, `image_url`, `created_at`, `image_hash`, `verified`, `ai_analysis`, `ai_verified`) VALUES
(12, 9, 10.772139352619016, 106.70308113098146, ' [AI: Rác thải vừa phải với tác động môi trường trung bình]', 'uploads/68609a09300e5.jpg', '2025-06-29 01:42:50', '59f29847f0230b13065a1bf8e7092509', 1, NULL, 0),
(13, 9, 10.785608883965764, 106.70752644582537, ' [AI: Rác thải vừa phải với tác động môi trường trung bình]', 'uploads/6860ae1073140.jpg', '2025-06-29 03:08:17', 'c812bdae12e3f61ca7833a7a1fa7c9d8', 1, NULL, 0),
(14, 9, 10.792079946684561, 106.71734333212953, ' [AI: Rác thải nhiều với tác động môi trường nghiêm trọng]', 'uploads/6860b5ef813ea.jpg', '2025-06-29 03:41:53', 'a10545b14a82ac6c3ddae39a47712a0f', 1, NULL, 0),
(15, 9, 10.78364856712734, 106.72747135336976, ' [AI: Rác thải nhiều với tác động môi trường nghiêm trọng]', 'uploads/68752ed952721.jpg', '2025-07-14 16:23:07', '2bb55ec4001ec3dbd23d3aeb46da973d', 0, '{\"image_type\":\"trash\",\"pollution_level\":4}', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) DEFAULT 0,
  `role` varchar(20) DEFAULT 'nguoi_dung',
  `pending_admin` tinyint(1) DEFAULT 0,
  `social_points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `date_of_birth`, `created_at`, `points`, `role`, `pending_admin`, `social_points`) VALUES
(9, 'Trương Nhật Anh', 'nhatanh28112008@gmail.com', '$2y$10$TR2tNkwKQA9QQvSwxFipbupiLct8dypPWdZ4MBT3n4IFVy8uPvrwO', '0862053855', '2000-12-28', '2025-06-29 01:12:41', 0, 'nguoi_dung', 0, 40),
(10, 'Hai Anh', 'nalana9569@gmai.com', '$2y$10$HbCsgxysOj3e2G7vlH9Q6eXwPtRoP8glb5wTE1Y8AyyH0kz9lCaIy', '', '2000-12-28', '2025-06-29 01:16:51', 0, 'admin', 0, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_applications`
--
ALTER TABLE `admin_applications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `su_kien`
--
ALTER TABLE `su_kien`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tham_gia_su_kien`
--
ALTER TABLE `tham_gia_su_kien`
  ADD PRIMARY KEY (`user_id`,`su_kien_id`),
  ADD KEY `su_kien_id` (`su_kien_id`);

--
-- Chỉ mục cho bảng `trash_points`
--
ALTER TABLE `trash_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_ai_verified` (`user_id`,`ai_verified`),
  ADD KEY `idx_ai_verified` (`ai_verified`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_applications`
--
ALTER TABLE `admin_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `su_kien`
--
ALTER TABLE `su_kien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `trash_points`
--
ALTER TABLE `trash_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tham_gia_su_kien`
--
ALTER TABLE `tham_gia_su_kien`
  ADD CONSTRAINT `tham_gia_su_kien_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tham_gia_su_kien_ibfk_2` FOREIGN KEY (`su_kien_id`) REFERENCES `su_kien` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `trash_points`
--
ALTER TABLE `trash_points`
  ADD CONSTRAINT `trash_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
