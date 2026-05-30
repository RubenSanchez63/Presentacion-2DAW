-- ============================================================
--  PDF Library  —  Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS pdf_library
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pdf_library;

-- ── Users ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS User (
    id           INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(255) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    name         VARCHAR(150) NOT NULL,
    role         ENUM('user', 'librarian', 'admin') NOT NULL DEFAULT 'user',
    createdAt    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin account (password: Admin1234)
INSERT INTO User (email, passwordHash, name, role) VALUES
('admin@library.com',
 '$2y$12$Ku.EZWsBf5LEMgeSXuP8UunhVYzSEuA2sIdCRNqnKbM6v7SY2ANQK',
 'Administrator', 'admin');

-- ── Categories ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Category (
    id          INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB;

INSERT INTO Category (name) VALUES
('Science Fiction'),
('History'),
('Technology'),
('Classic Literature'),
('Mathematics'),
('Law'),
('Medicine'),
('Art & Design');

-- ── Books ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS Book (
    id          INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    author      VARCHAR(255) NOT NULL,
    description TEXT,
    cover       VARCHAR(255) NOT NULL DEFAULT 'default.jpg',
    pdfPath     VARCHAR(255) NOT NULL,
    pages       SMALLINT UNSIGNED DEFAULT 0,
    category_id INT UNSIGNED NOT NULL,
    uploaded_by INT UNSIGNED NOT NULL,
    uploadedAt  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_book_category FOREIGN KEY (category_id) REFERENCES Category(id) ON DELETE RESTRICT,
    CONSTRAINT fk_book_user     FOREIGN KEY (uploaded_by) REFERENCES User(id)     ON DELETE CASCADE,
    INDEX idx_category    (category_id),
    INDEX idx_uploaded_by (uploaded_by)
) ENGINE=InnoDB;
