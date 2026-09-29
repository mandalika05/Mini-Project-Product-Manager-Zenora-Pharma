-- Database Zenora Pharma Smart Pharmacy (Product Manager) - Praktikum 3 Pemrograman Web
CREATE DATABASE IF NOT EXISTS store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE store_db;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  category VARCHAR(50) NOT NULL DEFAULT 'Obat Bebas',
  price DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, price, stock) VALUES
('Paracetamol 500 mg (Strip)', 'Obat Bebas', 5000, 120),
('Promag Tablet (Strip)', 'Obat Bebas', 9000, 80),
('OBH Combi Batuk Flu 100 ml', 'Obat Bebas Terbatas', 18500, 35),
('Bodrexin Anak Sirup', 'Obat Bebas Terbatas', 14000, 8),
('Amoxicillin 500 mg (Strip)', 'Obat Keras', 22000, 60),
('Vitamin C 500 mg (Botol 30)', 'Vitamin & Suplemen', 35000, 45),
('Multivitamin Anak Sirup', 'Vitamin & Suplemen', 48000, 0),
('Termometer Digital', 'Alat Kesehatan', 45000, 15),
('Masker Medis 3 Ply (Box 50)', 'Alat Kesehatan', 30000, 70),
('Minyak Kayu Putih 60 ml', 'Perawatan Tubuh', 16000, 9);
