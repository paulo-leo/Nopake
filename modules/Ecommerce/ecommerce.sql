-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 17-Nov-2021 às 13:52
-- Versão do servidor: 5.7.26
-- versão do PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `russel2`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_categories`
--

DROP TABLE IF EXISTS `so_categories`;
CREATE TABLE IF NOT EXISTS `so_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `category_order` int(3) DEFAULT '0',
  `category_id` int(11) DEFAULT NULL,
  `status` int(2) DEFAULT '0',
  `type` int(2) DEFAULT '1',
  `color` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_show` int(1) DEFAULT '0',
  `image` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_new` int(1) DEFAULT '0',
  `created_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_faqs`
--

DROP TABLE IF EXISTS `so_faqs`;
CREATE TABLE IF NOT EXISTS `so_faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `faq_order` int(3) DEFAULT '0',
  `faq_question` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `faq_reply` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_products`
--

DROP TABLE IF EXISTS `so_products`;
CREATE TABLE IF NOT EXISTS `so_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_order` int(3) DEFAULT '0',
  `product_show` int(1) DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '0',
  `product_new` int(1) DEFAULT '0',
  `product_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `promotional_price` float(10,2) DEFAULT NULL,
  `keywords` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(10) DEFAULT '0',
  `stock` int(1) DEFAULT '0',
  `image` int(11) DEFAULT NULL,
  `on_date` date DEFAULT NULL,
  `off_date` date DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `created_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_fk` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_taxes`
--

DROP TABLE IF EXISTS `so_taxes`;
CREATE TABLE IF NOT EXISTS `so_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tax_description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `tax_value` float(10,2) NOT NULL,
  `tax_status` int(2) DEFAULT '0',
  `tax_type` int(1) DEFAULT '1',
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `so_faqs`
--
ALTER TABLE `so_faqs`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `so_categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
