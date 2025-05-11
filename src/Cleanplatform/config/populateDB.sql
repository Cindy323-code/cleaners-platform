-- ==========================================================
--  Cleaning Platform  —  Data Population Script
--  run:  mysql -u root -p < populateDB.sql
-- ==========================================================

USE CleanPlatform;

-- Disable foreign key checks temporarily for easier loading
SET FOREIGN_KEY_CHECKS = 0;

-- Clear tables to start fresh (using DELETE instead of TRUNCATE to avoid issues with foreign key constraints)
DELETE FROM service_stats;
DELETE FROM match_histories;
DELETE FROM shortlists;
DELETE FROM cleaner_services;
DELETE FROM user_profiles;
DELETE FROM users;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------
--  1. Populate Users (100 users per role)
-- ----------------------------------------------------------

-- Insert Admins (keeping the existing admin intact)
INSERT INTO users (username, password_hash, email, role, status)
VALUES 
('admin', '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE', 'admin@example.com', 'admin', 'active');

-- Generate 99 more admin users
INSERT INTO users (username, password_hash, email, role, status)
SELECT 
    CONCAT('admin', n), 
    '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE', 
    CONCAT('admin', n, '@example.com'),
    'admin',
    CASE WHEN n % 10 = 0 THEN 'suspended' ELSE 'active' END
FROM 
    (SELECT 1 + ones.n + 10 * tens.n AS n 
     FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
          (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
     WHERE 1 + ones.n + 10 * tens.n <= 99) numbers;

-- Generate 100 managers
INSERT INTO users (username, password_hash, email, role, status)
SELECT 
    CONCAT('manager', n), 
    '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE', 
    CONCAT('manager', n, '@example.com'),
    'manager',
    CASE WHEN n % 10 = 0 THEN 'suspended' ELSE 'active' END
FROM 
    (SELECT 1 + ones.n + 10 * tens.n AS n 
     FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
          (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
     WHERE 1 + ones.n + 10 * tens.n <= 100) numbers;

-- Generate 100 cleaners
INSERT INTO users (username, password_hash, email, role, status)
SELECT 
    CONCAT('cleaner', n), 
    '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE', 
    CONCAT('cleaner', n, '@example.com'),
    'cleaner',
    CASE WHEN n % 10 = 0 THEN 'suspended' ELSE 'active' END
FROM 
    (SELECT 1 + ones.n + 10 * tens.n AS n 
     FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
          (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
     WHERE 1 + ones.n + 10 * tens.n <= 100) numbers;

-- Generate 100 homeowners
INSERT INTO users (username, password_hash, email, role, status)
SELECT 
    CONCAT('homeowner', n), 
    '$2y$10$abcdEfghIjklMnopQrstUvwxYz0123456789ABCDEFXYZabcdE', 
    CONCAT('homeowner', n, '@example.com'),
    'homeowner',
    CASE WHEN n % 10 = 0 THEN 'suspended' ELSE 'active' END
FROM 
    (SELECT 1 + ones.n + 10 * tens.n AS n 
     FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
          (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
     WHERE 1 + ones.n + 10 * tens.n <= 100) numbers;

-- ----------------------------------------------------------
--  2. Populate User Profiles (for cleaners and homeowners)
-- ----------------------------------------------------------

-- Create profiles for cleaners
INSERT INTO user_profiles (user_id, full_name, avatar_url, bio, availability)
SELECT 
    id,
    CONCAT('Cleaner ', id, ' Name'),
    CONCAT('https://randomuser.me/api/portraits/', CASE WHEN id % 2 = 0 THEN 'women' ELSE 'men' END, '/', (id % 70) + 1, '.jpg'),
    CONCAT('Professional cleaner with ', (id % 10) + 1, ' years of experience in residential and commercial cleaning.'),
    CASE 
        WHEN id % 4 = 0 THEN 'Weekdays 9am-5pm'
        WHEN id % 4 = 1 THEN 'Weekends 10am-6pm'
        WHEN id % 4 = 2 THEN 'Evenings 6pm-10pm'
        ELSE 'Flexible hours'
    END
FROM 
    users
WHERE 
    role = 'cleaner';

-- Create profiles for homeowners
INSERT INTO user_profiles (user_id, full_name, avatar_url, bio, availability)
SELECT 
    id,
    CONCAT('Homeowner ', id, ' Name'),
    CONCAT('https://randomuser.me/api/portraits/', CASE WHEN id % 2 = 0 THEN 'women' ELSE 'men' END, '/', (id % 70) + 1, '.jpg'),
    CONCAT('Looking for reliable cleaning services for my ', 
        CASE 
            WHEN id % 5 = 0 THEN 'apartment'
            WHEN id % 5 = 1 THEN 'house'
            WHEN id % 5 = 2 THEN 'condo'
            WHEN id % 5 = 3 THEN 'office'
            ELSE 'vacation rental'
        END, 
        ' in the area.'),
    CASE 
        WHEN id % 3 = 0 THEN 'Weekdays preferred'
        WHEN id % 3 = 1 THEN 'Weekends only'
        ELSE 'Flexible schedule'
    END
FROM 
    users
WHERE 
    role = 'homeowner';

-- ----------------------------------------------------------
--  3. Define common service types for population
-- ----------------------------------------------------------

-- Create temporary table with service types for population
CREATE TEMPORARY TABLE service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- Insert service types
INSERT INTO service_types (name) VALUES
('Residential Cleaning'),
('Commercial Cleaning'),
('Deep Cleaning'),
('Post-Construction Cleaning'),
('Window Cleaning'),
('Carpet Cleaning'),
('Move-In/Move-Out Cleaning'),
('Appliance Cleaning'),
('Bathroom Sanitization'),
('Kitchen Cleaning');

-- Add 90 more service types to reach 100
INSERT INTO service_types (name)
SELECT 
    CONCAT('Specialty Service ', n)
FROM 
    (SELECT 1 + ones.n + 10 * tens.n AS n 
     FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) ones,
          (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) tens
     WHERE 1 + ones.n + 10 * tens.n <= 90) numbers;

-- ----------------------------------------------------------
--  4. Populate Cleaner Services (100 services)
-- ----------------------------------------------------------

-- Get cleaner IDs
CREATE TEMPORARY TABLE cleaner_ids AS
SELECT id FROM users WHERE role = 'cleaner';

-- Create services for each cleaner
INSERT INTO cleaner_services (user_id, name, type, price, description)
SELECT 
    c.id,
    CONCAT('Service ', n, ' by Cleaner ', c.id),
    (SELECT name FROM service_types WHERE id = (c.id % 100) + 1),
    CASE 
        WHEN n % 4 = 0 THEN 'Basic'
        WHEN n % 4 = 1 THEN 'Standard'
        WHEN n % 4 = 2 THEN 'Premium'
        ELSE 'Deluxe'
    END,
    50 + (n % 150), -- Price between $50 and $200
    CONCAT('Professional cleaning service offering ', 
        CASE 
            WHEN n % 5 = 0 THEN 'thorough deep cleaning'
            WHEN n % 5 = 1 THEN 'eco-friendly solutions'
            WHEN n % 5 = 2 THEN 'fast and efficient service'
            WHEN n % 5 = 3 THEN 'specialized equipment'
            ELSE 'customized cleaning packages'
        END, 
        ' for your needs.')
FROM 
    cleaner_ids c,
    (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) numbers
ORDER BY c.id, n
LIMIT 100;

-- ----------------------------------------------------------
--  5. Populate Shortlists (100 entries)
-- ----------------------------------------------------------

-- Generate shortlists (homeowners saving services they're interested in)
INSERT INTO shortlists (user_id, service_id)
SELECT 
    h.id, -- Homeowner ID
    s.id  -- Service ID
FROM 
    (SELECT id FROM users WHERE role = 'homeowner' LIMIT 50) h,
    (SELECT id FROM cleaner_services LIMIT 50) s
WHERE 
    (h.id + s.id) % 5 != 0 -- Some random condition to limit entries
LIMIT 100;

-- ----------------------------------------------------------
--  6. Populate Match Histories (100 bookings)
-- ----------------------------------------------------------

-- Create match histories (service bookings)
INSERT INTO match_histories (service_id, cleaner_id, homeowner_id, service_date, status, feedback, price_charged)
SELECT 
    s.id, -- Service ID
    u.id, -- Cleaner ID (owner of the service)
    h.id, -- Homeowner ID
    DATE_ADD(CURRENT_DATE, INTERVAL (s.id % 30) DAY), -- Service date within next 30 days
    CASE 
        WHEN s.id % 3 = 0 THEN 'confirmed'
        WHEN s.id % 3 = 1 THEN 'completed'
        ELSE 'cancelled'
    END,
    CASE 
        WHEN s.id % 3 = 1 THEN -- Only completed services have feedback
            CASE 
                WHEN s.id % 5 = 0 THEN 'Excellent service, very professional!'
                WHEN s.id % 5 = 1 THEN 'Good job, would recommend.'
                WHEN s.id % 5 = 2 THEN 'Service was okay, but could improve on timeliness.'
                WHEN s.id % 5 = 3 THEN 'Very satisfied with the cleaning service.'
                ELSE 'The cleaner was thorough and efficient.'
            END
        ELSE NULL
    END,
    CASE 
        WHEN s.id % 3 = 1 THEN s.price -- Only completed services have price charged
        ELSE NULL
    END
FROM 
    cleaner_services s
    JOIN users u ON s.user_id = u.id
    JOIN (SELECT id FROM users WHERE role = 'homeowner' LIMIT 100) h 
        ON (s.id + h.id) % 100 < 50 -- Some random condition to pair services with homeowners
LIMIT 100;

-- ----------------------------------------------------------
--  7. Populate Service Stats (all services)
-- ----------------------------------------------------------

-- Add stats for all services
INSERT INTO service_stats (service_id, view_count, shortlist_count)
SELECT 
    id,
    FLOOR(RAND() * 1000), -- Random view count between 0-999
    FLOOR(RAND() * 50)    -- Random shortlist count between 0-49
FROM 
    cleaner_services;

-- ----------------------------------------------------------
--  Complete - Database is now populated with test data
-- ----------------------------------------------------------

-- Drop temporary tables
DROP TEMPORARY TABLE IF EXISTS cleaner_ids;
DROP TEMPORARY TABLE IF EXISTS service_types;