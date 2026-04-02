-- ============================================================
-- RecruiterMS Seed Data
-- ============================================================

USE recruiter_db;

-- Admin user (password: Admin@123)
-- bcrypt hash generated with cost factor 10
INSERT INTO users (name, email, password, role, is_verified, created_at)
VALUES (
    'System Admin',
    'admin@recruiterms.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
) ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Sample recruiters for demo
INSERT INTO users (name, email, password, role, is_verified, created_at) VALUES
('John Smith', 'john@techcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', 1, NOW()),
('Sarah Johnson', 'sarah@innovate.io', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', 1, NOW()),
('Mike Chen', 'mike@globalhr.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', 1, NOW()),
('Emily Davis', 'emily@startupx.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', 1, NOW()),
('Alex Turner', 'alex@enterprise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recruiter', 0, NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Link recruiters to profiles
INSERT INTO recruiters (user_id, company_name, position, phone, status) VALUES
((SELECT id FROM users WHERE email = 'john@techcorp.com'), 'TechCorp Solutions', 'Senior Technical Recruiter', '+1-555-0101', 'active'),
((SELECT id FROM users WHERE email = 'sarah@innovate.io'), 'Innovate.io', 'Head of Talent', '+1-555-0102', 'active'),
((SELECT id FROM users WHERE email = 'mike@globalhr.com'), 'GlobalHR Partners', 'Recruitment Manager', '+1-555-0103', 'active'),
((SELECT id FROM users WHERE email = 'emily@startupx.co'), 'StartupX', 'HR Lead', '+1-555-0104', 'active'),
((SELECT id FROM users WHERE email = 'alex@enterprise.com'), 'Enterprise Inc', 'Junior Recruiter', '+1-555-0105', 'inactive')
ON DUPLICATE KEY UPDATE company_name = VALUES(company_name);

-- Sample candidates
INSERT INTO candidates (name, email, resume_url, status) VALUES
('Alice Wonder', 'alice@email.com', 'https://resumes.example.com/alice.pdf', 'shortlisted'),
('Bob Martinez', 'bob@email.com', 'https://resumes.example.com/bob.pdf', 'pending'),
('Carol White', 'carol@email.com', 'https://resumes.example.com/carol.pdf', 'rejected'),
('David Kim', 'david@email.com', 'https://resumes.example.com/david.pdf', 'pending'),
('Eva Brown', 'eva@email.com', 'https://resumes.example.com/eva.pdf', 'shortlisted')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Sample job postings
INSERT INTO job_postings (recruiter_id, title, description, location, salary_range, deadline) VALUES
(1, 'Senior React Developer', 'Looking for an experienced React developer to join our frontend team.', 'San Francisco, CA', '$120,000 - $160,000', DATE_ADD(NOW(), INTERVAL 30 DAY)),
(1, 'Backend PHP Engineer', 'PHP developer with REST API experience needed.', 'Remote', '$100,000 - $140,000', DATE_ADD(NOW(), INTERVAL 45 DAY)),
(2, 'DevOps Engineer', 'Seeking a DevOps professional for CI/CD pipeline management.', 'New York, NY', '$130,000 - $170,000', DATE_ADD(NOW(), INTERVAL 20 DAY)),
(3, 'UI/UX Designer', 'Creative designer for enterprise dashboard applications.', 'Austin, TX', '$90,000 - $120,000', DATE_ADD(NOW(), INTERVAL 60 DAY)),
(4, 'Full Stack Developer', 'Startup looking for a versatile full-stack developer.', 'Remote', '$80,000 - $110,000', DATE_ADD(NOW(), INTERVAL 15 DAY))
ON DUPLICATE KEY UPDATE title = VALUES(title);
