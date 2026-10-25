CREATE DATABASE IF NOT EXISTS capacitors_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE capacitors_db;

DROP TABLE IF EXISTS parameters;
DROP TABLE IF EXISTS capacitors;
DROP TABLE IF EXISTS capacitor_types;

CREATE TABLE capacitor_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE capacitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation VARCHAR(50) NOT NULL,
    type_id INT NOT NULL,
    capacitance_uf DECIMAL(12,6) NOT NULL,
    voltage_v DECIMAL(10,2) NOT NULL,
    photo VARCHAR(255) NULL,
    FOREIGN KEY (type_id) REFERENCES capacitor_types(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capacitor_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    value VARCHAR(255) NOT NULL,
    FOREIGN KEY (capacitor_id) REFERENCES capacitors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
