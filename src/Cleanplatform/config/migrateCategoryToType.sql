-- ==========================================================
--  Cleaning Platform  —  Migration Script: Merge Category into Type
--  run:  mysql -u root -p < migrateCategoryToType.sql
-- ==========================================================

USE CleanPlatform;

-- First, update cleaner_services table to ensure type value is meaningful
-- Combine category name with current type value if both exist
UPDATE cleaner_services cs
JOIN service_categories sc ON cs.category_id = sc.id
SET cs.type = CASE 
    WHEN cs.type IS NULL OR cs.type = '' THEN sc.name
    ELSE CONCAT(sc.name, ' - ', cs.type)
END
WHERE cs.category_id IS NOT NULL;

-- Now set any remaining NULL type values to 'General'
UPDATE cleaner_services 
SET type = 'General' 
WHERE type IS NULL OR type = '';

-- Alter table to make type NOT NULL once data is migrated
ALTER TABLE cleaner_services MODIFY COLUMN type VARCHAR(100) NOT NULL;

-- Remove the foreign key constraint referencing service_categories
ALTER TABLE cleaner_services DROP FOREIGN KEY cleaner_services_ibfk_2;

-- Drop the category_id column
ALTER TABLE cleaner_services DROP COLUMN category_id;

-- Finally, drop the service_categories table
DROP TABLE service_categories;

-- Show the updated schema
DESCRIBE cleaner_services; 