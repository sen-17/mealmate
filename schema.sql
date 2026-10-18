CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    address TEXT,
    role ENUM('Admin', 'Pelanggan') NOT NULL DEFAULT 'Pelanggan'
);

CREATE TABLE IF NOT EXISTS menus (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    menu_name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS packages (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    description TEXT,
    price INT NOT NULL,
    image VARCHAR(255),
    duration_days INT NOT NULL,
    applicable_days ENUM('Senin-Jumat', 'Senin-Minggu') NOT NULL
);

CREATE TABLE IF NOT EXISTS daily_menus (
    daily_menu_id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    date DATE NOT NULL,
    slot ENUM('Pagi', 'Siang', 'Malam') NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    start_date DATE NOT NULL,
    slot ENUM('Pagi', 'Siang', 'Malam') NOT NULL,
    delivery_address TEXT NOT NULL,
    notes TEXT,
    total_price INT NOT NULL,
    status ENUM('Menunggu Verifikasi', 'Aktif', 'Selesai', 'Dibatalkan', 'Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_proof VARCHAR(255) NOT NULL,
    status ENUM('Valid', 'Tidak Valid') DEFAULT NULL,
    rejection_reason TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id) ON DELETE CASCADE ON UPDATE CASCADE
);