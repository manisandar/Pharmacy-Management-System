-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 06, 2026 at 05:10 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ================================================
-- DATABASE: `pharmacy_db`
-- ================================================
-- Pharmacy Management System Database
-- Contains customer management, medicine inventory, order processing, and user management
-- ================================================

-- ================================================
-- TABLE: `customers`
-- ================================================
-- Purpose: Store customer/patient information
-- Key Fields:
--   - customer_id: Primary key, auto-increment
--   - customer_name: Patient name
--   - contact_number: Phone number for communication
--   - address: Physical address of the customer
--   - created_at: Account creation timestamp
-- ================================================

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL COMMENT 'Unique customer identifier',
  `customer_name` varchar(100) NOT NULL COMMENT 'Full name of the customer/patient',
  `contact_number` varchar(20) DEFAULT NULL COMMENT 'Phone number for communication',
  `address` text DEFAULT NULL COMMENT 'Physical address',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Account creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_number`, `address`, `created_at`) VALUES
(33, 'Ananda', '0812345671', 'Bangkok', '2026-02-03 06:36:01'),
(34, 'Somsak', '0812345672', 'Nonthaburi', '2026-02-03 06:36:01'),
(35, 'Kanya', '0812345673', 'PathumThani', '2026-02-03 06:36:01'),
(36, 'Niran', '0812345674', 'Bangkok', '2026-02-03 06:36:01'),
(37, 'Pim', '0812345675', 'SamutPrakan', '2026-02-03 06:36:01'),
(38, 'Chai', '0812345676', 'Bangkok', '2026-02-03 06:36:01'),
(39, 'Arthit', '0812345677', 'Nonthaburi', '2026-02-03 06:36:01'),
(40, 'Mayura', '0812345678', 'Bangkok', '2026-02-03 06:36:01'),
(41, 'Thanin', '0812345679', 'PathumThani', '2026-02-03 06:36:01'),
(42, 'Suda', '0812345680', 'Bangkok', '2026-02-03 06:36:01'),
(43, 'user', '101111', 'bangkok', '2026-02-03 15:55:19'),
(44, 'test', '123', '123', '2026-02-05 03:35:05');

-- ================================================
-- TABLE: `medicines`
-- ================================================
-- Purpose: Store medicine inventory and product information
-- Key Fields:
--   - medicine_id: Primary key, unique medicine identifier
--   - medicine_name: Trade name of the medicine
--   - chemical_name: Generic/chemical name
--   - dosage_form: Form type (Tablet, Syrup, Injection, etc.)
--   - price_per_unit: Cost per unit
--   - quantity: Current stock level
--   - reorder_level: Threshold to trigger reordering
--   - expiry_date: Medicine expiration date
--   - supplier_id: Foreign key to suppliers table
-- ================================================

CREATE TABLE `medicines` (
  `medicine_id` int(1) NOT NULL COMMENT 'Unique medicine identifier',
  `medicine_name` varchar(100) NOT NULL COMMENT 'Commercial/trade name of the medicine',
  `chemical_name` varchar(100) DEFAULT NULL COMMENT 'Active ingredient or generic name',
  `dosage_form` varchar(50) DEFAULT NULL COMMENT 'Form: Tablet, Syrup, Injection, Cream, Inhaler, etc.',
  `price_per_unit` decimal(10,2) NOT NULL COMMENT 'Cost per unit in currency',
  `quantity` int(11) NOT NULL COMMENT 'Current stock quantity',
  `reorder_level` int(11) NOT NULL COMMENT 'Minimum stock level before reordering',
  `expiry_date` date NOT NULL COMMENT 'Product expiration date',
  `supplier_id` int(11) DEFAULT NULL COMMENT 'Foreign key: references suppliers table',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `medicine_name`, `chemical_name`, `dosage_form`, `price_per_unit`, `quantity`, `reorder_level`, `expiry_date`, `supplier_id`, `created_at`) VALUES
(99, 'Vitamin C Syrup', 'Ascorbic Acid', 'Syrup', 45.00, 0, 15, '2024-10-05', 11, '2026-02-03 06:21:29'),
(101, 'Antiseptic Cream', 'Povidone Iodine', 'Gel/Cream/Ointment', 35.00, 1000, 10, '2024-08-20', 13, '2026-02-03 06:21:29'),
(102, 'Eye Drops Clear', 'Hypromellose', 'Drops (Eye/Ear/Nasal)', 50.00, 0, 15, '2024-09-25', 14, '2026-02-03 06:21:29'),
(103, 'Insulin Regular', 'Human Insulin', 'Injection (Vial/Ampoule)', 180.00, 0, 10, '2024-10-12', 15, '2026-02-03 06:21:29'),
(105, 'Diclofenac Gel', 'Diclofenac', 'Gel/Cream/Ointment', 65.00, 0, 20, '2024-07-25', 7, '2026-02-03 06:21:29'),
(106, 'Antacid Syrup', 'Aluminium Hydroxide', 'Syrup', 40.00, 990, 30, '2024-09-01', 8, '2026-02-03 06:21:29'),
(107, 'Metformin 500mg', 'Metformin', 'Tablet', 2.50, 0, 50, '2024-11-15', 9, '2026-02-03 06:21:29'),
(109, 'Hydrocortisone Cream', 'Hydrocortisone', 'Gel/Cream/Ointment', 55.00, 0, 15, '2024-07-15', 11, '2026-02-03 06:21:29'),
(110, 'Nasal Spray', 'Oxymetazoline', 'Drops (Eye/Ear/Nasal)', 85.00, 0, 10, '2024-09-28', 12, '2026-02-03 06:21:29'),
(111, 'Ceftriaxone Injection', 'Ceftriaxone', 'Injection (Vial/Ampoule)', 90.00, 0, 20, '2024-10-22', 13, '2026-02-03 06:21:29'),
(112, 'Fever Suppository', 'Paracetamol', 'Suppository', 25.00, 0, 15, '2024-11-02', 14, '2026-02-03 06:21:29'),
(114, 'Paracetamol 500mg (Bulk)', 'Paracetamol', 'Tablet', 1.40, 200, 50, '2027-12-31', 6, '2026-02-03 06:23:41'),
(115, 'Vitamin C 1000mg', 'Ascorbic Acid', 'Tablet', 3.50, 150, 40, '2027-10-10', 7, '2026-02-03 06:23:41'),
(116, 'Amoxicillin 250mg', 'Amoxicillin', 'Capsule', 3.50, 109, 30, '2027-11-15', 8, '2026-02-03 06:23:41'),
(117, 'Ibuprofen 200mg', 'Ibuprofen', 'Tablet', 2.20, 180, 50, '2027-09-30', 9, '2026-02-03 06:23:41'),
(118, 'Cough Syrup Herbal', 'Herbal Mix', 'Syrup', 55.00, 100, 20, '2027-08-25', 10, '2026-02-03 06:23:41'),
(119, 'Skin Ointment', 'Zinc Oxide', 'Gel/Cream/Ointment', 40.00, 90, 20, '2027-10-01', 11, '2026-02-03 06:23:41'),
(120, 'Nasal Spray Saline', 'Saline', 'Drops (Eye/Ear/Nasal)', 75.00, 110, 30, '2027-11-05', 12, '2026-02-03 06:23:41'),
(121, 'Insulin Glargine', 'Insulin', 'Injection (Vial/Ampoule)', 350.00, 60, 15, '2027-12-20', 13, '2026-02-03 06:23:41'),
(122, 'Asthma Relief Inhaler', 'Salbutamol', 'Inhaler', 150.00, 70, 20, '2027-09-18', 14, '2026-02-03 06:23:41'),
(123, 'Fever Suppository Adult', 'Paracetamol', 'Suppository', 28.00, 70, 20, '2027-10-30', 15, '2026-02-03 06:23:41'),
(124, 'Amlodipine 5mg', 'Amlodipine', 'Tablet', 2.80, 128, 40, '2027-11-12', 6, '2026-02-03 06:23:41'),
(125, 'Losartan 50mg', 'Losartan', 'Tablet', 3.50, 140, 50, '2027-10-22', 7, '2026-02-03 06:23:41'),
(126, 'Metformin 850mg', 'Metformin', 'Tablet', 3.00, 180, 50, '2027-12-05', 8, '2026-02-03 06:23:41'),
(127, 'Omeprazole 20mg', 'Omeprazole', 'Capsule', 5.00, 130, 25, '2027-11-30', 9, '2026-02-03 06:23:41'),
(128, 'Cetirizine 10mg', 'Cetirizine', 'Tablet', 2.00, 170, 30, '2027-09-28', 10, '2026-02-03 06:23:41'),
(129, 'Antacid Tablet', 'Calcium Carbonate', 'Tablet', 1.20, 195, 50, '2027-08-15', 11, '2026-02-03 06:23:41'),
(130, 'Multivitamin Syrup', 'Multivitamin', 'Syrup', 65.00, 90, 25, '2027-10-10', 12, '2026-02-03 06:23:41'),
(131, 'Eye Lubricant Drops', 'Carboxymethylcellulose', 'Drops (Eye/Ear/Nasal)', 70.00, 85, 20, '2027-11-18', 13, '2026-02-03 06:23:41'),
(132, 'Antifungal Cream', 'Clotrimazole', 'Gel/Cream/Ointment', 60.00, 85, 20, '2027-09-10', 14, '2026-02-03 06:23:41'),
(133, 'Pain Relief Gel', 'Menthol', 'Gel/Cream/Ointment', 45.00, 110, 25, '2027-12-01', 15, '2026-02-03 06:23:41'),
(134, 'Cefixime Capsule', 'Cefixime', 'Capsule', 6.00, 100, 30, '2027-10-05', 6, '2026-02-03 06:23:41'),
(135, 'Azithromycin 250mg', 'Azithromycin', 'Tablet', 8.00, 90, 20, '2027-11-20', 7, '2026-02-03 06:23:41'),
(136, 'Vitamin B Complex', 'Vitamin B', 'Tablet', 3.50, 140, 30, '2027-12-15', 8, '2026-02-03 06:23:41'),
(137, 'Cough Suppressant', 'Dextromethorphan', 'Syrup', 60.00, 120, 30, '2027-09-05', 9, '2026-02-03 06:23:41'),
(138, 'Hydrocortisone Cream', 'Hydrocortisone', 'Gel/Cream/Ointment', 55.00, 100, 20, '2027-10-25', 10, '2026-02-03 06:23:41'),
(139, 'Nasal Decongestant', 'Oxymetazoline', 'Drops (Eye/Ear/Nasal)', 85.00, 75, 20, '2027-11-28', 11, '2026-02-03 06:23:41'),
(140, 'Insulin Aspart', 'Insulin', 'Injection (Vial/Ampoule)', 320.00, 65, 15, '2027-12-10', 12, '2026-02-03 06:23:41'),
(141, 'Asthma Preventer Inhaler', 'Budesonide', 'Inhaler', 190.00, 70, 20, '2027-09-22', 13, '2026-02-03 06:23:41'),
(142, 'Children Fever Syrup', 'Paracetamol', 'Syrup', 50.00, 130, 30, '2027-10-18', 14, '2026-02-03 06:23:41'),
(143, 'Electrolyte Solution', 'Oral Rehydration Salts', 'Syrup', 35.00, 160, 40, '2027-08-30', 15, '2026-02-03 06:23:41'),
(145, 'Amoxicillin 500mg', 'Amoxicillin', 'Syrup', 4.00, 4969, 30, '2026-11-15', 7, '2026-02-03 06:25:45'),
(146, 'Ibuprofen 400mg (Low)', 'Ibuprofen', 'Tablet', 3.00, 12, 40, '2026-10-30', 8, '2026-02-03 06:25:45'),
(147, 'Cetirizine 10mg (Low)', 'Cetirizine', 'Tablet', 2.00, 9, 30, '2026-09-20', 9, '2026-02-03 06:25:45'),
(148, 'Omeprazole 20mg (Low)', 'Omeprazole', 'Capsule', 5.00, 6, 25, '2026-12-10', 10, '2026-02-03 06:25:45'),
(149, 'Vitamin C Syrup (Low)', 'Ascorbic Acid', 'Syrup', 45.00, 7, 20, '2026-11-05', 11, '2026-02-03 06:25:45'),
(150, 'Cough Syrup DM (Low)', 'Dextromethorphan', 'Syrup', 60.00, 5, 25, '2026-10-18', 12, '2026-02-03 06:25:45'),
(151, 'Antiseptic Cream (Low)', 'Povidone Iodine', 'Gel/Cream/Ointment', 35.00, 400, 15, '2026-12-20', 13, '2026-02-03 06:25:45'),
(152, 'Eye Drops Clear (Low)', 'Hypromellose', 'Drops (Eye/Ear/Nasal)', 50.00, 6, 20, '2026-11-25', 14, '2026-02-03 06:25:45'),
(153, 'Insulin Regular (Low)', 'Human Insulin', 'Injection (Vial/Ampoule)', 180.00, 3, 10, '2026-12-05', 15, '2026-02-03 06:25:45'),
(154, 'Salbutamol Inhaler (Low)', 'Salbutamol', 'Inhaler', 120.00, 4, 20, '2026-10-10', 6, '2026-02-03 06:25:45'),
(155, 'Diclofenac Gel (Low)', 'Diclofenac', 'Gel/Cream/Ointment', 65.00, 6, 25, '2026-09-25', 7, '2026-02-03 06:25:45'),
(156, 'Antacid Syrup (Low)', 'Aluminium Hydroxide', 'Syrup', 40.00, 600, 30, '2026-11-01', 8, '2026-02-03 06:25:45'),
(157, 'Metformin 500mg (Low)', 'Metformin', 'Tablet', 2.50, 12, 50, '2026-12-15', 9, '2026-02-03 06:25:45'),
(158, 'Aspirin 300mg', 'Aspirin', 'Tablet', 1.20, 1000, 30, '2026-10-05', 10, '2026-02-03 06:25:45'),
(159, 'Hydrocortisone Cream (Low)', 'Hydrocortisone', 'Gel/Cream/Ointment', 55.00, 5, 20, '2026-09-15', 11, '2026-02-03 06:25:45'),
(160, 'Nasal Spray (Low)', 'Oxymetazoline', 'Drops (Eye/Ear/Nasal)', 85.00, 6, 15, '2026-11-28', 12, '2026-02-03 06:25:45'),
(161, 'Ceftriaxone Injection (Low)', 'Ceftriaxone', 'Injection (Vial/Ampoule)', 90.00, 4, 20, '2026-10-22', 13, '2026-02-03 06:25:45'),
(166, 'Multivitamin Syrup (Low)', 'Multivitamin', 'Syrup', 65.00, 6, 25, '2026-10-10', 8, '2026-02-03 06:25:45'),
(167, 'Eye Lubricant (Low)', 'Carboxymethylcellulose', 'Drops (Eye/Ear/Nasal)', 70.00, 5, 20, '2026-11-18', 9, '2026-02-03 06:25:45'),
(168, 'Antifungal Cream (Low)', 'Clotrimazole', 'Gel/Cream/Ointment', 60.00, 800, 20, '2026-09-10', 10, '2026-02-03 06:25:45'),
(169, 'Pain Relief Gel (Low)', 'Menthol', 'Gel/Cream/Ointment', 45.00, 9, 25, '2026-12-01', 11, '2026-02-03 06:25:45'),
(172, 'Children Fever Syrup (Low)', 'Paracetamol', 'Syrup', 50.00, 10, 30, '2026-10-18', 14, '2026-02-03 06:25:45'),
(173, 'Electrolyte Solution (Low)', 'Oral Rehydration Salts', 'Syrup', 35.00, 12, 40, '2026-08-30', 15, '2026-02-03 06:25:45'),
(176, 'Ibuprofen 400mg (Soon)', 'Ibuprofen', 'Tablet', 3.00, 80, 40, '2026-02-23', 8, '2026-02-03 06:27:57'),
(179, 'Vitamin C Syrup (Soon)', 'Ascorbic Acid', 'Syrup', 45.00, 50, 20, '2026-03-05', 11, '2026-02-03 06:27:57'),
(180, 'Cough Syrup DM (Soon)', 'Dextromethorphan', 'Syrup', 60.00, 40, 25, '2026-03-03', 12, '2026-02-03 06:27:57'),
(181, 'Antiseptic Cream (Soon)', 'Povidone Iodine', 'Gel/Cream/Ointment', 35.00, 35, 15, '2026-02-21', 13, '2026-02-03 06:27:57'),
(182, 'Eye Drops Clear (Soon)', 'Hypromellose', 'Drops (Eye/Ear/Nasal)', 50.00, 45, 20, '2026-02-25', 14, '2026-02-03 06:27:57'),
(183, 'Insulin Regular (Soon)', 'Human Insulin', 'Injection (Vial/Ampoule)', 180.00, 25, 10, '2026-03-15', 15, '2026-02-03 06:27:57'),
(184, 'Salbutamol Inhaler (Soon)', 'Salbutamol', 'Inhaler', 120.00, 30, 20, '2026-03-02', 6, '2026-02-03 06:27:57'),
(185, 'Diclofenac Gel (Soon)', 'Diclofenac', 'Gel/Cream/Ointment', 65.00, 55, 25, '2026-03-25', 7, '2026-02-03 06:27:57'),
(186, 'Antacid Syrup', 'Aluminium Hydroxide', 'Syrup', 40.00, 50, 30, '2026-03-08', 8, '2026-02-03 06:27:57'),
(187, 'Metformin 500mg (Soon)', 'Metformin', 'Tablet', 2.50, 85, 50, '2026-03-17', 9, '2026-02-03 06:27:57'),
(188, 'Aspirin 300mg (Soon)', 'Aspirin', 'Tablet', 1.20, 75, 30, '2026-02-22', 10, '2026-02-03 06:27:57'),
(189, 'Hydrocortisone Cream (Soon)', 'Hydrocortisone', 'Gel/Cream/Ointment', 55.00, 40, 20, '2026-02-27', 11, '2026-02-03 06:27:57'),
(190, 'Nasal Spray (Soon)', 'Oxymetazoline', 'Drops (Eye/Ear/Nasal)', 85.00, 35, 15, '2026-03-30', 12, '2026-02-03 06:27:57'),
(191, 'Ceftriaxone Injection (Soon)', 'Ceftriaxone', 'Injection (Vial/Ampoule)', 90.00, 28, 20, '2026-03-13', 13, '2026-02-03 06:27:57'),
(192, 'Fever Suppository (Soon)', 'Paracetamol', 'Suppository', 25.00, 45, 20, '2026-02-24', 14, '2026-02-03 06:27:57'),
(193, 'Vitamin B Complex (Soon)', 'Vitamin B', 'Tablet', 3.50, 55, 25, '2026-03-23', 15, '2026-02-03 06:27:57'),
(194, 'Losartan 50mg (Soon)', 'Losartan', 'Tablet', 3.50, 55, 40, '2026-03-04', 6, '2026-02-03 06:27:57'),
(196, 'Multivitamin Syrup (Soon)', 'Multivitamin', 'Syrup', 65.00, 48, 25, '2026-03-01', 8, '2026-02-03 06:27:57'),
(197, 'Eye Lubricant (Soon)', 'Carboxymethylcellulose', 'Drops (Eye/Ear/Nasal)', 70.00, 42, 20, '2026-03-27', 9, '2026-02-03 06:27:57'),
(198, 'Antifungal Cream (Soon)', 'Clotrimazole', 'Gel/Cream/Ointment', 60.00, 38, 20, '2026-03-16', 10, '2026-02-03 06:27:57'),
(199, 'Pain Relief Gel (Soon)', 'Menthol', 'Gel/Cream/Ointment', 45.00, 58, 25, '2026-03-11', 11, '2026-02-03 06:27:57'),
(200, 'Asthma Preventer Inhaler (Soon)', 'Budesonide', 'Inhaler', 190.00, 32, 20, '2026-02-26', 12, '2026-02-03 06:27:57'),
(201, 'Insulin Aspart (Soon)', 'Insulin', 'Injection (Vial/Ampoule)', 320.00, 2200, 15, '2026-03-22', 13, '2026-02-03 06:27:57'),
(202, 'Children Fever Syrup (Soon)', 'Paracetamol', 'Syrup', 50.00, 70, 30, '2026-03-06', 14, '2026-02-03 06:27:57'),
(203, 'Electrolyte Solution (Soon)', 'Oral Rehydration Salts', 'Syrup', 35.00, 90, 40, '2026-04-03', 15, '2026-02-03 06:27:57'),
(204, 'Paracetamol 325mg', 'Paracetamol', 'Injection (Vial/Ampoule)', 1.20, 1000, 40, '2024-09-15', 6, '2026-02-03 06:31:56'),
(206, 'Cetirizine Syrup', 'Cetirizine', 'Syrup', 55.00, 1000, 20, '2024-08-25', 8, '2026-02-03 06:31:56'),
(207, 'Ibuprofen Gel', 'Ibuprofen', 'Gel/Cream/Ointment', 60.00, 1000, 15, '2024-09-05', 9, '2026-02-03 06:31:56'),
(208, 'Metformin 500mg', 'Metformin', 'Tablet', 2.50, 1200, 50, '2026-12-10', 14, '2026-02-03 06:31:56'),
(209, 'Cefixime Capsule', 'Cefixime', 'Capsule', 6.00, 6, 30, '2026-09-30', 15, '2026-02-03 06:31:56'),
(212, 'Asthma Inhaler', 'Salbutamol', 'Inhaler', 120.00, 1000, 20, '2024-09-20', 12, '2026-02-03 06:31:56'),
(227, 'Budesonide Inhaler', 'Budesonide', 'Inhaler', 190.00, 12, 20, '2026-03-30', 10, '2026-02-03 06:31:56'),
(228, 'Skin Ointment Zinc', 'Zinc Oxide', 'Gel/Cream/Ointment', 40.00, 1000, 20, '2027-10-01', 13, '2026-02-03 06:31:56'),
(230, 'test', 'test', 'Tablet', 100.00, 1000, 20, '2026-02-28', 6, '2026-02-05 03:34:13');

-- ================================================
-- TABLE: `orders`
-- ================================================
-- Purpose: Store customer medicine orders and prescriptions
-- Key Fields:
--   - order_id: Primary key, unique order identifier
--   - customer_id: Foreign key to customers table
--   - staff_id: Foreign key to users table (staff member processing order)
--   - pharmacist_id: Foreign key to users table (pharmacist reviewing order)
--   - allergy_notes: Customer's known allergies
--   - pharmacist_instructions: Special instructions for dispensing
--   - total_amount: Total order value
--   - order_status: Status (pending, approved, rejected, completed)
--   - prescription_note: Doctor's prescription notes
--   - order_date: When order was created
-- Relationships:
--   - Links to customers (who ordered)
--   - Links to users (staff & pharmacist responsible)
--   - Related to order_items (medicines in order)
-- ================================================

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL COMMENT 'Unique order identifier',
  `customer_id` int(11) NOT NULL COMMENT 'Foreign key: customer placing order',
  `staff_id` int(11) NOT NULL COMMENT 'Foreign key: staff member processing order',
  `pharmacist_id` int(11) DEFAULT NULL COMMENT 'Foreign key: pharmacist approving order',
  `allergy_notes` text DEFAULT NULL COMMENT 'Customer allergy information',
  `pharmacist_instructions` text DEFAULT NULL COMMENT 'Special instructions for pharmacist',
  `total_amount` decimal(10,2) DEFAULT NULL COMMENT 'Total order value in currency',
  `order_status` enum('pending','approved','rejected','completed') DEFAULT 'pending' COMMENT 'Current status of the order',
  `prescription_note` text DEFAULT NULL COMMENT 'Prescription details from doctor',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Order creation timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `staff_id`, `pharmacist_id`, `allergy_notes`, `pharmacist_instructions`, `total_amount`, `order_status`, `prescription_note`, `order_date`) VALUES
(54, 33, 24, 25, '', '', 0.00, 'rejected', 'General check', '2026-02-03 06:42:08'),
(55, 34, 24, 25, 'Penicillin', 'Take after meal', 0.00, 'completed', 'Cold treatment', '2026-02-03 06:42:08'),
(56, 35, 24, 25, NULL, 'Out of stock item', 0.00, 'rejected', 'Fever', '2026-02-03 06:42:08'),
(57, 36, 24, 25, 'Seafood', 'Complete full course', 0.00, 'completed', 'Infection', '2026-02-03 06:42:08'),
(58, 37, 24, 25, '', '', 0.00, 'rejected', 'Cough', '2026-02-03 06:42:08'),
(59, 38, 24, 25, NULL, 'Use inhaler daily', 0.00, 'completed', 'Asthma', '2026-02-03 06:42:08'),
(60, 39, 24, 25, 'Aspirin', 'Allergy risk', 0.00, 'rejected', 'Pain relief', '2026-02-03 06:42:08'),
(61, 40, 24, 25, NULL, 'Monitor sugar level', 0.00, 'completed', 'Diabetes', '2026-02-03 06:42:08'),
(62, 41, 24, 25, '', '', 0.00, 'rejected', 'Vitamin supply', '2026-02-03 06:42:08'),
(63, 42, 24, 25, NULL, 'Finish syrup in 5 days', 0.00, 'completed', 'Flu', '2026-02-03 06:42:08'),
(64, 34, 24, 25, 'Penicillin ', 'Take three times a day after meal ', 63.00, 'completed', 'Dr. John', '2026-02-03 08:27:25'),
(65, 42, 24, 25, '', '', 41.00, 'approved', 'cold treatment ', '2026-02-03 09:16:19'),
(66, 42, 24, 25, 'No known allergy', 'Take once a day before meal ', 3.50, 'completed', 'No prescription', '2026-02-03 09:46:06'),
(67, 41, 24, 25, 'Penicillin', 'Drink 3 times a day before meal ', 134.80, 'approved', 'No prescription', '2026-02-03 14:07:38'),
(68, 43, 24, 25, '', '', 3400.00, 'approved', 'dr. john', '2026-02-03 15:56:23'),
(69, 42, 24, 25, 'No Known Allergy', 'Drink Before Meal', 108.00, 'approved', 'Dr. John', '2026-02-04 12:29:02'),
(70, 41, 24, NULL, NULL, NULL, 406.80, 'pending', 'No prescription', '2026-02-05 02:11:24'),
(71, 39, 24, NULL, NULL, NULL, 1040.00, 'pending', 'Dr. John ', '2026-02-05 02:12:04'),
(72, 44, 24, 25, 'no known allergy', 'take after meal', 28.00, 'completed', 'no prescripiton', '2026-02-05 03:35:27');

-- ================================================
-- TABLE: `order_items`
-- ================================================
-- Purpose: Store individual medicines in each order (junction table)
-- Key Fields:
--   - order_id: Foreign key to orders table
--   - medicine_id: Foreign key to medicines table
--   - quantity: Number of units ordered
--   - price: Price per unit at time of order
-- Note: Composite primary key (order_id, medicine_id)
-- This is a junction table connecting orders to medicines
-- ================================================

CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL COMMENT 'Foreign key: which order this item belongs to',
  `medicine_id` int(11) NOT NULL COMMENT 'Foreign key: which medicine is ordered',
  `quantity` int(11) NOT NULL COMMENT 'Number of units ordered',
  `price` decimal(10,2) NOT NULL COMMENT 'Price per unit (captured at time of order)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_id`, `medicine_id`, `quantity`, `price`) VALUES
(64, 124, 10, 2.80),
(64, 193, 10, 3.50),
(65, 116, 10, 3.50),
(65, 129, 5, 1.20),
(66, 116, 1, 3.50),
(67, 124, 1, 2.80),
(67, 145, 3, 4.00),
(67, 156, 3, 40.00),
(68, 122, 10, 150.00),
(68, 227, 10, 190.00),
(69, 124, 10, 2.80),
(69, 145, 20, 4.00),
(70, 106, 10, 40.00),
(70, 124, 1, 2.80),
(70, 145, 1, 4.00),
(71, 132, 10, 60.00),
(71, 145, 10, 4.00),
(71, 186, 10, 40.00),
(72, 124, 10, 2.80);

-- ================================================
-- TABLE: `order_items_backup`
-- ================================================
-- Purpose: Backup table for historical order items (no longer active)
-- Key Fields: Same as order_items, with order_item_id
-- Note: This is a legacy/archive table for data retention
-- ================================================

--
-- Dumping data for table `order_items_backup`
--

INSERT INTO `order_items_backup` (`order_item_id`, `order_id`, `medicine_id`, `quantity`, `price`) VALUES
(33, 28, 39, 100, 5.00),
(36, 29, 40, 10, 45.00),
(38, 30, 44, 100, 10.00),
(39, 31, 59, 50, 15.00),
(40, 32, 48, 100, 1.00),
(42, 34, 47, 10, 9.50),
(43, 35, 55, 1, 250.00),
(50, 40, 40, 10, 45.00),
(51, 41, 39, 5, 5.00),
(53, 41, 40, 5, 45.00),
(54, 42, 39, 5, 5.00),
(55, 42, 48, 5, 1.00),
(58, 44, 39, 10, 5.00),
(60, 45, 44, 20, 10.00),
(61, 46, 39, 1, 5.00),
(67, 50, 64, 1, 2.50),
(68, 50, 38, 10, 1.50),
(70, 51, 60, 1, 4.00),
(72, 52, 55, 10, 250.00),
(73, 53, 72, 10, 1000.00);

-- ================================================
-- TABLE: `suppliers`
-- ================================================
-- Purpose: Store medicine supplier/vendor information
-- Key Fields:
--   - supplier_id: Primary key, unique supplier identifier
--   - supplier_name: Company/supplier name
--   - contact_number: Phone number for supplier communication
--   - created_at: Record creation date
-- Relationships:
--   - Referenced by medicines table (many medicines per supplier)
-- ================================================

CREATE TABLE `suppliers` (
  `supplier_id` int(1) NOT NULL COMMENT 'Unique supplier identifier',
  `supplier_name` varchar(100) NOT NULL COMMENT 'Supplier/vendor company name',
  `contact_number` varchar(20) DEFAULT NULL COMMENT 'Supplier contact phone number',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_number`, `created_at`) VALUES
(6, 'Bangkok Pharma Supply Co., Ltd.', '02-245-8899', '2026-02-03 06:12:59'),
(7, 'Siam Medical Distribution', '02-613-4421', '2026-02-03 06:12:59'),
(8, 'Thai HealthCare Wholesale', '02-718-9033', '2026-02-03 06:12:59'),
(9, 'Chao Phraya Drug Supplier', '02-437-5566', '2026-02-03 06:12:59'),
(10, 'MedTech Bangkok Co., Ltd.', '02-102-7788', '2026-02-03 06:12:59'),
(11, 'Krungthep Pharmaceutical Trading', '02-691-2345', '2026-02-03 06:12:59'),
(12, 'Asia Med Supply (Thailand)', '02-381-9900', '2026-02-03 06:12:59'),
(13, 'BKK Hospital Drug Distributor', '02-580-1122', '2026-02-03 06:12:59'),
(14, 'Golden Life Pharmacy Supplier', '02-948-6677', '2026-02-03 06:12:59'),
(15, 'Central Thailand Medical Supply', '02-215-4433', '2026-02-03 06:12:59');

-- ================================================
-- TABLE: `users`
-- ================================================
-- Purpose: Store system users (admin, staff, pharmacist)
-- Key Fields:
--   - user_id: Primary key, unique user identifier
--   - username: Login username (unique)
--   - password: Encrypted password
--   - role: User role type (admin, staff, pharmacist)
--   - contact_number: User's phone number
--   - created_at: Account creation date
-- Roles:
--   - admin: Full system access
--   - staff: Create orders and manage customers
--   - pharmacist: Review and approve orders
-- Relationships:
--   - Referenced by orders table (staff_id, pharmacist_id)
-- ================================================

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL COMMENT 'Unique user identifier',
  `username` varchar(50) NOT NULL COMMENT 'Login username (unique)',
  `password` varchar(255) NOT NULL COMMENT 'Encrypted password',
  `role` enum('admin','staff','pharmacist') NOT NULL COMMENT 'User role: admin, staff, or pharmacist',
  `contact_number` varchar(20) DEFAULT NULL COMMENT 'User contact number',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Account creation date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `contact_number`, `created_at`) VALUES
(23, 'admin', '123', 'admin', NULL, '2026-02-03 06:00:52'),
(24, 'hello', '123', 'staff', NULL, '2026-02-03 06:09:08'),
(25, 'hi', '123', 'pharmacist', NULL, '2026-02-03 06:09:18'),
(26, 'staff2', '123', 'staff', NULL, '2026-02-03 15:54:21');

--

-- ================================================
-- INDEXES & CONSTRAINTS
-- ================================================
-- Primary Keys: Uniquely identify each record
-- Foreign Keys: Enforce referential integrity
-- Additional Keys: Improve query performance
-- ================================================

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`) COMMENT 'Primary key for customers';

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`) COMMENT 'Primary key for medicines',
  ADD KEY `supplier_id` (`supplier_id`) COMMENT 'Index for supplier lookups';

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`) COMMENT 'Primary key for orders',
  ADD KEY `orders_customer_fk` (`customer_id`) COMMENT 'Index for customer lookups',
  ADD KEY `orders_ibfk_2` (`staff_id`) COMMENT 'Index for staff lookups',
  ADD KEY `orders_ibfk_3` (`pharmacist_id`) COMMENT 'Index for pharmacist lookups';

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_id`,`medicine_id`) COMMENT 'Composite key linking orders and medicines',
  ADD KEY `medicine_id` (`medicine_id`) COMMENT 'Index for medicine lookups';

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

-- ================================================
-- AUTO_INCREMENT SEQUENCES
-- ================================================
-- Starting values for auto-generated IDs

ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Next customer_id will be 45', AUTO_INCREMENT=45;

ALTER TABLE `medicines`
  MODIFY `medicine_id` int(1) NOT NULL AUTO_INCREMENT COMMENT 'Next medicine_id will be 231', AUTO_INCREMENT=231;

ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Next order_id will be 73', AUTO_INCREMENT=73;

ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(1) NOT NULL AUTO_INCREMENT COMMENT 'Next supplier_id will be 17', AUTO_INCREMENT=17;

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Next user_id will be 28', AUTO_INCREMENT=28;

-- ================================================
-- FOREIGN KEY CONSTRAINTS
-- ================================================
-- Enforce referential integrity between tables
-- Define cascade behavior for updates and deletes
-- ================================================

--
-- Constraints for table `medicines`
-- Links medicines to their suppliers
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `orders`
-- Links orders to customers, staff, and pharmacists
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`pharmacist_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
-- Links order items to their medicines and orders
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`),
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
