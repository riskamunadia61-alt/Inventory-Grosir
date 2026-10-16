CREATE DATABASE IF NOT EXISTS grosir_db;
USE grosir_db;

-- 1. Tabel Users (Admin & Staff Gudang Grosir)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Categories Grosir
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- 3. Tabel Products Grosir
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    unit_type VARCHAR(30) NOT NULL DEFAULT 'Karton', -- Contoh: Karton, Dus, Bal, Pack
    price_per_unit DECIMAL(12,2) NOT NULL DEFAULT 0, -- Harga Grosir per Karton/Dus
    min_order_qty INT NOT NULL DEFAULT 1,            -- MOQ (Minimum Order Quantity)
    stock INT NOT NULL DEFAULT 0,                    -- Stok dalam Satuan Karton/Dus
    minimum_stock INT NOT NULL DEFAULT 10,           -- Batas Minimum Alert Stok Gudang
    FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- 4. Tabel Suppliers / Distributor Grosir
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT
);

-- 5. Tabel Junction Product-Supplier (Many-to-Many Relational)
CREATE TABLE product_suppliers (
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supply_price DECIMAL(12,2),
    PRIMARY KEY (product_id, supplier_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

-- 6. Tabel Stock Movements (Histori Barang Masuk/Keluar Grosir)
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL, -- Jumlah Karton/Dus
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Dummy Data Default (Password: "admin123" & "staff123")
INSERT INTO users (username, password_hash, role) VALUES 
('admin_grosir', '$2y$10$w0sD.1kQ8jT/.1J5Qv1z1.jH2l8Z9p9eP/8.8A7k4E8K5j21O1K2G', 'admin'),
('staff_gudang', '$2y$10$w0sD.1kQ8jT/.1J5Qv1z1.jH2l8Z9p9eP/8.8A7k4E8K5j21O1K2G', 'staff');

INSERT INTO categories (name) VALUES ('Sembako Grosir'), ('Minuman Kemasan'), ('Bumbu & Bahan Dapur');

INSERT INTO products (category_id, sku, name, unit_type, price_per_unit, min_order_qty, stock, minimum_stock) VALUES
(1, 'GROS-MIGOR-01', 'Minyak Goreng 2L (Isi 6 Pouch)', 'Karton', 180000, 5, 50, 10),
(2, 'GROS-AMDK-01', 'Air Mineral 600ml (Isi 24 Botol)', 'Dus', 42000, 10, 8, 15),
(1, 'GROS-GULA-01', 'Gula Pasir Kristal 1kg (Isi 20 Pack)', 'Karton', 310000, 2, 25, 5);