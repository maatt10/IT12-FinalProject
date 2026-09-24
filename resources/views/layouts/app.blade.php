<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', "Laraa's Flowershop")</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Instrument Sans', Arial, sans-serif;
        }

        body {
            background: #F9F6F0;
            color: #212121;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===============================
   SIDEBAR
   =============================== */
        .sidebar {
            width: 240px;
            background: #2E5A3B;
            color: #FFFFFF;
            padding: 25px 15px;
            display: flex;
            flex-direction: column;
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 10px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding-bottom: 20px;
            cursor: default;
            user-select: none;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid #D4AF37;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #F6C453;
        }

        .user-details {
            overflow: hidden;
            min-width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .user-role {
            font-size: 11px;
            color: #F6C453;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            margin-top: 2px;
        }

        .user-info strong {
            display: block;
            margin-bottom: 4px;
            color: #F6C453;
        }

        .nav-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #F6C453;
            letter-spacing: 1.5px;
            margin: 18px 10px 8px;
        }

        .nav-link {
            display: block;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 14px;
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

        /* ===============================
           MAIN CONTENT
           =============================== */
        .content {
            flex: 1;
            padding: 30px;
            overflow-x: auto;
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

        .card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #F0E6DD;
        }

        /* ===============================
           BUTTONS
           =============================== */
        .btn {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
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

        .btn-gold {
            background: #D4AF37;
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.3);
        }

        .btn-gold:hover {
            background: #C19B2E;
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

        /* ===============================
           TABLES
           =============================== */
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
            border-bottom: 2px solid #F8BBD0;
        }

        td {
            border-bottom: 1px solid #F0E6DD;
            color: #212121;
        }

        tbody tr:hover {
            background: #FFF9FB;
        }

        /* ===============================
           ALERTS
           =============================== */
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
            border-left: 4px solid #80B918;
        }

        .alert-error {
            background: #FDECEA;
            color: #C0392B;
            border-left: 4px solid #DC3545;
        }

        /* ===============================
           FORMS
           =============================== */
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
        }

        .form-control:focus {
            outline: none;
            border-color: #E85D75;
            box-shadow: 0 0 0 3px rgba(232, 93, 117, 0.1);
        }

        .error {
            color: #DC3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        /* ===============================
           LOGOUT BUTTON
           =============================== */
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

        .logout-btn:active {
            transform: translateY(0);
        }

        .logout-btn svg {
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .logout-btn:hover svg {
            transform: translateX(2px);
        }

        /* ===============================
           RESPONSIVE
           =============================== */
        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #F0E6DD;
            }

            .content {
                padding: 20px 15px;
            }

            .page-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="layout">

        <aside class="sidebar">

            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Lara's Flowershop">
            </div>
            <div class="brand">
                Lara's Flowershop
            </div>
            <div class="brand-sub">
                Est. 2021
            </div>

            <div class="user-info">
                <div class="user-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="user-details">
                    <span class="user-name">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </span>
                    <span class="user-role">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                </div>
            </div>

            <div class="nav-title">Main</div>
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>

            <div class="nav-title">Management</div>
            <a href="{{ route('products.index') }}"
                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                Products
            </a>
            <a href="{{ route('production.index') }}"
                class="nav-link {{ request()->routeIs('production.*') ? 'active' : '' }}">
                Production
            </a>
            <a href="{{ route('inventory.index') }}"
                class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                Inventory
            </a>
            <a href="{{ route('purchases.index') }}"
                class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                Purchases
            </a>
            <a href="{{ route('sales.create') }}"
                class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                Sales / POS
            </a>
            <a href="{{ route('customers.index') }}"
                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                Customers
            </a>

            @if(Auth::user()->role === 'owner')
            <div class="nav-title">Owner</div>
            <a href="{{ route('orders.index') }}"
                class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                Online Orders
            </a>
            <a href="{{ route('audit.index') }}"
                class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                Audit Trail
            </a>
            <a href="{{ route('backup.index') }}"
                class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                Backup &amp; Recovery
            </a>
            @endif

            <div class="nav-title">Reports</div>
            <a href="{{ route('reports.sales') }}"
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Sales Reports
            </a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>

        </aside>

        <main class="content">

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
            @endif

            @yield('content')

        </main>

    </div>

</body>

</html>