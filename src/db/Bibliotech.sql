CREATE DATABASE IF NOT EXISTS bibliotech
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;


USE Bibliotech;

--===============================
-- USERS TABLE
--===============================

CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

--===============================
-- CATEGORIES TABLE
--===============================

CREATE TABLE IF NOT EXISTS Categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);


--===============================
-- BOOKS TABLE
--===============================

CREATE TABLE IF NOT EXISTS Books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(50) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    publication_year YEAR NOT NULL,
    category_id INT NOT NULL,
    copies_number INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    CONSTRAINT fk_category
     FOREIGN KEY (category_id)
     REFERENCES Categories(id)
    ON DELETE SET NULL
);

--===============================
-- LOANS TABLE
--===============================

CREATE TABLE IF NOT EXISTS Loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    CONSTRAINT fk_user
     FOREIGN KEY (user_id)
     REFERENCES Users(id)
    ON DELETE CASCADE,
    CONSTRAINT fk_book
     FOREIGN KEY (book_id)
     REFERENCES Books(id)
    ON DELETE CASCADE
);
