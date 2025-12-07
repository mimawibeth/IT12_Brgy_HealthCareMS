-- =====================================================
-- BARANGAY HEALTH CARE MANAGEMENT SYSTEM - SEEDERS SQL
-- Generated: December 8, 2025
-- Run these queries in phpMyAdmin to populate your database
-- =====================================================

-- =====================================================
-- USERS TABLE
-- =====================================================
-- Default Login Credentials:
-- Super Admin: superadmin / Admin@123
-- Admin: admin / Admin@123  
-- BHW: worker / Admin@123

INSERT INTO `users` (`name`, `first_name`, `middle_name`, `last_name`, `username`, `email`, `password`, `role`, `contact_number`, `address`, `status`, `created_at`, `updated_at`) VALUES
('Super Administrator', 'Super', NULL, 'Administrator', 'superadmin', 'superadmin@bhc.com', '$2y$12$uuWkH/VSW/TkjKQigUyEOe9QU8LKuFHXksjZwWDoe4pF7BNnLwf0i', 'super_admin', '09170000001', 'Barangay Health Center Office', 'active', NOW(), NOW()),
('Admin User', 'Admin', NULL, 'User', 'admin', 'admin@bhc.com', '$2y$12$uuWkH/VSW/TkjKQigUyEOe9QU8LKuFHXksjZwWDoe4pF7BNnLwf0i', 'admin', '09170000002', 'Barangay Health Center', 'active', NOW(), NOW()),
('Barangay Health Worker', 'Barangay', 'Health', 'Worker', 'worker', 'worker@bhc.com', '$2y$12$uuWkH/VSW/TkjKQigUyEOe9QU8LKuFHXksjZwWDoe4pF7BNnLwf0i', 'bhw', '09170000003', 'Barangay Health Center', 'active', NOW(), NOW());

-- =====================================================
-- MEDICINES TABLE (Common Philippine Medicines)
-- =====================================================

INSERT INTO `medicines` (`medicine_name`, `dosage`, `form`, `description`, `reorder_level`, `created_at`, `updated_at`) VALUES
-- Pain Relief & Anti-inflammatory
('Paracetamol', '500mg', 'Tablet', 'Pain reliever and fever reducer', 500, NOW(), NOW()),
('Ibuprofen', '200mg', 'Tablet', 'Non-steroidal anti-inflammatory drug (NSAID)', 300, NOW(), NOW()),
('Aspirin', '80mg', 'Tablet', 'Blood thinner and pain reliever', 200, NOW(), NOW()),
('Mefenamic Acid', '500mg', 'Tablet', 'Pain reliever for menstrual cramps', 300, NOW(), NOW()),

-- Antibiotics
('Amoxicillin', '500mg', 'Capsule', 'Antibiotic for bacterial infections', 400, NOW(), NOW()),
('Cefalexin', '500mg', 'Capsule', 'Antibiotic for skin and respiratory infections', 300, NOW(), NOW()),
('Azithromycin', '500mg', 'Tablet', 'Antibiotic for respiratory infections', 200, NOW(), NOW()),
('Ciprofloxacin', '500mg', 'Tablet', 'Antibiotic for UTI and other infections', 200, NOW(), NOW()),
('Metronidazole', '500mg', 'Tablet', 'Antibiotic for bacterial and parasitic infections', 300, NOW(), NOW()),

-- Vitamins & Supplements
('Vitamin C', '500mg', 'Tablet', 'Immune system support', 800, NOW(), NOW()),
('Multivitamins', '1 tablet', 'Tablet', 'Daily vitamin supplement', 600, NOW(), NOW()),
('Folic Acid', '5mg', 'Tablet', 'Essential for pregnant women', 500, NOW(), NOW()),
('Ferrous Sulfate', '325mg', 'Tablet', 'Iron supplement for anemia', 400, NOW(), NOW()),
('Calcium Carbonate', '500mg', 'Tablet', 'Calcium supplement for bone health', 400, NOW(), NOW()),

-- Gastrointestinal
('Omeprazole', '20mg', 'Capsule', 'Reduces stomach acid', 300, NOW(), NOW()),
('Loperamide', '2mg', 'Capsule', 'Anti-diarrheal medication', 200, NOW(), NOW()),
('Aluminum Hydroxide', '500mg', 'Suspension', 'Antacid for heartburn', 300, NOW(), NOW()),
('Bismuth Subsalicylate', '262mg', 'Tablet', 'Treats stomach upset and diarrhea', 200, NOW(), NOW()),

-- Antihistamines & Allergies
('Cetirizine', '10mg', 'Tablet', 'Antihistamine for allergies', 400, NOW(), NOW()),
('Loratadine', '10mg', 'Tablet', 'Non-drowsy antihistamine', 300, NOW(), NOW()),
('Diphenhydramine', '25mg', 'Tablet', 'Antihistamine for allergic reactions', 200, NOW(), NOW()),

-- Cough & Cold
('Salbutamol', '2mg', 'Tablet', 'Bronchodilator for asthma', 300, NOW(), NOW()),
('Carbocisteine', '500mg', 'Capsule', 'Mucolytic for productive cough', 300, NOW(), NOW()),
('Dextromethorphan', '15mg', 'Syrup', 'Cough suppressant', 200, NOW(), NOW()),

-- Cardiovascular
('Amlodipine', '5mg', 'Tablet', 'Calcium channel blocker for hypertension', 400, NOW(), NOW()),
('Losartan', '50mg', 'Tablet', 'Angiotensin receptor blocker for hypertension', 400, NOW(), NOW()),
('Atenolol', '50mg', 'Tablet', 'Beta-blocker for hypertension', 300, NOW(), NOW()),
('Simvastatin', '20mg', 'Tablet', 'Cholesterol-lowering medication', 300, NOW(), NOW()),

-- Diabetes
('Metformin', '500mg', 'Tablet', 'Oral diabetes medication', 500, NOW(), NOW()),
('Glimepiride', '2mg', 'Tablet', 'Oral diabetes medication', 300, NOW(), NOW()),
('Insulin Glargine', '100 units/ml', 'Injection', 'Long-acting insulin', 50, NOW(), NOW()),

-- Topical & Dermatological
('Betamethasone', '0.1%', 'Cream', 'Topical corticosteroid for skin inflammation', 100, NOW(), NOW()),
('Clotrimazole', '1%', 'Cream', 'Antifungal cream', 200, NOW(), NOW()),
('Hydrocortisone', '1%', 'Cream', 'Mild corticosteroid for skin irritation', 150, NOW(), NOW()),
('Mupirocin', '2%', 'Ointment', 'Topical antibiotic for skin infections', 100, NOW(), NOW()),

-- Contraceptives
('Ethinyl Estradiol + Levonorgestrel', '0.03mg/0.15mg', 'Tablet', 'Oral contraceptive pill', 500, NOW(), NOW()),
('Medroxyprogesterone', '150mg/ml', 'Injection', 'Injectable contraceptive (Depo)', 100, NOW(), NOW()),

-- Antimalarial (important in Philippines)
('Chloroquine', '250mg', 'Tablet', 'Antimalarial medication', 200, NOW(), NOW()),

-- Others
('Oral Rehydration Salts', '1 sachet', 'Powder', 'Prevents dehydration from diarrhea', 500, NOW(), NOW()),
('Zinc Sulfate', '20mg', 'Tablet', 'Zinc supplement for immunity', 300, NOW(), NOW()),
('Mebendazole', '500mg', 'Tablet', 'Deworming medication', 400, NOW(), NOW()),
('Cloxacillin', '500mg', 'Capsule', 'Antibiotic for skin infections', 200, NOW(), NOW()),
('Ranitidine', '150mg', 'Tablet', 'H2 blocker for stomach acid', 250, NOW(), NOW()),
('Prednisolone', '5mg', 'Tablet', 'Corticosteroid for inflammation', 150, NOW(), NOW()),
('Diclofenac', '50mg', 'Tablet', 'NSAID for pain and inflammation', 250, NOW(), NOW()),
('Tramadol', '50mg', 'Capsule', 'Moderate pain relief', 100, NOW(), NOW());

-- =====================================================
-- NOTES FOR ADDITIONAL DATA
-- =====================================================

-- The following seeders generate large amounts of data:
-- - PatientSeeder: 100 patients with detailed health records
-- - AssessmentSeeder: 150 patient assessments  
-- - PrenatalSeeder: 50 prenatal records with visits
-- - FamilyPlanningSeeder: 80 family planning records
-- - NipSeeder: 100 National Immunization Program records
-- - MedicineDispenseSeeder: 150 medicine dispense transactions

-- To populate these tables, you have TWO OPTIONS:

-- OPTION 1 (RECOMMENDED): Use Laravel Seeders
-- Run in your terminal:
--   php artisan db:seed
-- This will automatically populate ALL tables with realistic sample data.

-- OPTION 2: Export from Local Database
-- 1. Run "php artisan db:seed" on your local machine
-- 2. Export the database from phpMyAdmin (Export → SQL format)
-- 3. Import the SQL file into your production database

-- =====================================================
-- MEDICAL SUPPLIES SAMPLE DATA (Optional)
-- =====================================================

INSERT INTO `medical_supplies` (`item_name`, `category`, `description`, `unit_of_measure`, `quantity_on_hand`, `created_at`, `updated_at`) VALUES
('Disposable Syringes 3ml', 'Consumables', 'Sterile disposable syringes for injections', 'pieces', 500, NOW(), NOW()),
('Surgical Gloves (Medium)', 'PPE', 'Latex surgical gloves size medium', 'boxes', 50, NOW(), NOW()),
('Face Masks (Surgical)', 'PPE', '3-ply surgical face masks', 'boxes', 100, NOW(), NOW()),
('Alcohol 70%', 'Disinfectants', 'Ethyl alcohol 70% for disinfection', 'bottles', 80, NOW(), NOW()),
('Cotton Balls', 'Consumables', 'Sterile cotton balls', 'packs', 60, NOW(), NOW()),
('Bandages (2 inch)', 'First Aid', 'Elastic bandages 2 inch width', 'rolls', 150, NOW(), NOW()),
('Gauze Pads (4x4)', 'First Aid', 'Sterile gauze pads', 'packs', 200, NOW(), NOW()),
('Thermometers (Digital)', 'Equipment', 'Digital thermometers for temperature monitoring', 'pieces', 25, NOW(), NOW()),
('Blood Pressure Cuff', 'Equipment', 'Manual BP apparatus', 'pieces', 15, NOW(), NOW()),
('Stethoscope', 'Equipment', 'Clinical stethoscope', 'pieces', 10, NOW(), NOW());

-- Sample supply history records
INSERT INTO `supply_history` (`medical_supply_id`, `item_name`, `quantity`, `received_from`, `date_received`, `handled_by`, `created_at`, `updated_at`) VALUES
(1, 'Disposable Syringes 3ml', 500, 'DOH Regional Office', '2024-11-15', 'Admin User', NOW(), NOW()),
(2, 'Surgical Gloves (Medium)', 50, 'LGU Health Office', '2024-11-20', 'Admin User', NOW(), NOW()),
(3, 'Face Masks (Surgical)', 100, 'DOH', '2024-12-01', 'Admin User', NOW(), NOW()),
(4, 'Alcohol 70%', 80, 'LGU Purchase', '2024-12-05', 'Admin User', NOW(), NOW()),
(5, 'Cotton Balls', 60, 'Donation - Private Company', '2024-11-25', 'Barangay Health Worker', NOW(), NOW());

-- =====================================================
-- END OF SQL SEEDERS
-- =====================================================

-- After running these queries, you should have:
-- ✓ 3 user accounts (Super Admin, Admin, BHW)
-- ✓ 48 medicines in inventory
-- ✓ 10 medical supply items with sample history
--
-- For complete sample data, run: php artisan db:seed
-- This will add 100 patients, assessments, health programs, etc.
