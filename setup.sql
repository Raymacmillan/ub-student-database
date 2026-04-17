-- ================================================
-- UB Student Database System — Setup Script
-- Run this file before starting the application
-- ================================================

CREATE DATABASE IF NOT EXISTS lab_db;
USE lab_db;

-- Students table (matches Lab 5 Task 3 specification)
CREATE TABLE IF NOT EXISTS test (
    id   VARCHAR(15) PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample student data (10 students)
INSERT IGNORE INTO test (id, name) VALUES
('20010001', 'Ray Macmillan'),
('20010002', 'Tumelo Kgosi'),
('20010003', 'Mpho Ditlhong'),
('20010004', 'Keone Selato'),
('20010005', 'Boitumelo Nkwe'),
('20010006', 'Tshegofatso Moyo'),
('20010007', 'Dineo Sithole'),
('20010008', 'Kabelo Molefe'),
('20010009', 'Lesego Tau'),
('20010010', 'Onkemetse Rre');