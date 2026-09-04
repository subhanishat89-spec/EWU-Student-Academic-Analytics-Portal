<?php
// ============================================================
//  process.php — Multi-Course Batch Processing
//  EWU Student Academic Portal v2
//  Receives arrays of course data, inserts each as a separate
//  row while sharing the same Name, ID, Dept, Semester, Year.
//  Strictly Procedural PHP + mysqli
// ============================================================

require_once 'db.php';

// ── Guard: POST only ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// ════════════════════════════════════════════════════════════
//  STEP 1 — Sanitise & validate shared student fields
//  (These are the same for every course row in this batch)
// ════════════════════════════════════════════════════════════
$name       = trim(mysqli_real_escape_string($conn, $_POST['name']       ?? ''));
$student_id = trim(mysqli_real_escape_string($conn, $_POST['student_id'] ?? ''));
$dept       = trim(mysqli_real_escape_string($conn, $_POST['dept']       ?? ''));
$semester   = trim(mysqli_real_escape_string($conn, $_POST['semester']   ?? ''));
$year       = (int)($_POST['year'] ?? date('Y'));

$student_errors = [];
if ($name       === '') $student_errors[] = 'Student Name is required.';
if ($student_id === '') $student_errors[] = 'Student ID is required.';
if ($dept       === '') $student_errors[] = 'Department is required.';
if ($semester   === '') $student_errors[] = 'Semester is required.';
if ($year < 2000 || $year > 2099) $student_errors[] = 'Year is invalid.';

if (!empty($student_errors)) {
    $msg = urlencode(implode(' | ', $student_errors));
    header("Location: index.php?status=error&msg=$msg");
    exit;
}

// ════════════════════════════════════════════════════════════
//  STEP 2 — Receive course arrays from the form
//  All course inputs are submitted as parallel arrays:
//    $_POST['course_code'][0..n]
//    $_POST['course_name'][0..n]
//    $_POST['marks'][0..n]
//    $_POST['credits'][0..n]
// ════════════════════════════════════════════════════════════
$course_codes  = $_POST['course_code'] ?? [];
$course_names  = $_POST['course_name'] ?? [];
$marks_arr     = $_POST['marks']       ?? [];
$credits_arr   = $_POST['credits']     ?? [];

// All arrays must have the same length (guaranteed by the JS form builder,
// but we validate defensively here too).
$total_courses = count($course_codes);

if ($total_courses === 0) {
    header('Location: index.php?status=error&msg=No+course+data+was+submitted.');
    exit;
}

if (
    count($course_names) !== $total_courses ||
    count($marks_arr)    !== $total_courses ||
    count($credits_arr)  !== $total_courses
) {
    header('Location: index.php?status=error&msg=Mismatched+course+data+arrays.+Please+try+again.');
    exit;
}

// ════════════════════════════════════════════════════════════
//  STEP 3 — Official EWU Grading Scale (helper function)
//  80–100 → 4.00 (A+)  |  75–79 → 3.75 (A)
//  70–74  → 3.50 (A-)  |  65–69 → 3.25 (B+)
//  60–64  → 3.00 (B)   |  55–59 → 2.50 (C+)
//  50–54  → 2.00 (C)   |  40–49 → 1.00 (D)
//  00–39  → 0.00 (F)
// ════════════════════════════════════════════════════════════
function ewu_grade($marks) {
    if ($marks >= 80) return ['grade_point' => 4.00, 'letter_grade' => 'A+'];
    if ($marks >= 75) return ['grade_point' => 3.75, 'letter_grade' => 'A'];
    if ($marks >= 70) return ['grade_point' => 3.50, 'letter_grade' => 'A-'];
    if ($marks >= 65) return ['grade_point' => 3.25, 'letter_grade' => 'B+'];
    if ($marks >= 60) return ['grade_point' => 3.00, 'letter_grade' => 'B'];
    if ($marks >= 55) return ['grade_point' => 2.50, 'letter_grade' => 'C+'];
    if ($marks >= 50) return ['grade_point' => 2.00, 'letter_grade' => 'C'];
    if ($marks >= 40) return ['grade_point' => 1.00, 'letter_grade' => 'D'];
    return             ['grade_point' => 0.00, 'letter_grade' => 'F'];
}

// ════════════════════════════════════════════════════════════
//  STEP 4 — Loop through each course, validate, and INSERT
//  Each course gets its own row in academic_records,
//  sharing the same name/student_id/dept/semester/year.
// ════════════════════════════════════════════════════════════
$saved_count  = 0;
$row_errors   = [];

foreach ($course_codes as $i => $raw_code) {

    // ── Per-course sanitisation ───────────────────────────────
    $course_code = trim(mysqli_real_escape_string($conn, $raw_code));
    $course_name = trim(mysqli_real_escape_string($conn, $course_names[$i] ?? ''));
    $marks       = (int)($marks_arr[$i]   ?? -1);
    $credits     = (float)($credits_arr[$i] ?? 0);
    $row_num     = $i + 1;   // 1-based for error messages

    // ── Per-course validation ─────────────────────────────────
    if ($course_code === '') {
        $row_errors[] = "Row $row_num: Course Code is empty.";
        continue;   // skip this row, keep processing others
    }
    if ($course_name === '') {
        $row_errors[] = "Row $row_num: Course Name is empty.";
        continue;
    }
    if ($marks < 0 || $marks > 100) {
        $row_errors[] = "Row $row_num ($course_code): Marks must be 0–100 (got $marks).";
        continue;
    }
    if ($credits <= 0) {
        $row_errors[] = "Row $row_num ($course_code): Credits must be positive.";
        continue;
    }

    // ── Grade calculation ─────────────────────────────────────
    $grade        = ewu_grade($marks);
    $grade_point  = $grade['grade_point'];
    $letter_grade = $grade['letter_grade'];

    // ── INSERT this course row ────────────────────────────────
    $sql = "INSERT INTO academic_records
                (name, student_id, dept, semester, year,
                 course_code, course_name,
                 marks, credits, grade_point, letter_grade)
            VALUES
                ('$name', '$student_id', '$dept', '$semester', $year,
                 '$course_code', '$course_name',
                 $marks, $credits, $grade_point, '$letter_grade')";

    if (mysqli_query($conn, $sql)) {
        $saved_count++;
    } else {
        $row_errors[] = "Row $row_num ($course_code): DB error — " . mysqli_error($conn);
    }
}

// ════════════════════════════════════════════════════════════
//  STEP 5 — Redirect with appropriate status
// ════════════════════════════════════════════════════════════
mysqli_close($conn);

if ($saved_count > 0 && empty($row_errors)) {
    // All courses saved successfully
    header("Location: index.php?status=success&saved=$saved_count");

} elseif ($saved_count > 0 && !empty($row_errors)) {
    // Partial success — some rows had issues
    $err_summary = urlencode(
        "$saved_count course(s) saved, but some rows had errors: " .
        implode(' | ', $row_errors)
    );
    header("Location: index.php?status=error&msg=$err_summary");

} else {
    // Nothing saved at all
    $err_summary = urlencode(
        'No records were saved. Errors: ' . implode(' | ', $row_errors)
    );
    header("Location: index.php?status=error&msg=$err_summary");
}

exit;
?>