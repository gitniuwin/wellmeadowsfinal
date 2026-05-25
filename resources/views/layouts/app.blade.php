<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>@yield('title', 'Hospital Management System')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark" aria-hidden="true">
            <svg fill="currentColor" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M493.666,102.065H380.904V61.664c0-10.125-8.209-18.334-18.334-18.334H149.43c-10.125,0-18.334,8.209-18.334,18.334v40.401H18.334C8.209,102.065,0,110.274,0,120.399v329.937c0,10.125,8.209,18.334,18.334,18.334c7.547,0,449.061,0,475.333,0c10.125,0,18.334-8.209,18.334-18.334V120.399C512,110.274,503.791,102.065,493.666,102.065z M131.096,432.002H36.667V216.347h94.429V432.002z M131.096,179.68H36.667v-40.947h94.429V179.68z M276.014,432.002h-40.539v-78.518h40.539V432.002z M344.238,432.002h-31.556v-96.851c0-10.125-8.209-18.334-18.334-18.334h-77.206c-10.125,0-18.334,8.209-18.334,18.334v96.851h-31.045c-0.001-13.078-0.001-335.565-0.001-352.004h176.475C344.238,96.603,344.238,419.21,344.238,432.002z M380.904,138.732h94.429v40.947h-94.429V138.732z M475.334,432.002h-94.429V216.347h94.429V432.002z"></path>
                <path d="M217.193,177.54h20.474v20.474c0,10.125,8.209,18.334,18.334,18.334s18.334-8.209,18.334-18.334V177.54h20.474c10.125,0,18.334-8.209,18.334-18.334s-8.209-18.334-18.334-18.334h-20.474v-20.474c0-10.125-8.209-18.334-18.334-18.334s-18.334,8.209-18.334,18.334v20.474h-20.474c-10.125,0-18.334,8.209-18.334,18.334S207.068,177.54,217.193,177.54z"></path>
            </svg>
        </div>
        <div class="logo-text">
            <span class="hospital-name">Wellmeadows Hospital</span>
            <span class="hospital-sub">Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('staff.index') }}" class="nav-item {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Staff &amp; Depts
        </a>
        <a href="{{ route('wards.index') }}" class="nav-item {{ request()->routeIs('wards.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Wards
        </a>
        <a href="{{ route('appointments.index') }}" class="nav-item {{ request()->routeIs('appointments.*') || request()->routeIs('treatments.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Appointments
        </a>
        <a href="{{ url('/billing') }}" class="nav-item {{ str_starts_with(request()->path(), 'billing') || str_starts_with(request()->path(), 'payments') || str_starts_with(request()->path(), 'reports') || str_starts_with(request()->path(), 'outstanding') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12l2 2 4-4"/>
            </svg>
            Billing &amp; Reporting
        </a>
        <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Patient Care
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
            <div class="user-meta">
                <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                <span class="user-role">Admin</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-signout">Sign out</button>
        </form>
    </div>
</aside>

<main class="main-content">
    <div class="topbar">
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        <div class="topbar-right">
            <span class="topbar-date">{{ now()->format('F j, Y') }} &nbsp; {{ now()->format('g:i A') }}</span>
            @yield('topbar-action')
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
