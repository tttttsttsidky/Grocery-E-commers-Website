CREATE DATABASE IF NOT EXISTS natural_basket;
USE natural_basket;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10, 2),
    weight VARCHAR(50),
    image VARCHAR(500),
    stock INT DEFAULT 100,
    description TEXT,
    farmer_name VARCHAR(100),
    farmer_loc VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    total_amount DECIMAL(10, 2),
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    payment_method VARCHAR(50),
    status VARCHAR(50) DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    product_name VARCHAR(255),
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    user_name VARCHAR(100),
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image VARCHAR(500),
    content TEXT,
    author VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS coupons (
    code VARCHAR(50) PRIMARY KEY,
    discount_percent INT,
    status ENUM('active', 'expired') DEFAULT 'active'
);

INSERT INTO users (name, email, password, role) VALUES 
('Super Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO products (name, category, price, weight, image, description, farmer_name, farmer_loc) VALUES
('সুন্দরবনের প্রাকৃতিক মধু', 'Honey', 850, '৫০০ গ্রাম', 'https://placehold.co/400x400/orange/white?text=Honey', 'সুন্দরবনের খলিসা ফুলের ১০০% প্রাকৃতিক মধু।', 'মৌয়াল জব্বার', 'সাতক্ষীরা'),
('কাঠের ঘানি ভাঙা সরিষার তেল', 'Oil', 320, '১ লিটার', 'https://placehold.co/400x400/gold/white?text=Mustard+Oil', 'দেশি মাঘী সরিষা থেকে কাঠের ঘানিতে ভাঙানো খাঁটি তেল।', 'রহিম চাচা', 'মাগুরা'),
('দিনাজপুরের কাটারিভোগ চাল', 'Grocery', 110, '১ কেজি', 'https://placehold.co/400x400/white/black?text=Rice', 'সুগন্ধি এবং সরু চাল।', 'কৃষক সমবায়', 'দিনাজপুর'),
('রাজশাহীর হিমসাগর আম', 'Fruits', 120, '১ কেজি', 'https://placehold.co/400x400/yellow/green?text=Mango', 'ফরমালিন মুক্ত বাগানের আম।', 'আফজাল হোসেন', 'রাজশাহী');

INSERT INTO coupons (code, discount_percent) VALUES ('SAVE10', 10), ('NATURAL20', 20);

INSERT INTO blogs (title, image, content, author) VALUES 
('অর্গানিক খাদ্যের উপকারিতা', 'https://placehold.co/600x400/green/white?text=Organic+Blog', 'অর্গানিক খাবার শরীরের রোগ প্রতিরোধ ক্ষমতা বাড়ায়...', 'ড. পুষ্টিবিদ'),
('মধু চেনার উপায়', 'https://placehold.co/600x400/orange/white?text=Honey+Blog', 'খাঁটি মধু চেনার সহজ উপায় হলো...', 'ন্যাচারাল টিম');