-- ==========================================================
--  Cleaning Platform  —  Schema DDL
--  run:  mysql -u root -p < create_cleaning_platform.sql
-- ==========================================================

DROP DATABASE IF EXISTS CleanPlatform;
CREATE DATABASE CleanPlatform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE CleanPlatform;

-- ----------------------------------------------------------
--  1.  User Accounts  (admin, cleaner, homeowner, manager)
-- ----------------------------------------------------------
CREATE TABLE admin_users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email         VARCHAR(100) NOT NULL,
    role          ENUM('admin','manager') DEFAULT 'admin',
    status        ENUM('active','suspended') DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE cleaners (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email         VARCHAR(100),
    role          VARCHAR(50) DEFAULT 'cleaner',
    status        ENUM('active','suspended') DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE homeowners (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email         VARCHAR(100),
    role          VARCHAR(50) DEFAULT 'homeowner',
    status        ENUM('active','suspended') DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  2.  Extended User Profiles (for cleaner / homeowner)
-- ----------------------------------------------------------
CREATE TABLE user_profiles (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    user_type     ENUM('cleaner','homeowner') NOT NULL,
    full_name     VARCHAR(100),
    avatar_url    VARCHAR(255),
    bio           TEXT,
    availability  VARCHAR(100),
    status        ENUM('active','inactive') DEFAULT 'active',
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_profile (user_id, user_type)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  3.  Service Categories (managed by platform manager)
-- ----------------------------------------------------------
CREATE TABLE service_categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  4.  Cleaner Services
-- ----------------------------------------------------------
CREATE TABLE cleaner_services (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    cleaner_id    INT NOT NULL,
    category_id   INT,
    name          VARCHAR(100) NOT NULL,
    type          VARCHAR(100),
    price         DECIMAL(10,2) NOT NULL,
    description   TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cleaner_id)  REFERENCES cleaners(id)   ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    FULLTEXT KEY ft_service_search (name, description)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  5.  Shortlist  (homeowners ♥ services)
-- ----------------------------------------------------------
CREATE TABLE shortlists (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    homeowner_id  INT NOT NULL,
    service_id    INT NOT NULL,
    added_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_shortlist (homeowner_id, service_id),
    FOREIGN KEY (homeowner_id) REFERENCES homeowners(id)       ON DELETE CASCADE,
    FOREIGN KEY (service_id)   REFERENCES cleaner_services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  6.  Match History (service bookings / completed jobs)
-- ----------------------------------------------------------
CREATE TABLE match_histories (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    service_id    INT NOT NULL,
    cleaner_id    INT NOT NULL,
    homeowner_id  INT NOT NULL,
    service_date  DATE NOT NULL,
    status        ENUM('confirmed','completed','cancelled') DEFAULT 'confirmed',
    feedback      TEXT,
    price_charged DECIMAL(10,2),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id)   REFERENCES cleaner_services(id),
    FOREIGN KEY (cleaner_id)   REFERENCES cleaners(id),
    FOREIGN KEY (homeowner_id) REFERENCES homeowners(id),
    INDEX idx_history_cleaner  (cleaner_id, service_date),
    INDEX idx_history_owner    (homeowner_id, service_date)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  7.  Simple View Counters  (profile & shortlist stats)
-- ----------------------------------------------------------
CREATE TABLE service_stats (
    service_id        INT PRIMARY KEY,
    view_count        INT DEFAULT 0,
    shortlist_count   INT DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES cleaner_services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO admin_users (username,password_hash,email,role,status)
VALUES ('admin',
        '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE',
        'admin@example.com',
        'admin',
        'active');

