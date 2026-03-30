-- Enhanced Database Schema Updates
-- Paste in phpMyAdmin after original database.sql

-- 1. Audit Trail Table
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    student_id VARCHAR(20),
    action VARCHAR(100) NOT NULL,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 2. Pending Payments (Student Uploads)
CREATE TABLE pending_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20),
    amount_claimed DECIMAL(10,2),
    payment_method ENUM('GCash', 'Bank Transfer', 'Maya') NOT NULL,
    proof_file VARCHAR(255),
    remarks TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(student_id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- 3. Notification Queue
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20),
    type ENUM('Receipt', 'Balance_Reminder') NOT NULL,
    message TEXT NOT NULL,
    sent_status ENUM('Pending', 'Sent', 'Failed') DEFAULT 'Pending',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(student_id)
);

-- 4. ALTER Existing Tables
ALTER TABLE payments ADD COLUMN qr_code VARCHAR(255) NULL AFTER remarks;
ALTER TABLE payments ADD COLUMN verification_status ENUM('Verified', 'Pending') DEFAULT 'Verified' AFTER qr_code;

ALTER TABLE scholarships ADD COLUMN gpa DECIMAL(3,2) NULL AFTER discount_type;
ALTER TABLE scholarships ADD COLUMN stackable TINYINT(1) DEFAULT 1 AFTER gpa;

-- 5. Demo Transactions for Testing (always have payments)
INSERT INTO payments (student_id, amount_paid, receipt_no, remarks) VALUES
('2024001', 1500.00, 'OPN-TEST1', 'Cash payment'),
('2024001', 500.00, 'OPN-TEST2', 'GCash'),
('2024002', 2000.00, 'OPN-TEST3', 'Full payment'),
('ADMIN001', 0.00, 'SYS-ADMIN', 'System demo');

-- 6. Update Demo Students Assessments
UPDATE users SET program = 'BSIT' WHERE student_id IN ('2024001', '2024002');

