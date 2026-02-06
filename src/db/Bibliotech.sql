CREATE DATABASE IF NOT EXISTS bibliotech
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bibliotech;

--===============================
-- users table
--===============================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    passwords VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


--===============================
-- import table
--===============================
CREATE TABLE IF NOT EXISTS books_import (
    inventory_number VARCHAR(50),
    author VARCHAR(100),
    title VARCHAR(150),
    publisher VARCHAR(100),
    category VARCHAR(50)
);


--===============================
-- categories table
--===============================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE
);

--===============================
-- books table
--===============================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_number VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(50) NOT NULL,
    publisher VARCHAR(50) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    publication_year YEAR NOT NULL,
    category_id INT NOT NULL,
    copies_number INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    CONSTRAINT fk_category
    FOREIGN KEY (category_id)
    REFERENCES categories (id)
    ON DELETE RESTRICT
);


--===============================
-- bridge table for books and categories (many-to-many)
--===============================
CREATE TABLE IF NOT EXISTS book_categories (
    book_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (book_id, category_id),
    CONSTRAINT fk_book_bridge
    FOREIGN KEY (book_id)
    REFERENCES books (id)
    ON DELETE CASCADE,
    CONSTRAINT fk_category_bridge
    FOREIGN KEY (category_id)
    REFERENCES categories (id)
    ON DELETE CASCADE
);

--===============================
-- loans table
--===============================
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    CONSTRAINT fk_user
    FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON DELETE CASCADE,
    CONSTRAINT fk_book
    FOREIGN KEY (book_id)
    REFERENCES books (id)
    ON DELETE CASCADE
);
