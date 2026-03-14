CREATE DATABASE IF NOT EXISTS online_products CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_products;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_products_name (name),
    INDEX idx_products_category_id (category_id)
);

INSERT INTO admins (name, email, password)
SELECT 'Admin User', 'admin@example.com', '$2y$12$JGSZtFJ8fbFsyHchs1Nz.OqiUy2z3HRpuoje4HT1lsrHgpOeuOzsq'
WHERE NOT EXISTS (
    SELECT 1 FROM admins WHERE email = 'admin@example.com'
);

INSERT INTO categories (name)
SELECT 'Laptops' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Laptops');
INSERT INTO categories (name)
SELECT 'Phones' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Phones');
INSERT INTO categories (name)
SELECT 'Accessories' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Accessories');
