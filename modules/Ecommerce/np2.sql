-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 26-Nov-2021 às 19:06
-- Versão do servidor: 8.0.21
-- versão do PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `np2`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_categories`
--

DROP TABLE IF EXISTS `so_categories`;
CREATE TABLE IF NOT EXISTS `so_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_order` int DEFAULT '0',
  `category_id` int DEFAULT NULL,
  `status` int DEFAULT '0',
  `type` int DEFAULT '1',
  `color` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_show` int DEFAULT '0',
  `image` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_new` int DEFAULT '0',
  `created_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `so_categories`
--

INSERT INTO `so_categories` (`id`, `name`, `category_order`, `category_id`, `status`, `type`, `color`, `category_show`, `image`, `description`, `keywords`, `category_new`, `created_in`) VALUES
(12, 'teste1234', 2, 1, 1, 0, 'vermelha', 1, NULL, 'Teste erick fraga da silva', 'erick,fraga,silva', 1, '2021-11-18 12:30:22'),
(19, 'teste1234', 2, 1, 1, 0, 'vermelha', 1, NULL, 'Teste erick fraga da silva', 'erick,fraga,silva', 1, '2021-11-18 14:30:08'),
(20, 'teste1234', 2, 1, 1, 0, 'vermelha', 1, NULL, 'Teste erick fraga da silva', 'erick,fraga,silva', 1, '2021-11-18 14:30:08'),
(26, 'teste1234', 2, 1, 1, 0, 'vermelha', 1, NULL, 'Teste erick fraga da silva', 'erick,fraga,silva', 1, '2021-11-22 15:17:59'),
(34, 'teste123', 2, 1, 1, 0, 'vermelha', 1, NULL, 'Teste erick fraga da silva', 'erick,fraga,silva', 1, '2021-11-23 18:06:19'),
(36, 'teste9988', 5, 6, 0, 0, 'cinza', 0, NULL, 'testando 12345678910', 'testando, criando', 0, '2021-11-24 15:38:42'),
(37, 'csdfdsfd', 5, 0, 1, 1, 'lçlçlçlç', 0, NULL, 'lçlçlçlçlç', 'çlçlçlçlç', 1, '2021-11-24 17:24:21'),
(38, 'testeUSA', 10, 0, 1, 1, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:27:41'),
(39, 'testeUS', 10, 0, 1, 5, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:28:50'),
(40, 'testeU', 10, 0, 1, 5, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:30:02'),
(41, 'testeUU', 10, 0, 1, 5, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:31:09'),
(42, 'testeUA', 10, 0, 1, 5, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:33:11'),
(43, 'testeUAA', 10, 0, 1, 5, 'ciano', 0, NULL, '12345', 'usa, brasil, teste', 1, '2021-11-24 17:34:44'),
(45, 'Erick Teste', 1, 0, 0, 2, 'preto', 1, NULL, 'testando 123', 'testando, 123', 1, '2021-11-24 18:12:57'),
(46, 'Erick Teste 2', 1, 0, 1, 2, 'preto', 0, NULL, 'testando 123', 'testando, 123', 0, '2021-11-24 18:15:25');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_faqs`
--

DROP TABLE IF EXISTS `so_faqs`;
CREATE TABLE IF NOT EXISTS `so_faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `faq_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faq_order` int DEFAULT NULL,
  `faq_question` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `faq_reply` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `faq_status` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_products`
--

DROP TABLE IF EXISTS `so_products`;
CREATE TABLE IF NOT EXISTS `so_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` int NOT NULL,
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_order` int DEFAULT '0',
  `product_show` int DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `product_new` int DEFAULT '0',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `promotional_price` float(10,2) DEFAULT NULL,
  `keywords` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `stock` int DEFAULT '0',
  `image` int DEFAULT NULL,
  `on_date` date DEFAULT NULL,
  `off_date` date DEFAULT NULL,
  `category_id` int NOT NULL,
  `created_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_fk` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `so_products`
--

INSERT INTO `so_products` (`id`, `type`, `code`, `barcode`, `product_order`, `product_show`, `status`, `product_new`, `name`, `description`, `price`, `promotional_price`, `keywords`, `quantity`, `stock`, `image`, `on_date`, `off_date`, `category_id`, `created_in`, `link`) VALUES
(10, 1, 'erick', 'erick', 1, 1, 1, 1, 'xxx', 'erick', 10.00, 10.00, 'erick', 10, 1, NULL, '2002-05-25', '0003-05-25', 10, '2021-11-22 13:46:33', ''),
(11, 1, 'erick', 'erick', 1, 1, 1, 1, 'erick', 'erick', 10.00, 10.00, 'erick', 10, 1, NULL, '2002-05-25', '0003-05-25', 10, '2021-11-22 15:58:33', ''),
(17, 1, 'erick', 'erick', 1, 1, 1, 1, 'erick', 'erick', 10.00, 10.00, 'erick', 10, 1, NULL, '2002-05-25', '0003-05-25', 10, '2021-11-22 16:28:11', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `so_taxes`
--

DROP TABLE IF EXISTS `so_taxes`;
CREATE TABLE IF NOT EXISTS `so_taxes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tax_description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tax_value` float(10,2) NOT NULL,
  `tax_status` int DEFAULT '0',
  `tax_type` int DEFAULT '1',
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Restrições para despejos de tabelas
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
