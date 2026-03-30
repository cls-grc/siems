<?php
$page_title = 'SIEMS - Student Integrated Education Management System';
require_once 'config/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    $dashboard = resolveUserHomePath($_SESSION['student_id'] ?? '', $_SESSION['role'] ?? '');

    if ($dashboard !== null && $dashboard !== 'index.php') {
        header("Location: " . $dashboard);
        exit;
    }

    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    $_SESSION['message'] = 'Your session was reset because its home page could not be resolved.';
    $_SESSION['msg_type'] = 'warning';
}

$programs = [];
$periods = [];

try {
    $programs = $pdo->query("
        SELECT program_code, program_name, department_name
        FROM programs
        WHERE active = 1
        ORDER BY program_code ASC
        LIMIT 8
    ")->fetchAll();
} catch (Throwable $e) {
}

try {
    $periods = $pdo->query("
        SELECT academic_year, semester, period_status, start_date, end_date
        FROM academic_periods
        ORDER BY
            CASE period_status
                WHEN 'Open' THEN 1
                WHEN 'Upcoming' THEN 2
                ELSE 3
            END,
            id DESC
        LIMIT 3
    ")->fetchAll();
} catch (Throwable $e) {
}

?>
<?php include 'includes/header.php'; ?>

<section class="sms-hero mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="landing-kicker">SCHOOL MANAGEMENT SYSTEM</span>
            <h1 class="landing-title mt-3 mb-3">A school-first SMS for academics, services, finance, and campus operations</h1>
            <p class="landing-copy mb-4">
                SIEMS is built as a full campus information environment for students, designated subsystem administrators,
                and school offices. It supports academic workflows, financial services, records processing, clinic operations,
                HR hiring, and account management in one connected school system.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="login.php" class="btn btn-success btn-lg fw-bold px-4 py-3" style="border-radius: 12px;">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to System
                </a>
                <a href="public_hiring.php" class="btn btn-outline-success btn-lg fw-bold px-4 py-3" style="border-radius: 12px;">
                    <i class="bi bi-briefcase me-2"></i>View Hiring
                </a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="hero-campus-card">
                <div class="hero-campus-badge">School Offers & Campus Environment</div>
                <div class="hero-campus-grid">
                    <div class="hero-campus-item">
                        <span>Program Tracks</span>
                        <strong>Industry-ready courses</strong>
                    </div>
                    <div class="hero-campus-item">
                        <span>Learning Spaces</span>
                        <strong>Classrooms, labs, and offices</strong>
                    </div>
                    <div class="hero-campus-item">
                        <span>Student Support</span>
                        <strong>Records, clinic, and services</strong>
                    </div>
                    <div class="hero-campus-item">
                        <span>Campus Culture</span>
                        <strong>Safe and service-oriented environment</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="landing-card">
            <i class="bi bi-mortarboard landing-card-icon"></i>
            <h5 class="section-title mb-2">Student Academic Services</h5>
            <p class="row-text mb-0">Handles student information, registration, curriculum flow, class scheduling, and grading in one academic chain.</p>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="landing-card">
            <i class="bi bi-buildings landing-card-icon"></i>
            <h5 class="section-title mb-2">School Office Operations</h5>
            <p class="row-text mb-0">Documents, clinic services, HR workflows, and user administration are managed inside the same office ecosystem.</p>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="landing-card">
            <i class="bi bi-cash-coin landing-card-icon"></i>
            <h5 class="section-title mb-2">Billing and Cashiering</h5>
            <p class="row-text mb-0">Assessment, statement generation, payment validation, and accounting records stay aligned to the student lifecycle.</p>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="landing-card">
            <i class="bi bi-shield-lock landing-card-icon"></i>
            <h5 class="section-title mb-2">Designated Office Access</h5>
            <p class="row-text mb-0">Every subsystem can have its own designated admin account, while the super admin oversees the entire campus system.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="landing-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Academic Calendar Snapshot</h5>
                <span class="landing-mini-pill">School Term Monitor</span>
            </div>
            <?php if (!empty($periods)): ?>
                <?php foreach ($periods as $period): ?>
                    <div class="school-list-item">
                        <div>
                            <div class="row-val"><?php echo htmlspecialchars($period['academic_year'] . ' / ' . $period['semester']); ?></div>
                            <div class="row-text">
                                <?php echo htmlspecialchars(($period['start_date'] ?? 'TBA') . ' to ' . ($period['end_date'] ?? 'TBA')); ?>
                            </div>
                        </div>
                        <span class="landing-status-pill"><?php echo htmlspecialchars($period['period_status']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row-text">No academic periods available yet.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="landing-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Program Offerings</h5>
                <span class="landing-mini-pill">Active Programs</span>
            </div>
            <?php if (!empty($programs)): ?>
                <?php foreach ($programs as $program): ?>
                    <div class="school-list-item">
                        <div>
                            <div class="row-val"><?php echo htmlspecialchars($program['program_code']); ?></div>
                            <div class="row-text"><?php echo htmlspecialchars($program['program_name']); ?></div>
                        </div>
                        <span class="group-label-grey"><?php echo htmlspecialchars($program['department_name']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row-text">No program offerings found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="landing-banner mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <h5 class="section-title mb-2">School Hiring Board</h5>
            <p class="row-text mb-0">Teaching and staff vacancies now open in a separate public hiring module where applicants can view openings and submit their requirements.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="public_hiring.php" class="btn btn-success fw-bold px-4 py-3" style="border-radius: 10px;">
                <i class="bi bi-briefcase me-2"></i>Open Hiring Module
            </a>
        </div>
    </div>
</section>

<div class="landing-banner">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <h5 class="section-title mb-2">Account Creation Is Managed Internally</h5>
            <p class="row-text mb-0">There is no public registration page. Student and teacher accounts are created by the school through subsystem 10, while public applicants can use the HR hiring board above.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="login.php" class="btn btn-outline-success fw-bold px-4 py-2" style="border-radius: 10px;">
                Proceed to Login
            </a>
        </div>
    </div>
</div>

<style>
.sms-hero {
    padding: 1rem 0 1.5rem;
    background:
        radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 35%),
        linear-gradient(135deg, #f0fdf4 0%, #ffffff 45%, #f8fafc 100%);
    border: 1px solid #d1fae5;
    border-radius: 28px;
    padding: 2rem;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.05);
}

.landing-kicker {
    display: inline-flex;
    align-items: center;
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.landing-title {
    color: #0f172a;
    font-weight: 900;
    font-size: clamp(2rem, 4vw, 3.5rem);
    line-height: 1.05;
    letter-spacing: -1px;
}

.landing-copy {
    color: #475569;
    font-size: 1.02rem;
    line-height: 1.75;
    max-width: 58ch;
}

.landing-section-title {
    color: #0f172a;
    font-weight: 900;
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    letter-spacing: -0.6px;
}

.hero-campus-card {
    background: linear-gradient(145deg, #0f172a 0%, #14532d 100%);
    border-radius: 24px;
    padding: 1.25rem;
    color: white;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
}

.hero-campus-badge {
    display: inline-flex;
    padding: 0.45rem 0.75rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.14);
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.45px;
    margin-bottom: 1rem;
}

.hero-campus-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.hero-campus-item {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    padding: 1rem;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hero-campus-item span {
    color: rgba(255,255,255,0.74);
    font-size: 0.8rem;
    font-weight: 700;
}

.hero-campus-item strong {
    font-size: 1.3rem;
    font-weight: 900;
}

.landing-card {
    height: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fffb 100%);
    padding: 1.25rem;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}

.landing-card-icon {
    display: inline-flex;
    margin-bottom: 0.9rem;
    color: #16a34a;
    font-size: 1.7rem;
}

.landing-banner {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: #f8fafc;
    padding: 1.25rem;
}

.landing-mini-pill,
.landing-status-pill,
.career-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.35px;
}

.landing-mini-pill {
    background: #dcfce7;
    color: #166534;
}

.landing-status-pill {
    background: #dbeafe;
    color: #1d4ed8;
}

.school-list-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.95rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.school-list-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

@media (max-width: 991.98px) {
    .sms-hero {
        padding: 1.25rem;
    }

    .hero-campus-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
