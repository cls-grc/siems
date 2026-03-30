<?php
session_start();
require_once '../config/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/integration.php';
require_once '../includes/auth.php';

requireSubsystemAccess('student_information');

$page_title = 'Student Information';

function getStudentPhotoDirectory() {
    return __DIR__ . '/../uploads/student_photos';
}

function findStudentPhotoPath($studentId) {
    $photoDir = getStudentPhotoDirectory();
    foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
        $path = $photoDir . '/' . $studentId . '.' . $ext;
        if (file_exists($path)) {
            return $path;
        }
    }

    return null;
}

function getStudentPhotoUrl($studentId) {
    $path = findStudentPhotoPath($studentId);
    if ($path === null) {
        return null;
    }

    return '../uploads/student_photos/' . basename($path) . '?v=' . filemtime($path);
}

function buildStudentBarcodeSvg($value) {
    $cleanValue = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $value);
    if ($cleanValue === '') {
        $cleanValue = 'UNKNOWN';
    }

    $bars = '';
    $x = 0;
    $barHeight = 70;
    $patternSeed = strtoupper($cleanValue);

    for ($i = 0; $i < strlen($patternSeed); $i++) {
        $ascii = ord($patternSeed[$i]);
        for ($bit = 0; $bit < 7; $bit++) {
            $isDark = (($ascii >> $bit) & 1) === 1;
            $width = ($bit % 2 === 0) ? 2 : 1;
            if ($isDark) {
                $bars .= '<rect x="' . $x . '" y="0" width="' . $width . '" height="' . $barHeight . '" fill="#111827" />';
            }
            $x += $width + 1;
        }
        $x += 2;
    }

    $width = max($x + 10, 180);
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' 95" role="img" aria-label="Student barcode">'
        . '<rect width="' . $width . '" height="95" fill="#ffffff" />'
        . $bars
        . '<text x="' . ($width / 2) . '" y="88" text-anchor="middle" font-size="12" font-family="Arial, sans-serif" fill="#111827">' . htmlspecialchars($cleanValue, ENT_QUOTES, 'UTF-8') . '</text>'
        . '</svg>';
}

$studentTablesReady = siemsTableExists('users');
if (!$studentTablesReady) {
    $_SESSION['message'] = 'Unified student tables are not available yet in the active database.';
    $_SESSION['msg_type'] = 'danger';
}

$search = trim($_GET['search'] ?? $_POST['search'] ?? '');
$selectedStudentId = $_GET['student_id'] ?? $_POST['student_id'] ?? '';
$programs = $studentTablesReady ? siemsGetPrograms() : [];
$module = $_GET['module'] ?? '';
$allowedModules = ['registration', 'profile', 'records', 'id', 'status', 'audit'];
if (!in_array($module, $allowedModules, true)) {
    $module = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $studentTablesReady) {
    if (isset($_POST['save_student_registration']) && !empty($_POST['new_student_id'])) {
        $newStudentId = strtoupper(trim($_POST['new_student_id']));
        $fullName = trim($_POST['new_full_name'] ?? '');
        $program = trim($_POST['new_program'] ?? '');
        $yearLevel = (int) ($_POST['new_year_level'] ?? 1);
        $email = trim($_POST['new_email'] ?? '');
        $phone = trim($_POST['new_phone'] ?? '');
        $password = trim($_POST['new_password'] ?? '') ?: 'password';
        $statusDate = $_POST['new_status_effective_date'] ?? date('Y-m-d');
        $studentStatus = trim($_POST['new_student_status'] ?? 'Active');
        $enrollmentStatus = trim($_POST['new_enrollment_status'] ?? 'Pending');
        $firstName = trim($_POST['new_first_name'] ?? '');
        $middleName = trim($_POST['new_middle_name'] ?? '') ?: null;
        $lastName = trim($_POST['new_last_name'] ?? '');
        $birthdate = $_POST['new_birthdate'] ?: null;
        $sex = trim($_POST['new_sex'] ?? '') ?: null;
        $civilStatus = trim($_POST['new_civil_status'] ?? '') ?: null;
        $address = trim($_POST['new_address'] ?? '') ?: null;
        $guardianName = trim($_POST['new_guardian_name'] ?? '') ?: null;
        $guardianContact = trim($_POST['new_guardian_contact'] ?? '') ?: null;

        if ($fullName === '' && $firstName !== '' && $lastName !== '') {
            $fullName = trim($firstName . ' ' . $lastName);
        }

        try {
            $pdo->beginTransaction();

            $duplicateUser = siemsFetchOne("SELECT student_id FROM users WHERE student_id = ? LIMIT 1", [$newStudentId]);
            if ($duplicateUser) {
                throw new RuntimeException('Student ID already exists.');
            }

            $pdo->prepare("
                INSERT INTO users
                    (student_id, full_name, email, phone, program, year_level, enrollment_status, student_status, role, password_hash, must_change_password, is_active)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, 'student', ?, 1, 1)
            ")->execute([
                $newStudentId,
                $fullName,
                $email !== '' ? $email : null,
                $phone !== '' ? $phone : null,
                $program !== '' ? $program : null,
                $yearLevel > 0 ? $yearLevel : 1,
                $enrollmentStatus,
                $studentStatus,
                password_hash($password, PASSWORD_DEFAULT),
            ]);

            if (siemsTableExists('student_profiles') && $firstName !== '' && $lastName !== '') {
                $pdo->prepare("
                    INSERT INTO student_profiles
                        (student_id, first_name, middle_name, last_name, birthdate, sex, civil_status, address, guardian_name, guardian_contact)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $newStudentId,
                    $firstName,
                    $middleName,
                    $lastName,
                    $birthdate,
                    $sex,
                    $civilStatus,
                    $address,
                    $guardianName,
                    $guardianContact,
                ]);
            }

            if (siemsTableExists('student_status_history')) {
                $pdo->prepare("
                    INSERT INTO student_status_history
                        (student_id, status_value, remarks, effective_date, recorded_by)
                    VALUES
                        (?, ?, ?, ?, ?)
                ")->execute([
                    $newStudentId,
                    $studentStatus,
                    'Initial registration in Student Information subsystem',
                    $statusDate,
                    $_SESSION['user_id'],
                ]);
            }

            if (siemsTableExists('student_ids')) {
                $pdo->prepare("
                    INSERT INTO student_ids
                        (student_id, card_number, qr_code_value, issued_at, valid_until, status)
                    VALUES
                        (?, ?, ?, NOW(), ?, 'Active')
                ")->execute([
                    $newStudentId,
                    'SID-' . $newStudentId,
                    'QR-' . $newStudentId,
                    $_POST['new_valid_until'] ?: null,
                ]);
            }

            siemsLogSubsystemEvent(
                'Student Information',
                'Registered student profile',
                $newStudentId,
                null,
                $studentStatus . ' / ' . $enrollmentStatus
            );

            $pdo->commit();
            $_SESSION['message'] = 'Student registered successfully. Default password: ' . $password;
            $_SESSION['msg_type'] = 'success';
            header('Location: student_information.php?module=profile&student_id=' . urlencode($newStudentId));
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $_SESSION['message'] = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to register student.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: student_information.php?module=registration');
            exit;
        }
    }

    if (isset($_POST['save_student_info']) && !empty($_POST['student_id'])) {
        $selectedStudentId = trim($_POST['student_id']);
        $statusDate = $_POST['status_effective_date'] ?? date('Y-m-d');

        try {
            $pdo->beginTransaction();

            $existingUser = siemsFetchOne("SELECT * FROM users WHERE student_id = ? LIMIT 1", [$selectedStudentId]);
            if (!$existingUser) {
                throw new RuntimeException('Student record not found.');
            }

        $newFullName = trim($_POST['full_name'] ?? $existingUser['full_name']);
        $newEmail = trim($_POST['email'] ?? '');
        $newPhone = trim($_POST['phone'] ?? '');
        $newProgram = trim($_POST['program'] ?? '');
        $newYearLevel = (int) ($_POST['year_level'] ?? 0);
        $newEnrollmentStatus = trim($_POST['enrollment_status'] ?? $existingUser['enrollment_status']);
        $newStudentStatus = trim($_POST['student_status'] ?? $existingUser['student_status']);

        $stmt = $pdo->prepare("
            UPDATE users
            SET full_name = ?, email = ?, phone = ?, program = ?, year_level = ?, enrollment_status = ?, student_status = ?
            WHERE student_id = ?
        ");
        $stmt->execute([
            $newFullName,
            $newEmail !== '' ? $newEmail : null,
            $newPhone !== '' ? $newPhone : null,
            $newProgram !== '' ? $newProgram : null,
            $newYearLevel > 0 ? $newYearLevel : null,
            $newEnrollmentStatus,
            $newStudentStatus,
            $selectedStudentId,
        ]);

        if (siemsTableExists('student_profiles')) {
            $profile = siemsFetchOne("SELECT id FROM student_profiles WHERE student_id = ? LIMIT 1", [$selectedStudentId]);
            $profileParams = [
                trim($_POST['first_name'] ?? ''),
                trim($_POST['middle_name'] ?? '') ?: null,
                trim($_POST['last_name'] ?? ''),
                $_POST['birthdate'] ?: null,
                trim($_POST['sex'] ?? '') ?: null,
                trim($_POST['civil_status'] ?? '') ?: null,
                trim($_POST['address'] ?? '') ?: null,
                trim($_POST['guardian_name'] ?? '') ?: null,
                trim($_POST['guardian_contact'] ?? '') ?: null,
            ];

            if ($profile) {
                $stmt = $pdo->prepare("
                    UPDATE student_profiles
                    SET first_name = ?, middle_name = ?, last_name = ?, birthdate = ?, sex = ?, civil_status = ?, address = ?, guardian_name = ?, guardian_contact = ?
                    WHERE student_id = ?
                ");
                $stmt->execute([...$profileParams, $selectedStudentId]);
            } elseif ($profileParams[0] !== '' && $profileParams[2] !== '') {
                $stmt = $pdo->prepare("
                    INSERT INTO student_profiles
                        (student_id, first_name, middle_name, last_name, birthdate, sex, civil_status, address, guardian_name, guardian_contact)
                    VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$selectedStudentId, ...$profileParams]);
            }
        }

        if (siemsTableExists('student_status_history') && $existingUser['student_status'] !== $newStudentStatus) {
            $stmt = $pdo->prepare("
                INSERT INTO student_status_history
                    (student_id, status_value, remarks, effective_date, recorded_by)
                VALUES
                    (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $selectedStudentId,
                $newStudentStatus,
                trim($_POST['status_remarks'] ?? 'Updated from student information module') ?: null,
                $statusDate,
                $_SESSION['user_id'],
            ]);
        }

        if (siemsTableExists('student_ids')) {
            $cardNumber = trim($_POST['card_number'] ?? '');
            $qrCode = trim($_POST['qr_code_value'] ?? '');
            $validUntil = $_POST['valid_until'] ?: null;
            $cardStatus = trim($_POST['id_status'] ?? 'Active');

            if ($cardNumber !== '' || $qrCode !== '' || $validUntil !== null) {
                $existingCard = siemsFetchOne("
                    SELECT id
                    FROM student_ids
                    WHERE student_id = ?
                    ORDER BY issued_at DESC, id DESC
                    LIMIT 1
                ", [$selectedStudentId]);

                if ($existingCard) {
                    $stmt = $pdo->prepare("
                        UPDATE student_ids
                        SET card_number = ?, qr_code_value = ?, valid_until = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $cardNumber !== '' ? $cardNumber : 'SID-' . $selectedStudentId,
                        $qrCode !== '' ? $qrCode : null,
                        $validUntil,
                        $cardStatus,
                        $existingCard['id'],
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO student_ids
                            (student_id, card_number, qr_code_value, issued_at, valid_until, status)
                        VALUES
                            (?, ?, ?, NOW(), ?, ?)
                    ");
                    $stmt->execute([
                        $selectedStudentId,
                        $cardNumber !== '' ? $cardNumber : 'SID-' . $selectedStudentId,
                        $qrCode !== '' ? $qrCode : ('QR-' . $selectedStudentId),
                        $validUntil,
                        $cardStatus,
                    ]);
                }
            }
        }

        siemsLogSubsystemEvent(
            'Student Information',
            'Updated student profile',
            $selectedStudentId,
            $existingUser['student_status'] . ' / ' . $existingUser['enrollment_status'],
            $newStudentStatus . ' / ' . $newEnrollmentStatus
        );

            $pdo->commit();
            $_SESSION['message'] = 'Student information updated successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: student_information.php?module=profile&student_id=' . urlencode($selectedStudentId) . ($search !== '' ? '&search=' . urlencode($search) : ''));
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $_SESSION['message'] = 'Unable to save student information.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: student_information.php?module=profile&student_id=' . urlencode($selectedStudentId));
            exit;
        }
    }

    if (isset($_POST['save_student_id_assets']) && !empty($_POST['student_id'])) {
        $selectedStudentId = trim($_POST['student_id']);

        try {
            $pdo->beginTransaction();

            $existingUser = siemsFetchOne("SELECT student_id FROM users WHERE student_id = ? LIMIT 1", [$selectedStudentId]);
            if (!$existingUser) {
                throw new RuntimeException('Student record not found.');
            }

            $cardNumber = trim($_POST['card_number'] ?? '');
            $qrCode = trim($_POST['qr_code_value'] ?? '');
            $validUntil = $_POST['valid_until'] ?: null;
            $cardStatus = trim($_POST['id_status'] ?? 'Active');

            if (siemsTableExists('student_ids')) {
                $existingCard = siemsFetchOne("
                    SELECT id
                    FROM student_ids
                    WHERE student_id = ?
                    ORDER BY issued_at DESC, id DESC
                    LIMIT 1
                ", [$selectedStudentId]);

                if ($existingCard) {
                    $stmt = $pdo->prepare("
                        UPDATE student_ids
                        SET card_number = ?, qr_code_value = ?, valid_until = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $cardNumber !== '' ? $cardNumber : 'SID-' . $selectedStudentId,
                        $qrCode !== '' ? $qrCode : ('QR-' . $selectedStudentId),
                        $validUntil,
                        $cardStatus,
                        $existingCard['id'],
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO student_ids
                            (student_id, card_number, qr_code_value, issued_at, valid_until, status)
                        VALUES
                            (?, ?, ?, NOW(), ?, ?)
                    ");
                    $stmt->execute([
                        $selectedStudentId,
                        $cardNumber !== '' ? $cardNumber : 'SID-' . $selectedStudentId,
                        $qrCode !== '' ? $qrCode : ('QR-' . $selectedStudentId),
                        $validUntil,
                        $cardStatus,
                    ]);
                }
            }

            if (!empty($_FILES['student_photo']['name'] ?? '')) {
                $photoDir = getStudentPhotoDirectory();
                if (!is_dir($photoDir)) {
                    mkdir($photoDir, 0777, true);
                }

                $tmpPath = $_FILES['student_photo']['tmp_name'] ?? '';
                if (!is_uploaded_file($tmpPath)) {
                    throw new RuntimeException('Student photo upload failed.');
                }

                $extension = strtolower(pathinfo($_FILES['student_photo']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowedExtensions, true)) {
                    throw new RuntimeException('Student photo must be JPG, JPEG, PNG, GIF, or WEBP.');
                }

                foreach ($allowedExtensions as $existingExtension) {
                    $existingPath = $photoDir . '/' . $selectedStudentId . '.' . $existingExtension;
                    if (file_exists($existingPath)) {
                        unlink($existingPath);
                    }
                }

                $destination = $photoDir . '/' . $selectedStudentId . '.' . $extension;
                if (!move_uploaded_file($tmpPath, $destination)) {
                    throw new RuntimeException('Unable to save the student photo.');
                }
            }

            siemsLogSubsystemEvent(
                'Student Information',
                'Updated student ID assets',
                $selectedStudentId,
                null,
                'Card assets refreshed'
            );

            $pdo->commit();
            $_SESSION['message'] = 'Student ID assets updated successfully.';
            $_SESSION['msg_type'] = 'success';
            header('Location: student_information.php?module=id&student_id=' . urlencode($selectedStudentId));
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $_SESSION['message'] = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to update student ID assets.';
            $_SESSION['msg_type'] = 'danger';
            header('Location: student_information.php?module=id&student_id=' . urlencode($selectedStudentId));
            exit;
        }
    }
}

$studentsSql = "
    SELECT u.student_id, u.full_name, u.program, u.year_level, u.enrollment_status, u.student_status,
           sp.address
    FROM users u
    LEFT JOIN student_profiles sp ON sp.student_id = u.student_id
    WHERE u.role = 'student'
";
$studentParams = [];
if ($search !== '') {
    $studentsSql .= " AND (u.student_id LIKE ? OR u.full_name LIKE ? OR u.program LIKE ?)";
    $like = '%' . $search . '%';
    $studentParams = [$like, $like, $like];
}
$studentsSql .= " ORDER BY u.full_name ASC";
$students = $studentTablesReady ? siemsFetchAll($studentsSql, $studentParams) : [];

if ($selectedStudentId === '' && !empty($students)) {
    $selectedStudentId = $students[0]['student_id'];
}

$bundle = ($studentTablesReady && $selectedStudentId !== '') ? siemsGetStudentProfileBundle($selectedStudentId) : null;
$selectedUser = $bundle['user'] ?? null;
$selectedProfile = $bundle['profile'] ?? null;
$selectedCard = $bundle['student_id_card'] ?? null;
$statusHistory = $bundle['status_history'] ?? [];
$academicRecords = $bundle['academic_records'] ?? [];
$selectedPhotoUrl = $selectedStudentId !== '' ? getStudentPhotoUrl($selectedStudentId) : null;
$barcodeValue = $selectedCard['qr_code_value'] ?? ($selectedCard['card_number'] ?? ('SID-' . $selectedStudentId));
$barcodeSvg = $selectedStudentId !== '' ? buildStudentBarcodeSvg($barcodeValue) : '';
$statusCounts = [
    'active' => 0,
    'alumni' => 0,
    'dropped' => 0,
];
foreach ($students as $student) {
    $status = strtolower((string) ($student['student_status'] ?? ''));
    if ($status === 'active') {
        $statusCounts['active']++;
    } elseif ($status === 'alumni') {
        $statusCounts['alumni']++;
    } elseif ($status === 'dropped') {
        $statusCounts['dropped']++;
    }
}

$studentActivityLogs = siemsTableExists('audit_log') ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE " . (siemsColumnExists('audit_log', 'subsystem') ? "al.subsystem = 'Student Information'" : "(al.action LIKE 'Updated student profile%' OR al.action LIKE 'Registered student profile%')") . "
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 12
") : [];

$selectedStudentLogs = ($selectedStudentId !== '' && siemsTableExists('audit_log')) ? siemsFetchAll("
    SELECT al.*, u.full_name AS user_name
    FROM audit_log al
    LEFT JOIN users u ON u.id = al.user_id
    WHERE al.student_id = ?
    ORDER BY al.created_at DESC, al.id DESC
    LIMIT 10
", [$selectedStudentId]) : [];
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <div>
        <h3 class="dashboard-title mb-1"><i class="bi bi-person-vcard text-success me-2"></i>Student Information</h3>
        <div class="row-text">Subsystem 1 for registration, student records, ID generation, status tracking, and audit visibility.</div>
    </div>
    <a href="siems_hub.php" class="btn btn-outline-secondary fw-bold" style="border-radius: 8px;">
        <i class="bi bi-arrow-left me-1"></i> SIEMS Hub
    </a>
</div>

<div class="card-outline bg-white mb-4">
    <div class="balance-header py-3 px-4">
        <span class="section-title">QUICK ACTION BUTTONS</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=registration" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-plus d-block fs-4 mb-2"></i>Registration
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=profile<?php echo $selectedStudentId !== '' ? '&student_id=' . urlencode($selectedStudentId) : ''; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-pencil-square d-block fs-4 mb-2"></i>Profile Update
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=records<?php echo $selectedStudentId !== '' ? '&student_id=' . urlencode($selectedStudentId) : ''; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-journal-text d-block fs-4 mb-2"></i>Academic Records
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=id<?php echo $selectedStudentId !== '' ? '&student_id=' . urlencode($selectedStudentId) : ''; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-person-vcard d-block fs-4 mb-2"></i>ID Generation
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=status<?php echo $selectedStudentId !== '' ? '&student_id=' . urlencode($selectedStudentId) : ''; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-activity d-block fs-4 mb-2"></i>Status Tracking
                </a>
            </div>
            <div class="col-md-4 col-lg-2">
                <a href="student_information.php?module=audit<?php echo $selectedStudentId !== '' ? '&student_id=' . urlencode($selectedStudentId) : ''; ?>" class="btn btn-outline-success w-100 fw-bold py-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius: 10px; min-height: 130px;">
                    <i class="bi bi-clock-history d-block fs-4 mb-2"></i>Audit Logs
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ($module === ''): ?>
<div class="card-outline bg-white p-5 text-center">
    <i class="bi bi-grid-1x2 fs-1 text-success d-block mb-3"></i>
    <h4 class="dashboard-title mb-2">Subsystem 1 Module Launcher</h4>
    <div class="row-text">Choose a quick action button above to open one module at a time.</div>
</div>
<?php elseif ($module === 'registration'): ?>
<div class="row g-4">
    <div class="col-lg-7 mx-auto">
        <div class="card-outline bg-white mb-4" id="registration-module">
            <div class="balance-header py-3 px-4">
                <span class="section-title">STUDENT PROFILE REGISTRATION</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" class="row g-3">
                    <div class="col-12">
                        <input type="text" name="new_student_id" class="form-control row-text" placeholder="Student ID" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-12">
                        <input type="text" name="new_full_name" class="form-control row-text" placeholder="Full name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="new_first_name" class="form-control row-text" placeholder="First name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="new_middle_name" class="form-control row-text" placeholder="Middle name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="new_last_name" class="form-control row-text" placeholder="Last name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <select name="new_program" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <option value="">Select program</option>
                            <?php foreach ($programs as $programRow): ?>
                                <option value="<?php echo htmlspecialchars($programRow['program_code']); ?>"><?php echo htmlspecialchars($programRow['program_code'] . ' - ' . $programRow['program_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="new_year_level" min="1" max="6" value="1" class="form-control row-text" placeholder="Year level" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="email" name="new_email" class="form-control row-text" placeholder="Email" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_phone" class="form-control row-text" placeholder="Phone" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="password" name="new_password" class="form-control row-text" placeholder="Default password: password" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="date" name="new_birthdate" class="form-control row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <select name="new_student_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <?php foreach (['Active', 'Alumni', 'Dropped', 'Graduated', 'On Leave'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $status === 'Active' ? 'selected' : ''; ?>><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="new_enrollment_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            <?php foreach (['Pending', 'Submitted', 'For Review', 'Validated', 'Approved', 'Paid', 'Enrolled', 'Returned'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $status === 'Pending' ? 'selected' : ''; ?>><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_sex" class="form-control row-text" placeholder="Sex" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_civil_status" class="form-control row-text" placeholder="Civil status" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-12">
                        <input type="text" name="new_address" class="form-control row-text" placeholder="Address" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_guardian_name" class="form-control row-text" placeholder="Guardian name" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_guardian_contact" class="form-control row-text" placeholder="Guardian contact" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val">Status Effective Date</label>
                        <input type="date" name="new_status_effective_date" value="<?php echo date('Y-m-d'); ?>" class="form-control row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val">ID Valid Until</label>
                        <input type="date" name="new_valid_until" class="form-control row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="save_student_registration" class="btn btn-success fw-bold w-100" style="border-radius: 8px;">
                            <i class="bi bi-person-plus me-1"></i> Register Student Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php elseif (in_array($module, ['profile', 'records', 'id', 'status', 'audit'], true)): ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-outline bg-white mb-4">
            <div class="balance-header py-3 px-4">
                <span class="section-title">STUDENT DIRECTORY</span>
            </div>
            <div class="card-body p-4">
                <form method="GET" class="mb-3">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control row-text" placeholder="Search student ID, name, or program" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                </form>
                <div style="max-height: 420px; overflow-y: auto;">
                    <?php foreach ($students as $student): ?>
                        <a href="?<?php echo $module !== '' ? 'module=' . urlencode($module) . '&' : ''; ?>student_id=<?php echo urlencode($student['student_id']); ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="text-decoration-none">
                            <div class="p-3 mb-2 rounded-3 <?php echo $selectedStudentId === $student['student_id'] ? 'border border-success bg-light' : 'border'; ?>" style="border-color: #e2e8f0 !important;">
                                <div class="row-val"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                <div class="row-text"><?php echo htmlspecialchars($student['student_id']); ?> | <?php echo htmlspecialchars($student['program'] ?? 'N/A'); ?></div>
                                <div class="group-label-grey mt-1">Year <?php echo htmlspecialchars((string) ($student['year_level'] ?? 'N/A')); ?> | <?php echo htmlspecialchars($student['enrollment_status']); ?> | <?php echo htmlspecialchars($student['student_status']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            No students found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($module === 'audit'): ?>
            <div class="card-outline bg-white mb-4" id="audit-module">
                <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="section-title">USER ACTIVITY LOGS</span>
                    <a href="audit_trail.php" class="btn btn-outline-success btn-sm fw-bold" style="border-radius: 8px;">
                        Open Audit Trail
                    </a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($studentActivityLogs)): ?>
                        <?php foreach ($studentActivityLogs as $log): ?>
                            <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="row-val"><?php echo htmlspecialchars($log['action']); ?></div>
                                    <div class="group-label-grey"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></div>
                                </div>
                                <div class="row-text mt-2"><?php echo htmlspecialchars($log['student_id'] ?? 'General event'); ?></div>
                                <div class="group-label-grey mt-2">By: <?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted">No recent subsystem activity found.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-8">
        <?php if ($selectedUser): ?>
            <?php if ($module === 'profile'): ?>
                <div class="card-outline bg-white mb-4" id="profile-module">
                    <div class="balance-header py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="section-title">STUDENT PERSONAL INFORMATION UPDATE</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <?php echo htmlspecialchars($selectedUser['student_id']); ?>
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($selectedUser['student_id']); ?>">
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

                            <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label row-val">Full Name</label>
                                <input type="text" name="full_name" class="form-control row-text" value="<?php echo htmlspecialchars($selectedUser['full_name']); ?>" required style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Program</label>
                                <input type="text" name="program" class="form-control row-text" value="<?php echo htmlspecialchars($selectedUser['program'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label row-val">Email</label>
                                <input type="email" name="email" class="form-control row-text" value="<?php echo htmlspecialchars($selectedUser['email'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label row-val">Phone</label>
                                <input type="text" name="phone" class="form-control row-text" value="<?php echo htmlspecialchars($selectedUser['phone'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label row-val">Year Level</label>
                                <input type="number" name="year_level" min="1" max="6" class="form-control row-text" value="<?php echo htmlspecialchars((string) ($selectedUser['year_level'] ?? '')); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label row-val">Student Status</label>
                                <select name="student_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    <?php foreach (['Active', 'Alumni', 'Dropped', 'Graduated', 'On Leave'] as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($selectedUser['student_status'] ?? '') === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Enrollment Status</label>
                                <select name="enrollment_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    <?php foreach (['Pending', 'Submitted', 'For Review', 'Validated', 'Approved', 'Paid', 'Enrolled', 'Returned'] as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($selectedUser['enrollment_status'] ?? '') === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Status Effective Date</label>
                                <input type="date" name="status_effective_date" class="form-control row-text" value="<?php echo date('Y-m-d'); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label row-val">First Name</label>
                                <input type="text" name="first_name" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['first_name'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['middle_name'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Last Name</label>
                                <input type="text" name="last_name" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['last_name'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Birthdate</label>
                                <input type="date" name="birthdate" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['birthdate'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Sex</label>
                                <input type="text" name="sex" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['sex'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Civil Status</label>
                                <input type="text" name="civil_status" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['civil_status'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label row-val">Address</label>
                                <input type="text" name="address" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['address'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['guardian_name'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Guardian Contact</label>
                                <input type="text" name="guardian_contact" class="form-control row-text" value="<?php echo htmlspecialchars($selectedProfile['guardian_contact'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Card Number</label>
                                <input type="text" name="card_number" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['card_number'] ?? ''); ?>" placeholder="SID-<?php echo htmlspecialchars($selectedUser['student_id']); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">QR Code Value</label>
                                <input type="text" name="qr_code_value" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['qr_code_value'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">Card Valid Until</label>
                                <input type="date" name="valid_until" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['valid_until'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label row-val">ID Status</label>
                                <select name="id_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    <?php foreach (['Active', 'Expired', 'Replaced'] as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($selectedCard['status'] ?? 'Active') === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label row-val">Status Remarks</label>
                                <input type="text" name="status_remarks" class="form-control row-text" placeholder="Optional note for the status history log" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" name="save_student_info" class="btn btn-success fw-bold px-4" style="border-radius: 8px;">
                                    <i class="bi bi-save me-1"></i> Save Student Information
                                </button>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($module === 'status'): ?>
                <div class="col-md-12" id="status-module">
                    <div class="card-outline bg-white h-100">
                        <div class="balance-header py-3 px-4">
                            <span class="section-title">STATUS HISTORY</span>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($statusHistory)): ?>
                                <?php foreach ($statusHistory as $history): ?>
                                    <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="row-val"><?php echo htmlspecialchars($history['status_value']); ?></div>
                                            <div class="group-label-grey"><?php echo date('M j, Y', strtotime($history['effective_date'])); ?></div>
                                        </div>
                                        <div class="row-text mt-2"><?php echo htmlspecialchars($history['remarks'] ?? 'No remarks'); ?></div>
                                        <div class="group-label-grey mt-2">Recorded by: <?php echo htmlspecialchars($history['recorded_by_name'] ?? 'System'); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted">No status history yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'records'): ?>
                <div class="col-md-12" id="records-module">
                    <div class="card-outline bg-white h-100">
                        <div class="balance-header py-3 px-4">
                            <span class="section-title">ACADEMIC RECORDS VIEWER</span>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($academicRecords)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="group-label-grey">SUBJECT</th>
                                                <th class="group-label-grey">TERM</th>
                                                <th class="group-label-grey">GRADE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($academicRecords as $record): ?>
                                                <tr>
                                                    <td>
                                                        <div class="row-val"><?php echo htmlspecialchars($record['code']); ?></div>
                                                        <div class="row-text"><?php echo htmlspecialchars($record['description']); ?></div>
                                                    </td>
                                                    <td class="row-text"><?php echo htmlspecialchars($record['academic_year'] . ' / ' . $record['semester']); ?></td>
                                                    <td class="row-val"><?php echo htmlspecialchars((string) ($record['final_grade'] ?? '-')); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-muted">No academic records found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'id'): ?>
                <div class="col-md-12" id="id-module">
                    <div class="card-outline bg-white h-100">
                        <div class="balance-header py-3 px-4">
                            <span class="section-title">STUDENT ID GENERATION</span>
                        </div>
                        <div class="card-body p-4">
                            <style>
                                .student-id-card {
                                    width: 100%;
                                    max-width: 520px;
                                    margin: 0 auto;
                                    border-radius: 22px;
                                    overflow: hidden;
                                    border: 1px solid #d1fae5;
                                    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
                                    background: linear-gradient(160deg, #064e3b 0%, #10b981 55%, #ecfdf5 55%, #ffffff 100%);
                                }
                                .student-id-header {
                                    padding: 24px 24px 12px;
                                    color: #ffffff;
                                }
                                .student-id-body {
                                    padding: 18px 24px 24px;
                                    display: grid;
                                    grid-template-columns: 140px 1fr;
                                    gap: 18px;
                                    align-items: start;
                                }
                                .student-photo-frame {
                                    width: 140px;
                                    height: 170px;
                                    border-radius: 18px;
                                    overflow: hidden;
                                    background: #e5e7eb;
                                    border: 4px solid rgba(255, 255, 255, 0.75);
                                }
                                .student-photo-frame img {
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                }
                                .student-photo-placeholder {
                                    width: 100%;
                                    height: 100%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: #6b7280;
                                    font-weight: 700;
                                    text-align: center;
                                    padding: 12px;
                                    background: linear-gradient(135deg, #f3f4f6, #d1d5db);
                                }
                                .student-id-meta {
                                    display: grid;
                                    gap: 10px;
                                }
                                .student-id-label {
                                    font-size: 0.72rem;
                                    letter-spacing: 0.12em;
                                    text-transform: uppercase;
                                    color: #6b7280;
                                    font-weight: 700;
                                }
                                .student-id-value {
                                    font-size: 1rem;
                                    color: #111827;
                                    font-weight: 700;
                                }
                                .student-id-name {
                                    font-size: 1.4rem;
                                    color: #065f46;
                                    font-weight: 800;
                                    line-height: 1.15;
                                }
                                .student-barcode {
                                    background: #ffffff;
                                    border-radius: 16px;
                                    padding: 10px 12px 6px;
                                    border: 1px solid #d1d5db;
                                }
                                @media print {
                                    body * {
                                        visibility: hidden !important;
                                    }
                                    #printable-student-id,
                                    #printable-student-id * {
                                        visibility: visible !important;
                                    }
                                    #printable-student-id {
                                        position: absolute;
                                        left: 0;
                                        top: 0;
                                        width: 100%;
                                        margin: 0;
                                        padding: 24px;
                                        background: #ffffff;
                                    }
                                }
                            </style>

                            <form method="POST" enctype="multipart/form-data" class="mb-4">
                                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($selectedUser['student_id']); ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label row-val">Card Number</label>
                                        <input type="text" name="card_number" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['card_number'] ?? ('SID-' . $selectedUser['student_id'])); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label row-val">QR / Barcode Value</label>
                                        <input type="text" name="qr_code_value" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['qr_code_value'] ?? ('QR-' . $selectedUser['student_id'])); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label row-val">Valid Until</label>
                                        <input type="date" name="valid_until" class="form-control row-text" value="<?php echo htmlspecialchars($selectedCard['valid_until'] ?? ''); ?>" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label row-val">ID Status</label>
                                        <select name="id_status" class="form-select row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                            <?php foreach (['Active', 'Expired', 'Replaced'] as $status): ?>
                                                <option value="<?php echo $status; ?>" <?php echo ($selectedCard['status'] ?? 'Active') === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label row-val">Student Photo</label>
                                        <input type="file" name="student_photo" accept=".jpg,.jpeg,.png,.gif,.webp" class="form-control row-text" style="border: 2px solid #e2e8f0; border-radius: 8px;">
                                    </div>
                                    <div class="col-12 d-flex flex-wrap gap-2">
                                        <button type="submit" name="save_student_id_assets" class="btn btn-success fw-bold px-4" style="border-radius: 8px;">
                                            <i class="bi bi-save me-1"></i> Save ID Assets
                                        </button>
                                        <button type="button" onclick="window.print()" class="btn btn-outline-secondary fw-bold px-4" style="border-radius: 8px;">
                                            <i class="bi bi-printer me-1"></i> Print Student ID
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div id="printable-student-id">
                                <div class="student-id-card">
                                    <div class="student-id-header">
                                        <div class="group-label-grey text-white" style="opacity: 0.9;">SCHOOL IDENTIFICATION CARD</div>
                                        <div style="font-size: 1.35rem; font-weight: 800; letter-spacing: 0.04em;">SIEMS STUDENT ID</div>
                                    </div>
                                    <div class="student-id-body">
                                        <div class="student-photo-frame">
                                            <?php if ($selectedPhotoUrl): ?>
                                                <img src="<?php echo htmlspecialchars($selectedPhotoUrl); ?>" alt="Student photo">
                                            <?php else: ?>
                                                <div class="student-photo-placeholder">No Photo Uploaded</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="student-id-meta">
                                            <div>
                                                <div class="student-id-label">Student Name</div>
                                                <div class="student-id-name"><?php echo htmlspecialchars($selectedUser['full_name']); ?></div>
                                            </div>
                                            <div>
                                                <div class="student-id-label">Student ID</div>
                                                <div class="student-id-value"><?php echo htmlspecialchars($selectedUser['student_id']); ?></div>
                                            </div>
                                            <div>
                                                <div class="student-id-label">Program / Year</div>
                                                <div class="student-id-value"><?php echo htmlspecialchars(($selectedUser['program'] ?? 'N/A') . ' / Year ' . ($selectedUser['year_level'] ?? 'N/A')); ?></div>
                                            </div>
                                            <div>
                                                <div class="student-id-label">Card Number</div>
                                                <div class="student-id-value"><?php echo htmlspecialchars($selectedCard['card_number'] ?? ('SID-' . $selectedUser['student_id'])); ?></div>
                                            </div>
                                            <div>
                                                <div class="student-id-label">Valid Until</div>
                                                <div class="student-id-value"><?php echo htmlspecialchars($selectedCard['valid_until'] ?? 'Not set'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 0 24px 24px;">
                                        <div class="student-barcode">
                                            <?php echo $barcodeSvg; ?>
                                        </div>
                                        <div class="group-label-grey mt-2 text-center">Machine-readable ID code for <?php echo htmlspecialchars($selectedUser['student_id']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($module === 'audit'): ?>
                <div class="col-md-12">
                    <div class="card-outline bg-white h-100">
                        <div class="balance-header py-3 px-4">
                            <span class="section-title">AUDIT TRAIL FOR SELECTED STUDENT</span>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($selectedStudentLogs)): ?>
                                <?php foreach ($selectedStudentLogs as $log): ?>
                                    <div class="border rounded-3 p-3 mb-3" style="border-color: #e2e8f0 !important;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="row-val"><?php echo htmlspecialchars($log['action']); ?></div>
                                            <div class="group-label-grey"><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($log['created_at']))); ?></div>
                                        </div>
                                        <div class="row-text mt-2">Actor: <?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></div>
                                        <?php if (!empty($log['old_value']) || !empty($log['new_value'])): ?>
                                            <div class="group-label-grey mt-2">Change: <?php echo htmlspecialchars(trim(($log['old_value'] ?? '-') . ' -> ' . ($log['new_value'] ?? '-'))); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted">No audit trail entries found for this student yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card-outline bg-white p-5 text-center">
                <i class="bi bi-person-x fs-1 text-muted d-block mb-3"></i>
                <div class="row-text">Select a student from the left to manage profile details.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
