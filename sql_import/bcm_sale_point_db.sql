-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 26, 2024 at 03:16 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bcm_sale_point_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `catid` int(11) NOT NULL,
  `category` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`catid`, `category`) VALUES
(28, 'pharmacie'),
(29, 'beaute'),
(30, 'litterature');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_invoice`
--

CREATE TABLE `tbl_invoice` (
  `invoice_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `subtotal` double NOT NULL,
  `discount` double NOT NULL DEFAULT 0,
  `sgst` float NOT NULL DEFAULT 0,
  `cgst` float NOT NULL DEFAULT 0,
  `total` double NOT NULL,
  `payment_type` tinytext NOT NULL,
  `due` double NOT NULL,
  `paid` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_invoice`
--

INSERT INTO `tbl_invoice` (`invoice_id`, `order_date`, `subtotal`, `discount`, `sgst`, `cgst`, `total`, `payment_type`, `due`, `paid`) VALUES
(52, '2023-06-29', 71400, 0, 0, 0, 71400, 'Cash', -8600, 80000),
(53, '2023-06-29', 9000, 0, 0, 0, 9000, 'Cash', -1000, 10000),
(54, '2023-07-07', 65000, 0, 0, 0, 65000, 'Cash', -5000, 70000),
(55, '2023-07-07', 108000, 0, 0, 0, 108000, 'Cash', -2000, 110000),
(56, '2023-07-07', 15000, 0, 0, 0, 15000, 'Cash', 15000, 0),
(57, '2023-07-16', 8500, 0, 0, 0, 8500, 'Cash', -1500, 10000),
(58, '2023-07-16', 14900, 0, 0, 0, 14900, 'Cash', -5100, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_invoice_details`
--

CREATE TABLE `tbl_invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `barcode` varchar(200) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `qty` int(11) NOT NULL,
  `rate` double NOT NULL,
  `saleprice` double NOT NULL,
  `order_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_invoice_details`
--

INSERT INTO `tbl_invoice_details` (`id`, `invoice_id`, `barcode`, `product_id`, `product_name`, `qty`, `rate`, `saleprice`, `order_date`) VALUES
(221, 52, '6001106127688', 41, 'Nurofen', 1, 6000, 6000, '2023-06-29'),
(222, 52, '6001137101312', 40, 'Rehidrat', 4, 7000, 28000, '2023-06-29'),
(223, 52, '6006352014386', 37, 'Flomist', 2, 6700, 13400, '2023-06-29'),
(224, 52, '6001390144866', 38, 'ride-12,5', 3, 8000, 24000, '2023-06-29'),
(225, 53, '6009826650325', 39, 'Dynadol Syrup', 1, 9000, 9000, '2023-06-29'),
(226, 54, '6001137101312', 40, 'Rehidrat', 3, 7000, 21000, '2023-07-07'),
(227, 54, '6009826650325', 39, 'Dynadol Syrup', 4, 9000, 36000, '2023-07-07'),
(228, 54, '6001390144866', 38, 'ride-12,5', 1, 8000, 8000, '2023-07-07'),
(229, 55, '6001106127688', 41, 'Nurofen', 18, 6000, 108000, '2023-07-07'),
(232, 57, '6009693920279', 42, 'DECOFED Syrup', 9, 500, 4500, '2023-07-16'),
(233, 57, '43180555230716', 43, 'BRUFEN ', 4, 1000, 4000, '2023-07-16'),
(234, 58, '6009693920279', 42, 'DECOFED Syrup', 3, 500, 1500, '2023-07-16'),
(235, 58, '6006352014386', 37, 'Flomist', 2, 6700, 13400, '2023-07-16');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `pid` int(11) NOT NULL,
  `barcode` varchar(1000) NOT NULL,
  `product` varchar(200) NOT NULL,
  `category` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `stock` int(11) NOT NULL,
  `purchaseprice` float NOT NULL,
  `saleprice` float NOT NULL,
  `image` varchar(200) NOT NULL,
  `Supplier` varchar(100) NOT NULL,
  `unit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`pid`, `barcode`, `product`, `category`, `description`, `stock`, `purchaseprice`, `saleprice`, `image`, `Supplier`, `unit`) VALUES
(37, '6006352014386', 'Flomist', 'pharmacie', 'produit pour la toux', 296, 5500, 6700, '649caa1465d9e.jpeg', 'Matos', 'paquet'),
(38, '6001390144866', 'ride-12,5', 'pharmacie', 'produit pour la faim', 446, 7000, 8000, '649caabe0e372.jpeg', 'Matos', 'paquet'),
(39, '6009826650325', 'Dynadol Syrup', 'pharmacie', 'produit pour grippe', 245, 6700, 9000, '649cab48b3860.jpeg', 'Matos', 'paquet'),
(40, '6001137101312', 'Rehidrat', 'pharmacie', 'produit pour la faim', 193, 5500, 7000, '649cabd7da2bf.jpeg', 'Matos', 'paquet'),
(41, '6001106127688', 'Nurofen', 'pharmacie', 'produit pour sur la fièvre', 321, 4500, 6000, '649cac997bec1.jpeg', 'Matos', 'paquet'),
(42, '6009693920279', 'DECOFED Syrup', 'beaute', 'un prdt de beaute pour femme', 58, 300, 500, '64b414f3beaec.jpeg', 'Litha', 'boite'),
(43, '43180555230716', 'BRUFEN ', 'litterature', 'livre pour enfants', 86, 500, 1000, '64b4156312134.jpeg', 'Abbott', 'paquet');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_supplier`
--

CREATE TABLE `tbl_supplier` (
  `SupplierId` int(20) NOT NULL,
  `SupplierName` varchar(200) NOT NULL,
  `SupplierNumber` varchar(200) NOT NULL,
  `SupplierEmail` varchar(200) NOT NULL,
  `SupplierAddress` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_supplier`
--

INSERT INTO `tbl_supplier` (`SupplierId`, `SupplierName`, `SupplierNumber`, `SupplierEmail`, `SupplierAddress`) VALUES
(10, 'Matos', '+242 06 956 30 22', 'matos@gmail.com', 'brazzaville'),
(11, 'Abbott', '+27813623440', 'abbott@gmail.com', 'paris rue 209'),
(12, 'Litha', '0813623440', 'litha@gmail.com', 'brazzaville');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_taxdis`
--

CREATE TABLE `tbl_taxdis` (
  `taxdis_id` int(11) NOT NULL,
  `sgst` float NOT NULL,
  `cgst` float NOT NULL,
  `discount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_taxdis`
--

INSERT INTO `tbl_taxdis` (`taxdis_id`, `sgst`, `cgst`, `discount`) VALUES
(4, 2.5, 2.5, 2),
(5, 5, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit`
--

CREATE TABLE `tbl_unit` (
  `unitid` int(20) NOT NULL,
  `unitname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_unit`
--

INSERT INTO `tbl_unit` (`unitid`, `unitname`) VALUES
(11, 'paquet'),
(12, 'carton'),
(13, 'boite');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `userid` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `useremail` varchar(200) NOT NULL,
  `userpassword` varchar(200) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`userid`, `username`, `useremail`, `userpassword`, `role`) VALUES
(18, 'Rusty', 'rusty@gmail.com', 'rusty10', 'Admin'),
(19, 'Adele', 'adele@gamil.com', '12345', 'Utilisateur'),
(20, 'Felie', 'felie@gmail.com', '1234', 'Utilisateur'),
(21, 'User', 'user@gmail.com', '123', 'Utilisateur'),
(23, 'Stephanie', 'stephanie@gmail.com', 'cleve10', 'Utilisateur');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`catid`);

--
-- Indexes for table `tbl_invoice`
--
ALTER TABLE `tbl_invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `tbl_invoice_details`
--
ALTER TABLE `tbl_invoice_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  ADD PRIMARY KEY (`SupplierId`);

--
-- Indexes for table `tbl_taxdis`
--
ALTER TABLE `tbl_taxdis`
  ADD PRIMARY KEY (`taxdis_id`);

--
-- Indexes for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  ADD PRIMARY KEY (`unitid`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `catid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_invoice`
--
ALTER TABLE `tbl_invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tbl_invoice_details`
--
ALTER TABLE `tbl_invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `SupplierId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_taxdis`
--
ALTER TABLE `tbl_taxdis`
  MODIFY `taxdis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  MODIFY `unitid` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
