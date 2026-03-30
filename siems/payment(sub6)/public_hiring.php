<?php
$page_title = 'SIEMS - Public Hiring Board';
require_once 'config/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    $dashboard = getUserHomePath($_SESSION['student_id'] ?? '', $role);
    header("Location: " . $dashboard);
    exit;
}

$jobPostings = [];

function uploadHiringDocument(array $file, string $prefix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Please re-upload the hiring document.');
    }

    if (($file['size'] ?? 0) > (5 * 1024 * 1024)) {
        throw new RuntimeException('Uploaded documents must be 5MB or smaller.');
    }

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'doc', 'docx'];
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Only PDF, DOC, and DOCX files are allowed.');
    }

    $uploadDirectory = __DIR__ . '/uploads/hr_applications';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Unable to prepare the hiring uploads directory.');
    }

    $safePrefix = preg_replace('/[^a-z0-9]+/i', '-', strtolower($prefix)) ?: 'document';
    $filename = $safePrefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
    $targetPath = $uploadDirectory . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Unable to save the uploaded file.');
    }

    return 'uploads/hr_applications/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    $jobPostingId = (int) ($_POST['job_posting_id'] ?? 0);
    $applicantName = trim($_POST['applicant_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $highestEducation = trim($_POST['highest_education'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $teachingExperience = trim($_POST['teaching_experience'] ?? '');
    $portfolioLink = trim($_POST['portfolio_link'] ?? '');
    $coverLetter = trim($_POST['cover_letter'] ?? '');
    $requirements = array_map('trim', $_POST['requirements'] ?? []);
    $requirementsNotes = trim($_POST['requirements_notes'] ?? '');

    try {
        if (
            $jobPostingId <= 0 ||
            $applicantName === '' ||
            $email === '' ||
            $phone === '' ||
            $address === '' ||
            $highestEducation === '' ||
            $specialization === '' ||
            $coverLetter === ''
        ) {
            throw new RuntimeException('Please complete the required hiring details before submitting.');
        }

        if (count($requirements) < 2) {
            throw new RuntimeException('Please confirm the required application documents.');
        }

        $resumeFile = uploadHiringDocument($_FILES['resume_file'] ?? [], 'resume');
        if ($resumeFile === null) {
            throw new RuntimeException('Please upload your resume or CV.');
        }

        $supportingDocuments = uploadHiringDocument($_FILES['supporting_documents'] ?? [], 'supporting');
        $requirementSummary = implode(', ', $requirements);
        if ($requirementsNotes !== '') {
            $requirementSummary .= '. Notes: ' . $requirementsNotes;
        }

        $stmt = $pdo->prepare("
            INSERT INTO applicants (
                job_posting_id, applicant_name, email, phone, address, highest_education,
                specialization, teaching_experience, portfolio_link, cover_letter, resume_file,
                supporting_documents, requirements_notes, application_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Screening')
        ");
        $stmt->execute([
            $jobPostingId,
            $applicantName,
            $email,
            $phone,
            $address,
            $highestEducation,
            $specialization,
            $teachingExperience !== '' ? $teachingExperience : null,
            $portfolioLink !== '' ? $portfolioLink : null,
            $coverLetter,
            $resumeFile,
            $supportingDocuments,
            $requirementSummary !== '' ? $requirementSummary : null,
        ]);

        $_SESSION['message'] = 'Application submitted successfully. HR can now review your resume and requirements from subsystem 8.';
        $_SESSION['msg_type'] = 'success';
        header('Location: public_hiring.php#apply-posting');
        exit;
    } catch (Throwable $e) {
        $_SESSION['message'] = $e->getMessage() ?: 'Unable to submit application. Please complete all required fields and try again.';
        $_SESSION['msg_type'] = 'danger';
        header('Location: public_hiring.php#apply-posting');
        exit;
    }
}

try {
    $jobPostings = $pdo->query("
        SELECT id, job_title, department_name, employment_type, posting_status
        FROM job_postings
        WHERE posting_status = 'Open'
        ORDER BY
            CASE
                WHEN job_title LIKE '%Teacher%' THEN 1
                WHEN job_title LIKE '%Faculty%' THEN 1
                WHEN department_name LIKE '%Academic%' THEN 2
                ELSE 3
            END,
            id DESC
    ")->fetchAll();
} catch (Throwable $e) {
}
?>
<?php include 'includes/header.php'; ?>

<section class="public-hiring-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="landing-kicker">PUBLIC HIRING MODULE</span>
            <h1 class="landing-title mt-3 mb-3">School hiring board for teachers, staff, and campus support roles</h1>
            <p class="landing-copy mb-0">
                Browse current openings and submit a complete application with resume, contact details, cover letter, and supporting requirements.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="index.php" class="btn btn-outline-success fw-bold px-4 py-3" style="border-radius: 12px;">
                <i class="bi bi-arrow-left me-2"></i>Back to Landing Page
            </a>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <span class="landing-kicker">HR PUBLIC HIRING BOARD</span>
            <h2 class="landing-section-title mt-2 mb-2">Open school hiring and faculty opportunities</h2>
            <p class="landing-copy mb-0">
                Applicants can view open HR postings here and send an application directly into the Human Resource Management subsystem.
            </p>
        </div>
        <a href="login.php" class="btn btn-outline-success fw-bold px-4 py-2" style="border-radius: 10px;">
            HR / Staff Login
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="landing-card h-100">
                <h5 class="section-title mb-3">Current Openings</h5>
                <?php if (!empty($jobPostings)): ?>
                    <?php foreach ($jobPostings as $posting): ?>
                        <?php $isTeachingPost = stripos($posting['job_title'], 'teacher') !== false || stripos($posting['job_title'], 'faculty') !== false; ?>
                        <div class="career-card">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="row-val fs-6"><?php echo htmlspecialchars($posting['job_title']); ?></div>
                                    <div class="row-text mt-1"><?php echo htmlspecialchars($posting['department_name']); ?> | <?php echo htmlspecialchars($posting['employment_type']); ?></div>
                                </div>
                                <div class="text-end">
                                    <?php if ($isTeachingPost): ?>
                                        <span class="career-pill">Teaching</span>
                                    <?php endif; ?>
                                    <div class="group-label-grey mt-2"><?php echo htmlspecialchars($posting['posting_status']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="row-text">There are no open HR job postings at the moment.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div id="apply-posting" class="landing-card h-100">
                <h5 class="section-title mb-3">Apply to an Open Posting</h5>
                <div class="career-note mb-3">
                    Required before HR review: resume/CV, active contact details, current address, highest education, specialization, and a short cover letter.
                </div>
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Open Position</label>
                        <select name="job_posting_id" class="form-select row-text" required style="border: 2px solid #e2e8f0; border-radius: 10px;">
                            <option value="">Select an open posting</option>
                            <?php foreach ($jobPostings as $posting): ?>
                                <option value="<?php echo (int) $posting['id']; ?>">
                                    <?php echo htmlspecialchars($posting['job_title'] . ' - ' . $posting['department_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Applicant Name</label>
                        <input type="text" name="applicant_name" class="form-control row-text" required placeholder="Your full name" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Email Address</label>
                        <input type="email" name="email" class="form-control row-text" required placeholder="name@example.com" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Mobile Number</label>
                        <input type="text" name="phone" class="form-control row-text" required placeholder="09XXXXXXXXX" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Highest Education</label>
                        <input type="text" name="highest_education" class="form-control row-text" required placeholder="BSEd English / MAEd / etc." style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Current Address</label>
                        <input type="text" name="address" class="form-control row-text" required placeholder="City / Province / Full address" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Area of Specialization</label>
                        <input type="text" name="specialization" class="form-control row-text" required placeholder="Math, English, IT, HR, Clinic Support" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Teaching / Work Experience</label>
                        <input type="text" name="teaching_experience" class="form-control row-text" placeholder="Example: 2 years SHS teaching" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Portfolio or LinkedIn Link</label>
                        <input type="url" name="portfolio_link" class="form-control row-text" placeholder="https://example.com/profile" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Resume / CV</label>
                        <input type="file" name="resume_file" class="form-control row-text" required accept=".pdf,.doc,.docx" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label row-val mb-2">Supporting Document</label>
                        <input type="file" name="supporting_documents" class="form-control row-text" accept=".pdf,.doc,.docx" style="border: 2px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Cover Letter</label>
                        <textarea name="cover_letter" rows="4" class="form-control row-text" required placeholder="Briefly explain why you are applying and what you can contribute to the school." style="border: 2px solid #e2e8f0; border-radius: 10px;"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Required Application Checklist</label>
                        <div class="career-requirements">
                            <label class="career-check"><input type="checkbox" name="requirements[]" value="Resume/CV attached" required> <span>Resume or curriculum vitae attached</span></label>
                            <label class="career-check"><input type="checkbox" name="requirements[]" value="Cover letter completed" required> <span>Cover letter completed</span></label>
                            <label class="career-check"><input type="checkbox" name="requirements[]" value="Transcript and diploma ready"> <span>Transcript of records and diploma ready for HR review</span></label>
                            <label class="career-check"><input type="checkbox" name="requirements[]" value="Certifications or licenses available"> <span>Relevant certificates, trainings, or licenses available</span></label>
                            <label class="career-check"><input type="checkbox" name="requirements[]" value="Valid government ID available"> <span>Valid government ID available for interview stage</span></label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label row-val mb-2">Other Requirement Notes</label>
                        <textarea name="requirements_notes" rows="2" class="form-control row-text" placeholder="Example: LET passer, PRC license, TOR on hand, demo teaching available" style="border: 2px solid #e2e8f0; border-radius: 10px;"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="career-note">
                            Applications submitted here go straight into the HR applicant queue with initial status set to `Screening`. Accepted files: PDF, DOC, or DOCX up to 5MB each.
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="apply_job" class="btn btn-success w-100 fw-bold py-3" style="border-radius: 12px;">
                            <i class="bi bi-send-check me-2"></i>Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.public-hiring-hero {
    padding: 2rem;
    border: 1px solid #d1fae5;
    border-radius: 24px;
    background:
        radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 35%),
        linear-gradient(135deg, #f0fdf4 0%, #ffffff 45%, #f8fafc 100%);
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
    font-size: clamp(2rem, 4vw, 3.2rem);
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
    font-size: clamp(1.5rem, 3vw, 2.1rem);
    letter-spacing: -0.6px;
}

.landing-card {
    height: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fffb 100%);
    padding: 1.25rem;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
}

.career-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fffb 100%);
    margin-bottom: 1rem;
}

.career-card:last-child {
    margin-bottom: 0;
}

.career-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.35px;
    background: #fef3c7;
    color: #92400e;
}

.career-note {
    border: 1px dashed #bbf7d0;
    background: #f0fdf4;
    color: #166534;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
}

.career-requirements {
    display: grid;
    gap: 0.7rem;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1rem;
    background: #fff;
}

.career-check {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    color: #334155;
    font-size: 0.94rem;
    line-height: 1.5;
}

.career-check input {
    margin-top: 0.25rem;
}
</style>

<?php include 'includes/footer.php'; ?>
