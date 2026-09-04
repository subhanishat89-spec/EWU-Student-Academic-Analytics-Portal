<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EWU Student Portal — Login</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    /* ── Reset ─────────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── Full-screen EWU Green background ──────────────────────── */
    body {
      min-height: 100vh;
      background: #006a4e;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'DM Sans', sans-serif;
      overflow: hidden;
      position: relative;
    }

    /* ── Decorative background blobs ──────────────────────────── */
    body::before {
      content: '';
      position: fixed;
      top: -120px; left: -120px;
      width: 480px; height: 480px;
      border-radius: 50%;
      background: rgba(242, 169, 0, 0.18);
      filter: blur(80px);
      pointer-events: none;
      animation: float 8s ease-in-out infinite alternate;
    }
    body::after {
      content: '';
      position: fixed;
      bottom: -100px; right: -100px;
      width: 420px; height: 420px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.07);
      filter: blur(70px);
      pointer-events: none;
      animation: float 10s ease-in-out infinite alternate-reverse;
    }

    @keyframes float {
      from { transform: translateY(0px) scale(1); }
      to   { transform: translateY(30px) scale(1.05); }
    }

    /* ── Extra subtle circle ornament ─────────────────────────── */
    .bg-orb {
      position: fixed;
      top: 50%; left: 10%;
      transform: translateY(-50%);
      width: 300px; height: 300px;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,0.06);
      pointer-events: none;
    }
    .bg-orb-2 {
      position: fixed;
      top: 15%; right: 8%;
      width: 200px; height: 200px;
      border-radius: 50%;
      border: 1px solid rgba(242,169,0,0.12);
      pointer-events: none;
    }

    /* ── Glassmorphism Login Card ──────────────────────────────── */
    .login-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      background: rgba(255, 255, 255, 0.10);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.20);
      border-radius: 20px;
      padding: 44px 40px 36px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.30),
                  inset 0 1px 0 rgba(255,255,255,0.15);
      animation: cardIn 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(28px) scale(0.97); }
      to   { opacity: 1; transform: translateY(0)    scale(1); }
    }

    /* ── Logo mark ─────────────────────────────────────────────── */
    .login-logo-ring {
      width: 64px; height: 64px;
      border-radius: 50%;
      background: rgba(242, 169, 0, 0.20);
      border: 2px solid rgba(242, 169, 0, 0.45);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 18px;
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      font-weight: 700;
      color: #f2a900;
      box-shadow: 0 0 0 6px rgba(242,169,0,0.07);
    }

    /* ── Title ─────────────────────────────────────────────────── */
    .login-title {
      text-align: center;
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 700;
      color: #f2a900;
      letter-spacing: 0.01em;
      margin-bottom: 4px;
    }
    .login-subtitle {
      text-align: center;
      font-size: 13px;
      color: rgba(255,255,255,0.60);
      margin-bottom: 32px;
      letter-spacing: 0.04em;
    }

    /* ── Gold divider ──────────────────────────────────────────── */
    .gold-divider {
      height: 2px;
      background: linear-gradient(90deg, transparent, #f2a900, transparent);
      border-radius: 2px;
      margin-bottom: 28px;
    }

    /* ── Form fields ───────────────────────────────────────────── */
    .field-group {
      margin-bottom: 18px;
    }
    .field-group label {
      display: block;
      font-size: 11.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: rgba(255,255,255,0.65);
      margin-bottom: 7px;
    }
    .field-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .field-wrap .field-icon {
      position: absolute;
      left: 13px;
      color: rgba(255,255,255,0.45);
      font-size: 15px;
      pointer-events: none;
      transition: color 0.2s;
    }
    .field-wrap input {
      width: 100%;
      padding: 11px 14px 11px 38px;
      background: rgba(255,255,255,0.08);
      border: 1.5px solid rgba(255,255,255,0.18);
      border-radius: 10px;
      color: #ffffff;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      outline: none;
      transition: border-color 0.22s, background 0.22s, box-shadow 0.22s;
    }
    .field-wrap input::placeholder {
      color: rgba(255,255,255,0.35);
    }
    .field-wrap input:focus {
      border-color: #f2a900;
      background: rgba(255,255,255,0.12);
      box-shadow: 0 0 0 3px rgba(242,169,0,0.18);
    }
    .field-wrap input:focus + .field-icon,
    .field-wrap:focus-within .field-icon {
      color: #f2a900;
    }

    /* ── Password toggle eye ───────────────────────────────────── */
    .eye-toggle {
      position: absolute;
      right: 13px;
      background: none;
      border: none;
      color: rgba(255,255,255,0.40);
      cursor: pointer;
      font-size: 15px;
      padding: 0;
      transition: color 0.2s;
    }
    .eye-toggle:hover { color: #f2a900; }

    /* ── Error message ─────────────────────────────────────────── */
    .login-error {
      display: none;
      align-items: center;
      gap: 8px;
      background: rgba(231,76,60,0.18);
      border: 1px solid rgba(231,76,60,0.45);
      border-radius: 8px;
      padding: 10px 14px;
      color: #ff8080;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 18px;
      animation: shakeError 0.4s ease;
    }
    .login-error.visible { display: flex; }

    @keyframes shakeError {
      0%,100% { transform: translateX(0); }
      20%     { transform: translateX(-8px); }
      40%     { transform: translateX(8px); }
      60%     { transform: translateX(-5px); }
      80%     { transform: translateX(5px); }
    }

    /* ── Login button ──────────────────────────────────────────── */
    .login-btn {
      width: 100%;
      padding: 13px;
      background: #f2a900;
      border: none;
      border-radius: 10px;
      color: #1a1000;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.04em;
      cursor: pointer;
      box-shadow: 0 4px 18px rgba(242,169,0,0.40);
      transition: all 0.22s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 6px;
    }
    .login-btn:hover {
      background: #e09800;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(242,169,0,0.50);
    }
    .login-btn:active {
      transform: scale(0.98);
      box-shadow: 0 2px 10px rgba(242,169,0,0.30);
    }

    /* ── Footer note ───────────────────────────────────────────── */
    .login-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 11.5px;
      color: rgba(255,255,255,0.35);
      letter-spacing: 0.03em;
    }
    .login-footer span { color: rgba(242,169,0,0.65); }

    /* ── Demo hint badge ───────────────────────────────────────── */
    .demo-hint {
      text-align: center;
      margin-bottom: 20px;
    }
    .demo-hint span {
      display: inline-block;
      background: rgba(242,169,0,0.12);
      border: 1px solid rgba(242,169,0,0.25);
      border-radius: 50px;
      padding: 4px 14px;
      font-size: 11.5px;
      color: rgba(242,169,0,0.80);
      letter-spacing: 0.04em;
    }

    @media (max-width: 480px) {
      .login-card { margin: 16px; padding: 32px 24px 28px; }
      .login-title { font-size: 1.35rem; }
    }
  </style>
</head>
<body>

  <!-- Decorative rings -->
  <div class="bg-orb"></div>
  <div class="bg-orb-2"></div>

  <!-- ════════════════════════════════════════════════════════════
       LOGIN CARD
       ════════════════════════════════════════════════════════════ -->
  <div class="login-card">

    <!-- Logo mark -->
    <div class="login-logo-ring">EWU</div>

    <!-- Title -->
    <div class="login-title">EWU Student Portal</div>
    <div class="login-subtitle">Academic & Predictive Analytics System</div>

    <!-- Gold divider -->
    <div class="gold-divider"></div>

    <!-- Demo hint -->
    <div class="demo-hint">
      <span><i class="bi bi-info-circle"></i>&nbsp; Enter ID and password to continue</span>
    </div>

    <!-- Error message (hidden by default) -->
    <div class="login-error" id="loginError">
      <i class="bi bi-exclamation-triangle-fill"></i>
      Please enter your credentials.
    </div>

    <!-- Student ID field -->
    <div class="field-group">
      <label for="studentId">Student ID</label>
      <div class="field-wrap">
        <i class="bi bi-person-badge-fill field-icon"></i>
        <input type="text"
               id="studentId"
               placeholder="e.g. 2023-1-60-161"
               autocomplete="username"
               oninput="clearError()">
      </div>
    </div>

    <!-- Password field -->
    <div class="field-group">
      <label for="password">Password</label>
      <div class="field-wrap">
        <i class="bi bi-lock-fill field-icon"></i>
        <input type="password"
               id="password"
               placeholder="Enter your password"
               autocomplete="current-password"
               oninput="clearError()"
               onkeydown="if(event.key==='Enter') handleLogin()">
        <button class="eye-toggle" type="button"
                onclick="togglePassword()" id="eyeBtn"
                title="Show/hide password">
          <i class="bi bi-eye-fill" id="eyeIcon"></i>
        </button>
      </div>
    </div>

    <!-- Login button -->
    <button class="login-btn" onclick="handleLogin()">
      <i class="bi bi-box-arrow-in-right"></i>
      Login to Portal
    </button>

    <!-- Footer -->
    <div class="login-footer">
      <span>East West University</span> &nbsp;&bull;&nbsp;
       2026
    </div>

  </div><!-- end .login-card -->

  <script>
  
  // ============================================================
  function handleLogin() {
    const id  = document.getElementById('studentId').value.trim();
    const pwd = document.getElementById('password').value.trim();
    const err = document.getElementById('loginError');

    if (id === '' || pwd === '') {
      // Show shake animation by removing and re-adding the class
      err.classList.remove('visible');
      void err.offsetWidth; // force reflow for re-animation
      err.classList.add('visible');
      document.getElementById(id === '' ? 'studentId' : 'password').focus();
      return;
    }

    // ── Both fields filled → go to portal ──────────────────────
    // Brief button feedback before redirect
    const btn = document.querySelector('.login-btn');
    btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin .6s linear infinite;"></i> Logging in…';
    btn.style.opacity = '0.85';
    btn.disabled = true;

    setTimeout(function () {
      window.location.href = 'index.php';
    }, 700);
  }

  function clearError() {
    document.getElementById('loginError').classList.remove('visible');
  }

  function togglePassword() {
    const input   = document.getElementById('password');
    const icon    = document.getElementById('eyeIcon');
    const showing = input.type === 'text';
    input.type    = showing ? 'password' : 'text';
    icon.className = showing ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
  }

  // Spin keyframe for loading icon
  const style = document.createElement('style');
  style.textContent = '@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }';
  document.head.appendChild(style);
  </script>

</body>
</html>
