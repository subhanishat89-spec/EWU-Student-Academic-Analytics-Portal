<?php
// ============================================================
//  index.php — EWU Student Academic Portal (Main Hub)
//  East West University | Semester Project
//  v3: Multi-Course Entry + GPA Target Predictor
//  Procedural PHP + Bootstrap 5 + Vanilla JS
// ============================================================
require_once 'db.php';

// ── Fetch all records ────────────────────────────────────────
$all_query  = "SELECT * FROM academic_records ORDER BY id DESC";
$all_result = mysqli_query($conn, $all_query);
$records    = [];
while ($row = mysqli_fetch_assoc($all_result)) {
    $records[] = $row;
}

// ── CGPA & Analytics calculation ─────────────────────────────
$total_credits   = 0;
$weighted_gp_sum = 0;
$total_courses   = count($records);
$highest_gp      = 0;
$lowest_gp       = PHP_INT_MAX;

foreach ($records as $rec) {
    $total_credits   += $rec['credits'];
    $weighted_gp_sum += ($rec['grade_point'] * $rec['credits']);
    if ($rec['grade_point'] > $highest_gp) $highest_gp = $rec['grade_point'];
    if ($rec['grade_point'] < $lowest_gp)  $lowest_gp  = $rec['grade_point'];
}

$cgpa = ($total_credits > 0) ? round($weighted_gp_sum / $total_credits, 2) : 0;
if ($total_courses === 0) $lowest_gp = 0;

// ── AI Motivation logic ───────────────────────────────────────
$mot_class = $mot_icon = $mot_title = $mot_text = '';
if ($total_courses > 0) {
    if ($cgpa >= 3.50) {
        $mot_class = 'mot-excellent';
        $mot_icon  = '🌟';
        $mot_title = 'Outstanding Performance!';
        $mot_text  = "Excellent work! You are shining bright. Keep up this momentum — the Dean's List is within reach!";
    } elseif ($cgpa >= 3.00) {
        $mot_class = 'mot-good';
        $mot_icon  = '💪';
        $mot_title = 'Solid Progress!';
        $mot_text  = "Great job! You're doing well. Aim a little higher next semester — just a small push will make a big difference!";
    } else {
        $mot_class = 'mot-work';
        $mot_icon  = '📘';
        $mot_title = "Keep Going — You've Got This!";
        $mot_text  = "Don't lose heart! Hard work pays off — focus on your weak areas and you'll bounce back stronger next semester!";
    }
}

// ── Grade badge CSS class map ─────────────────────────────────
function grade_badge_class($letter) {
    $map = [
        'A+' => 'badge-Aplus',  'A'  => 'badge-A',
        'A-' => 'badge-Aminus', 'B+' => 'badge-Bplus',
        'B'  => 'badge-B',      'C+' => 'badge-Cplus',
        'C'  => 'badge-C',      'D'  => 'badge-D',
        'F'  => 'badge-F',
    ];
    return $map[$letter] ?? 'badge-D';
}

// ── Marks progress bar colour ─────────────────────────────────
function marks_bar_class($marks) {
    if ($marks >= 75) return 'high';
    if ($marks >= 60) return 'good';
    if ($marks >= 40) return 'mid';
    return 'low';
}

// ── Status / flash message ────────────────────────────────────
$status      = $_GET['status']  ?? '';
$msg         = htmlspecialchars($_GET['msg']   ?? '');
$saved_count = (int)($_GET['saved'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EWU Academic Portal — Student Records</title>

  <!-- Bootstrap 5 CDN -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="style.css">

  <style>
    /* ── Course-count selector ───────────────────────────────── */
    .course-count-row {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
      padding: 14px 0 6px;
    }
    .course-count-row label {
      font-size: 13px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: var(--text-secondary);
      white-space: nowrap;
    }
    .course-count-select {
      padding: 8px 36px 8px 14px;
      border: 1.5px solid var(--ewu-gold);
      border-radius: var(--radius-sm);
      background: var(--bg-input);
      color: var(--text-primary);
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      outline: none;
      transition: var(--transition);
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%23f2a900' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      min-width: 130px;
    }
    .course-count-select:focus {
      border-color: var(--ewu-green);
      box-shadow: 0 0 0 3px rgba(0,106,78,.15);
    }
    .course-count-hint {
      font-size: 12px;
      color: var(--text-muted);
      font-style: italic;
    }

    /* ── Multi-course table ──────────────────────────────────── */
    #courseRowsWrapper { margin-top: 18px; }

    .course-rows-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }
    .course-rows-table thead th {
      background: var(--ewu-green);
      color: #fff;
      padding: 10px 12px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .07em;
      position: sticky;
      top: 0;
      z-index: 2;
    }
    .course-rows-table thead th:first-child { border-radius: 8px 0 0 0; }
    .course-rows-table thead th:last-child  { border-radius: 0 8px 0 0; }
    .course-rows-table tbody tr { transition: background .17s; }
    .course-rows-table tbody tr:nth-child(odd)  { background: var(--bg-table-row); }
    .course-rows-table tbody tr:nth-child(even) { background: var(--bg-table-alt); }
    .course-rows-table tbody tr:hover           { background: var(--bg-table-hover) !important; }
    .course-rows-table tbody td {
      padding: 8px 10px;
      vertical-align: middle;
      border-bottom: 1px solid var(--border-color);
    }
    .row-num-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 26px; height: 26px;
      border-radius: 50%;
      background: var(--ewu-green);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      flex-shrink: 0;
    }
    .course-rows-table input[type="text"],
    .course-rows-table input[type="number"] {
      width: 100%;
      padding: 7px 10px;
      border: 1.5px solid var(--border-input);
      border-radius: var(--radius-sm);
      background: var(--bg-input);
      color: var(--text-primary);
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      outline: none;
      transition: var(--transition);
    }
    .course-rows-table input:focus {
      border-color: var(--ewu-green);
      box-shadow: 0 0 0 3px rgba(0,106,78,.13);
    }
    .course-rows-table input::placeholder { color: var(--text-muted); }

    /* Live grade chip */
    .live-grade-chip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 11px;
      border-radius: 50px;
      background: var(--border-color);
      color: var(--text-muted);
      transition: background .25s, color .25s;
      min-width: 90px;
      white-space: nowrap;
    }
    .live-grade-chip.chip-Aplus  { background:#006a4e; color:#fff; }
    .live-grade-chip.chip-A      { background:#1a8a68; color:#fff; }
    .live-grade-chip.chip-Aminus { background:#059669; color:#fff; }
    .live-grade-chip.chip-Bplus  { background:#2563eb; color:#fff; }
    .live-grade-chip.chip-B      { background:#3b82f6; color:#fff; }
    .live-grade-chip.chip-Cplus  { background:#d97706; color:#fff; }
    .live-grade-chip.chip-C      { background:#f59e0b; color:#1a1000; }
    .live-grade-chip.chip-D      { background:#dc2626; color:#fff; }
    .live-grade-chip.chip-F      { background:#7f1d1d; color:#fff; }

    @keyframes rowSlideIn {
      from { opacity:0; transform: translateY(-8px); }
      to   { opacity:1; transform: translateY(0); }
    }
    .course-row-anim { animation: rowSlideIn .22s ease both; }

    /* Batch success banner */
    .batch-success-banner {
      background: linear-gradient(135deg, #006a4e 0%, #1a8a68 100%);
      color: #fff;
      border-radius: var(--radius);
      padding: 16px 22px;
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 22px;
      animation: slideDown .35s ease;
    }
    .batch-success-banner .bsb-icon  { font-size: 2.2rem; }
    .batch-success-banner .bsb-count {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 700;
      line-height: 1;
    }
    .batch-success-banner .bsb-label { font-size: 13px; opacity: .85; }

    .form-section-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: var(--ewu-gold);
      margin: 6px 0 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .form-section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border-color);
    }

    #emptyRowsPlaceholder {
      text-align: center;
      padding: 32px 16px;
      color: var(--text-muted);
      font-size: 13.5px;
      border: 2px dashed var(--border-color);
      border-radius: var(--radius-sm);
    }
    #emptyRowsPlaceholder i { font-size: 2rem; display:block; margin-bottom:8px; }

    /* ── GPA Target Predictor ────────────────────────────────── */
    .predictor-card {
      background: rgba(0, 106, 78, 0.07);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1.5px solid rgba(0, 106, 78, 0.22);
      border-radius: 16px;
      padding: 28px 28px 24px;
      margin-bottom: 28px;
      box-shadow: 0 8px 32px rgba(0, 106, 78, 0.10);
      transition: var(--transition);
    }
    [data-theme="dark"] .predictor-card {
      background: rgba(0, 106, 78, 0.13);
      border-color: rgba(0, 200, 140, 0.18);
      box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    }
    .predictor-card:hover {
      box-shadow: 0 12px 40px rgba(0, 106, 78, 0.16);
    }

    .predictor-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 6px;
    }
    .predictor-icon-box {
      background: linear-gradient(135deg, var(--ewu-green), #1a8a68);
      color: #fff;
      border-radius: 12px;
      width: 46px; height: 46px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem;
      flex-shrink: 0;
      box-shadow: 0 4px 14px rgba(0,106,78,0.35);
    }
    .predictor-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-heading);
      line-height: 1.2;
    }
    .predictor-subtitle {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }
    .predictor-badge {
      margin-left: auto;
      background: linear-gradient(135deg, var(--ewu-gold), #e09800);
      color: #1a1000;
      padding: 4px 14px;
      border-radius: 50px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .06em;
      box-shadow: 0 2px 8px rgba(242,169,0,0.3);
      white-space: nowrap;
    }
    .predictor-divider {
      height: 2px;
      background: linear-gradient(90deg, var(--ewu-gold), transparent);
      border-radius: 2px;
      margin: 16px 0 22px;
    }

    .predictor-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 20px;
    }
    .predictor-field label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: var(--text-secondary);
      margin-bottom: 6px;
    }
    .predictor-field input {
      width: 100%;
      padding: 9px 13px;
      border: 1.5px solid var(--border-input);
      border-radius: 8px;
      background: var(--bg-input);
      color: var(--text-primary);
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      outline: none;
      transition: var(--transition);
    }
    .predictor-field input:focus {
      border-color: var(--ewu-green);
      box-shadow: 0 0 0 3px rgba(0,106,78,.15);
    }
    .predictor-field input.gold-border { border-color: var(--ewu-gold); }
    .predictor-field input.gold-border:focus {
      border-color: var(--ewu-gold);
      box-shadow: 0 0 0 3px rgba(242,169,0,.18);
    }
    .predictor-field input::placeholder { color: var(--text-muted); }

    .predictor-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 26px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(135deg, var(--ewu-green), #1a8a68);
      color: #fff;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(0,106,78,0.35);
      transition: var(--transition);
    }
    .predictor-btn:hover {
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0 8px 22px rgba(0,106,78,0.4);
    }
    .predictor-btn:active {
      transform: scale(0.97);
    }

    /* Result states */
    .pred-result {
      margin-top: 20px;
      border-radius: 12px;
      padding: 18px 22px;
      animation: slideDown .3s ease;
    }
    .pred-result-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }
    .pred-result-emoji { font-size: 1.7rem; }
    .pred-result-title {
      font-family: 'Playfair Display', serif;
      font-size: 1rem;
      font-weight: 700;
    }
    .pred-result-text {
      font-size: 13.5px;
      color: var(--text-primary);
      line-height: 1.65;
      margin: 0 0 14px;
    }

    /* GPA progress bar inside result */
    .pred-bar-wrap {
      background: var(--border-color);
      border-radius: 6px;
      height: 10px;
      overflow: hidden;
      margin-bottom: 5px;
    }
    .pred-bar-fill {
      height: 100%;
      border-radius: 6px;
      background: linear-gradient(90deg, var(--ewu-green), var(--ewu-gold));
      transition: width .7s cubic-bezier(.4,0,.2,1);
    }
    .pred-bar-labels {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      color: var(--text-muted);
    }

    /* Result colour themes */
    .pred-impossible {
      background: rgba(220,38,38,0.08);
      border: 1.5px solid rgba(220,38,38,0.4);
      border-left: 5px solid #dc2626;
    }
    .pred-achieved {
      background: rgba(0,106,78,0.08);
      border: 1.5px solid rgba(0,106,78,0.35);
      border-left: 5px solid var(--ewu-green);
    }
    .pred-hard {
      background: rgba(242,169,0,0.08);
      border: 1.5px solid rgba(242,169,0,0.45);
      border-left: 5px solid var(--ewu-gold);
    }
    .pred-medium {
      background: rgba(37,99,235,0.07);
      border: 1.5px solid rgba(37,99,235,0.35);
      border-left: 5px solid #2563eb;
    }
    .pred-easy {
      background: rgba(0,106,78,0.08);
      border: 1.5px solid rgba(0,106,78,0.35);
      border-left: 5px solid var(--ewu-green);
    }
    .pred-validation {
      background: rgba(220,38,38,0.08);
      border: 1.5px solid #dc2626;
      border-radius: 10px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #dc2626;
      font-weight: 600;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .predictor-badge { display: none; }
      .course-rows-table { font-size: 12px; }
      .course-rows-table thead th,
      .course-rows-table tbody td { padding: 6px 8px; }
      .live-grade-chip { min-width: 70px; font-size:11px; }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════════════════════════════════════
     HEADER
     ════════════════════════════════════════════════════════════ -->
<header class="ewu-header no-print">
  <div class="header-inner">
    <div class="ewu-logo-area">
      <div class="ewu-logo-circle">EWU</div>
      <div class="ewu-title-block">
        <h1>East West University</h1>
        <p>Student Academic Portal &mdash; Multi-Course Entry System</p>
      </div>
    </div>
    <div class="header-controls">
      <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme()">
        <i class="bi bi-moon-fill" id="themeIcon"></i>
        <span id="themeLabel">Dark Mode</span>
      </button>
      <button class="btn-ewu btn-gold no-print" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Print Transcript
      </button>
    </div>
  </div>
</header>

<!-- Print-only transcript header -->
<div class="print-transcript-title">
  East West University — Official Academic Transcript
</div>

<div class="portal-wrapper">

  <!-- ══════════════════════════════════════════════════════════
       FLASH / STATUS ALERTS
       ══════════════════════════════════════════════════════════ -->
  <?php if ($status === 'success' && $saved_count > 0): ?>
  <div class="batch-success-banner no-print" id="flashAlert">
    <div class="bsb-icon">🎓</div>
    <div>
      <div class="bsb-count">
        <?= $saved_count ?> Course<?= $saved_count > 1 ? 's' : '' ?> Saved!
      </div>
      <div class="bsb-label">
        All records inserted with official EWU grades calculated automatically.
      </div>
    </div>
  </div>

  <?php elseif ($status === 'error'): ?>
  <div class="alert alert-error no-print" id="flashAlert">
    <i class="bi bi-x-circle-fill"></i>
    <?= $msg ?: 'Something went wrong. Please try again.' ?>
  </div>

  <?php elseif ($status === 'deleted'): ?>
  <div class="alert alert-deleted no-print" id="flashAlert">
    <i class="bi bi-trash-fill"></i> Record deleted successfully.
  </div>
  <?php endif; ?>

  <!-- ══════════════════════════════════════════════════════════
       ANALYTICS BAR
       ══════════════════════════════════════════════════════════ -->
  <div class="analytics-bar">
    <div class="stat-card">
      <div class="stat-label">Cumulative GPA</div>
      <div class="stat-value"><?= number_format($cgpa, 2) ?></div>
      <div class="stat-sub">out of 4.00</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Credits</div>
      <div class="stat-value"><?= number_format($total_credits, 1) ?></div>
      <div class="stat-sub">credit hours</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Courses Taken</div>
      <div class="stat-value"><?= $total_courses ?></div>
      <div class="stat-sub">enrolled courses</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Best Grade</div>
      <div class="stat-value">
        <?= $total_courses > 0 ? number_format($highest_gp, 2) : '—' ?>
      </div>
      <div class="stat-sub">grade point</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Lowest Grade</div>
      <div class="stat-value">
        <?= $total_courses > 0 ? number_format($lowest_gp, 2) : '—' ?>
      </div>
      <div class="stat-sub">grade point</div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════
       AI MOTIVATION BANNER
       ══════════════════════════════════════════════════════════ -->
  <?php if ($total_courses > 0): ?>
  <div class="motivation-banner <?= $mot_class ?>">
    <div class="mot-icon"><?= $mot_icon ?></div>
    <div>
      <div class="mot-title"><?= $mot_title ?></div>
      <div class="mot-text"><?= $mot_text ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ══════════════════════════════════════════════════════════
       MULTI-COURSE ENTRY FORM
       ══════════════════════════════════════════════════════════ -->
  <div class="card-panel no-print">
    <div class="card-panel-header">
      <span class="panel-icon"><i class="bi bi-journal-plus"></i></span>
      <h2>Add Course Records — Multi-Entry</h2>
      <span style="margin-left:auto; background:rgba(255,255,255,.18);
                   padding:3px 12px; border-radius:50px; font-size:12px;">
        Enter up to 7 courses at once
      </span>
    </div>

    <div class="card-panel-body">
      <form action="process.php" method="POST" id="multiCourseForm" novalidate>

        <!-- SECTION A: Student Info -->
        <div class="form-section-label">
          <i class="bi bi-person-badge-fill"></i> Student Information
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="name">
              <i class="bi bi-person-fill"></i> Full Name
            </label>
            <input type="text" id="name" name="name"
                   placeholder="e.g. Subha Ahmed" required>
          </div>
          <div class="form-group">
            <label for="student_id">
              <i class="bi bi-card-text"></i> Student ID
            </label>
            <input type="text" id="student_id" name="student_id"
                   placeholder="e.g. 2021-1-60-001" required>
          </div>
          <div class="form-group">
            <label for="dept">
              <i class="bi bi-building"></i> Department
            </label>
            <select id="dept" name="dept" required>
              <option value="" disabled selected>— Select Dept —</option>
              <option value="CSE">CSE</option>
              <option value="EEE">EEE</option>
              <option value="BBA">BBA</option>
              <option value="Pharmacy">Pharmacy</option>
              <option value="English">English</option>
              <option value="Law">Law</option>
              <option value="Economics">Economics</option>
            </select>
          </div>
          <div class="form-group">
            <label for="semester">
              <i class="bi bi-calendar3"></i> Semester
            </label>
            <select id="semester" name="semester" required>
              <option value="" disabled selected>— Select —</option>
              <option value="Spring">Spring</option>
              <option value="Summer">Summer</option>
              <option value="Fall">Fall</option>
            </select>
          </div>
          <div class="form-group">
            <label for="year">
              <i class="bi bi-calendar-event"></i> Year
            </label>
            <input type="number" id="year" name="year"
                   min="2000" max="2099" value="<?= date('Y') ?>" required>
          </div>
        </div>

        <!-- SECTION B: Course Count Dropdown -->
        <div class="form-section-label" style="margin-top:22px;">
          <i class="bi bi-grid-3x3-gap-fill"></i> Course Details
        </div>

        <div class="course-count-row">
          <label for="courseCountSelect">
            <i class="bi bi-list-ol"></i> How many courses did you take?
          </label>
          <select id="courseCountSelect"
                  class="course-count-select"
                  onchange="generateCourseRows(this.value)">
            <option value="0" selected>— Pick a number —</option>
            <option value="1">1 course</option>
            <option value="2">2 courses</option>
            <option value="3">3 courses</option>
            <option value="4">4 courses</option>
            <option value="5">5 courses</option>
            <option value="6">6 courses</option>
            <option value="7">7 courses</option>
          </select>
          <span class="course-count-hint">
            Input rows appear instantly below ↓
          </span>
        </div>

        <!-- SECTION C: Dynamic Course Rows -->
        <div id="courseRowsWrapper">
          <div id="emptyRowsPlaceholder">
            <i class="bi bi-arrow-up-circle"></i>
            Select the number of courses above — rows will appear here.
          </div>
          <div id="courseTableContainer"
               style="display:none; overflow-x:auto;
                      border-radius:8px; border:1px solid var(--border-color);">
            <table class="course-rows-table">
              <thead>
                <tr>
                  <th style="width:42px;">#</th>
                  <th>Course Code</th>
                  <th>Course Name</th>
                  <th style="width:100px;">
                    Marks
                    <small style="opacity:.65; font-size:9px; display:block;">(0–100)</small>
                  </th>
                  <th style="width:90px;">Credits</th>
                  <th style="width:130px;">Grade Preview</th>
                </tr>
              </thead>
              <tbody id="courseRowsBody"></tbody>
            </table>
          </div>
        </div>

        <!-- Submit / Reset -->
        <div style="margin-top:22px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
          <button type="submit" class="btn-ewu btn-primary"
                  id="submitBtn" disabled>
            <i class="bi bi-floppy-fill"></i>
            <span id="submitBtnLabel">Save All Courses</span>
          </button>
          <button type="button" class="btn-ewu btn-outline"
                  onclick="resetForm()">
            <i class="bi bi-arrow-counterclockwise"></i> Reset
          </button>
          <span id="courseCountBadge"
                style="display:none; margin-left:4px;
                       background:var(--ewu-gold); color:#1a1000;
                       padding:4px 14px; border-radius:50px;
                       font-size:12px; font-weight:700;">
          </span>
        </div>

      </form>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════
       ACADEMIC RECORDS TABLE
       ══════════════════════════════════════════════════════════ -->
  <div class="card-panel">
    <div class="card-panel-header">
      <span class="panel-icon"><i class="bi bi-table"></i></span>
      <h2>Academic Records</h2>
      <?php if ($total_courses > 0): ?>
      <span style="margin-left:auto; background:rgba(255,255,255,.2);
                   padding:3px 12px; border-radius:50px; font-size:12px;">
        <?= $total_courses ?> course<?= $total_courses !== 1 ? 's' : '' ?>
      </span>
      <?php endif; ?>
    </div>

    <div class="card-panel-body" style="padding:0;">
      <?php if (empty($records)): ?>
      <div class="empty-state">
        <div class="empty-icon">📋</div>
        <p>No records yet. Add your first courses using the form above!</p>
      </div>

      <?php else: ?>
      <div class="table-responsive">
        <table class="records-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Student Name</th>
              <th>Student ID</th>
              <th>Dept</th>
              <th>Semester / Year</th>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Marks</th>
              <th>Credits</th>
              <th>Grade Point</th>
              <th>Letter</th>
              <th class="no-print">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $serial = 1;
            foreach ($records as $rec):
              $bar_class = marks_bar_class((int)$rec['marks']);
              $badge     = grade_badge_class($rec['letter_grade']);
              $pct       = (int)$rec['marks'];
            ?>
            <tr>
              <td class="text-muted"><?= $serial++ ?></td>
              <td class="fw-600"><?= htmlspecialchars($rec['name']) ?></td>
              <td style="font-size:12px; font-family:monospace;">
                <?= htmlspecialchars($rec['student_id']) ?>
              </td>
              <td>
                <span style="background:var(--ewu-green-light);
                             color:var(--ewu-green-dark);
                             padding:2px 9px; border-radius:50px;
                             font-size:11.5px; font-weight:600;">
                  <?= htmlspecialchars($rec['dept']) ?>
                </span>
              </td>
              <td>
                <?= htmlspecialchars($rec['semester']) ?>
                <?= (int)$rec['year'] ?>
              </td>
              <td style="font-family:monospace; font-size:12.5px;">
                <?= htmlspecialchars($rec['course_code']) ?>
              </td>
              <td><?= htmlspecialchars($rec['course_name']) ?></td>
              <td>
                <div class="marks-bar-wrap">
                  <span class="marks-num"><?= $pct ?></span>
                  <div class="marks-bar">
                    <div class="marks-bar-fill <?= $bar_class ?>"
                         style="width:<?= $pct ?>%"></div>
                  </div>
                </div>
              </td>
              <td><?= number_format((float)$rec['credits'], 1) ?></td>
              <td><strong><?= number_format((float)$rec['grade_point'], 2) ?></strong></td>
              <td>
                <span class="grade-badge <?= $badge ?>">
                  <?= htmlspecialchars($rec['letter_grade']) ?>
                </span>
              </td>
              <td class="no-print">
                <a href="delete.php?id=<?= (int)$rec['id'] ?>"
                   class="btn-ewu btn-danger"
                   onclick="return confirm('Delete <?= addslashes(htmlspecialchars($rec['course_code'])) ?>?')">
                  <i class="bi bi-trash3-fill"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>

          <!-- CGPA Footer -->
          <tfoot>
            <tr>
              <td colspan="8"
                  style="text-align:right; font-size:11px;
                         letter-spacing:.05em; opacity:.85;">
                WEIGHTED CGPA = Σ(GP × Credits) / Σ Credits
              </td>
              <td><strong><?= number_format($total_credits, 1) ?></strong></td>
              <td colspan="3">
                <span style="font-size:1.05rem; font-family:'Playfair Display',serif;">
                  CGPA: <?= number_format($cgpa, 2) ?> / 4.00
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════
       EWU GRADING SCALE REFERENCE
       ══════════════════════════════════════════════════════════ -->
  <div class="card-panel">
    <div class="card-panel-header">
      <span class="panel-icon"><i class="bi bi-info-circle-fill"></i></span>
      <h2>Official EWU Grading Scale</h2>
    </div>
    <div class="card-panel-body">
      <div class="table-responsive">
        <table class="records-table" style="font-size:13px;">
          <thead>
            <tr>
              <th>Marks Range</th>
              <th>Letter Grade</th>
              <th>Grade Point</th>
              <th>Performance</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $scale = [
              ['80–100','A+','4.00','Outstanding'],
              ['75–79', 'A', '3.75','Excellent'],
              ['70–74', 'A-','3.50','Very Good'],
              ['65–69', 'B+','3.25','Good'],
              ['60–64', 'B', '3.00','Above Average'],
              ['55–59', 'C+','2.50','Average'],
              ['50–54', 'C', '2.00','Satisfactory'],
              ['40–49', 'D', '1.00','Pass'],
              ['0–39',  'F', '0.00','Fail'],
            ];
            foreach ($scale as $s):
              $bc = grade_badge_class($s[1]);
            ?>
            <tr>
              <td><strong><?= $s[0] ?></strong></td>
              <td><span class="grade-badge <?= $bc ?>"><?= $s[1] ?></span></td>
              <td><?= $s[2] ?></td>
              <td><?= $s[3] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════
       GPA TARGET PREDICTOR  🎯
       ══════════════════════════════════════════════════════════ -->
  <div class="predictor-card no-print">

    <!-- Header row -->
    <div class="predictor-header">
      <div class="predictor-icon-box">🎯</div>
      <div>
        <div class="predictor-title">GPA Target Predictor</div>
        <div class="predictor-subtitle">
          Find out what GPA you need next semester to hit your goal
        </div>
      </div>
      <div class="predictor-badge">CALCULATOR</div>
    </div>

    <!-- Gold divider -->
    <div class="predictor-divider"></div>

    <!-- Four input fields -->
    <div class="predictor-grid">

      <div class="predictor-field">
        <label>
          <i class="bi bi-graph-up" style="color:var(--ewu-green);"></i>
          Current CGPA
        </label>
        <input type="number"
               id="pred_current_cgpa"
               min="0" max="4" step="0.01"
               placeholder="e.g. <?= number_format($cgpa, 2) ?>"
               value="<?= $total_courses > 0 ? number_format($cgpa, 2) : '' ?>">
      </div>

      <div class="predictor-field">
        <label>
          <i class="bi bi-stack" style="color:var(--ewu-green);"></i>
          Credits Completed
        </label>
        <input type="number"
               id="pred_current_credits"
               min="0" step="0.5"
               placeholder="e.g. <?= number_format($total_credits, 1) ?>"
               value="<?= $total_courses > 0 ? number_format($total_credits, 1) : '' ?>">
      </div>

      <div class="predictor-field">
        <label>
          <i class="bi bi-calendar-plus" style="color:var(--ewu-green);"></i>
          Next Semester Credits
        </label>
        <input type="number"
               id="pred_next_credits"
               min="0.5" max="30" step="0.5"
               placeholder="e.g. 15">
      </div>

      <div class="predictor-field">
        <label>
          <i class="bi bi-trophy-fill" style="color:var(--ewu-gold);"></i>
          Target CGPA
        </label>
        <input type="number"
               id="pred_target_cgpa"
               class="gold-border"
               min="0" max="4" step="0.01"
               placeholder="e.g. 3.50">
      </div>

    </div>

    <!-- Calculate button -->
    <button class="predictor-btn" onclick="runGpaPredictor()">
      <i class="bi bi-calculator-fill"></i> Calculate Required GPA
    </button>

    <!-- Result area — hidden until JS populates it -->
    <div id="predictorResult" style="display:none;"></div>

  </div>

</div><!-- end .portal-wrapper -->

<!-- FOOTER -->
<footer class="portal-footer">
  East West University — Academic Portal &nbsp;|&nbsp;
  &copy; <?= date('Y') ?>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ============================================================
//  Vanilla JS — Theme + Multi-Course + GPA Predictor
// ============================================================

// ── EWU Grading Scale ────────────────────────────────────────
const EWU_SCALE = [
  { min:80, gp:4.00, letter:'A+',  chip:'chip-Aplus'  },
  { min:75, gp:3.75, letter:'A',   chip:'chip-A'      },
  { min:70, gp:3.50, letter:'A-',  chip:'chip-Aminus' },
  { min:65, gp:3.25, letter:'B+',  chip:'chip-Bplus'  },
  { min:60, gp:3.00, letter:'B',   chip:'chip-B'      },
  { min:55, gp:2.50, letter:'C+',  chip:'chip-Cplus'  },
  { min:50, gp:2.00, letter:'C',   chip:'chip-C'      },
  { min:40, gp:1.00, letter:'D',   chip:'chip-D'      },
  { min: 0, gp:0.00, letter:'F',   chip:'chip-F'      },
];
function getGrade(marks) {
  for (const g of EWU_SCALE) if (marks >= g.min) return g;
  return EWU_SCALE[EWU_SCALE.length - 1];
}

// ════════════════════════════════════════════════════════════
//  THEME ENGINE
// ════════════════════════════════════════════════════════════
function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  const icon  = document.getElementById('themeIcon');
  const label = document.getElementById('themeLabel');
  if (theme === 'dark') {
    icon.className    = 'bi bi-sun-fill';
    label.textContent = 'Light Mode';
  } else {
    icon.className    = 'bi bi-moon-fill';
    label.textContent = 'Dark Mode';
  }
  localStorage.setItem('ewu-theme', theme);
}
function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme') || 'light';
  applyTheme(current === 'dark' ? 'light' : 'dark');
}
// Restore on load
(function () {
  const saved = localStorage.getItem('ewu-theme');
  if (saved) applyTheme(saved);
})();

// ════════════════════════════════════════════════════════════
//  AUTO-DISMISS FLASH ALERTS
// ════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
  const flash = document.getElementById('flashAlert');
  if (flash) {
    setTimeout(function () {
      flash.style.transition = 'opacity .5s ease';
      flash.style.opacity    = '0';
      setTimeout(() => flash.remove(), 550);
    }, 6000);
  }
});

// ════════════════════════════════════════════════════════════
//  MULTI-COURSE ROW ENGINE
// ════════════════════════════════════════════════════════════
let currentRowCount = 0;

function generateCourseRows(n) {
  n = parseInt(n, 10);
  currentRowCount = n;

  const body         = document.getElementById('courseRowsBody');
  const placeholder  = document.getElementById('emptyRowsPlaceholder');
  const tableWrapper = document.getElementById('courseTableContainer');
  const submitBtn    = document.getElementById('submitBtn');
  const badge        = document.getElementById('courseCountBadge');
  const btnLabel     = document.getElementById('submitBtnLabel');

  body.innerHTML = '';

  if (n === 0) {
    placeholder.style.display  = 'block';
    tableWrapper.style.display = 'none';
    submitBtn.disabled         = true;
    badge.style.display        = 'none';
    return;
  }

  placeholder.style.display  = 'none';
  tableWrapper.style.display = 'block';
  submitBtn.disabled         = false;
  badge.style.display        = 'inline-block';
  badge.textContent          = n + ' course' + (n > 1 ? 's' : '') + ' ready';
  btnLabel.textContent       = 'Save ' + n + ' Course' + (n > 1 ? 's' : '');

  for (let i = 1; i <= n; i++) {
    const tr = buildCourseRow(i);
    body.appendChild(tr);
  }
}

function buildCourseRow(rowNum) {
  const tr     = document.createElement('tr');
  tr.className = 'course-row-anim';
  tr.style.animationDelay = (rowNum * 0.045) + 's';

  tr.innerHTML = `
    <td><span class="row-num-badge">${rowNum}</span></td>
    <td>
      <input type="text" name="course_code[]"
             placeholder="e.g. CSE207" autocomplete="off" required>
    </td>
    <td>
      <input type="text" name="course_name[]"
             placeholder="e.g. Data Structures" required>
    </td>
    <td>
      <input type="number" name="marks[]"
             min="0" max="100" placeholder="0–100" required
             oninput="updateRowGrade(${rowNum}, this.value)">
    </td>
    <td>
      <input type="number" name="credits[]"
             min="0.5" max="6" step="0.5" placeholder="e.g. 3" required>
    </td>
    <td>
      <span class="live-grade-chip" id="grade-chip-${rowNum}">— / —</span>
    </td>
  `;
  return tr;
}

function updateRowGrade(rowNum, marksVal) {
  const chip  = document.getElementById('grade-chip-' + rowNum);
  if (!chip) return;
  const marks = parseInt(marksVal, 10);
  if (isNaN(marks) || String(marksVal).trim() === '') {
    chip.textContent = '— / —';
    chip.className   = 'live-grade-chip';
    return;
  }
  const grade      = getGrade(Math.max(0, Math.min(100, marks)));
  chip.textContent = grade.letter + '  ·  GP ' + grade.gp.toFixed(2);
  chip.className   = 'live-grade-chip ' + grade.chip;
}

function resetForm() {
  document.getElementById('multiCourseForm').reset();
  document.getElementById('courseCountSelect').value = '0';
  generateCourseRows(0);
}

// ── Form validation before POST ──────────────────────────────
document.getElementById('multiCourseForm').addEventListener('submit', function (e) {
  const name = document.getElementById('name').value.trim();
  const sid  = document.getElementById('student_id').value.trim();
  const dept = document.getElementById('dept').value;
  const sem  = document.getElementById('semester').value;
  const yr   = document.getElementById('year').value;

  if (!name || !sid || !dept || !sem || !yr) {
    e.preventDefault();
    alert('⚠ Please complete all Student Information fields before saving.');
    return;
  }
  if (currentRowCount === 0) {
    e.preventDefault();
    alert('⚠ Please select how many courses you took using the dropdown.');
    return;
  }

  const rows = document.querySelectorAll('#courseRowsBody tr');
  for (let i = 0; i < rows.length; i++) {
    const rowNum  = i + 1;
    const code    = rows[i].querySelector('input[name="course_code[]"]').value.trim();
    const cname   = rows[i].querySelector('input[name="course_name[]"]').value.trim();
    const marksEl = rows[i].querySelector('input[name="marks[]"]');
    const credEl  = rows[i].querySelector('input[name="credits[]"]');
    const marks   = parseInt(marksEl.value, 10);
    const credits = parseFloat(credEl.value);

    if (!code)  { e.preventDefault(); alert('⚠ Row ' + rowNum + ': Course Code cannot be empty.'); return; }
    if (!cname) { e.preventDefault(); alert('⚠ Row ' + rowNum + ': Course Name cannot be empty.'); return; }
    if (isNaN(marks)   || marks < 0 || marks > 100) {
      e.preventDefault(); marksEl.focus();
      alert('⚠ Row ' + rowNum + ': Marks must be between 0 and 100.'); return;
    }
    if (isNaN(credits) || credits <= 0) {
      e.preventDefault(); credEl.focus();
      alert('⚠ Row ' + rowNum + ': Credits must be a positive number.'); return;
    }
  }
});

// ════════════════════════════════════════════════════════════
//  GPA TARGET PREDICTOR
//  Formula:
//  Required = ((Target × (CurrentCredits + NextCredits))
//              - (CurrentCGPA × CurrentCredits))
//             / NextCredits
// ════════════════════════════════════════════════════════════
function runGpaPredictor() {
  const currentCGPA    = parseFloat(document.getElementById('pred_current_cgpa').value);
  const currentCredits = parseFloat(document.getElementById('pred_current_credits').value);
  const nextCredits    = parseFloat(document.getElementById('pred_next_credits').value);
  const targetCGPA     = parseFloat(document.getElementById('pred_target_cgpa').value);
  const resultBox      = document.getElementById('predictorResult');

  // ── Helper to show a validation error ──────────────────────
  function showError(msg) {
    resultBox.style.display = 'block';
    resultBox.innerHTML = `
      <div class="pred-validation">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:1.2rem;"></i>
        ${msg}
      </div>`;
  }

  // ── Input validation ────────────────────────────────────────
  if (isNaN(currentCGPA) || isNaN(currentCredits) ||
      isNaN(nextCredits)  || isNaN(targetCGPA)) {
    showError('Please fill in all four fields before calculating.'); return;
  }
  if (currentCGPA  < 0 || currentCGPA  > 4) { showError('Current CGPA must be between 0.00 and 4.00.'); return; }
  if (targetCGPA   < 0 || targetCGPA   > 4) { showError('Target CGPA must be between 0.00 and 4.00.'); return; }
  if (currentCredits < 0)                    { showError('Credits Completed cannot be negative.'); return; }
  if (nextCredits   <= 0)                    { showError('Next Semester Credits must be greater than 0.'); return; }

  // ── Core formula ────────────────────────────────────────────
  const totalNewCredits = currentCredits + nextCredits;
  const requiredGPA     = ((targetCGPA * totalNewCredits) - (currentCGPA * currentCredits)) / nextCredits;
  const rounded         = Math.round(requiredGPA * 100) / 100;
  const fillPct         = Math.min((Math.max(rounded, 0) / 4) * 100, 100).toFixed(1);

  resultBox.style.display = 'block';

  // ── Case 1: Mathematically impossible ───────────────────────
  if (requiredGPA > 4.00) {
    resultBox.innerHTML = `
      <div class="pred-result pred-impossible">
        <div class="pred-result-header">
          <span class="pred-result-emoji">😟</span>
          <span class="pred-result-title" style="color:#dc2626;">
            Target Out of Reach This Semester
          </span>
        </div>
        <p class="pred-result-text">
          Reaching a CGPA of <strong style="color:#dc2626;">${targetCGPA.toFixed(2)}</strong>
          in one semester would require a GPA of
          <strong style="color:#dc2626;">${rounded.toFixed(2)}</strong> —
          which exceeds the maximum of <strong>4.00</strong>.
          Consider spreading your goal across
          <strong>more semesters</strong> with consistent effort. Keep going! 💪
        </p>
      </div>`;

  // ── Case 2: Already achieved / maintaining ───────────────────
  } else if (targetCGPA <= currentCGPA) {
    resultBox.innerHTML = `
      <div class="pred-result pred-achieved">
        <div class="pred-result-header">
          <span class="pred-result-emoji">🎉</span>
          <span class="pred-result-title" style="color:var(--ewu-green);">
            You've Already Hit Your Target!
          </span>
        </div>
        <p class="pred-result-text">
          Your current CGPA of
          <strong style="color:var(--ewu-green);">${currentCGPA.toFixed(2)}</strong>
          already meets your target of
          <strong>${targetCGPA.toFixed(2)}</strong>. 🌟
          To <em>maintain</em> it, aim for at least
          <strong style="color:var(--ewu-green);">${rounded.toFixed(2)}</strong>
          next semester.
        </p>
        <div class="pred-bar-wrap">
          <div class="pred-bar-fill" style="width:${fillPct}%;"></div>
        </div>
        <div class="pred-bar-labels">
          <span>0.00</span>
          <span style="color:var(--ewu-green); font-weight:700;">
            Needed: ${rounded.toFixed(2)}
          </span>
          <span>4.00</span>
        </div>
      </div>`;

  // ── Case 3: Achievable — colour by difficulty ────────────────
  } else {
    const level = requiredGPA >= 3.75 ? 'hard' : requiredGPA >= 3.00 ? 'medium' : 'easy';
    const meta  = {
      hard:   { cls:'pred-hard',   color:'#b07800',             emoji:'🔥', label:'Challenge Accepted!' },
      medium: { cls:'pred-medium', color:'#1d4ed8',             emoji:'📈', label:'Your Target is Achievable!' },
      easy:   { cls:'pred-easy',   color:'var(--ewu-green)',    emoji:'✅', label:'Smooth Sailing Ahead!' },
    }[level];

    resultBox.innerHTML = `
      <div class="pred-result ${meta.cls}">
        <div class="pred-result-header">
          <span class="pred-result-emoji">${meta.emoji}</span>
          <span class="pred-result-title" style="color:${meta.color};">
            ${meta.label}
          </span>
        </div>
        <p class="pred-result-text">
          To reach your target CGPA of
          <strong style="color:var(--ewu-gold);">${targetCGPA.toFixed(2)}</strong>,
          you need a GPA of
          <strong style="font-size:1.15rem; color:${meta.color};">${rounded.toFixed(2)}</strong>
          in your upcoming semester
          <span style="color:var(--text-muted);">(${nextCredits} credits)</span>.
        </p>
        <div class="pred-bar-wrap">
          <div class="pred-bar-fill" style="width:${fillPct}%;"></div>
        </div>
        <div class="pred-bar-labels">
          <span>0.00</span>
          <span style="color:${meta.color}; font-weight:700;">
            Required: ${rounded.toFixed(2)}
          </span>
          <span>4.00</span>
        </div>
      </div>`;
  }

  // Smooth scroll to show result
  resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

</body>
</html>
<?php mysqli_close($conn); ?>