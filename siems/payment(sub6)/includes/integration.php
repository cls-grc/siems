<?php
require_once __DIR__ . '/../config/db_connect.php';

function siemsTableExists($tableName) {
    global $pdo;

    static $cache = [];
    if (array_key_exists($tableName, $cache)) {
        return $cache[$tableName];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
              AND table_name = ?
        ");
        $stmt->execute([$tableName]);
        $cache[$tableName] = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache[$tableName] = false;
    }

    return $cache[$tableName];
}

function siemsColumnExists($tableName, $columnName) {
    global $pdo;

    static $cache = [];
    $cacheKey = $tableName . '.' . $columnName;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    if (!siemsTableExists($tableName)) {
        $cache[$cacheKey] = false;
        return false;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND column_name = ?
        ");
        $stmt->execute([$tableName, $columnName]);
        $cache[$cacheKey] = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache[$cacheKey] = false;
    }

    return $cache[$cacheKey];
}

function siemsCountRows($tableName, $whereSql = '1=1', array $params = []) {
    global $pdo;

    if (!siemsTableExists($tableName)) {
        return 0;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$tableName} WHERE {$whereSql}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function siemsSumAmount($tableName, $columnName, $whereSql = '1=1', array $params = []) {
    global $pdo;

    if (!siemsTableExists($tableName)) {
        return 0.0;
    }

    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM({$columnName}), 0) FROM {$tableName} WHERE {$whereSql}");
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0.0;
    }
}

function getSubsystemRegistry() {
    return [
        [
            'slug' => 'student-information',
            'name' => 'Student Information',
            'icon' => 'bi-person-vcard',
            'description' => 'Profiles, academic records, ID generation, and student status tracking.',
            'admin_url' => 'student_information.php',
            'student_url' => 'student_information.php',
        ],
        [
            'slug' => 'enrollment-registration',
            'name' => 'Enrollment & Registration',
            'icon' => 'bi-journal-check',
            'description' => 'Applications, subject selection, validation, and enrollment progress.',
            'admin_url' => 'enrollment_registration.php',
            'student_url' => 'enrollment_registration.php',
        ],
        [
            'slug' => 'curriculum-course',
            'name' => 'Curriculum & Course',
            'icon' => 'bi-diagram-3',
            'description' => 'Programs, subject catalog, curriculum revisions, and prerequisites.',
            'admin_url' => 'curriculum_course.php',
            'student_url' => 'curriculum_course.php',
        ],
        [
            'slug' => 'class-scheduling',
            'name' => 'Class Scheduling',
            'icon' => 'bi-calendar3-week',
            'description' => 'Sections, rooms, faculty loading, and conflict-aware timetables.',
            'admin_url' => 'class_scheduling.php',
            'student_url' => 'class_scheduling.php',
        ],
        [
            'slug' => 'grades-assessment',
            'name' => 'Grades & Assessment',
            'icon' => 'bi-clipboard-data',
            'description' => 'Grade encoding, verification, correction workflow, and grade reports.',
            'admin_url' => 'grades_assessment.php',
            'student_url' => 'grades_assessment.php',
        ],
        [
            'slug' => 'payment-accounting',
            'name' => 'Payment & Accounting',
            'icon' => 'bi-cash-coin',
            'description' => 'Assessment, payment posting, scholarships, SOA, and transaction logs.',
            'admin_url' => 'dashboard.php',
            'student_url' => 'dashboard.php',
        ],
        [
            'slug' => 'documents-credentials',
            'name' => 'Documents & Credentials',
            'icon' => 'bi-folder2-open',
            'description' => 'Document requests, processing workflow, generation, and release.',
            'admin_url' => 'documents_credentials.php',
            'student_url' => 'documents_credentials.php',
        ],
        [
            'slug' => 'human-resources',
            'name' => 'Human Resource Management',
            'icon' => 'bi-people',
            'description' => 'Recruitment, onboarding, employment records, service, and clearance.',
            'admin_url' => 'human_resources.php',
            'student_url' => '#',
        ],
        [
            'slug' => 'clinic-medical',
            'name' => 'Clinic & Medical',
            'icon' => 'bi-heart-pulse',
            'description' => 'Medical records, consultations, medicine inventory, and clearances.',
            'admin_url' => 'clinic_medical.php',
            'student_url' => 'clinic_medical.php',
        ],
        [
            'slug' => 'user-management',
            'name' => 'User Management',
            'icon' => 'bi-shield-lock',
            'description' => 'Accounts, roles, permissions, security, recovery, and audit trails.',
            'admin_url' => 'user_management.php',
            'student_url' => '#',
        ],
    ];
}

function getAdminIntegratedSummary() {
    return [
        'students' => siemsCountRows('users', "role = 'student'"),
        'pending_enrollments' => siemsCountRows('enrollment_applications', "status IN ('Pending', 'Submitted', 'For Review')"),
        'active_curricula' => siemsCountRows('curricula', "status = 'Active'"),
        'open_sections' => siemsCountRows('sections', "status = 'Open'"),
        'pending_grades' => siemsCountRows('grade_submissions', "status IN ('Submitted', 'Pending Verification')"),
        'pending_documents' => siemsCountRows('document_requests', "status IN ('Pending', 'Processing', 'For Approval')"),
        'employees' => siemsCountRows('employee_records', "employment_status IN ('Active', 'Probationary', 'Regular')"),
        'clinic_visits_today' => siemsCountRows('clinic_consultations', 'DATE(consultation_date) = CURDATE()'),
        'verified_payments' => siemsCountRows('payments', "verification_status = 'Verified'"),
        'verified_collection' => siemsSumAmount('payments', 'amount_paid', "verification_status = 'Verified'"),
        'audit_events' => siemsCountRows('audit_log', '1=1'),
    ];
}

function getStudentIntegratedSummary($studentId) {
    global $pdo;
    require_once __DIR__ . '/functions.php'; // Ensure calculateAssessment available

    // Force recalculation to ensure latest balance
    calculateAssessment($studentId);

    $summary = [
        'student_name' => $_SESSION['full_name'] ?? 'Student',
        'program' => $_SESSION['program'] ?? 'N/A',
        'year_level' => $_SESSION['year_level'] ?? 'N/A',
        'enrollment_status' => 'Pending',
        'latest_balance' => 0.0,
        'requested_documents' => 0,
        'approved_grades' => 0,
        'medical_clearance' => 'Not Issued',
        'current_section' => 'Not Assigned',
        'recent_payments' => 0,
    ];

    if (siemsTableExists('users')) {
        try {
            $stmt = $pdo->prepare("SELECT full_name, program, year_level, enrollment_status FROM users WHERE student_id = ? LIMIT 1");
            $stmt->execute([$studentId]);
            $row = $stmt->fetch();
            if ($row) {
                $summary['student_name'] = $row['full_name'] ?: $summary['student_name'];
                $summary['program'] = $row['program'] ?: $summary['program'];
                $summary['year_level'] = $row['year_level'] ?: $summary['year_level'];
                $summary['enrollment_status'] = $row['enrollment_status'] ?: $summary['enrollment_status'];
            }
        } catch (Throwable $e) {
        }
    }

    if (siemsTableExists('student_assessments')) {
        try {
            $stmt = $pdo->prepare("SELECT balance FROM student_assessments WHERE student_id = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$studentId]);
            $summary['latest_balance'] = (float) ($stmt->fetchColumn() ?: 0);
        } catch (Throwable $e) {
        }
    }

    $summary['requested_documents'] = siemsCountRows('document_requests', 'student_id = ?', [$studentId]);
    $summary['approved_grades'] = siemsCountRows('grade_submissions', "student_id = ? AND status = 'Approved'", [$studentId]);
    $summary['recent_payments'] = siemsCountRows('payments', 'student_id = ?', [$studentId]);

    if (siemsTableExists('medical_clearances')) {
        try {
            $stmt = $pdo->prepare("SELECT status FROM medical_clearances WHERE student_id = ? ORDER BY issued_at DESC LIMIT 1");
            $stmt->execute([$studentId]);
            $status = $stmt->fetchColumn();
            if ($status) {
                $summary['medical_clearance'] = $status;
            }
        } catch (Throwable $e) {
        }
    }

    if (siemsTableExists('enrollment_application_items')) {
        try {
            $stmt = $pdo->prepare("
                SELECT eai.section_code
                FROM enrollment_application_items eai
                INNER JOIN enrollment_applications ea ON ea.id = eai.application_id
                WHERE ea.student_id = ?
                ORDER BY eai.id DESC
                LIMIT 1
            ");
            $stmt->execute([$studentId]);
            $section = $stmt->fetchColumn();
            if ($section) {
                $summary['current_section'] = $section;
            }
        } catch (Throwable $e) {
        }
    }

    return $summary;
}

function getStudentSubsystemStatusCards($studentId) {
    $summary = getStudentIntegratedSummary($studentId);

    return [
        ['label' => 'Enrollment', 'value' => $summary['enrollment_status'], 'tone' => 'success', 'icon' => 'bi-journal-check'],
        ['label' => 'Balance', 'value' => 'PHP ' . number_format($summary['latest_balance'], 2), 'tone' => 'warning', 'icon' => 'bi-cash-stack'],
        ['label' => 'Documents', 'value' => $summary['requested_documents'] . ' request(s)', 'tone' => 'info', 'icon' => 'bi-folder-check'],
        ['label' => 'Grades Posted', 'value' => $summary['approved_grades'], 'tone' => 'primary', 'icon' => 'bi-clipboard2-check'],
        ['label' => 'Medical', 'value' => $summary['medical_clearance'], 'tone' => 'danger', 'icon' => 'bi-heart-pulse'],
        ['label' => 'Section', 'value' => $summary['current_section'], 'tone' => 'secondary', 'icon' => 'bi-calendar3-week'],
    ];
}

function siemsFetchAll($sql, array $params = []) {
    global $pdo;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function siemsFetchOne($sql, array $params = []) {
    global $pdo;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function siemsLogSubsystemEvent($subsystem, $action, $studentId = null, $oldValue = null, $newValue = null) {
    global $pdo;

    if (!siemsTableExists('audit_log')) {
        return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    try {
        if (siemsColumnExists('audit_log', 'subsystem')) {
            $stmt = $pdo->prepare("
                INSERT INTO audit_log
                    (user_id, student_id, subsystem, action, old_value, new_value, ip_address, user_agent)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $studentId, $subsystem, $action, $oldValue, $newValue, $ip, $agent]);
            return;
        }

        $stmt = $pdo->prepare("
            INSERT INTO audit_log
                (user_id, student_id, action, old_value, new_value, ip_address, user_agent, created_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $studentId, $action, $oldValue, $newValue, $ip, $agent]);
    } catch (Throwable $e) {
    }
}

function siemsGetOpenAcademicPeriods() {
    if (!siemsTableExists('academic_periods')) {
        return [];
    }

    return siemsFetchAll("
        SELECT *
        FROM academic_periods
        WHERE period_status IN ('Open', 'Upcoming')
        ORDER BY
            CASE period_status
                WHEN 'Open' THEN 1
                WHEN 'Upcoming' THEN 2
                ELSE 3
            END,
            id DESC
    ");
}

function siemsGetStudentProfileBundle($studentId) {
    $bundle = [
        'user' => null,
        'profile' => null,
        'student_id_card' => null,
        'status_history' => [],
        'academic_records' => [],
        'latest_application' => null,
    ];

    if (siemsTableExists('users')) {
        $bundle['user'] = siemsFetchOne("SELECT * FROM users WHERE student_id = ? LIMIT 1", [$studentId]) ?: null;
    }

    if (siemsTableExists('student_profiles')) {
        $bundle['profile'] = siemsFetchOne("SELECT * FROM student_profiles WHERE student_id = ? LIMIT 1", [$studentId]) ?: null;
    }

    if (siemsTableExists('student_ids')) {
        $bundle['student_id_card'] = siemsFetchOne("
            SELECT *
            FROM student_ids
            WHERE student_id = ?
            ORDER BY issued_at DESC, id DESC
            LIMIT 1
        ", [$studentId]) ?: null;
    }

    if (siemsTableExists('student_status_history')) {
        $bundle['status_history'] = siemsFetchAll("
            SELECT ssh.*, u.full_name AS recorded_by_name
            FROM student_status_history ssh
            LEFT JOIN users u ON ssh.recorded_by = u.id
            WHERE ssh.student_id = ?
            ORDER BY ssh.effective_date DESC, ssh.id DESC
        ", [$studentId]);
    }

    if (siemsTableExists('academic_records') && siemsTableExists('subjects')) {
        $bundle['academic_records'] = siemsFetchAll("
            SELECT ar.*, s.code, s.description, s.units
            FROM academic_records ar
            INNER JOIN subjects s ON ar.subject_id = s.id
            WHERE ar.student_id = ?
            ORDER BY ar.academic_year DESC, ar.semester DESC, s.code ASC
        ", [$studentId]);
    }

    if (siemsTableExists('enrollment_applications') && siemsTableExists('academic_periods')) {
        $bundle['latest_application'] = siemsFetchOne("
            SELECT ea.*, ap.academic_year, ap.semester, ap.period_status
            FROM enrollment_applications ea
            INNER JOIN academic_periods ap ON ap.id = ea.academic_period_id
            WHERE ea.student_id = ?
            ORDER BY ea.id DESC
            LIMIT 1
        ", [$studentId]) ?: null;
    }

    return $bundle;
}

function siemsGetEnrollmentSubjectsForStudent($studentId) {
    $user = siemsFetchOne("SELECT program, year_level FROM users WHERE student_id = ? LIMIT 1", [$studentId]);
    if (!$user || !siemsTableExists('subjects')) {
        return [];
    }

    return siemsFetchAll("
        SELECT s.*,
               (
                   SELECT GROUP_CONCAT(dep.code SEPARATOR ', ')
                   FROM course_dependencies cd
                   INNER JOIN subjects dep ON dep.id = cd.dependency_subject_id
                   WHERE cd.subject_id = s.id
                     AND cd.dependency_type = 'Prerequisite'
               ) AS prerequisite_codes
        FROM subjects s
        WHERE s.active = 1
          AND (s.program = ? OR s.program IS NULL OR s.program = '')
          AND (s.year_level = ? OR s.year_level IS NULL)
        ORDER BY s.semester ASC, s.code ASC
    ", [$user['program'], $user['year_level']]);
}

function siemsGetStudentEnrollmentHistory($studentId) {
    if (!siemsTableExists('enrollment_applications') || !siemsTableExists('academic_periods')) {
        return [];
    }

    return siemsFetchAll("
        SELECT ea.*, ap.academic_year, ap.semester, ap.period_status,
               ev.validation_status, ev.validation_notes, ev.validated_at
        FROM enrollment_applications ea
        INNER JOIN academic_periods ap ON ap.id = ea.academic_period_id
        LEFT JOIN enrollment_validations ev ON ev.application_id = ea.id
        WHERE ea.student_id = ?
        ORDER BY ea.id DESC
    ", [$studentId]);
}

function siemsGetPrograms() {
    if (!siemsTableExists('programs')) {
        return [];
    }

    return siemsFetchAll("
        SELECT *
        FROM programs
        WHERE active = 1
        ORDER BY program_code ASC
    ");
}

function siemsGetAdminCurriculumOverview() {
    $overview = [
        'subjects' => siemsCountRows('subjects', 'active = 1'),
        'curricula' => siemsCountRows('curricula', '1=1'),
        'active_curricula' => siemsCountRows('curricula', "status = 'Active'"),
        'dependencies' => siemsCountRows('course_dependencies', '1=1'),
    ];

    if (siemsTableExists('curricula')) {
        $overview['curriculum_rows'] = siemsFetchAll("
            SELECT c.*, COUNT(cc.id) AS subject_count
            FROM curricula c
            LEFT JOIN curriculum_courses cc ON cc.curriculum_id = c.id
            GROUP BY c.id
            ORDER BY
                CASE c.status
                    WHEN 'Active' THEN 1
                    WHEN 'Draft' THEN 2
                    ELSE 3
                END,
                c.created_at DESC
        ");
    } else {
        $overview['curriculum_rows'] = [];
    }

    if (siemsTableExists('subjects')) {
        $overview['subject_rows'] = siemsFetchAll("
            SELECT s.*,
                   (
                       SELECT GROUP_CONCAT(dep.code SEPARATOR ', ')
                       FROM course_dependencies cd
                       INNER JOIN subjects dep ON dep.id = cd.dependency_subject_id
                       WHERE cd.subject_id = s.id
                         AND cd.dependency_type = 'Prerequisite'
                   ) AS prerequisite_codes
            FROM subjects s
            ORDER BY s.program ASC, s.year_level ASC, s.code ASC
        ");
    } else {
        $overview['subject_rows'] = [];
    }

    return $overview;
}

function siemsGetStudentCurriculumOverview($studentId) {
    $user = siemsFetchOne("SELECT program, year_level FROM users WHERE student_id = ? LIMIT 1", [$studentId]);
    if (!$user) {
        return ['user' => null, 'curriculum' => null, 'curriculum_courses' => [], 'subjects' => []];
    }

    $curriculum = siemsFetchOne("
        SELECT *
        FROM curricula
        WHERE program_code = ?
        ORDER BY
            CASE status
                WHEN 'Active' THEN 1
                WHEN 'Draft' THEN 2
                ELSE 3
            END,
            id DESC
        LIMIT 1
    ", [$user['program']]);

    $curriculumCourses = [];
    if ($curriculum && siemsTableExists('curriculum_courses')) {
        $curriculumCourses = siemsFetchAll("
            SELECT cc.*, s.code, s.description, s.units, s.category
            FROM curriculum_courses cc
            INNER JOIN subjects s ON s.id = cc.subject_id
            WHERE cc.curriculum_id = ?
            ORDER BY cc.year_level ASC, cc.semester ASC, s.code ASC
        ", [$curriculum['id']]);
    }

    $subjects = siemsFetchAll("
        SELECT s.*,
               (
                   SELECT GROUP_CONCAT(dep.code SEPARATOR ', ')
                   FROM course_dependencies cd
                   INNER JOIN subjects dep ON dep.id = cd.dependency_subject_id
                   WHERE cd.subject_id = s.id
                     AND cd.dependency_type = 'Prerequisite'
               ) AS prerequisite_codes
        FROM subjects s
        WHERE s.program = ?
        ORDER BY s.year_level ASC, s.semester ASC, s.code ASC
    ", [$user['program']]);

    return [
        'user' => $user,
        'curriculum' => $curriculum ?: null,
        'curriculum_courses' => $curriculumCourses,
        'subjects' => $subjects,
    ];
}

function siemsGetAdminScheduleOverview() {
    return [
        'rooms' => siemsTableExists('rooms') ? siemsFetchAll("SELECT * FROM rooms ORDER BY room_code ASC") : [],
        'sections' => siemsTableExists('sections') ? siemsFetchAll("
            SELECT s.*, u.full_name AS adviser_name
            FROM sections s
            LEFT JOIN users u ON u.id = s.adviser_user_id
            ORDER BY s.program_code ASC, s.year_level ASC, s.section_code ASC
        ") : [],
        'schedules' => siemsTableExists('class_schedules') ? siemsFetchAll("
            SELECT cs.*, sec.section_code, sec.program_code, sec.year_level,
                   sub.code, sub.description,
                   rm.room_code,
                   teacher.full_name AS teacher_name
            FROM class_schedules cs
            INNER JOIN sections sec ON sec.id = cs.section_id
            INNER JOIN subjects sub ON sub.id = cs.subject_id
            LEFT JOIN rooms rm ON rm.id = cs.room_id
            LEFT JOIN users teacher ON teacher.id = cs.teacher_user_id
            ORDER BY sec.section_code ASC, FIELD(cs.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), cs.time_start ASC
        ") : [],
    ];
}

function siemsGetStudentScheduleOverview($studentId) {
    $sectionCode = null;
    if (siemsTableExists('enrollment_application_items') && siemsTableExists('enrollment_applications')) {
        $row = siemsFetchOne("
            SELECT eai.section_code
            FROM enrollment_application_items eai
            INNER JOIN enrollment_applications ea ON ea.id = eai.application_id
            WHERE ea.student_id = ?
            ORDER BY eai.id DESC
            LIMIT 1
        ", [$studentId]);
        $sectionCode = $row['section_code'] ?? null;
    }

    if (!$sectionCode && siemsTableExists('enrollments')) {
        $row = siemsFetchOne("
            SELECT section_code
            FROM enrollments
            WHERE student_id = ?
              AND section_code IS NOT NULL
            ORDER BY id DESC
            LIMIT 1
        ", [$studentId]);
        $sectionCode = $row['section_code'] ?? null;
    }

    $section = null;
    $schedules = [];
    if ($sectionCode && siemsTableExists('sections')) {
        $section = siemsFetchOne("
            SELECT s.*, u.full_name AS adviser_name
            FROM sections s
            LEFT JOIN users u ON u.id = s.adviser_user_id
            WHERE s.section_code = ?
            LIMIT 1
        ", [$sectionCode]);

        if ($section && siemsTableExists('class_schedules')) {
            $schedules = siemsFetchAll("
                SELECT cs.*, sub.code, sub.description, sub.units,
                       rm.room_code,
                       teacher.full_name AS teacher_name
                FROM class_schedules cs
                INNER JOIN subjects sub ON sub.id = cs.subject_id
                LEFT JOIN rooms rm ON rm.id = cs.room_id
                LEFT JOIN users teacher ON teacher.id = cs.teacher_user_id
                WHERE cs.section_id = ?
                ORDER BY FIELD(cs.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), cs.time_start ASC
            ", [$section['id']]);
        }
    }

    return [
        'section_code' => $sectionCode,
        'section' => $section,
        'schedules' => $schedules,
    ];
}

function siemsGetAdminGradesOverview() {
    return [
        'submissions' => siemsTableExists('grade_submissions') ? siemsFetchAll("
            SELECT gs.*, s.code, s.description, u.full_name AS student_name,
                   enc.full_name AS encoded_by_name, ver.full_name AS verified_by_name
            FROM grade_submissions gs
            INNER JOIN subjects s ON s.id = gs.subject_id
            INNER JOIN users u ON u.student_id = gs.student_id
            LEFT JOIN users enc ON enc.id = gs.encoded_by
            LEFT JOIN users ver ON ver.id = gs.verified_by
            ORDER BY gs.submitted_at DESC, gs.id DESC
        ") : [],
        'change_requests' => siemsTableExists('grade_change_requests') ? siemsFetchAll("
            SELECT gcr.*, gs.student_id, s.code, s.description, u.full_name AS requested_by_name
            FROM grade_change_requests gcr
            INNER JOIN grade_submissions gs ON gs.id = gcr.grade_submission_id
            INNER JOIN subjects s ON s.id = gs.subject_id
            LEFT JOIN users u ON u.id = gcr.requested_by
            ORDER BY gcr.id DESC
        ") : [],
        'records' => siemsCountRows('academic_records', '1=1'),
    ];
}

function siemsGetStudentGradesOverview($studentId) {
    return [
        'submissions' => siemsTableExists('grade_submissions') ? siemsFetchAll("
            SELECT gs.*, s.code, s.description
            FROM grade_submissions gs
            INNER JOIN subjects s ON s.id = gs.subject_id
            WHERE gs.student_id = ?
            ORDER BY gs.submitted_at DESC, gs.id DESC
        ", [$studentId]) : [],
        'records' => siemsTableExists('academic_records') ? siemsFetchAll("
            SELECT ar.*, s.code, s.description, s.units
            FROM academic_records ar
            INNER JOIN subjects s ON s.id = ar.subject_id
            WHERE ar.student_id = ?
            ORDER BY ar.academic_year DESC, ar.semester DESC, s.code ASC
        ", [$studentId]) : [],
        'change_requests' => siemsTableExists('grade_change_requests') ? siemsFetchAll("
            SELECT gcr.*, gs.subject_id, s.code, s.description
            FROM grade_change_requests gcr
            INNER JOIN grade_submissions gs ON gs.id = gcr.grade_submission_id
            INNER JOIN subjects s ON s.id = gs.subject_id
            WHERE gs.student_id = ?
            ORDER BY gcr.id DESC
        ", [$studentId]) : [],
    ];
}

function siemsGetAdminDocumentsOverview() {
    global $pdo;
    return [
        'document_types' => siemsTableExists('document_types') ? siemsFetchAll("SELECT * FROM document_types ORDER BY document_name ASC") : [],
        'requests' => siemsTableExists('document_requests') ? $pdo->query("
            SELECT dr.*, u.full_name, u.program, u.year_level, 
                   COALESCE(dr.payment_status, 'Pending') AS payment_status,
                   dr.payment_receipt_no,
                   COALESCE((SELECT p.verification_status FROM payments p WHERE p.remarks LIKE CONCAT('%[DOCREQ:', dr.id, ']%') ORDER BY p.id DESC LIMIT 1), 'Pending') AS payment_verification
            FROM document_requests dr
            INNER JOIN users u ON u.student_id = dr.student_id
            ORDER BY dr.requested_at DESC, dr.id DESC
        ")->fetchAll() : [],
        'releases' => siemsTableExists('document_releases') ? siemsFetchAll("
            SELECT drel.*, dr.student_id, dr.document_type, u.full_name AS released_by_name
            FROM document_releases drel
            INNER JOIN document_requests dr ON dr.id = drel.document_request_id
            LEFT JOIN users u ON u.id = drel.released_by
            ORDER BY drel.released_at DESC, drel.id DESC
        ") : [],
        'archives' => siemsTableExists('archived_records') ? siemsFetchAll("
            SELECT ar.*, u.full_name AS archived_by_name
            FROM archived_records ar
            LEFT JOIN users u ON u.id = ar.archived_by
            ORDER BY ar.archived_at DESC, ar.id DESC
        ") : [],
    ];
}

function siemsGetStudentDocumentsOverview($studentId) {
    return [
        'document_types' => siemsTableExists('document_types') ? siemsFetchAll("SELECT * FROM document_types ORDER BY document_name ASC") : [],
        'requests' => siemsTableExists('document_requests') ? siemsFetchAll("
            SELECT dr.*, drel.claim_reference, drel.released_at
            FROM document_requests dr
            LEFT JOIN document_releases drel ON drel.document_request_id = dr.id
            WHERE dr.student_id = ?
            ORDER BY dr.requested_at DESC, dr.id DESC
        ", [$studentId]) : [],
    ];
}

function siemsGetHumanResourcesOverview() {
    return [
        'jobs' => siemsTableExists('job_postings') ? siemsFetchAll("SELECT * FROM job_postings ORDER BY id DESC") : [],
        'applicants' => siemsTableExists('applicants') ? siemsFetchAll("
            SELECT a.*, jp.job_title, jp.department_name
            FROM applicants a
            INNER JOIN job_postings jp ON jp.id = a.job_posting_id
            ORDER BY a.id DESC
        ") : [],
        'employees' => siemsTableExists('employee_records') ? siemsFetchAll("
            SELECT er.*, u.full_name, u.employee_id, u.email
            FROM employee_records er
            INNER JOIN users u ON u.id = er.user_id
            ORDER BY er.hired_at DESC, er.id DESC
        ") : [],
        'performance' => siemsTableExists('employee_performance') ? siemsFetchAll("
            SELECT ep.*, er.department_name, er.position_title, u.full_name
            FROM employee_performance ep
            INNER JOIN employee_records er ON er.id = ep.employee_record_id
            INNER JOIN users u ON u.id = er.user_id
            ORDER BY ep.id DESC
        ") : [],
        'clearances' => siemsTableExists('employee_clearances') ? siemsFetchAll("
            SELECT ec.*, er.department_name, er.position_title, u.full_name
            FROM employee_clearances ec
            INNER JOIN employee_records er ON er.id = ec.employee_record_id
            INNER JOIN users u ON u.id = er.user_id
            ORDER BY ec.id DESC
        ") : [],
    ];
}

function siemsGetAdminClinicOverview() {
    return [
        'medical_records' => siemsTableExists('medical_records') ? siemsFetchAll("
            SELECT mr.*, u.full_name, u.program, u.year_level
            FROM medical_records mr
            INNER JOIN users u ON u.student_id = mr.student_id
            ORDER BY u.full_name ASC
        ") : [],
        'consultations' => siemsTableExists('clinic_consultations') ? siemsFetchAll("
            SELECT cc.*, u.full_name, u.program
            FROM clinic_consultations cc
            INNER JOIN users u ON u.student_id = cc.student_id
            ORDER BY cc.consultation_date DESC, cc.id DESC
        ") : [],
        'medicines' => siemsTableExists('medicines') ? siemsFetchAll("SELECT * FROM medicines ORDER BY medicine_name ASC") : [],
        'dispensings' => siemsTableExists('medicine_dispensings') ? siemsFetchAll("
            SELECT md.*, m.medicine_name, cc.student_id, u.full_name
            FROM medicine_dispensings md
            INNER JOIN medicines m ON m.id = md.medicine_id
            INNER JOIN clinic_consultations cc ON cc.id = md.consultation_id
            INNER JOIN users u ON u.student_id = cc.student_id
            ORDER BY md.id DESC
        ") : [],
        'clearances' => siemsTableExists('medical_clearances') ? siemsFetchAll("
            SELECT mc.*, u.full_name, u.program
            FROM medical_clearances mc
            INNER JOIN users u ON u.student_id = mc.student_id
            ORDER BY mc.issued_at DESC, mc.id DESC
        ") : [],
        'incidents' => siemsTableExists('health_incidents') ? siemsFetchAll("
            SELECT hi.*, u.full_name, u.program
            FROM health_incidents hi
            INNER JOIN users u ON u.student_id = hi.student_id
            ORDER BY hi.incident_date DESC, hi.id DESC
        ") : [],
    ];
}

function siemsGetStudentClinicOverview($studentId) {
    return [
        'medical_record' => siemsTableExists('medical_records') ? siemsFetchOne("SELECT * FROM medical_records WHERE student_id = ? LIMIT 1", [$studentId]) : null,
        'consultations' => siemsTableExists('clinic_consultations') ? siemsFetchAll("
            SELECT *
            FROM clinic_consultations
            WHERE student_id = ?
            ORDER BY consultation_date DESC, id DESC
        ", [$studentId]) : [],
        'clearance' => siemsTableExists('medical_clearances') ? siemsFetchOne("
            SELECT *
            FROM medical_clearances
            WHERE student_id = ?
            ORDER BY issued_at DESC, id DESC
            LIMIT 1
        ", [$studentId]) : null,
        'incidents' => siemsTableExists('health_incidents') ? siemsFetchAll("
            SELECT *
            FROM health_incidents
            WHERE student_id = ?
            ORDER BY incident_date DESC, id DESC
        ", [$studentId]) : [],
    ];
}

function siemsGetUserManagementOverview() {
    return [
        'users' => siemsTableExists('users') ? siemsFetchAll("SELECT * FROM users ORDER BY created_at DESC, id DESC") : [],
        'roles' => siemsTableExists('roles') ? siemsFetchAll("SELECT * FROM roles ORDER BY role_name ASC") : [],
        'permissions' => siemsTableExists('permissions') ? siemsFetchAll("SELECT * FROM permissions ORDER BY permission_key ASC") : [],
        'role_permissions' => siemsTableExists('role_permissions') ? siemsFetchAll("
            SELECT rp.*, r.role_name, p.permission_key
            FROM role_permissions rp
            INNER JOIN roles r ON r.id = rp.role_id
            INNER JOIN permissions p ON p.id = rp.permission_id
            ORDER BY r.role_name ASC, p.permission_key ASC
        ") : [],
        'password_resets' => siemsTableExists('password_resets') ? siemsFetchAll("
            SELECT pr.*, u.full_name, u.student_id, u.employee_id
            FROM password_resets pr
            INNER JOIN users u ON u.id = pr.user_id
            ORDER BY pr.created_at DESC, pr.id DESC
        ") : [],
        'audit_logs' => siemsTableExists('audit_log') ? siemsFetchAll("
            SELECT al.*, u.full_name AS user_name
            FROM audit_log al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.created_at DESC, al.id DESC
            LIMIT 300
        ") : [],
    ];
}
?>
