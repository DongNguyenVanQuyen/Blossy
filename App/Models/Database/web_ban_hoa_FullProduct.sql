-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 08, 2025 lúc 07:20 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_ban_hoa`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `recipient` varchar(128) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `line1` varchar(191) NOT NULL,
  `line2` varchar(191) DEFAULT NULL,
  `ward` varchar(128) DEFAULT NULL,
  `district` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `recipient`, `phone`, `line1`, `line2`, `ward`, `district`, `city`, `note`, `is_default`, `created_at`) VALUES
(1, 2, 'Nguyễn A', '0911111111', '123 Đường Hoa', NULL, NULL, 'Quận 1', 'TP.HCM', NULL, 1, '2025-09-14 20:57:51'),
(3, 8, '', '', '- 574/3/25/7A Kinh Dương Vương, Phường An Lạc, Quận Bình Tân', NULL, NULL, NULL, NULL, NULL, 0, '2025-10-06 14:56:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `entity` varchar(64) NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `updated_at`) VALUES
(1, 8, '2025-10-08 23:57:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `added_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `added_at`) VALUES
(27, 1, 2, 1, '2025-10-08 23:57:53');

--
-- Bẫy `cart_items`
--
DELIMITER $$
CREATE TRIGGER `trg_cart_items_after_delete` AFTER DELETE ON `cart_items` FOR EACH ROW BEGIN
  UPDATE carts
  SET updated_at = NOW()
  WHERE id = OLD.cart_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_cart_items_after_insert` AFTER INSERT ON `cart_items` FOR EACH ROW BEGIN
  UPDATE carts
  SET updated_at = NOW()
  WHERE id = NEW.cart_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_cart_items_after_update` AFTER UPDATE ON `cart_items` FOR EACH ROW BEGIN
  UPDATE carts
  SET updated_at = NOW()
  WHERE id = NEW.cart_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Hoa bó', 'hoa-bo', 1, '2025-09-14 20:57:51', '2025-09-14 20:57:51'),
(2, NULL, 'Hoa giỏ', 'hoa-gio', 1, '2025-09-14 20:57:51', '2025-09-14 20:57:51'),
(3, NULL, 'Hoa cưới', 'hoa-cuoi', 1, '2025-09-14 20:57:51', '2025-09-14 20:57:51'),
(4, NULL, 'Hoa Tình Yêu', 'hoa-tinh-yeu', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(5, NULL, 'Hoa Sinh Nhật', 'hoa-sinh-nhat', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(6, NULL, 'Cây Văn Phòng', 'cay-van-phong', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(7, NULL, 'Mẫu Hoa Mới', 'mau-hoa-moi', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(8, NULL, 'Hoa Chúc Mừng', 'hoa-chuc-mung', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(9, NULL, 'Hoa Chia Buồn', 'hoa-chia-buon', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(10, NULL, 'Hoa Tốt Nghiệp', 'hoa-tot-nghiep', 1, '2025-09-14 21:41:25', '2025-09-14 21:46:36'),
(11, NULL, 'Hoa Cao Cấp', 'hoa-cao-cap', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(12, NULL, 'Hoa Theo Mùa', 'hoa-theo-mua', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(13, NULL, 'Hoa Sự Kiện', 'hoa-su-kien', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25'),
(15, NULL, 'Khác', 'khac', 1, '2025-09-14 21:41:25', '2025-09-14 21:41:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_levels`
--

CREATE TABLE `customer_levels` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(16) NOT NULL,
  `name` varchar(32) NOT NULL,
  `min_total_spent` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_levels`
--

INSERT INTO `customer_levels` (`id`, `code`, `name`, `min_total_spent`) VALUES
(1, 'silver', 'Silver', 0.00),
(2, 'gold', 'Gold', 5000000.00),
(3, 'diamond', 'Diamond', 15000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(5, 8, 4, '2025-10-06 23:03:16'),
(7, 8, 12, '2025-10-06 23:07:17'),
(8, 8, 9, '2025-10-06 23:07:34'),
(9, 8, 17, '2025-10-06 23:08:51'),
(12, 8, 3, '2025-10-06 23:09:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 5,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`product_id`, `stock`, `low_stock_threshold`, `updated_at`) VALUES
(1, 40, 5, '2025-09-29 14:44:06'),
(2, 25, 5, '2025-10-09 00:03:25'),
(3, 52, 5, '2025-10-03 17:24:50'),
(4, 10, 5, '2025-10-03 17:24:50'),
(5, 20, 10, '2025-10-03 17:24:50'),
(6, 66, 5, '2025-10-03 17:24:50'),
(7, 6, 5, '2025-10-03 17:24:50'),
(8, 9, 5, '2025-10-03 17:24:50'),
(9, 21, 10, '2025-10-03 17:24:50'),
(10, 8, 5, '2025-10-03 17:24:50'),
(11, 0, 10, '2025-10-08 23:49:31'),
(12, 30, 5, '2025-10-03 17:24:50'),
(13, 0, 5, '2025-10-03 17:24:50'),
(14, 11, 10, '2025-10-03 17:24:50'),
(15, 16, 5, '2025-10-03 17:24:50'),
(16, 4, 10, '2025-10-03 17:24:50'),
(17, 0, 5, '2025-10-03 17:24:50'),
(18, 18, 5, '2025-10-03 17:24:50'),
(19, 10, 10, '2025-10-03 17:24:50'),
(20, 11, 10, '2025-10-03 17:24:50'),
(21, 12, 5, '2025-10-03 17:24:50'),
(22, 8, 5, '2025-10-03 17:24:50'),
(23, 10, 5, '2025-10-03 17:24:50'),
(24, 0, 5, '2025-10-03 17:24:50'),
(25, 20, 5, '2025-10-03 17:24:50'),
(26, 10, 5, '2025-10-03 17:24:50'),
(27, 18, 5, '2025-10-03 17:24:50'),
(28, 5, 5, '2025-10-03 17:24:50'),
(29, 30, 5, '2025-10-03 17:24:50'),
(30, 25, 5, '2025-10-03 17:24:50'),
(31, 9, 5, '2025-10-03 17:24:50'),
(32, 35, 5, '2025-10-03 17:29:44'),
(33, 42, 5, '2025-10-03 17:29:44'),
(34, 13, 5, '2025-10-03 17:29:44'),
(35, 19, 5, '2025-10-03 17:29:44'),
(36, 0, 5, '2025-10-09 00:16:48'),
(37, 15, 5, '2025-10-09 00:16:48'),
(38, 14, 5, '2025-10-09 00:16:48'),
(39, 16, 10, '2025-10-09 00:16:48'),
(40, 6, 5, '2025-10-09 00:16:48'),
(41, 7, 5, '2025-10-09 00:16:48'),
(42, 10, 5, '2025-10-09 00:16:48'),
(43, 11, 5, '2025-10-09 00:16:48'),
(44, 20, 5, '2025-10-09 00:16:48'),
(45, 12, 5, '2025-10-09 00:16:48'),
(46, 0, 5, '2025-10-09 00:16:48'),
(47, 19, 5, '2025-10-09 00:16:48'),
(48, 11, 5, '2025-10-09 00:16:48'),
(49, 5, 5, '2025-10-09 00:16:48'),
(50, 20, 5, '2025-10-09 00:16:48'),
(51, 12, 5, '2025-10-09 00:16:48'),
(52, 10, 5, '2025-10-09 00:16:48'),
(53, 8, 5, '2025-10-09 00:16:48'),
(54, 6, 5, '2025-10-09 00:16:48'),
(55, 15, 10, '2025-10-09 00:16:48'),
(56, 14, 5, '2025-10-09 00:16:48'),
(57, 20, 5, '2025-10-09 00:16:48'),
(58, 14, 5, '2025-10-09 00:16:48'),
(59, 13, 5, '2025-10-09 00:16:48'),
(60, 0, 5, '2025-10-09 00:16:48'),
(61, 19, 10, '2025-10-09 00:16:48'),
(62, 10, 5, '2025-10-09 00:16:48'),
(63, 7, 5, '2025-10-09 00:16:48'),
(64, 5, 5, '2025-10-09 00:16:48'),
(65, 3, 5, '2025-10-09 00:16:48'),
(66, 11, 5, '2025-10-09 00:16:48'),
(67, 17, 10, '2025-10-09 00:16:48'),
(68, 16, 5, '2025-10-09 00:16:48'),
(69, 22, 5, '2025-10-09 00:16:48'),
(70, 18, 5, '2025-10-09 00:16:48'),
(71, 10, 10, '2025-10-09 00:16:48'),
(72, 11, 5, '2025-10-09 00:16:48'),
(73, 6, 5, '2025-10-09 00:16:48'),
(74, 13, 5, '2025-10-09 00:16:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `body` text NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `title`, `body`, `created_by`, `created_at`) VALUES
(1, 'Ưu đãi mùa Thu', 'Giảm 15% cho bộ sưu tập mùa Thu!', 1, '2025-09-14 20:57:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message_users`
--

CREATE TABLE `message_users` (
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `message_users`
--

INSERT INTO `message_users` (`message_id`, `user_id`, `is_read`, `read_at`) VALUES
(1, 2, 0, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('cho_xac_nhan','dang_giao','thanh_cong','da_huy') NOT NULL DEFAULT 'cho_xac_nhan',
  `payment_method` enum('cod','vnpay','momo','bank') NOT NULL DEFAULT 'cod',
  `payment_status` enum('chua_thanh_toan','da_thanh_toan','hoan_tien') NOT NULL DEFAULT 'chua_thanh_toan',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `voucher_code` varchar(64) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `status`, `payment_method`, `payment_status`, `subtotal`, `discount_total`, `shipping_fee`, `grand_total`, `voucher_code`, `note`, `created_at`, `updated_at`) VALUES
(27, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 660000.00, 0.00, 30000.00, 690000.00, '', 'Giao hàng tận nơi', '2025-10-08 23:39:41', '2025-10-08 23:39:41'),
(28, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 660000.00, 0.00, 30000.00, 690000.00, '', 'Giao hàng tận nơi', '2025-10-08 23:48:03', '2025-10-08 23:48:03'),
(29, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 660000.00, 0.00, 30000.00, 690000.00, '', 'Giao hàng tận nơi', '2025-10-08 23:49:31', '2025-10-08 23:49:31'),
(30, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 350000.00, 0.00, 30000.00, 380000.00, '', 'Giao hàng tận nơi', '2025-10-08 23:58:02', '2025-10-08 23:58:02'),
(31, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 350000.00, 0.00, 30000.00, 380000.00, '', 'Giao hàng tận nơi', '2025-10-08 23:58:53', '2025-10-08 23:58:53'),
(32, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 350000.00, 0.00, 30000.00, 380000.00, '', 'Giao hàng tận nơi', '2025-10-09 00:00:58', '2025-10-09 00:00:58'),
(33, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 350000.00, 0.00, 30000.00, 380000.00, '', 'Giao hàng tận nơi', '2025-10-09 00:01:47', '2025-10-09 00:01:47'),
(34, 8, 1, 'cho_xac_nhan', 'cod', 'chua_thanh_toan', 350000.00, 0.00, 30000.00, 380000.00, '', 'Giao hàng tận nơi', '2025-10-09 00:03:25', '2025-10-09 00:03:25');

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_orders_after_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
  IF NEW.status = 'thanh_cong' AND OLD.status <> 'thanh_cong' THEN
    -- Trừ kho theo các dòng hàng
    UPDATE inventory inv
    JOIN order_items oi ON oi.product_id = inv.product_id
    SET inv.stock = GREATEST(inv.stock - oi.quantity, 0)
    WHERE oi.order_id = NEW.id;

    -- Cập nhật tổng chi tiêu để nâng hạng (đơn giản: dựa vào grand_total tích luỹ)
    UPDATE users u
    SET u.updated_at = NOW()
    WHERE u.id = NEW.user_id;

    -- Nâng hạng theo tổng chi tiêu (tính nhanh qua subquery)
    UPDATE users u
    JOIN (
      SELECT o.user_id, SUM(o.grand_total) AS total_spent
      FROM orders o
      WHERE o.status = 'thanh_cong' AND o.user_id = NEW.user_id
      GROUP BY o.user_id
    ) s ON s.user_id = u.id
    SET u.level_id = (
      SELECT cl.id FROM customer_levels cl
      WHERE s.total_spent >= cl.min_total_spent
      ORDER BY cl.min_total_spent DESC
      LIMIT 1
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(191) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `line_total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `line_total`) VALUES
(96, 27, 11, 'Giỏ Hoa Brave', 660000.00, 1, 660000.00),
(97, 28, 11, 'Giỏ Hoa Brave', 660000.00, 1, 660000.00),
(98, 29, 11, 'Giỏ Hoa Brave', 660000.00, 1, 660000.00),
(99, 30, 2, 'Hoa Mẫu Đan', 350000.00, 1, 350000.00),
(100, 31, 2, 'Hoa Mẫu Đan', 350000.00, 1, 350000.00),
(101, 32, 2, 'Hoa Mẫu Đan', 350000.00, 1, 350000.00),
(102, 33, 2, 'Hoa Mẫu Đan', 350000.00, 1, 350000.00),
(103, 34, 2, 'Hoa Mẫu Đan', 350000.00, 1, 350000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `season` enum('spring','summer','autumn','winter') DEFAULT NULL,
  `sku` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `compare_at_price` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `color`, `slug`, `season`, `sku`, `description`, `price`, `compare_at_price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hoa Hướng Dương', 'Vàng', 'bo-hoa-huong-duong', 'summer', 'SP001', 'Hoa hướng dương tươi rực rỡ – biểu tượng cho sự mạnh mẽ và năng lượng mùa hè.', 390000.00, 450000.00, 1, '2025-09-29 14:44:06', '2025-09-29 14:44:06'),
(2, 1, 'Hoa Mẫu Đan', 'Hồng', 'bo-hoa-mau-don', 'spring', 'SP002', 'Hoa mẫu đơn – biểu tượng của sự thịnh vượng, sang trọng và tình yêu nồng nàn. Những cánh hoa mềm mại, xếp lớp tinh tế mang đến vẻ đẹp lộng lẫy và đầy cuốn hút, thích hợp để làm quà tặng trong các dịp đặc biệt, đặc biệt rực rỡ vào mùa xuân.', 350000.00, 450000.00, 1, '2025-09-29 14:50:35', '2025-09-29 14:50:35'),
(3, 1, 'Hoa Vàng Sunny', 'Vàng', 'hoa-vang-sunny', 'spring', 'SP003', 'Hoa Vàng Sunny mang sắc vàng tươi rực rỡ, tượng trưng cho niềm vui, sự ấm áp và nguồn năng lượng tích cực. Với vẻ đẹp giản dị nhưng nổi bật, loài hoa này thường được lựa chọn để gửi gắm thông điệp hạnh phúc, may mắn và khởi đầu mới đầy hy vọng. Thích hợp cho cả trang trí không gian lẫn làm quà tặng, Hoa Vàng Sunny luôn mang đến cảm giác lạc quan và tràn đầy sức sống.', 890000.00, 1200000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(4, 1, 'Mắt Biếc', 'Trắng', 'mat-biec', 'spring', 'SP004', 'Mỗi người đều có những ước mơ, mục tiêu của riêng mình, vì vậy một lời chúc may mắn, một lời động viên từ những người thương yêu nhất sẽ tiếp thêm sức mạnh để bạn có thêm niềm tin trên con đường đến với ước mơ đầy tươi xanh.', 690000.00, 810000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(5, 1, 'Ban Mai', 'Trắng', 'ban-mai', 'spring', 'SP005', 'Bó hoa được lấy cảm hứng từ vẻ đẹp của tình yêu đó, được tạo nên từ những cánh hồng với tông màu pastel cùng cát tường trắng và các loại hoa tươi nhất.', 630000.00, 710000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(6, 1, 'Hoa Tú Cầu', 'Xanh', 'hoa-tu-cau', 'spring', 'SP006', 'Hoa Tú Cầu (Hydrangea) tượng trưng cho sự chân thành, biết ơn và những cảm xúc sâu sắc trong tình yêu. Với những cánh hoa mềm mại kết thành từng chùm tròn lớn, Tú Cầu mang vẻ đẹp dịu dàng, lãng mạn nhưng cũng không kém phần nổi bật.', 1550000.00, 1805000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(7, 1, 'Bó Hoa Bright Pink', 'Hồng', 'bo-hoa-bright-pink', 'spring', 'SP007', 'Một bó hoa \"giải cảm\" giúp cho tinh thần của người nhận trở nên phấn chấn và tràn đầy năng lượng hơn. Thật là một liều thuốc tinh thần thật tốt đẹp phải không nào, còn chần chừ gì mà không tặng nó ngay cho người mà bạn yêu quý nhất!', 1700000.00, 1880000.00, 0, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(8, 1, 'Melodious', 'Hồng', 'melodious', 'spring', 'SP008', 'Bó hoa là sự lựa chọn phù hợp để gửi tặng vợ, bạn gái hay cô bạn thân thiết vào dịp sinh nhật, kỷ niệm ngày yêu, ngày cưới.', 690000.00, 760000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(9, 1, 'Naive', 'Xanh biển', 'naive', 'winter', 'SP009', 'Bó hoa Naive là sự kết hợp hoàn hảo giữa hoa đồng tiền trắng phun xanh, cẩm tú cầu, cát tường trắng những loài hoa tượng trưng cho vẻ đẹp tinh khôi và sự thuần khiết.', 460000.00, 530000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(10, 2, 'Giỏ Hoa Cúc Trắng', 'Trắng', 'gio-hoa-cuc-trang', 'summer', 'SP0010', 'Giỏ hoa Cúc với màu trắng tinh khiết chủ đạo mang lại cảm giác thoải mái, dễ chịu. Giỏ hoa thích hợp dành tặng cho ngày sinh nhật hoặc các dịp đặc biệt.', 640000.00, 810000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(11, 2, 'Giỏ Hoa Brave', 'Vàng', 'gio-hoa-brave', 'spring', 'SP0011', 'Giỏ hoa Brave được tạo nên từ những cành hoa hướng dương tươi sáng, cúc ping phong vàng và trắng tạo nên nét đẹp tràn đầy sức sống nhưng cũng rất nhẹ nhàng và gần gũi.', 660000.00, 720000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(12, 2, 'Giỏ hoa Pinky Heaven', 'Hồng', 'gio-hoa-pinky-heaven', 'spring', 'SP0012', 'Mang tone hồng pastel chủ đạo, giỏ hoa tượng trưng cho sự ngây ngô, thuần khiết nhưng rất tha thiết như người con gái đang yêu.', 560000.00, 670000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(13, 2, 'Giỏ Hoa Thạch Thảo', 'Trắng', 'gio-hoa-thach-thao', 'spring', 'SP0013', 'Giỏ hoa được thiết kế từ hoa thạch thảo trắng, loài hoa tượng trưng cho tình yêu, tình bạn và sự biết ơn.', 860000.00, 950000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(14, 2, 'Giỏ Hoa Song Hỷ', 'Hồng', 'gio-hoa-song-hy', 'spring', 'SP0014', 'Giỏ hoa Song Hỷ được thiết kế từ hoa hồng đỏ và hoa lan vàng, tượng trưng cho tình yêu nồng nàn, hạnh phúc viên mãn và sự may mắn trong ngày trọng đại.', 570000.00, 650000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(15, 3, 'Bó Hoa Cưới Mùa Yêu Đầu', 'Đỏ', 'bo-hoa-cuoi-mua-yeu-dau', 'spring', 'SP0015', 'Bó hoa được thiết kế với tông màu đỏ - trắng, là sự kết hợp đơn giản nhưng tinh tế và đầy cuốn hút, sẽ giúp cô dâu thêm phần nổi bật trong ngày trọng đại nhất của cuộc đời.', 900000.00, 1000000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(16, 3, 'Bó Hoa Cưới Sunshine', 'Vàng', 'bo-hoa-cuoi-sunshine', 'summer', 'SP0016', 'Bó hoa cưới Sunshine với tông màu vàng làm chủ đạo tạo nên 1 tác phẩm rực rỡ và đầy ấn tượng. Mang ý nghĩa cho sự chung thủy, chân thành, ấm áp trong tình yêu.', 810000.00, 910000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(17, 3, 'Hoa Cưới Dream Of You', 'Trắng', 'hoa-cuoi-dream-of-you', 'spring', 'SP0017', 'Bó với tinh khiết với màu trắng đến từ hoa hồng, cẩm tú cầu và baby trắng. Đây sẽ là bó hoa thích hợp cho ngày trọng đại của bạn.', 820000.00, 1000000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(18, 3, 'Hoa Cưới Mao Lương Trắng', 'Trắng', 'hoa-cuoi-mao-luong-trang', 'spring', 'SP0018', 'Màu trắng tinh khôi của hoa mao lương kết hợp với hoa thúy châu tạo nên bó hoa cưới vừa tinh tế, vừa sang trọng cho các nàng dâu.', 2050000.00, 2540000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(19, 3, 'Hoa Cưới Love Story', 'Hồng', 'hoa-cuoi-love-story', 'spring', 'SP0019', 'Bó hoa cưới Love Story được thiết kế từ hoa mẫu đơn hồng loài hoa tượng trưng cho tình yêu và sắc đẹp. Nếu bạn cần một mẫu hoa sang trọng cho lễ cưới, Love Story là sự lựa chọn không thể bỏ qua.', 2331000.00, 2590000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(20, 4, 'Greeny', 'Xanh lá', 'greeny', 'spring', 'SP0020', 'Greeny được thiết kế từ những bông hoa cát tường xanh, loài hoa tượng trưng cho sự may mắn, thành công và tài lộc. Bó hoa là lựa chọn hoàn hảo để tặng cô bạn thân vào dịp sinh nhật hay lễ tốt nghiệp.', 550000.00, 610000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(21, 4, 'Passionate Heart', 'Đỏ', 'passionate-heart', 'spring', 'SP0021', 'Bạn có đang đắm chìm trong một tình yêu tràn đầy sự mãnh liệt với một trái tim nhiệt huyết và cháy bỏng hơn bao giờ hết không? Để thể hiện tình yêu chân thành của bạn dành cho người ấy, bó hoa Passionate Heart với 30 đóa hồng đỏ rực rỡ sẽ là sự lựa chọn tuyệt vời.', 731000.00, 860000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(22, 4, 'Red rose', 'Đỏ', 'red-rose', 'spring', 'SP0022', 'Bó hoa được thiết kế từ những đóa hồng đỏ rực rỡ, loài hoa tượng trưng cho tình yêu nồng nàn, cháy bỏng và sự thủy chung. Những cánh hoa mềm mại, xếp tầng lớp tạo nên vẻ đẹp kiêu sa, lãng mạn.', 874000.00, 950000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(23, 4, 'An Nhiên', 'Xanh biển', 'an-nhien', 'spring', 'SP0023', 'Hoa hồng trắng mang tông màu tươi sáng cùng hình dáng dài đều tượng trưng cho niềm hy vọng vào những điều tươi sáng và hạnh phúc nhất. Kết hợp cùng tú cầu xanh và giấy gói màu biếc nhẹ nhàng, bó hoa không chỉ đẹp mà còn gói gọn hương thơm của cả một mùa xuân vào trong một khoảnh khắc.', 657000.00, 730000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(24, 4, 'Scented Love', 'Hồng', 'scented-love', 'spring', 'SP0024', 'Bó hoa Scented Love mang tone hồng chủ đạo với 12 bông hồng dâu sẽ là quà tặng hoàn hảo cho bạn gái', 500000.00, 550000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(25, 4, 'Thanh Tú', 'Trắng', 'thanh-tu', 'spring', 'SP0025', 'Thạch thảo trắng nhìn mong manh nhưng không hề yếu đuối. Nó biểu trưng cho sự che chở và mong mỏi. Thạch thảo trắng biểu tượng cho người con gái đang yêu: có chút e dè, ngại ngùng nhưng cũng đầy mạnh mẽ. Thạch thảo trắng cũng biểu tượng cho tình yêu trong sáng và nhẹ nhàng.', 350000.00, 410000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(26, 5, 'Lucky', 'Xanh lá', 'lucky', 'spring', 'SP0026', 'May mắn thường luôn mang đến niềm vui và hạnh phúc. Một bó hoa được thiết kế bởi những cành hồng vàng sẽ mang đến cho bạn bè, người thân của bạn những cảm giác tuyệt vời nhất. Tất cả không chỉ là niềm vui, mà còn là sự động viên, lời chúc đầy ý nghĩa.', 360000.00, 440000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(27, 5, 'Miracle', 'Tím', 'miracle', 'spring', 'SP0027', 'Bó hoa Miracle được thiết kế từ hoa thạch thảo tím loài hoa đại diện cho tình yêu thủy chung, sự dễ thương, sức mạnh và phép màu. Bó hoa Miracle là lựa chọn hoàn hảo để gửi tặng các cô gái vào những dịp đặc biệt như sinh nhật, lễ tốt nghiệp hay kỷ niệm ngày yêu.', 370000.00, 430000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(28, 5, 'Bó Cẩm Tú Cầu Trắng', 'Trắng', 'bo-cam-tu-cau-trang', 'spring', 'SP0028', 'Hoa cẩm tú cầu trắng tượng trưng cho sự thuần khiết, trong trắng, tinh khôi và ngây thơ thường được sử dụng trong lễ cưới hoặc gửi tặng bạn gái để thể hiện tình yêu chân thành.', 370000.00, 430000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(29, 5, 'Best Wishes', 'Trắng', 'best-wishes', 'spring', 'SP0029', 'Bó hoa Best Wishes được thiết kế từ hoa cúc tana, một trong những loại hoa cúc được yêu thích nhất hiện nay. Best Wishes phù hợp để làm quà tặng vợ, bạn gái, ba mẹ hoặc đồng nghiệp trong dịp sinh nhật.', 450000.00, 510000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(30, 5, 'Sắc Lửa Thiêng', 'Đỏ', 'sac-lua-thieng', 'spring', 'SP0030', 'Bình hoa Sắc lửa là sự kết hợp tinh tế giữa hoa hồng nhung và hoa baby trắng, rất phù hợp để trao tặng người thân, công ty, gia đình trong dịp đặc biệt', 1500000.00, 1760000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(31, 6, 'Bình Hoa Tuyết Mai', 'Xanh lá', 'binh-hoa-tuyet-mai', 'spring', 'SP0031', 'Bình hoa được cắm từ những cành liễu mảnh mai, mềm mại và uyển chuyển. Từng nhánh liễu vươn dài, rũ xuống một cách tự nhiên, mang đến cảm giác thanh thoát và nhẹ nhàng. Màu xanh non của lá điểm trên nền cành nâu tạo nên sự hài hòa, tinh tế.', 1820000.00, 2080000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(32, 6, 'Tinh Tế', 'Cam', 'tinh-te', 'spring', 'SP0032', 'Bình hoa Tinh tế được thiết kế theo phong cách đơn giản với hoa thiên điểu kết hợp với các loại lá phụ. Nếu bạn đang cần một mẫu hoa giá rẻ để chưng bàn làm việc, hoặc khu vực lễ tân, bình hoa Tinh tế là sự lựa chọn không thể bỏ qua.', 780000.00, 960000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(33, 6, 'Chút Vấn Vương', 'Tím', 'chut-van-vuong', 'spring', 'SP0033', 'Thứ bạn nuối tiếc chính là mình đã không dành nhiều thời gian bên gia đình hay bên những người mình thương yêu nhất. Ngay khi còn có thể, hay yêu thương và sẻ chia, hãy thắp sáng nụ cười những người quan trọng ấy bằng một bình hoa ly vàng thật đẹp.', 1540000.00, 1660000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(34, 6, 'Tinh Thuần', 'Trắng', 'tinh-thuan', 'spring', 'SP0034', 'Những sắc trắng từ những loài hoa khác nhau đan xen tạo nên một bình hoa trang trọng nhưng không hề nhàm chán nhờ sự kết hợp và sắp xếp chuyên nghiệp. Bình hoa là lựa chọn hoàn hảo dành tặng cho người yêu sắc trắng tinh khôi, thanh khiết.', 1440000.00, 1660000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(35, 7, 'Vitamin Sea', 'Vàng', 'vitamin-sea', 'spring', 'SP0035', 'Bó hoa được kết từ những đóa hồng vàng rực rỡ, loài hoa tượng trưng cho niềm vui, hạnh phúc và tình bạn chân thành. Xen kẽ giữa sắc vàng tươi sáng là vài nhành hoa baby xanh dịu nhẹ, tạo điểm nhấn hài hòa và tinh tế.', 810000.00, 900000.00, 1, '2025-10-03 17:23:05', '2025-10-03 17:23:05'),
(36, 7, 'My Muse', 'Trắng', 'my-muse', 'spring', 'SP0036', 'Một bó hoa đầy ngọt ngào và tỏa sáng, được thiết kế từ những cành hồng trắng và hồng trà đẹp nhất. Thợ hoa đã cẩn thận lựa chọn những cành thật tươi, khéo léo thiết kế bởi những lớp giấy gói đẹp mặt và tinh tế. Món quà sẽ rất phù hợpcho nhiều dịp, từ sinh nhật cho đến chúc mừng.', 480000.00, 520000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(37, 7, 'Thanh Xuân', 'Hồng', 'thanh-xuan', 'spring', 'SP0037', 'Bó hoa được tạo nên từ những đóa hồng pastel hồng nhẹ nhàng, mang vẻ đẹp ngọt ngào và tinh khôi. Từng cánh hoa mềm mại bung nở, xếp lớp uyển chuyển như gửi gắm sự dịu dàng và lãng mạn.', 770000.00, 880000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(38, 7, 'Thấu cảm', 'Trắng', 'thau-cam', 'spring', 'SP0038', 'Bó hoa Hồng Xanh Thiên Thanh là sự kết hợp tinh tế giữa vẻ đẹp thuần khiết và màu sắc độc đáo. Mười hai đóa hồng trắng được điểm xuyết một cách nghệ thuật bằng viền xanh dương nhẹ nhàng, tạo cảm giác như những áng mây bồng bềnh giữa bầu trời trong xanh.', 490000.00, 540000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(39, 7, 'Pretty', 'Hồng', 'pretty', 'spring', 'SP0039', 'Một bó hoa \"giải cảm\" giúp cho tinh thần của người nhận trở nên phấn chấn và tràn đầy năng lượng hơn. Thật là một liều thuốc tinh thần thật tốt đẹp phải không nào, còn chần chừ gì mà không tặng nó ngay cho người mà bạn yêu quý nhất!', 770000.00, 860000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(40, 8, 'Sắc tím mộng mơ', 'Tím', 'sac-tim-mong-mo', 'spring', 'SP0040', 'Hoa Thạch thảo tím mang trên mình một nét đẹp nhẹ nhàng lại dễ thương, xinh xắn. Hoa thạch thảo tím gắn liền với những ý nghĩa sâu sắc về tình yêu đôi lứa. Loài hoa này mang ý nghĩa tượng trưng cho một tình yêu bình dị và nhẹ nhàng. ', 590000.00, 640000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(41, 8, 'Lẵng hoa Yellow lover', 'Vàng', 'lang-hoa-yellow-lover', 'spring', 'SP0041', 'Hoa Thạch thảo tím mang trên mình một nét đẹp nhẹ nhàng lại dễ thương, xinh xắn. Hoa thạch thảo tím gắn liền với những ý nghĩa sâu sắc về tình yêu đôi lứa. Loài hoa này mang ý nghĩa tượng trưng cho một tình yêu bình dị và nhẹ nhàng. ', 750000.00, 810000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(42, 8, 'Summer of soul', 'Hồng', 'summer-of-soul', 'spring', 'SP0042', 'Lẵng hoa khai trương Summer of soul mang tông màu sáng đầy vui tươi, ngập tràn ánh nắng và niềm vui hân hoan sẽ giúp bạn gửi đến đối tác, doanh nghiệp những lời chúc chân thành và hoan hỉ nhất. Luôn tỏa sáng và hướng đến tương lai tươi đẹp cũng là ý nghĩa', 870000.00, 1020000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(43, 8, 'Tươi Sáng', 'Trắng', 'tuoi-sang', 'spring', 'SP0043', 'Lẵng hoa khai trương Tươi Sáng mang tông màu sáng đầy vui tươi, ngập tràn ánh nắng và niềm vui hân hoan sẽ giúp bạn gửi đến đối tác, doanh nghiệp những lời chúc chân thành và hoan hỉ nhất. Luôn tỏa sáng và hướng đến tương lai tươi đẹp cũng là ý nghĩa mà lẵng hoa hồng Tươi Sáng này mang đến với hoa hồng, môn xanh và mõm sói', 1130000.00, 1200000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(44, 8, 'Món Quà Chúc Mừng', 'Hồng', 'mon-qua-chuc-mung', 'spring', 'SP0044', 'Lẵng hoa mừng khai trương với những cát tường màu dâu rực rỡ như màu sắc màu đam mê, sẽ mang đến những điều may mắn, những niềm vui, trong khi hoa hồng kem tượng trưng cho tinh thần mạnh mẽ, sự nhiệt huyết và không ngừng chiến đấu. Kết hợp cùng lá cỏ đồng tiền, lẵng hoa hồng món quà chúc mừng thay bạn gửi đi nhửng lời chúc chân thành và đầy hân hoan.', 950000.00, 1090000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(45, 9, 'Hoa Chia Buồn Hồi Ức', 'Trắng', 'hoa-chia-buon-hoi-uc', 'spring', 'SP0045', 'Hoa tươi không chỉ được dùng để chúc mừng, để tặng nhau những dịp vui vẻ. Hoa tươi còn có thể được dùng để san sẻ nỗi buồn, thể hiện sự thành kính, tiếc nuối cho những gì còn dang dở. Kính viếng một chiếc kệ hoa để thể hiện lời cầu nguyện, tiếng lòng hiếu thảo đến với người đã an nghỉ. ', 945000.00, 10500000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(46, 9, 'Hoa Tang Lễ - Chia Xa', 'Trắng', 'hoa-tang-le-chia-xa', 'spring', 'SP0046', 'Dùng hoa tươi chia buồn còn giúp cho ta thể hiện tiếc thương cho những khát khao và niềm hy vọng của một con người còn dang dở. Hay đó cũng chính là lời cầu nguyện, tiếng lòng hiếu thảo của con cháu dâng kính lên các bậc bề trên của mình một cách dễ dàng và tỏ lòng hơn.', 1160000.00, 14200000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(47, 9, 'Lời Giã Từ', 'Trắng', 'loi-gia-tu', 'spring', 'SP0047', 'Nếu bạn muốn tạo một chút khác biệt cho vòng hoa chia buồn mà vẫn mang vẻ trang trọng của sắc trắng, vậy hãy điểm thêm 1 chút hồng phớt từ hoa hồng. Nhẹ nhàng, lịch sự, chân thành sẽ là cảm giác mà vòng hoa này sẽ mang lại, như một lời chia buồn thành kính nhất gửi đến gia đình người đã mất.', 1420000.00, 19400000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(48, 9, 'Hoa Chia Buồn Tàn Thu', 'Trắng', 'hoa-chia-buon-tan-thu', 'spring', 'SP0048', 'Những đoá hoa trắng tươi mang trên mình sự tinh khiết, thanh tao nhưng cũng rất mạnh mẽ và đầy sức sống. Những đoá hoa trắng này thể hiện cho tâm hồn tinh khiết, thanh tao của người đã yên nghỉ, và chúng cũng thể hiện sự tôn trọng cũng như lòng hiếu thảo của gia quyến, người ở lại dành cho người đã khuất', 1670000.00, 1770000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(49, 9, 'Hoa Tang Lễ Thiên Thu', 'Trắng', 'hoa-tang-le-thien-thu', 'spring', 'SP0049', 'Hoa ly là loài hoa phù hợp nhất để thể hiện lòng thành kính đối với người đã khuất, vì loài hoa này thể hiện cho tâm hồn trong sáng và lương thiện. Hoa ly trắng còn đại hiện cho sự thuỷ chung và sự tinh khiết.', 1980000.00, 2190000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(50, 10, 'Giọt Nắng', 'Vàng', 'giot-nang', 'spring', 'SP0050', 'Bó hoa Giọt Nắng được lấy cảm hứng từ những tia nắng lấp lánh, ánh lên năng lượng tràn đầy tích cực. Được thiết kế đặc biệt từ một bông hướng dương nở to đẹp nhất, điểm to bởi nhiều hoa cúc dại xung quanh thật xinh, sản phẩm như một lời chúc may mắn, sức khỏe, và như một lời khen ấm áp dành cho người được nhận. ', 190000.00, 220000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(51, 10, 'Lời Nhắn', 'Hồng', 'loi-nhan', 'spring', 'SP0051', 'Bó hoa lời nhắn được thiết kế với hoa đồng tiền hồng loài hoa đại diện cho tình yêu và may mắn. Chính vì thế, bạn có thể lựa chọn bó hoa lời nhắn để làm hoa tặng tốt nghiệp hoặc tặng sinh nhật những người bạn thân hay đồng nghiệp.', 370000.00, 430000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(52, 10, 'Nắng Hạ', 'Vàng', 'nang-ha', 'spring', 'SP0052', 'Bó hoa Nắng Hạ được thiết kế với hoa hướng dương. Ánh Hạ là lựa chọn hoàn hảo để gửi tặng bạn thân, đồng nghiệp trong dịp sinh nhật hoặc lễ tốt nghiệp.', 380000.00, 500000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(53, 10, 'Little Tana', 'Trắng', 'little-tana', 'spring', 'SP0053', 'Bó hoa Little Tana nhỏ xinh được thiết kế từ hoa cúc tana theo phong cách đơn giản, mộc mạc phù hợp để làm hoa tặng tốt nghiệp hay hoa sinh nhật người thân, bạn bè.', 390000.00, 420000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(54, 10, 'Soulmate', 'Vàng', 'soulmate', 'spring', 'SP0054', 'Hoa hồng vàng đại diện cho tình bạn chân thành, ấm áp. Bó hoa Soulmate được thiết kế với 12 bông hồng vàng là lựa chọn hoàn hảo để tặng bạn thân vào những dịp đặc biệt.', 500000.00, 530000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(55, 11, 'Be Mine', 'Đỏ', 'be-mine', 'spring', 'SP0055', 'Bó hoa Be Mine thực sự là một lời cầu hôn đầy chân thành dành cho một nửa của bạn với những đóa hồng Ecuador đỏ rực rỡ kết hợp với Tulip Nam Phi và những loài hoa khác.', 6715000.00, 7900000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(56, 11, 'Bầu trời xanh', 'Xanh biển', 'bau-troi-xanh', 'spring', 'SP0056', 'Một sự kết hợp vô cùng xuất sắc giữa Hoa hồng Blue Sky - Hoa Thanh Liễu và Tuyết Mai đã tạo nên bó hoa xinh đẹp này.Hoa và lá lại thường mọc với mật độ dày nên tạo cho chúng ta có được cảm giác xum xuê, thể hiện cho sự phồn thịnh, thịnh vượng, may mắn', 3249000.00, 3610000.00, 0, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(57, 11, 'Magnificent', 'Xanh biển', 'magnificent', 'spring', 'SP0057', 'Bó hoa Tulip Magnificent được thiết kế từ 30 cành hoa tulip màu xanh dương kết hợp cùng hoa cẩm tú cầu và lá bạc. Bó hoa là lựa chọn hoàn hảo để gửi tặng vợ, bạn gái vào dịp sinh nhật, lễ tình nhân valentine hay ngày kỉ niệm ngày cưới.', 3467000.00, 3650000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(58, 11, 'Hồng Trắng Kiêu Sa', 'Trắng', 'hong-trang-kieu-xa', 'spring', 'SP0058', 'Hoa hồng trắng tượng trưng cho sự duyên dáng, ngây thơ và cảm thông. Đây không chỉ là hình ảnh đại diện cho một tình yêu thuần thiết, cao thượng mà nó còn được dùng như một lời xin lỗi đến những người yêu thương khi phạm phải lỗi lầm.', 2835000.00, 3150000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(59, 11, 'Sương Mai', 'Hồng', 'suong-mai', 'spring', 'SP0059', 'Nụ cười, ánh mắt người ấy lúc nào cũng lung linh trong tim bạn nhưng vì ngại mà chưa từng một lần nói ra. Đừng ngại ngùng bạn nhé, không cần gì nhiều đâu, chỉ cần 1 bó hoa cúc hồng nhập khẩu sang trọng, và một tấm thiệp cùng lời nhắn dễ thương sẽ giúp bạn nói ra lời yêu thương một cách đầy chân thành đấy.', 5224000.00, 6530000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(60, 12, 'Tết Xuân', 'Đỏ', 'tet-xuan', 'spring', 'SP0060', 'Màu đỏ rực rỡ của những cành đào đông sẽ mang không khí Tết đến với Nàng. Bó hoa sẽ là lựa chọn hoàn hảo để gửi tặng nàng dịp cận Tết.', 1782000.00, 1980000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(61, 12, 'Many Hug', 'Xanh lá', 'many-hug', 'winter', 'SP0061', 'Giáng Sinh đang cận kề, còn đợi gì nữa mà không đặt ngay một bó hoa với cành thông thật kết hợp với hoa hồng đỏ để tặng cho người thân yêu của mình', 760000.00, 800000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(62, 12, 'Je taime', 'Đỏ', 'je-taime', 'winter', 'SP0062', 'Hạnh phúc là khi bạn thấy ấm áp trong cái lạnh của đêm Giáng Sinh, hơi ấm từ bạn bè và người thân mà họ dành tặng cho bạn trong một tấm thiệp, một lời chúc dí dỏm, một buổi đi chơi giản dị nhưng đầy ắp tình cảm.', 867000.00, 1020000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(63, 12, 'Nụ Hoa Xuân', 'Đỏ', 'nu-hoa-xuan', 'spring', 'SP0063', 'Bó hoa ước vọng là sự kết hợp giữa những đóa hoa đồng tiền đỏ thắm cùng lá trang trí phụ khác, tượng trưng cho niềm tin và khát khao thành công, hạnh phúc', 350000.00, 410000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(64, 12, 'Lạc Thần', 'Hồng', 'lac-than', 'summer', 'SP0064', 'Hoa hồng lạc thần kết hợp với hoa cúc tana tạo nên một bó hoa xinh xắn, dễ thương khiến bất kì cô gái nào cũng phải xiêu lòng. Bó hoa thích hợp để gửi tặng vợ, bạn gái hay cô bạn thân thiết vào những dịp đặc biệt như sinh nhật, kỷ niệm ngày yêu, ngày cưới...', 680000.00, 741000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(65, 13, 'Gracias', 'Vàng', 'gracias', 'spring', 'SP0065', 'Hướng dương luôn mang một luồng năng lượng tươi trẻ, tích cực và tràn đầy ấm áp. Bó hoa Gracias đem đến ý nghĩa rằng dù cuộc sống có ra sao, thì những điều tốt đẹp rồi cũng sẽ đến.', 490000.00, 540000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(66, 13, 'Đỏ Rực Rỡ', 'Đỏ', 'do-ruc-ro', 'spring', 'SP0066', 'Dù cho khó khăn, anh vẫn sẽ luôn bên cạnh chở che,động viên và yêu thương em suốt cuộc đời. Bó hoa với tông màu đỏ rực rỡ và đầy đam mê sẽ mang những lời nhắn ngọt ngào nhất đến một nữa kia của bạn. Vì hoa hồng đỏ luôn là biểu tượng kinh điển cho tình yêu nồng cháy và sâu đậm nhất.', 517000.00, 550000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(67, 13, 'All The Best', 'Trắng', 'all-the-best', 'spring', 'SP0067', 'Những bông cẩm chướng trắng tinh khôi tượng trưng cho vẻ đẹp thuần khiết và lời chúc may mắn. Bó hoa cẩm chướng trắng là sự lựa chọn hoàn hảo để gửi tặng mẹ vào những dịp đặc biệt.', 494000.00, 520000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(68, 13, 'Summer Vibe', 'Cam', 'summer-vibe', 'spring', 'SP0068', 'Một bó hoa nhỏ xinh được thiết kế từ 12 bông hồng cam rực rỡ sẽ là lựa chọn hoàn hảo để gửi tặng bạn bè, người thân vào những dịp đặc biệt.', 490000.00, 530000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(69, 13, 'Thanh Tao', 'Tím', 'thanh-tao', 'spring', 'SP0069', 'Bó hoa Thanh tao là sự phối hợp giữa nhiều loại hoa quen thuộc nhưng khi đứng cùng nhau, lại tạo nên 1 tác phẩm độc đáo, mang màu sắc tươi mới và tinh tế, sang trọng. Bó hoa thích hợp tặng trong dịp sinh nhật hay kỷ niệm như 1 lời chúc chân thành nhất đến người được tặng.', 646000.00, 680000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(70, 15, 'Niềm mong', 'Hồng', 'miem-mong', 'spring', 'SP0070', 'Là sự kết hợp giữa hoa hồng kem và baby phun hồng quen thuộc. Bó hoa vừa mang vẻ bình dị, vừa sang trọng. Rất thích hợp để tặng cho bạn bè, người thân hay cả người yêu.', 418000.00, 440000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(71, 15, 'Mát Xanh', 'Xanh biển', 'mat-xanh', 'spring', 'SP0071', 'Hoa baby tượng trưng cho tình yêu tinh khiết, sự trong trắng, mỏng manh, thanh tao như chính vẻ ngoài của hoa mang lại. Chính vì vậy hoa baby thường được nam giới sử dụng để tặng cho người mình yêu, đây là ý nghĩa đặc biệt nhất mà loài hoa baby đẹp mang lại.', 475000.00, 500000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(72, 15, 'Bó Hoa Tulip - Dreamcatcher', 'Trắng', 'bo-hoa-tulip-dreamcatcher', 'spring', 'SP0072', 'Hoa Tulip trắng tượng trưng cho tình yêu trong sáng, thuần khiết. Bó hoa tulip trắng Dreamcatcher là lựa chọn hoàn hảo làm hoa tặng sinh nhật cô bạn gái mới quen.', 1782000.00, 1980000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(73, 15, 'Bó Hoa Tulip - Violet', 'Tím', 'bo-hoa-tulip-violet', 'spring', 'SP0073', '20 bông hoa tulip màu tím được gói đơn giản nhưng vẫn không kém sang trọng, tinh tế. Bó hoa là lựa chọn hòa hảo để gửi tặng vợ, bạn gái vào những dịp đặc biệt.', 1881000.00, 1980000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30'),
(74, 15, 'Forever Young', 'Cam', 'forever-young', 'spring', 'SP0074', 'Bó hoa Forever Young là sự kết hợp hoàn hảo giữa hoa hồng cappuccino và hoa cẩm chướng màu hồng loài hoa tượng trưng cho tuổi trẻ và sự vĩnh cửu. Forever Young là lựa chọn phù hợp để làm quà tặng cho người thân, bạn bè vào mọi dịp đặc biệt.', 672000.00, 840000.00, 1, '2025-10-09 00:16:30', '2025-10-09 00:16:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `url`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 1, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759130918/i5iwg7zmdssbca5kh1tx.jpg', 1, 1, '2025-09-29 14:44:06'),
(2, 1, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759130918/kdalqqr9ae56roddci3l.jpg', 0, 2, '2025-09-29 14:44:06'),
(3, 1, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759130918/sybpgjt3sz7ndh1rvdp3.jpg', 0, 3, '2025-09-29 14:44:06'),
(4, 2, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759132028/nnhh5admm488tqcbq1rj.jpg', 1, 1, '2025-09-29 14:50:35'),
(5, 2, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759132028/bu5xgqfh0lmxlqylewtv.jpg', 0, 2, '2025-09-29 14:50:35'),
(6, 2, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759132029/rpzcwaayf0k6e2mxena8.jpg', 0, 3, '2025-09-29 14:50:35'),
(124, 3, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759482356/yfeds6zmkpjg0ae6s6z5.jpg', 1, 1, '2025-10-03 17:30:02'),
(125, 3, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759482355/a8ycbsmd6d9w0ctyholo.jpg', 1, 2, '2025-10-03 17:30:02'),
(126, 3, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759482355/kzzf2stxfpn3q2m7yleg.jpg', 1, 3, '2025-10-03 17:30:02'),
(127, 4, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759315404/znlolgdiuqrrtjdfrnqn.jpg', 1, 1, '2025-10-03 17:30:02'),
(128, 4, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759315512/mntb8ryp75t26zhdutpr.png', 1, 2, '2025-10-03 17:30:02'),
(129, 4, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759315514/wp65m0hbery1swclnt0o.jpg', 1, 3, '2025-10-03 17:30:02'),
(130, 5, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316061/at3sjxyuilcvh5243hc6.webp', 1, 1, '2025-10-03 17:30:02'),
(131, 5, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316063/xkqnn3lscqvhhvuqwmhv.webp', 1, 2, '2025-10-03 17:30:02'),
(132, 5, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316068/yggsflkwoycwcxhpud1m.webp', 1, 3, '2025-10-03 17:30:02'),
(133, 6, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759486274/rfkd9z4pclxgkyobsthg.jpg', 1, 1, '2025-10-03 17:30:02'),
(134, 6, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759486274/lws0k8dhinhkz9y07qqz.jpg', 1, 2, '2025-10-03 17:30:02'),
(135, 6, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759486276/fj0r3wthsxdy1kfuolqu.jpg', 1, 3, '2025-10-03 17:30:02'),
(136, 7, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316286/uhkfydbuuiiqxjtfgtmg.jpg', 1, 1, '2025-10-03 17:30:02'),
(137, 7, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316271/dl4axwslbxjrd7k7avwk.jpg', 1, 2, '2025-10-03 17:30:02'),
(138, 7, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316263/qf0vxkqhl5etcfvrbm43.webp', 1, 3, '2025-10-03 17:30:02'),
(139, 8, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316068/yggsflkwoycwcxhpud1m.webp', 1, 1, '2025-10-03 17:30:02'),
(140, 8, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316769/lofxvrbtm0qjfpvonk5z.jpg', 1, 2, '2025-10-03 17:30:02'),
(141, 8, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759316789/vobdwosypqjzydubjznq.jpg', 1, 3, '2025-10-03 17:30:02'),
(142, 9, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317146/zbprsrutk58xavsm4upn.webp', 1, 1, '2025-10-03 17:30:02'),
(143, 9, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317198/acfxorfv60x726y4or2j.jpg', 1, 2, '2025-10-03 17:30:02'),
(144, 9, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317344/mzy4wqqs15jftdt59sne.jpg', 1, 3, '2025-10-03 17:30:02'),
(145, 10, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317893/szcafnhaaasiom2m9to1.webp', 1, 1, '2025-10-03 17:30:02'),
(146, 10, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317896/xs1ahzvaf1itijdqqr0o.jpg', 1, 2, '2025-10-03 17:30:02'),
(147, 10, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759317900/ibfgicpxxun5bpqj8ril.jpg', 1, 3, '2025-10-03 17:30:02'),
(148, 11, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759318321/ylop7zaaeyjwxiowlrod.webp', 1, 1, '2025-10-03 17:30:02'),
(149, 11, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759318311/mib2m52bmkfrjty9nvjl.jpg', 1, 2, '2025-10-03 17:30:02'),
(150, 11, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759318307/wfpv0vrupns4qa65cdt7.jpg', 1, 3, '2025-10-03 17:30:02'),
(151, 12, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319106/nebigsdymsyk8jwnbxu1.webp', 1, 1, '2025-10-03 17:30:02'),
(152, 12, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319111/b3hps0gvcjlkoob9x3nm.jpg', 1, 2, '2025-10-03 17:30:02'),
(153, 12, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319124/kctyb6x1uutusc2sfj7m.jpg', 1, 3, '2025-10-03 17:30:02'),
(154, 13, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319571/zzitqkncw1std5m7izai.webp', 1, 1, '2025-10-03 17:30:02'),
(155, 13, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319591/ispmo3td1a3lcatoooqa.jpg', 1, 2, '2025-10-03 17:30:02'),
(156, 13, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319592/kbqaeh6btxxteotdrkbm.jpg', 1, 3, '2025-10-03 17:30:02'),
(157, 14, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319814/vxlgzoyp1dnfsssm66f1.webp', 1, 1, '2025-10-03 17:30:02'),
(158, 14, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319811/ecing7bfhfl5srsbnris.jpg', 1, 2, '2025-10-03 17:30:02'),
(159, 14, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319816/teqsv4o1k9zlcgm3cgjy.jpg', 1, 3, '2025-10-03 17:30:02'),
(160, 15, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319995/bj7uxpef9lc8orzdh8pm.webp', 1, 1, '2025-10-03 17:30:02'),
(161, 15, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319997/ynhayp13ltsv4mp9nkjc.jpg', 1, 2, '2025-10-03 17:30:02'),
(162, 15, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759319991/wnpxjgqysnjkncfkhw5c.jpg', 1, 3, '2025-10-03 17:30:02'),
(163, 16, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320563/dxbasl1vpzofsdlmg2dc.webp', 1, 1, '2025-10-03 17:30:02'),
(164, 16, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320562/pqz6lojwiucv2neew8hv.jpg', 1, 2, '2025-10-03 17:30:02'),
(165, 16, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320567/k8xkg5mh2whlgjvfh8wc.jpg', 1, 3, '2025-10-03 17:30:02'),
(166, 17, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320916/mahdrckiiiavlkdo46e9.webp', 1, 1, '2025-10-03 17:30:02'),
(167, 17, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320918/kugndhr7pjm9aqmfjdhi.jpg', 1, 2, '2025-10-03 17:30:02'),
(168, 17, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759320921/z7fi77mpuxwloa4jyngt.jpg', 1, 3, '2025-10-03 17:30:02'),
(169, 18, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321218/tad1ocau3wfghc3tnnei.jpg', 1, 1, '2025-10-03 17:30:02'),
(170, 18, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321215/gxgsxlu0qicncn5vq9mh.webp', 1, 2, '2025-10-03 17:30:02'),
(171, 18, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321212/p3pa1nk0saufyqeo2ycj.jpg', 1, 3, '2025-10-03 17:30:02'),
(172, 19, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321446/e0se3ptqszspifhrwxfg.jpg', 1, 1, '2025-10-03 17:30:02'),
(173, 19, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321443/aqc7ewxbc8y4vvnqn3fq.webp', 1, 2, '2025-10-03 17:30:02'),
(174, 19, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321449/s0ajykmntoytkzsp8ljf.jpg', 1, 3, '2025-10-03 17:30:02'),
(175, 20, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321905/mepfwk5y5ogfz78de2vv.webp', 1, 1, '2025-10-03 17:30:02'),
(176, 20, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321901/njdnt0axlb73bartmqe6.jpg', 1, 2, '2025-10-03 17:30:02'),
(177, 20, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759321908/gqe4k8zzxpc9bs10vosr.jpg', 1, 3, '2025-10-03 17:30:02'),
(178, 21, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322186/kspenbygh9uvuie5sumn.webp', 1, 1, '2025-10-03 17:30:02'),
(179, 21, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322182/znm74g1r3kogm18zikag.jpg', 1, 2, '2025-10-03 17:30:02'),
(180, 21, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322189/q6rnuzhljm2nyv7an6hv.jpg', 1, 3, '2025-10-03 17:30:02'),
(181, 22, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322742/ayofhuumtnh3j7hhqqbo.webp', 1, 1, '2025-10-03 17:30:02'),
(182, 22, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322742/jbyd70nzdryhgyko6pri.jpg', 1, 2, '2025-10-03 17:30:02'),
(183, 22, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322741/mx5wghlexzwdhcuinsln.jpg', 1, 3, '2025-10-03 17:30:02'),
(184, 23, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322948/oj9f55nwckw63chd5apv.jpg', 1, 1, '2025-10-03 17:30:02'),
(185, 23, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322949/lu4ehph93wph3wwhlbo9.webp', 1, 2, '2025-10-03 17:30:02'),
(186, 23, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759322951/hvvollouwo2ptk6oynsx.jpg', 1, 3, '2025-10-03 17:30:02'),
(187, 24, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385793/iay4feyq4utfuuxjrgcr.jpg', 1, 1, '2025-10-03 17:30:02'),
(188, 24, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385793/lewnz4zdx6zt7rdnmwwi.jpg', 1, 2, '2025-10-03 17:30:02'),
(189, 24, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385794/cv9rgk17tt0u8uogekgb.webp', 1, 3, '2025-10-03 17:30:02'),
(190, 25, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385936/byj55fratcfnh9qnm2bp.jpg', 1, 1, '2025-10-03 17:30:02'),
(191, 25, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385936/mwxsgqfw4wir0tg5lpeg.jpg', 1, 2, '2025-10-03 17:30:02'),
(192, 25, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759385937/eij5jhzubhsenir5dv1f.webp', 1, 3, '2025-10-03 17:30:02'),
(193, 26, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386112/lzwiey6utgbnza3tgv9a.jpg', 1, 1, '2025-10-03 17:30:02'),
(194, 26, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386112/i7nvnn14pt4rkxvxeuu8.jpg', 1, 2, '2025-10-03 17:30:02'),
(195, 26, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386112/osqju9yxpe9fbdlvgc8f.jpg', 1, 3, '2025-10-03 17:30:02'),
(196, 27, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386542/mnysliayazdbb3my7fhp.webp', 1, 1, '2025-10-03 17:30:02'),
(197, 27, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386541/shkruvu3t1t6dyflibcm.jpg', 1, 2, '2025-10-03 17:30:02'),
(198, 27, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759386542/s1aunkdnghxc6kfxu5g9.jpg', 1, 3, '2025-10-03 17:30:02'),
(199, 28, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387422/uv07stv2t01igcycf6nz.jpg', 1, 1, '2025-10-03 17:30:02'),
(200, 28, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387422/cvyvwhq0bhnz3aidttqu.jpg', 1, 2, '2025-10-03 17:30:02'),
(201, 28, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387422/vhf5ucqpzp85ocvcyii8.jpg', 1, 3, '2025-10-03 17:30:02'),
(202, 29, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387991/sx1oabdfnkdo31qnka2p.jpg', 1, 1, '2025-10-03 17:30:02'),
(203, 29, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387991/jq3isaioywkkdo2wb2n8.jpg', 1, 2, '2025-10-03 17:30:02'),
(204, 29, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759387991/tu8kokgko4ezpkwse5qq.jpg', 1, 3, '2025-10-03 17:30:02'),
(205, 30, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759388332/m2a2rn3txjubpbiwgrtx.webp', 1, 1, '2025-10-03 17:30:02'),
(206, 30, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759388331/lnuvugvlxytajmo4wf53.jpg', 1, 2, '2025-10-03 17:30:02'),
(207, 30, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759388331/uumtgooejxoflxnbqce3.jpg', 1, 3, '2025-10-03 17:30:02'),
(208, 31, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403516/cp8hfvuhgqrmacafmmwn.webp', 1, 1, '2025-10-03 17:30:02'),
(209, 31, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403515/z3rkbseg8pzxlcecf83p.webp', 1, 2, '2025-10-03 17:30:02'),
(210, 31, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403515/dzyhtegeewmnok9gabea.webp', 1, 3, '2025-10-03 17:30:02'),
(211, 32, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403752/t5ik6pcvah2o6nqqhpz2.jpg', 1, 1, '2025-10-03 17:30:02'),
(212, 32, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403752/azc8ezbamkqdwaocbo0n.jpg', 1, 2, '2025-10-03 17:30:02'),
(213, 32, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759403753/oo6lckhnsp3d8gy9usol.jpg', 1, 3, '2025-10-03 17:30:02'),
(214, 33, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404031/atjysuyycqyfqyhhqru7.webp', 1, 1, '2025-10-03 17:30:02'),
(215, 33, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404030/cjcxjoeg6kmn2ho03ncu.jpg', 1, 2, '2025-10-03 17:30:02'),
(216, 33, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404030/pp0o7mfniqlxmhymgjwx.jpg', 1, 3, '2025-10-03 17:30:02'),
(217, 34, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404279/t6dgvf0idxhjd1ts4ywg.webp', 1, 1, '2025-10-03 17:30:02'),
(218, 34, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404278/zw3lk5iy0nwilf7usd9i.jpg', 1, 2, '2025-10-03 17:30:02'),
(219, 34, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404278/zsrgtuhazalvkur78jzl.jpg', 1, 3, '2025-10-03 17:30:02'),
(220, 35, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404502/su52nradizsxfg2jt9tz.jpg', 1, 1, '2025-10-03 17:30:02'),
(221, 35, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404502/l9az7vcf6jkrqrzvjmkg.jpg', 1, 2, '2025-10-03 17:30:02'),
(222, 35, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759404502/c08xckwwydgeiawla5pj.jpg', 1, 3, '2025-10-03 17:30:02'),
(320, 36, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407414/uwwxr71h35uwfjdqlu5d.webp', 1, 1, '2025-10-09 00:19:58'),
(321, 36, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407413/x86mwdj0vrnmprt9ppip.jpg', 1, 2, '2025-10-09 00:19:58'),
(322, 36, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407414/fopob06fmpmpc0whdvez.jpg', 1, 3, '2025-10-09 00:19:58'),
(323, 37, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407695/nystys4wnvgnehslblvo.jpg', 1, 1, '2025-10-09 00:19:58'),
(324, 37, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407703/isaypufzshdaxlumfdf5.jpg', 1, 2, '2025-10-09 00:19:58'),
(325, 37, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407710/pxqkrywuviprdlfiyjzr.jpg', 1, 3, '2025-10-09 00:19:58'),
(326, 38, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407958/xjlg1wmpp34k2pwutmiy.webp', 1, 1, '2025-10-09 00:19:58'),
(327, 38, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407958/ch0j3nd7se9he9stxyz0.jpg', 1, 2, '2025-10-09 00:19:58'),
(328, 38, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759407957/hr13ztpl5fjqgkeng7ua.jpg', 1, 3, '2025-10-09 00:19:58'),
(329, 39, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759408233/gdidcpylvif7yxt1hrc8.webp', 1, 1, '2025-10-09 00:19:58'),
(330, 39, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759408233/t3d8keeybzc9uxh0sgsb.jpg', 1, 2, '2025-10-09 00:19:58'),
(331, 39, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759408232/nchgmrujc5carzlaqcm2.jpg', 1, 3, '2025-10-09 00:19:58'),
(332, 40, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713139/ch3gchowklayfpetcctj.jpg', 1, 1, '2025-10-09 00:19:58'),
(333, 40, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713139/n2p5ugbkhhkj4dltxfda.jpg', 1, 2, '2025-10-09 00:19:58'),
(334, 40, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713140/wbjpfqnck1tscj6m3ty1.webp', 1, 3, '2025-10-09 00:19:58'),
(335, 41, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713359/aey5hthkqrdukl7emqe6.webp', 1, 1, '2025-10-09 00:19:58'),
(336, 41, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713358/sg46aukc9jphqrtcubfw.jpg', 1, 2, '2025-10-09 00:19:58'),
(337, 41, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713358/hwwewqaffam59u5vovci.jpg', 1, 3, '2025-10-09 00:19:58'),
(338, 42, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713592/kgolzmbavhyp2iavbseh.jpg', 1, 1, '2025-10-09 00:19:58'),
(339, 42, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713592/nupvdzgummoitvn66won.webp', 1, 2, '2025-10-09 00:19:58'),
(340, 42, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713591/hiema75zeogyoja6ldqg.jpg', 1, 3, '2025-10-09 00:19:58'),
(341, 43, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713847/bvhmevggcujoqemf3uzn.webp', 1, 1, '2025-10-09 00:19:58'),
(342, 43, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713846/zlefdmtsipkq6hcszx4d.jpg', 1, 2, '2025-10-09 00:19:58'),
(343, 43, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759713847/ahjskite4yw6om6kdzyj.jpg', 1, 3, '2025-10-09 00:19:58'),
(344, 44, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714192/hd8flcb73bxfushivwbw.jpg', 1, 1, '2025-10-09 00:19:58'),
(345, 44, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714192/uunaoyaxczmhsdqxryzn.jpg', 1, 2, '2025-10-09 00:19:58'),
(346, 44, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714193/zvzwc4x50q1yi2iabgla.webp', 1, 3, '2025-10-09 00:19:58'),
(347, 45, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714462/peywdokpwepobwgt7bed.webp', 1, 1, '2025-10-09 00:19:58'),
(348, 45, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714461/sh4vh3efvka2xb7a17ch.jpg', 1, 2, '2025-10-09 00:19:58'),
(349, 45, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714461/rlfczi2buuqjoi5i5rh6.jpg', 1, 3, '2025-10-09 00:19:58'),
(350, 46, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714763/xn6q0mftgpsdagxhhheb.webp', 1, 1, '2025-10-09 00:19:58'),
(351, 46, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714762/dhtlaedev4x89dlu5tbw.jpg', 1, 2, '2025-10-09 00:19:58'),
(352, 46, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759714762/slj5usr396787rkgdm5f.jpg', 1, 3, '2025-10-09 00:19:58'),
(353, 47, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715098/ygcketzw7lrjlos9mhhj.jpg', 1, 1, '2025-10-09 00:19:58'),
(354, 47, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715098/lnlewuqorshywmcc9lx6.jpg', 1, 2, '2025-10-09 00:19:58'),
(355, 47, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715098/ebspdifpke7pmlhybj2v.jpg', 1, 3, '2025-10-09 00:19:58'),
(356, 48, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715436/enspxu0svtuzzvicklfm.webp', 1, 1, '2025-10-09 00:19:58'),
(357, 48, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715434/penb8brbnjtt2iicetug.jpg', 1, 2, '2025-10-09 00:19:58'),
(358, 48, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715434/rhz0n28gfgbccgrpo97m.jpg', 1, 3, '2025-10-09 00:19:58'),
(359, 49, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715728/vjezm3ufzkczwkyzbhkj.webp', 1, 1, '2025-10-09 00:19:58'),
(360, 49, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715724/hcx31mu3c7xzouzvtzil.jpg', 1, 2, '2025-10-09 00:19:58'),
(361, 49, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759715723/v88zzxzxdvkmellzztxm.jpg', 1, 3, '2025-10-09 00:19:58'),
(362, 50, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759716048/namnxu27si00ecvlbqhj.webp', 1, 1, '2025-10-09 00:19:58'),
(363, 50, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759716048/vpdvvuqg4tvgakytcbul.jpg', 1, 2, '2025-10-09 00:19:58'),
(364, 50, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759716048/ivuzhbsdbukgmdjzjj4k.jpg', 1, 3, '2025-10-09 00:19:58'),
(365, 51, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759719556/ciuovovtlmqkmt9q7awz.jpg', 1, 1, '2025-10-09 00:19:58'),
(366, 51, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759719557/u1lo4u5jrihyk757lwme.jpg', 1, 2, '2025-10-09 00:19:58'),
(367, 51, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759719557/yu7apmrwndjh4clxyam2.jpg', 1, 3, '2025-10-09 00:19:58'),
(368, 52, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759722001/rrwt7lg28x1untuhgite.jpg', 1, 1, '2025-10-09 00:19:58'),
(369, 52, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759722001/kj55nznr7jlf8qwxc8qg.jpg', 1, 2, '2025-10-09 00:19:58'),
(370, 52, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759722001/rbtoanuppystbdyvrelu.jpg', 1, 3, '2025-10-09 00:19:58'),
(371, 53, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759756147/tuwvyc8v2cr8peryqorp.webp', 1, 1, '2025-10-09 00:19:58'),
(372, 53, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759756146/ivoptgghwpglfbsxv5zy.jpg', 1, 2, '2025-10-09 00:19:58'),
(373, 53, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759756147/qwruxhruuor7x8bzytjj.jpg', 1, 3, '2025-10-09 00:19:58'),
(374, 54, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922088/ahbqez74tz7zhp8bxvlc.webp', 1, 1, '2025-10-09 00:19:58'),
(375, 54, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922088/lcimafwwxojuxbazjixj.jpg', 1, 2, '2025-10-09 00:19:58'),
(376, 54, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922088/pbruvwerawz4bmzmhmyv.jpg', 1, 3, '2025-10-09 00:19:58'),
(377, 55, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922947/mvkw2dmvvld2lmfpfsui.jpg', 1, 1, '2025-10-09 00:19:58'),
(378, 55, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922947/lwpfgsr8igopzdobtc5o.jpg', 1, 2, '2025-10-09 00:19:58'),
(379, 55, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759922947/zet0hkybifu1dwqcvso8.jpg', 1, 3, '2025-10-09 00:19:58'),
(380, 56, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923264/atbp50hshkhlcbmtydef.jpg', 1, 1, '2025-10-09 00:19:58'),
(381, 56, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923264/uzls5iunqmdcuidxz0ko.jpg', 1, 2, '2025-10-09 00:19:58'),
(382, 56, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923264/ip2ubigwgtnwu9vjl8wn.jpg', 1, 3, '2025-10-09 00:19:58'),
(383, 57, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923909/o5vmipoo9urvgkzsg7hv.jpg', 1, 1, '2025-10-09 00:19:58'),
(384, 57, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923909/xwgzk22lxytncyi4negk.jpg', 1, 2, '2025-10-09 00:19:58'),
(385, 57, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759923908/cy7q8ahaijvijlxp7k11.jpg', 1, 3, '2025-10-09 00:19:58'),
(386, 58, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759924335/jjyn4cqz5dnondr2nwyp.webp', 1, 1, '2025-10-09 00:19:58'),
(387, 58, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759924334/jxenxpots6m5zmgocxat.jpg', 1, 2, '2025-10-09 00:19:58'),
(388, 58, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759924336/trtkfhln0chutsihjojc.jpg', 1, 3, '2025-10-09 00:19:58'),
(389, 59, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925420/ugiq5bemfo8cvlc51gop.jpg', 1, 1, '2025-10-09 00:19:58'),
(390, 59, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925420/v6xzhseonogy56xlbste.jpg', 1, 2, '2025-10-09 00:19:58'),
(391, 59, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925421/wulqfqokfvw1swxpob1e.jpg', 1, 3, '2025-10-09 00:19:58'),
(392, 60, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925554/sah2szgn6favaobmhj4w.webp', 1, 1, '2025-10-09 00:19:58'),
(393, 60, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925553/iners5hfyb2qtqwhkwv9.jpg', 1, 2, '2025-10-09 00:19:58'),
(394, 60, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925554/eloou5wylrrirysfugx3.jpg', 1, 3, '2025-10-09 00:19:58'),
(395, 61, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925810/av0ntkal5bfc3idmhwmm.webp', 1, 1, '2025-10-09 00:19:58'),
(396, 61, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925809/q8wjug8fo3bak1x5agg0.jpg', 1, 2, '2025-10-09 00:19:58'),
(397, 61, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759925810/btrdctsbfnfoy2wxvk8k.jpg', 1, 3, '2025-10-09 00:19:58'),
(398, 62, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926099/w6lzbnbo2chsbybox8xk.jpg', 1, 1, '2025-10-09 00:19:58'),
(399, 62, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926098/va26kd8hx5kwhwvflnsn.jpg', 1, 2, '2025-10-09 00:19:58'),
(400, 62, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926098/ijykpfoobuanvynxkcla.jpg', 1, 3, '2025-10-09 00:19:58'),
(401, 63, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926248/hqmwvslwm8txwcjs5uuv.jpg', 1, 1, '2025-10-09 00:19:58'),
(402, 63, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926247/vvo7vrsjosprkj7oagey.jpg', 1, 2, '2025-10-09 00:19:58'),
(403, 63, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926248/xbovm4ss0bujjf8sx3g3.webp', 1, 3, '2025-10-09 00:19:58'),
(404, 64, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926371/n1z9aejzxkb02tdvteta.jpg', 1, 1, '2025-10-09 00:19:58'),
(405, 64, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926371/in17izeipsyah7uu2jbd.webp', 1, 2, '2025-10-09 00:19:58'),
(406, 64, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926370/vwjqkanxmtbnz6czu0hm.jpg', 1, 3, '2025-10-09 00:19:58'),
(407, 65, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926519/fyueb1ckuq9wl8bwo09n.jpg', 1, 1, '2025-10-09 00:19:58'),
(408, 65, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926519/awle9zzcwtar4du3vsgv.webp', 1, 2, '2025-10-09 00:19:58'),
(409, 65, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926518/jfhvi4ta1fev7v8upvpx.jpg', 1, 3, '2025-10-09 00:19:58'),
(410, 66, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926747/cqqgdci17gcqihtqytyh.jpg', 1, 1, '2025-10-09 00:19:58'),
(411, 66, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926748/zg9ieh0hsm6arhc3waux.webp', 1, 2, '2025-10-09 00:19:58'),
(412, 66, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759926749/hjzllbiq8zhnguu7yfzz.jpg', 1, 3, '2025-10-09 00:19:58'),
(413, 67, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927025/evq7mznguaqei0lveboz.jpg', 1, 1, '2025-10-09 00:19:58'),
(414, 67, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927025/uiskh8doj1hlqlvb7gjo.webp', 1, 2, '2025-10-09 00:19:58'),
(415, 67, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927024/yyvumc4klltmockvzxor.jpg', 1, 3, '2025-10-09 00:19:58'),
(416, 68, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927217/h9318im1txesj5vbwfoo.jpg', 1, 1, '2025-10-09 00:19:58'),
(417, 68, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927218/drcr7eyyqrkcvgcjgph0.jpg', 1, 2, '2025-10-09 00:19:58'),
(418, 68, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927220/euownpkvvxjzulqvcvzw.jpg', 1, 3, '2025-10-09 00:19:58'),
(419, 69, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927386/h6mddrbwzqyipydjx1cd.jpg', 1, 1, '2025-10-09 00:19:58'),
(420, 69, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927385/ryows6msu09lx0e8xgag.jpg', 1, 2, '2025-10-09 00:19:58'),
(421, 69, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927386/rwmvfgzm3mgjkwr0xba8.webp', 1, 3, '2025-10-09 00:19:58'),
(422, 70, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927499/tiqbxtbvl9oszlbcxgbe.jpg', 1, 1, '2025-10-09 00:19:58'),
(423, 70, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927500/afc9nzyb9xuhuiwbinaf.jpg', 1, 2, '2025-10-09 00:19:58'),
(424, 70, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927500/gtpbplatlgmbeyky1pxv.webp', 1, 3, '2025-10-09 00:19:58'),
(425, 71, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927638/nv5mud9cqtgdxbehyadm.jpg', 1, 1, '2025-10-09 00:19:58'),
(426, 71, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927638/guw63msigrifeljw75vd.webp', 1, 2, '2025-10-09 00:19:58'),
(427, 71, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927637/n68gjaeazuerao5fltgn.jpg', 1, 3, '2025-10-09 00:19:58'),
(428, 72, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927807/jhgliuccjnyxcczajzni.webp', 1, 1, '2025-10-09 00:19:58'),
(429, 72, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927807/ob4c60l0uukbpmnrjngx.jpg', 1, 2, '2025-10-09 00:19:58'),
(430, 72, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927808/t30qh8ekwoxtrbqog5ju.jpg', 1, 3, '2025-10-09 00:19:58'),
(431, 73, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927901/rmry81jxvwrnfm2feean.jpg', 1, 1, '2025-10-09 00:19:58'),
(432, 73, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927901/mxnu2tap545wrohilo74.webp', 1, 2, '2025-10-09 00:19:58'),
(433, 73, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759927903/hn2d10ypwndsflvy3j1s.jpg', 1, 3, '2025-10-09 00:19:58'),
(434, 74, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759928009/nqrvznlgknylf9e0abwx.webp', 1, 1, '2025-10-09 00:19:58'),
(435, 74, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759928011/bbks4w0gkhdepgk9cytf.jpg', 1, 2, '2025-10-09 00:19:58'),
(436, 74, 'https://res.cloudinary.com/dgdes7cnj/image/upload/v1759928009/mkzzhcd5s2aqg0b1qnl7.jpg', 1, 3, '2025-10-09 00:19:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(64) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL CHECK (`discount_percent` >= 0 and `discount_percent` <= 100),
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `name`, `discount_percent`, `starts_at`, `ends_at`, `is_active`, `created_by`, `created_at`) VALUES
(1, NULL, 'Thu vàng', 15.00, '2025-09-14 20:57:51', '2025-10-14 20:57:51', 1, 1, '2025-09-14 20:57:51'),
(2, 'muaxuan', 'Khuyến mãi mùa xuân', 10.00, '2025-03-01 00:00:00', '2025-03-31 00:00:00', 0, 1, '2025-09-14 21:28:36'),
(3, 'muaha', 'Khuyến mãi mùa hè', 10.00, '2025-06-01 00:00:00', '2025-06-30 00:00:00', 0, 1, '2025-09-14 21:28:36'),
(4, 'muathu', 'Khuyến mãi mùa thu', 10.00, '2025-09-01 00:00:00', '2025-09-30 00:00:00', 1, 1, '2025-09-14 21:28:36'),
(5, 'muadong', 'Khuyến mãi mùa đông', 10.00, '2025-12-01 00:00:00', '2025-12-31 00:00:00', 0, 1, '2025-09-14 21:28:36'),
(6, 'ngay8thang3', 'Mừng ngày 8/3', 15.00, '2025-03-07 00:00:00', '2025-03-09 00:00:00', 0, 1, '2025-09-14 21:28:36'),
(7, 'ngay2011', 'Mừng ngày 20/11', 15.00, '2025-11-19 00:00:00', '2025-11-21 00:00:00', 0, 1, '2025-09-14 21:28:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion_products`
--

CREATE TABLE `promotion_products` (
  `promotion_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(191) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Bẫy `reviews`
--
DELIMITER $$
CREATE TRIGGER `trg_reviews_before_insert` BEFORE INSERT ON `reviews` FOR EACH ROW BEGIN
  IF NEW.order_item_id IS NOT NULL THEN
    IF NOT EXISTS (
      SELECT 1
      FROM order_items oi
      JOIN orders o ON o.id = oi.order_id
      WHERE oi.id = NEW.order_item_id
        AND oi.product_id = NEW.product_id
        AND o.user_id = NEW.user_id
        AND o.status = 'thanh_cong'
    ) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Review không hợp lệ: chưa mua sản phẩm này';
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`) VALUES
(1, 'user', 'Khách hàng'),
(2, 'staff', 'Nhân viên'),
(3, 'admin', 'Quản trị');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipments`
--

CREATE TABLE `shipments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `carrier` varchar(64) DEFAULT NULL,
  `tracking_code` varchar(128) DEFAULT NULL,
  `status` enum('cho_lay','dang_giao','giao_thanh_cong','that_bai','tra_hang') DEFAULT 'cho_lay',
  `estimated_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staff_profiles`
--

CREATE TABLE `staff_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(128) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `staff_profiles`
--

INSERT INTO `staff_profiles` (`id`, `user_id`, `position`, `is_locked`, `created_at`) VALUES
(1, 1, 'Admin', 0, '2025-09-14 20:57:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `level_id` tinyint(3) UNSIGNED DEFAULT 1,
  `email` varchar(191) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
  `address` varchar(255) DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `role_id`, `level_id`, `email`, `first_name`, `last_name`, `password_hash`, `phone`, `gender`, `address`, `is_blocked`, `created_at`, `updated_at`, `password`) VALUES
(1, 3, 3, 'admin@hoa.vn', NULL, NULL, 'admin@123', '0900000000', 'Nam', NULL, 0, '2025-09-14 20:57:51', '2025-09-14 20:57:51', ''),
(2, 1, 1, 'user@hoa.vn', NULL, NULL, 'user@123', '0911111111', 'Nam', NULL, 0, '2025-09-14 20:57:51', '2025-09-14 20:57:51', ''),
(7, 1, 1, 'dnvq2911@gmail.com', 'Quyến', 'Văn', '', '0586964157', 'Nam', '574/3/25/7A Kinh Dương Vương, Phường An Lạc, Quận Bình Tân', 0, '2025-09-21 20:15:06', '2025-09-21 20:15:06', '0586964157'),
(8, 1, 1, 'dnvq291104@gmail.com', 'Văn', 'Quyến', '', '0586964157', 'Nam', 'hehe', 0, '2025-09-28 21:59:19', '2025-10-06 15:13:58', '0586964157');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_cards`
--

CREATE TABLE `user_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `card_holder` varchar(100) NOT NULL,
  `card_number_last4` varchar(4) NOT NULL,
  `expiry_date` varchar(10) NOT NULL,
  `card_brand` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `full_card_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_cards`
--

INSERT INTO `user_cards` (`id`, `user_id`, `card_holder`, `card_number_last4`, `expiry_date`, `card_brand`, `created_at`, `full_card_number`) VALUES
(1, 8, 'Đồng Nguyễn Văn Quyến', '6789', '11/27', 'Visa', '2025-10-06 16:04:11', NULL),
(2, 8, 'Đồng Nguyễn Văn Quyến', '4157', '11//26', 'Visa', '2025-10-06 16:04:32', NULL),
(3, 8, 'Đồng Nguyễn Văn Quyến', '3132', '21/20', 'Visa', '2025-10-06 22:08:16', '12312313132'),
(4, 8, 'Đồng Nguyễn Văn Quyến', '4157', '12/11', 'Visa', '2025-10-08 15:34:37', '0586964157'),
(5, 8, 'Đồng Nguyễn Văn Quyến', '4157', '21/11', 'MoMo', '2025-10-08 15:39:16', '0586964157');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(64) NOT NULL,
  `type` enum('percent','amount') NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `min_order_total` decimal(12,2) DEFAULT 0.00,
  `total_quantity` int(11) NOT NULL DEFAULT 0,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `type`, `value`, `max_discount`, `min_order_total`, `total_quantity`, `per_user_limit`, `starts_at`, `ends_at`, `is_active`, `created_by`, `created_at`) VALUES
(1, 'FLOW10', 'percent', 10.00, 50000.00, 300000.00, 100, 3, '2025-09-14 20:57:51', '2025-12-13 20:57:51', 1, 1, '2025-09-14 20:57:51'),
(2, 'SALE50K', 'amount', 50000.00, NULL, 400000.00, 200, 2, '2025-09-14 20:57:51', '2025-12-13 20:57:51', 1, 1, '2025-09-14 20:57:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher_usages`
--

CREATE TABLE `voucher_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `voucher_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `used_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Bẫy `voucher_usages`
--
DELIMITER $$
CREATE TRIGGER `trg_voucher_usage_before_insert` BEFORE INSERT ON `voucher_usages` FOR EACH ROW BEGIN
  DECLARE v_total INT;
  DECLARE v_used  INT;
  DECLARE v_limit INT;
  DECLARE v_used_user INT;
  DECLARE v_start DATETIME;
  DECLARE v_end   DATETIME;
  DECLARE v_active TINYINT;

  SELECT total_quantity, per_user_limit, starts_at, ends_at, is_active
    INTO v_total, v_limit, v_start, v_end, v_active
  FROM vouchers WHERE id = NEW.voucher_id;

  IF v_active = 0 OR NOW() NOT BETWEEN v_start AND v_end THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Voucher không còn hiệu lực';
  END IF;

  SELECT COUNT(*) INTO v_used FROM voucher_usages WHERE voucher_id = NEW.voucher_id;
  IF v_total > 0 AND v_used >= v_total THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Voucher đã hết lượt';
  END IF;

  SELECT COUNT(*) INTO v_used_user
  FROM voucher_usages WHERE voucher_id = NEW.voucher_id AND user_id = NEW.user_id;
  IF v_used_user >= v_limit THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bạn đã sử dụng voucher tối đa';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_revenue_daily`
-- (See below for the actual view)
--
CREATE TABLE `v_revenue_daily` (
`day` date
,`revenue` decimal(34,2)
,`orders_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_top_products`
-- (See below for the actual view)
--
CREATE TABLE `v_top_products` (
`product_id` bigint(20) unsigned
,`name` varchar(191)
,`total_qty` decimal(32,0)
,`total_sales` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_revenue_daily`
--
DROP TABLE IF EXISTS `v_revenue_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_revenue_daily`  AS SELECT cast(`o`.`updated_at` as date) AS `day`, sum(`o`.`grand_total`) AS `revenue`, count(0) AS `orders_count` FROM `orders` AS `o` WHERE `o`.`status` = 'thanh_cong' GROUP BY cast(`o`.`updated_at` as date) ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_top_products`
--
DROP TABLE IF EXISTS `v_top_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_products`  AS SELECT `oi`.`product_id` AS `product_id`, `p`.`name` AS `name`, sum(`oi`.`quantity`) AS `total_qty`, sum(`oi`.`line_total`) AS `total_sales` FROM ((`order_items` `oi` join `orders` `o` on(`o`.`id` = `oi`.`order_id` and `o`.`status` = 'thanh_cong')) join `products` `p` on(`p`.`id` = `oi`.`product_id`)) GROUP BY `oi`.`product_id`, `p`.`name` ORDER BY sum(`oi`.`quantity`) DESC ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_addr_user` (`user_id`);

--
-- Chỉ mục cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_entity` (`entity`,`entity_id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_prod` (`cart_id`,`product_id`),
  ADD KEY `fk_citem_prod` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_cat_parent` (`parent_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cmt_user` (`user_id`),
  ADD KEY `idx_comments_product` (`product_id`);

--
-- Chỉ mục cho bảng `customer_levels`
--
ALTER TABLE `customer_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_product` (`user_id`,`product_id`),
  ADD KEY `fk_fav_product` (`product_id`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`product_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_msg_staff` (`created_by`);

--
-- Chỉ mục cho bảng `message_users`
--
ALTER TABLE `message_users`
  ADD PRIMARY KEY (`message_id`,`user_id`),
  ADD KEY `fk_mu_user` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_address` (`address_id`),
  ADD KEY `idx_orders_user_status` (`user_id`,`status`),
  ADD KEY `idx_orders_updated_at` (`updated_at`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oit_order` (`order_id`),
  ADD KEY `fk_oit_prod` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_active` (`is_active`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pimg_prod` (`product_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_promo_staff` (`created_by`),
  ADD KEY `idx_promotions_active` (`is_active`,`starts_at`,`ends_at`);

--
-- Chỉ mục cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`promotion_id`,`product_id`),
  ADD KEY `fk_pp_prod` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_review_once` (`user_id`,`product_id`,`order_item_id`),
  ADD KEY `idx_reviews_product` (`product_id`),
  ADD KEY `fk_rev_oi` (`order_item_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`),
  ADD KEY `fk_users_level` (`level_id`);

--
-- Chỉ mục cho bảng `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_card_user` (`user_id`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_v_staff` (`created_by`),
  ADD KEY `idx_vouchers_active` (`is_active`,`starts_at`,`ends_at`);

--
-- Chỉ mục cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vu_voucher` (`voucher_id`),
  ADD KEY `fk_vu_user` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customer_levels`
--
ALTER TABLE `customer_levels`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=437;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `staff_profiles`
--
ALTER TABLE `staff_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `user_cards`
--
ALTER TABLE `user_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_citem_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_citem_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_cmt_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cmt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_fav_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inv_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_staff` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `message_users`
--
ALTER TABLE `message_users`
  ADD CONSTRAINT `fk_mu_msg` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oit_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oit_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_pimg_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `fk_promo_staff` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `fk_pp_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_promo` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_rev_oi` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `fk_ship_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_level` FOREIGN KEY (`level_id`) REFERENCES `customer_levels` (`id`),
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Các ràng buộc cho bảng `user_cards`
--
ALTER TABLE `user_cards`
  ADD CONSTRAINT `fk_card_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `fk_v_staff` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD CONSTRAINT `fk_vu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vu_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
