-- Pawfect Pet Shop Database Schema
-- No date fields as requested

CREATE DATABASE IF NOT EXISTS pawfect_db;
USE pawfect_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avatar TEXT NOT NULL DEFAULT '/uploads/placeholder.png',
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    is_banned BOOLEAN DEFAULT FALSE
);

-- Pets table
CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    pet_image TEXT NOT NULL DEFAULT '/uploads/placeholder.png',
    is_adopted BOOLEAN DEFAULT FALSE,
    type ENUM('cats', 'dogs') NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    age INT NOT NULL,
    breed VARCHAR(100),
    adopted_by_user_id INT NULL,
    FOREIGN KEY (adopted_by_user_id) REFERENCES users(id)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    product_image TEXT NOT NULL DEFAULT '/uploads/placeholder.png',
    stock_quantity INT DEFAULT 0,
    type ENUM('accessories', 'foods') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    is_archived BOOLEAN DEFAULT FALSE
);

-- Create delivery_addresses table
CREATE TABLE IF NOT EXISTS delivery_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    street VARCHAR(255) NOT NULL,
    zipcode VARCHAR(20) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    shipped_date DATETIME NULL,
    delivered_date DATETIME NULL,
    payment_method ENUM('COD', 'GCASH') DEFAULT 'COD',
    delivery_address_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (delivery_address_id) REFERENCES delivery_addresses(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Shipments table
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    shipped_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    tracking_number VARCHAR(100),
    carrier VARCHAR(100),
    status VARCHAR(50) DEFAULT 'shipped',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Settings table for admin customization
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT
);

-- Insert default admin user password 123123123
INSERT INTO users (first_name, last_name, email, password, role) 
VALUES ('Admin', 'User', 'admin@pawfect.com', '$2y$10$obSMKhiOl.UZH4ThQCOjs.KScyb8yeW1olywqOlMyi.KnCa/6cmEW', 'admin');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('site_logo', 'http://localhost/Pawfect/public/uploads/logo/6834bad72c5c5_PawfectPetShopLogo.jpg'),
('primary_color', '#FF8C00'),
('secondary_color', '#FFD700');

-- Sample pets data
INSERT INTO pets (name, pet_image, type, gender, age, breed) VALUES
('Buddy', '/placeholder.svg?height=300&width=300', 'dogs', 'male', 2, 'Golden Retriever'),
('Luna', '/placeholder.svg?height=300&width=300', 'cats', 'female', 1, 'Persian'),
('Max', '/placeholder.svg?height=300&width=300', 'dogs', 'male', 3, 'German Shepherd'),
('Bella', '/placeholder.svg?height=300&width=300', 'cats', 'female', 2, 'Siamese');

-- Sample products data
INSERT INTO products (name, product_image, stock_quantity, type, price, description) VALUES
('Premium Dog Food', '/placeholder.svg?height=200&width=200', 50, 'foods', 29.99, 'High-quality nutrition for dogs'),
('Cat Toy Set', '/placeholder.svg?height=200&width=200', 25, 'accessories', 15.99, 'Interactive toys for cats'),
('Dog Leash', '/placeholder.svg?height=200&width=200', 30, 'accessories', 12.99, 'Durable and comfortable leash'),
('Cat Food Premium', '/placeholder.svg?height=200&width=200', 40, 'foods', 24.99, 'Nutritious food for cats');

-- Create uploads directory structure in database (for tracking)
CREATE TABLE IF NOT EXISTS uploads (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_entity` (`entity_type`, `entity_id`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

