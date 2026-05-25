<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wellmeadows Hospital — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
  :root {
    --navy:#0F1E3D; --navy-mid:#1B2D5B; --navy-light:#2a4080;
    --sky:#5B9BD5; --sky-light:#A8CBF0; --sky-pale:#D6EAFA;
    --white:#FFFFFF; --muted:rgba(255,255,255,0.55);
    --border:rgba(255,255,255,0.15); --glass:rgba(10,22,50,0.72);
    --error:#ff6b6b;
  }
  html, body { height:100%; font-family:'DM Sans',sans-serif; }
  body { display:flex; align-items:stretch; min-height:100vh; overflow:hidden; }

  /* BG */
  .bg {
    position:fixed; inset:0;
    background:url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
    z-index:0;
  }
  .bg::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(120deg,rgba(10,22,55,0.93) 0%,rgba(15,30,70,0.80) 45%,rgba(10,22,50,0.58) 100%);
  }

  .accent-line { position:fixed; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--navy-mid),var(--sky),var(--sky-light),var(--sky),var(--navy-mid)); z-index:10; }

  .particles { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
  .particle { position:absolute; border-radius:50%; background:rgba(91,155,213,0.1); animation:float linear infinite; }
  @keyframes float { 0%{transform:translateY(100vh) scale(0);opacity:0} 10%{opacity:1} 90%{opacity:0.5} 100%{transform:translateY(-10vh) scale(1);opacity:0} }

  /* LAYOUT */
  .layout { position:relative; z-index:1; display:flex; width:100%; min-height:100vh; }

  /* LEFT BRAND */
  .brand { flex:1; display:flex; flex-direction:column; justify-content:space-between; padding:52px 56px; }
  .brand-logo { display:flex; align-items:center; gap:14px; }
  .logo-mark { width:44px; height:44px; background:var(--sky); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 20px rgba(91,155,213,0.4); }
  .logo-mark svg { width:22px; height:22px; fill:white; }
  .logo-name { font-family:'Cormorant Garamond',serif; font-size:20px; font-weight:600; color:white; line-height:1.1; }
  .logo-name span { display:block; font-family:'DM Sans',sans-serif; font-size:10px; font-weight:400; color:var(--sky-light); letter-spacing:2.5px; text-transform:uppercase; margin-top:3px; }
  .brand-hero h1 { font-family:'Cormorant Garamond',serif; font-size:clamp(36px,5vw,62px); font-weight:300; color:white; line-height:1.1; margin-bottom:18px; letter-spacing:-0.5px; }
  .brand-hero h1 em { font-style:italic; color:var(--sky-light); }
  .brand-hero p { font-size:14px; line-height:1.8; color:var(--muted); max-width:380px; }
  .brand-modules-label { font-size:10px; text-transform:uppercase; letter-spacing:2px; color:rgba(255,255,255,0.3); margin-bottom:8px; }
  .module-row { display:flex; align-items:center; gap:10px; color:rgba(255,255,255,0.4); font-size:12px; margin-bottom:6px; }
  .module-row .line { width:20px; height:1px; background:rgba(255,255,255,0.2); }
  .module-row.hl { color:var(--sky-light); font-weight:500; }
  .module-row.hl .line { background:var(--sky); width:28px; }

  /* RIGHT FORM */
  .form-panel { width:440px; flex-shrink:0; display:flex; align-items:center; justify-content:center; padding:40px 24px; }
  .glass-card {
    width:100%; background:var(--glass);
    border:1px solid var(--border); border-radius:20px;
    padding:38px 34px; backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px);
    box-shadow:0 0 0 1px rgba(91,155,213,0.08),0 40px 80px rgba(0,0,0,0.45);
    animation:slideUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
    max-height:90vh; overflow-y:auto;
  }
  .glass-card::-webkit-scrollbar { width:4px; }
  .glass-card::-webkit-scrollbar-track { background:transparent; }
  .glass-card::-webkit-scrollbar-thumb { background:rgba(91,155,213,0.3); border-radius:4px; }

  @keyframes slideUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

  .tab-toggle { display:flex; background:rgba(255,255,255,0.07); border-radius:10px; padding:4px; margin-bottom:26px; border:1px solid var(--border); }
  .tab-btn { flex:1; padding:9px; border:none; background:transparent; color:var(--muted); font-family:'DM Sans',sans-serif; font-size:13px; font-weight:500; cursor:pointer; border-radius:7px; transition:all 0.2s; }
  .tab-btn.active { background:var(--sky); color:white; box-shadow:0 2px 12px rgba(91,155,213,0.4); }

  .form-heading { font-family:'Cormorant Garamond',serif; font-size:26px; font-weight:400; color:white; margin-bottom:4px; }
  .form-sub { font-size:12px; color:var(--muted); margin-bottom:20px; }

  .field { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
  .field label { font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.5); }
  .field input, .field select {
    height:42px; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12);
    border-radius:9px; padding:0 14px; color:white;
    font-family:'DM Sans',sans-serif; font-size:13px; outline:none; transition:border-color 0.2s,background 0.2s;
  }
  .field input::placeholder { color:rgba(255,255,255,0.25); }
  .field select option { background:var(--navy-mid); color:white; }
  .field input:focus, .field select:focus { border-color:var(--sky); background:rgba(91,155,213,0.1); }
  .field-row { display:flex; gap:10px; }
  .field-row .field { flex:1; }

  .forgot { text-align:right; margin-top:-4px; margin-bottom:16px; }
  .forgot a { font-size:12px; color:var(--sky-light); text-decoration:none; opacity:0.8; }
  .forgot a:hover { opacity:1; }

  .submit-btn { width:100%; height:44px; background:var(--sky); border:none; border-radius:9px; color:white; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:500; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 20px rgba(91,155,213,0.35); letter-spacing:0.3px; }
  .submit-btn:hover { background:#4a8ec4; box-shadow:0 6px 28px rgba(91,155,213,0.5); }
  .submit-btn:active { transform:scale(0.99); }

  .switch-text { text-align:center; font-size:12px; color:rgba(255,255,255,0.4); margin-top:14px; }
  .switch-text a { color:var(--sky-light); text-decoration:none; font-weight:500; }

  .badge-note { background:rgba(91,155,213,0.12); border:1px solid rgba(91,155,213,0.2); border-radius:8px; padding:10px 12px; font-size:11px; color:var(--sky-light); margin-bottom:16px; line-height:1.6; }

  /* role access info */
  .role-info { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:10px 12px; margin-bottom:16px; }
  .role-info-title { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); margin-bottom:8px; }
  .role-row { display:flex; align-items:flex-start; gap:8px; margin-bottom:6px; font-size:11px; color:rgba(255,255,255,0.5); line-height:1.4; }
  .role-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; margin-top:4px; }

  /* alerts */
  .alert { padding:10px 14px; border-radius:8px; font-size:12px; margin-bottom:16px; line-height:1.5; }
  .alert-error { background:rgba(255,107,107,0.12); border:1px solid rgba(255,107,107,0.25); color:#ff9999; }
  .alert-success { background:rgba(46,139,106,0.15); border:1px solid rgba(46,139,106,0.3); color:#7DEFA1; }

  .panel { display:none; }
  .panel.active { display:block; }

  @media (max-width:820px) {
    .brand { display:none; }
    .form-panel { width:100%; padding:28px 16px; }
  }
</style>
</head>
<body>

<div class="accent-line"></div>
<div class="bg"></div>
<div class="particles" id="particles"></div>

<div class="layout">

  <!-- LEFT BRAND -->
  <div class="brand">
    <div class="brand-logo">
      <div class="logo-mark">
  <svg fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
    <path d="M493.666,102.065H380.904V61.664c0-10.125-8.209-18.334-18.334-18.334H149.43c-10.125,0-18.334,8.209-18.334,18.334v40.401H18.334C8.209,102.065,0,110.274,0,120.399v329.937c0,10.125,8.209,18.334,18.334,18.334c7.547,0,449.061,0,475.333,0c10.125,0,18.334-8.209,18.334-18.334V120.399C512,110.274,503.791,102.065,493.666,102.065z M131.096,432.002H36.667V216.347h94.429V432.002z M131.096,179.68H36.667v-40.947h94.429V179.68z M276.014,432.002h-40.539v-78.518h40.539V432.002z M344.238,432.002h-31.556v-96.851c0-10.125-8.209-18.334-18.334-18.334h-77.206c-10.125,0-18.334,8.209-18.334,18.334v96.851h-31.045c-0.001-13.078-0.001-335.565-0.001-352.004h176.475C344.238,96.603,344.238,419.21,344.238,432.002z M380.904,138.732h94.429v40.947h-94.429V138.732z M475.334,432.002h-94.429V216.347h94.429V432.002z"></path>

    <path d="M217.193,177.54h20.474v20.474c0,10.125,8.209,18.334,18.334,18.334s18.334-8.209,18.334-18.334V177.54h20.474c10.125,0,18.334-8.209,18.334-18.334s-8.209-18.334-18.334-18.334h-20.474v-20.474c0-10.125-8.209-18.334-18.334-18.334s-18.334,8.209-18.334,18.334v20.474h-20.474c-10.125,0-18.334,8.209-18.334,18.334S207.068,177.54,217.193,177.54z"></path>
  </svg>
</div>
      <div class="logo-name">
        Wellmeadows Hospital
        <span>Management System</span>
      </div>
    </div>

    <div class="brand-hero">
      <h1>Caring for <em>lives,</em><br>powered by<br>technology.</h1>
      <p>A unified hospital information system for staff management, patient care, and clinical operations across all departments.</p>
    </div>

    <div>
      <div class="brand-modules-label">System Modules</div>
      <div class="module-row"><div class="line"></div>Patient Management</div>
      <div class="module-row"><div class="line"></div>Staff & Department Management</div>
      <div class="module-row"><div class="line"></div>Ward & Bed Management</div>
      <div class="module-row"><div class="line"></div>Appointment & Treatment</div>
      <div class="module-row"><div class="line"></div>Billing & Reporting</div>
    </div>
  </div>

  <!-- RIGHT FORM -->
  <div class="form-panel">
    <div class="glass-card">

      <div class="tab-toggle">
        <button class="tab-btn active" onclick="showTab('login',this)">Sign In</button>
        <button class="tab-btn" onclick="showTab('register',this)">Register</button>
      </div>

      {{-- ===== LOGIN PANEL ===== --}}
      <div class="panel active" id="tab-login">
        <div class="form-heading">Welcome back</div>
        <div class="form-sub">Sign in to access your dashboard</div>

        {{-- Error --}}
        @if($errors->any() && old('_tab','login') === 'login')
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <input type="hidden" name="_tab" value="login">

          <div class="field">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@wellmeadows.com">
          </div>
          <div class="field">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
          </div>

          <div class="forgot"><a href="javascript:void(0)" onclick="alert('Please ask the system administrator or HR staff to reset your account password.')">Forgot password?</a></div>

          <div class="field" style="flex-direction:row;align-items:center;gap:8px;margin-bottom:16px">
            <input type="checkbox" name="remember" id="remember" style="width:auto;height:auto;accent-color:var(--sky)">
            <label for="remember" style="text-transform:none;letter-spacing:0;font-size:12px;color:rgba(255,255,255,0.45);cursor:pointer">Remember me</label>
          </div>

          <button type="submit" class="submit-btn">Sign In</button>
        </form>

        <div class="switch-text">
          No account? <a href="javascript:void(0)" onclick="switchTo('register')">Register here</a>
        </div>
      </div>

      {{-- ===== REGISTER PANEL ===== --}}
      <div class="panel" id="tab-register">
        <div class="form-heading">Create account</div>
        <div class="form-sub">Register as a Wellmeadows staff member</div>

        {{-- Success --}}
        @if(session('registered'))
          <div class="alert alert-success">{{ session('registered') }}</div>
        @endif

        {{-- Error --}}
        @if($errors->any() && old('_tab') === 'register')
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="badge-note">
          &#9432; New accounts require <strong>admin approval</strong> before you can sign in.
        </div>

        {{-- Role access info --}}
        <div class="role-info">
          <div class="role-info-title">Access levels for Module 2</div>
          <div class="role-row">
            <div class="role-dot" style="background:#5B9BD5"></div>
            <span><strong style="color:rgba(255,255,255,0.7)">Medical Director</strong> — Full access: manage departments, assign staff, oversee schedules</span>
          </div>
          <div class="role-row">
            <div class="role-dot" style="background:#A8CBF0"></div>
            <span><strong style="color:rgba(255,255,255,0.7)">Charge Nurse</strong> — Partial access: view schedules and assigned staff in ward</span>
          </div>
          <div class="role-row">
            <div class="role-dot" style="background:#7DEFA1"></div>
            <span><strong style="color:rgba(255,255,255,0.7)">Personnel/HR Staff</strong> — Full access: manage staff records, schedules, assignments</span>
          </div>
        </div>

        <form method="POST" action="{{ route('register') }}">
          @csrf
          <input type="hidden" name="_tab" value="register">

          <div class="field-row">
            <div class="field">
              <label>First Name</label>
              <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Jane">
            </div>
            <div class="field">
              <label>Last Name</label>
              <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe">
            </div>
          </div>

          <div class="field">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@wellmeadows.com">
          </div>

          <div class="field">
            <label>Role</label>
            <select name="role" required>
              <option value="" disabled selected>Select your role</option>
              <option value="Medical Director" {{ old('role')==='Medical Director'?'selected':'' }}>Medical Director</option>
              <option value="Charge Nurse" {{ old('role')==='Charge Nurse'?'selected':'' }}>Charge Nurse</option>
              <option value="Personnel/HR Staff" {{ old('role')==='Personnel/HR Staff'?'selected':'' }}>Personnel / HR Staff</option>
            </select>
          </div>

          <div class="field">
            <label>Department</label>
            <select name="department">
              <option value="">Select department</option>
              <option>Emergency</option>
              <option>Cardiology</option>
              <option>Pediatrics</option>
              <option>Orthopedics</option>
              <option>Neurology</option>
              <option>General Medicine</option>
              <option>Administration</option>
            </select>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Password</label>
              <input type="password" name="password" required placeholder="Min. 6 characters">
            </div>
            <div class="field">
              <label>Confirm</label>
              <input type="password" name="password_confirmation" required placeholder="••••••••">
            </div>
          </div>

          <button type="submit" class="submit-btn" style="margin-top:6px">Create Account</button>
        </form>

        <div class="switch-text">
          Already registered? <a href="javascript:void(0)" onclick="switchTo('login')">Sign in</a>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
  function showTab(name, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
  }

  function switchTo(name) {
    const idx = name === 'login' ? 0 : 1;
    showTab(name, document.querySelectorAll('.tab-btn')[idx]);
  }

  // Auto-show register tab if registration errors or success
  @if(session('registered') || (old('_tab') === 'register'))
    switchTo('register');
  @endif

  // Floating particles
  const container = document.getElementById('particles');
  for (let i = 0; i < 18; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    const size = Math.random() * 60 + 20;
    p.style.cssText = `width:${size}px;height:${size}px;left:${Math.random()*100}%;animation-duration:${Math.random()*20+15}s;animation-delay:${Math.random()*-25}s;`;
    container.appendChild(p);
  }
</script>
</body>
</html>
