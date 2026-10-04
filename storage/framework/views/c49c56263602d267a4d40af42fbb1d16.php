<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', "Lara's Flowershop"); ?></title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        body {
            background: #F9F6F0;
            color: #212121;
            overflow-x: hidden;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: #2E5A3B;
            color: #FFFFFF;
            padding: 25px 15px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .brand-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: #FFFFFF;
            border-radius: 50%;
            padding: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 2px solid #D4AF37;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #FFFFFF;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 10px;
            text-align: center;
            color: #F6C453;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #D4AF37;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 2px;
        }

        .nav-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #F6C453;
            letter-spacing: 1.5px;
            margin: 16px 12px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 9px 12px;
            border-radius: 8px;
            margin-bottom: 2px;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
        }

        .nav-link.active {
            background: #E85D75;
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(232, 93, 117, 0.5);
        }

        .nav-link svg {
            flex-shrink: 0;
        }

        .nav-link.sub {
            padding-left: 24px;
            font-size: 13px;
        }

        .nav-link.sub::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            margin-right: 6px;
            flex-shrink: 0;
        }

        .nav-link.sub.active::before {
            background: #FFFFFF;
        }

        .logout-form {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: none;
            color: rgba(255, 255, 255, 0.85);
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #E85D75;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(232, 93, 117, 0.4);
        }

        /* MAIN WRAPPER */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: #FFFFFF;
            border-bottom: 1px solid #F0E6DD;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .topbar-clock {
            display: flex;
            flex-direction: column;
        }

        .clock-date {
            font-size: 11px;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .clock-time {
            font-size: 18px;
            font-weight: 700;
            color: #2E5A3B;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .clock-time span.seconds {
            color: #E85D75;
            font-size: 15px;
            margin-left: 2px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 20px;
            border-left: 1px solid #F0E6DD;
            cursor: default;
            user-select: none;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FCE4EC;
            border: 2px solid #E85D75;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #E85D75;
        }

        .user-details {
            overflow: hidden;
            min-width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #212121;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .user-role {
            font-size: 10px;
            color: #D4AF37;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-top: 2px;
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            font-size: 26px;
            color: #212121;
            font-weight: 700;
        }

        .page-header p {
            font-size: 14px;
            color: #64748B;
            margin-top: 4px;
        }

        .card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #F0E6DD;
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #E85D75;
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(232, 93, 117, 0.3);
        }

        .btn-primary:hover {
            background: #D14A62;
            box-shadow: 0 4px 10px rgba(232, 93, 117, 0.4);
        }

        .btn-secondary {
            background: #F0E6DD;
            color: #212121;
        }

        .btn-secondary:hover {
            background: #E5D5C5;
        }

        .btn-danger {
            background: #DC3545;
            color: #FFFFFF;
        }

        .btn-danger:hover {
            background: #C82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 14px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #FCE4EC;
            color: #E85D75;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            ;
        }

        td {
            border-bottom: 1px solid #F0E6DD;
            color: #212121;
        }

        tbody tr:hover {
            background: #FFF9FB;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #E8F5E9;
            color: #2E5A3B;
            solid #80B918;
        }

        .alert-error {
            background: #FDECEA;
            color: #C0392B;
            solid #DC3545;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
            color: #212121;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #F0E6DD;
            border-radius: 8px;
            font-size: 14px;
            background: #FFFFFF;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #E85D75;
            box-shadow: 0 0 0 3px rgba(232, 93, 117, 0.1);
        }

        /* Centered form pages */
        .form-page {
            max-width: 720px;
            margin: 0 auto;
        }

        .form-page-wide {
            max-width: 900px;
            margin: 0 auto;
        }

        .error {
            color: #DC3545;
            font-size: 12px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
                height: auto;
            }

            .sidebar-nav {
                max-height: 400px;
            }

            .topbar {
                padding: 12px 16px;
                flex-wrap: wrap;
            }

            .content {
                padding: 20px 15px;
            }

            .clock-date {
                font-size: 10px;
            }

            .clock-time {
                font-size: 16px;
            }

            .user-details {
                display: none;
            }
        }

        table td.num,
        table th.num {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <div class="layout">

        
        <aside class="sidebar">

            <div class="brand-logo">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Lara's Flowershop">
            </div>
            <div class="brand">Lara's Flowershop</div>
            <div class="brand-sub">Est. 2021</div>

            <div class="sidebar-nav">
                <div class="nav-title">Main</div>


                
                <a href="<?php echo e(route('dashboard')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                
                <a href="<?php echo e(route('pos.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('pos.*') ? 'active' : ''); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    POS
                </a>

                
                <a href="<?php echo e(route('products.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('products.*') || request()->routeIs('inventory.*') ? 'active' : ''); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Inventory
                </a>

                
                <a href="<?php echo e(route('reports.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports
                </a>

                
                <?php if(auth()->user()->role === 'owner'): ?>
                <a href="<?php echo e(route('records.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('records.*') ? 'active' : ''); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Records
                </a>

                <div class="nav-title">System</div>

                <a href="<?php echo e(route('audit.index')); ?>"
                    class="nav-link sub <?php echo e(request()->routeIs('audit.*') ? 'active' : ''); ?>">
                    Audit Trail
                </a>

                <a href="<?php echo e(route('backup.index')); ?>"
                    class="nav-link sub <?php echo e(request()->routeIs('backup.*') ? 'active' : ''); ?>">
                    Backup &amp; Recovery
                </a>
                <?php endif; ?>

            </div>

            
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="logout-form">
                <?php echo csrf_field(); ?>
                <button type="submit" class="logout-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </button>
            </form>

        </aside>

        
        <div class="main-wrapper">

            <div class="topbar">
                <div class="topbar-clock">
                    <div class="clock-date" id="live-date">—</div>
                    <div class="clock-time" id="live-time">—</div>
                </div>

                <div class="topbar-user">
                    <div class="user-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo e(auth()->user()->full_name); ?></span>
                        <span class="user-role"><?php echo e(auth()->user()->role); ?></span>
                    </div>
                </div>
            </div>

            <div class="content">

                <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                <div class="alert alert-error"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>

            </div>

        </div>

    </div>

    <script>
        function updateClock() {
            const now = new Date();

            const dateStr = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });

            const hours = String(now.getHours() % 12 || 12).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = now.getHours() >= 12 ? 'PM' : 'AM';

            const dateEl = document.getElementById('live-date');
            const timeEl = document.getElementById('live-time');

            if (dateEl) dateEl.textContent = dateStr;
            if (timeEl) timeEl.innerHTML = `${hours}:${minutes}<span class="seconds">:${seconds}</span> ${ampm}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>

</body>

</html><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/layouts/app.blade.php ENDPATH**/ ?>