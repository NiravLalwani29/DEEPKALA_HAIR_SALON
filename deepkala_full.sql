-- ================================================================
-- Deepkala Hair Salon — COMPLETE database (fresh install, ONE file)
-- phpMyAdmin -> Import tab -> choose this file -> Go
-- Creates: users (with wallet), barbers, appointments, admins,
--          barber_leaves, wallet_txn, password_resets  + the default admin login.
-- Safe to import on a new PC. (Uses IF NOT EXISTS, so it won't wipe data.)
--
-- ADMIN LOGIN (created automatically, no setup.php needed):
--     Email:    admin@deepkala.com
--     Password: Admin@123
-- ================================================================

CREATE DATABASE IF NOT EXISTS deepkala;
USE deepkala;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    password   VARCHAR(100) NOT NULL,
    membership VARCHAR(20)  DEFAULT 'none',
    wallet     INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS barbers (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(150)
);

INSERT INTO barbers (name, role)
SELECT * FROM (
    SELECT 'Deepesh Parmar' AS name, 'Founder & Master Stylist' AS role UNION ALL
    SELECT 'Kalpesh Patel',  'Senior Fade & Colour Specialist' UNION ALL
    SELECT 'Sanjay Kumar',   'Beard Specialist & Spa Therapist' UNION ALL
    SELECT 'Nisha Solanki',  'Bridal & Wedding Makeup Artist' UNION ALL
    SELECT 'Rahul Desai',    'Senior Barber' UNION ALL
    SELECT 'Amit Joshi',     'Beard & Grooming Specialist' UNION ALL
    SELECT 'Priya Shah',     'Hair Colour Specialist' UNION ALL
    SELECT 'Vikram Rathod',  'Stylist'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM barbers);

CREATE TABLE IF NOT EXISTS appointments (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    barber_id         INT NOT NULL,
    name              VARCHAR(150) NOT NULL,
    phone             VARCHAR(20)  NOT NULL,
    service           VARCHAR(150) NOT NULL,
    appointment_date  DATE NOT NULL,
    appointment_time  VARCHAR(20) NOT NULL,
    notes             TEXT,
    amount            INT NOT NULL,
    payment_method    VARCHAR(60),
    status            ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending',
    admin_note        VARCHAR(255) NULL,
    approved_at       DATETIME NULL,
    cancelled_at      DATETIME NULL,
    rescheduled_count INT NOT NULL DEFAULT 0,
    cancelled_by      VARCHAR(10) NULL,
    refunded          INT NOT NULL DEFAULT 0,
    resched_note      VARCHAR(255) NULL,
    resched_by        VARCHAR(10) NULL,
    old_slot          VARCHAR(60) NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_appt_status (status),
    INDEX idx_appt_slot (appointment_date, appointment_time),
    INDEX idx_appt_user (user_id)
);

CREATE TABLE IF NOT EXISTS barber_leaves (
    barber_id  INT  NOT NULL,
    leave_date DATE NOT NULL,
    PRIMARY KEY (barber_id, leave_date)
);

CREATE TABLE IF NOT EXISTS admins (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wallet_txn (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    appointment_id INT NULL,
    type           ENUM('credit','debit') NOT NULL,
    amount         INT NOT NULL,
    reason         VARCHAR(255) NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wallet_user (user_id)
);

CREATE TABLE IF NOT EXISTS password_resets (
    email    VARCHAR(150) PRIMARY KEY,
    otp_hash VARCHAR(255) NOT NULL,
    expires  DATETIME NOT NULL,
    tries    INT NOT NULL DEFAULT 0
);

-- Default admin (password Admin@123). Skipped if this email already exists.
INSERT IGNORE INTO admins (name, email, password_hash)
VALUES ('Owner', 'admin@deepkala.com', '$2y$10$6oCnfususRwBp/ic3d5b2um6Lirv2rrtcB1yXQAJQ9Xc/0acaE8Zu');
