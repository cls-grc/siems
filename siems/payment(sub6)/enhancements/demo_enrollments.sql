-- Make regular students enrolled (paste in phpMyAdmin)
-- Adds payments so balance = 0 and enrolled status

INSERT IGNORE INTO payments (student_id, amount_paid, receipt_no, payment_date, remarks) VALUES
('2024003', 50000.00, 'REG-001', NOW(), 'Full payment - Regular student'),
('2024004', 50000.00, 'REG-002', NOW(), 'Full payment - Regular student'),
('2024005', 50000.00, 'REG-003', NOW(), 'Full payment - Regular student');

-- Trigger assessment recalc for these students
UPDATE student_assessments SET balance = 0 WHERE student_id IN ('2024003', '2024004', '2024005');

