-- Create scholarships table (missing dependency)
-- Paste this NOW in phpMyAdmin

CREATE TABLE IF NOT EXISTS scholarships (
    student_id VARCHAR(20) PRIMARY KEY,
    discount_type VARCHAR(50) NOT NULL DEFAULT 'None',
    gpa DECIMAL(3,2),
    stackable TINYINT(1) DEFAULT 1,
    assigned_by INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(student_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);

-- Sample scholarships for demo
INSERT INTO scholarships (student_id, discount_type, gpa, stackable) VALUES
('2024001', 'Academic 50%', 1.45, 1),
'QC Foundation 75%', 1.10, 0);

-- Ensure demo payments exist
INSERT IGNORE INTO payments (student_id, amount_paid, receipt_no, remarks) VALUES
('2024001', 1500.00, 'DEMO-001', 'Cash'),
('2024002', 2500.00, 'DEMO-002', 'Scholarship payment');

