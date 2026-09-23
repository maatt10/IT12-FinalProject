<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', "Lara's Flowershop")</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
            color: #333;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: #2f5d50;
            color: white;
            padding: 25px 15px;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .user-info strong {
            display: block;
            margin-bottom: 4px;
        }

        .nav-title {
            font-size: 11px;
            text-transform: uppercase;
            opacity: 0.7;
            margin: 15px 10px 8px;
        }

        .nav-link {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
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
        }

        .page-header h1 {
            font-size: 28px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2f5d50;
            color: white;
        }

        .btn-secondary {
            background: #777;
            color: white;
        }

        .btn-danger {
            background: #c0392b;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f3f3f3;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dff0d8;
            color: #356635;
        }

        .alert-error {
            background: #f2dede;
            color: #a94442;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .error {
            color: #c0392b;
            font-size: 13px;
            margin-top: 5px;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        .logout-form {
            margin-top: 25px;
        }

        .logout-btn {
            width: 100%;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>

    <div class="layout">

        <aside class="sidebar">

            <div class="brand">
                Lara's Flowershop
            </div>

            <div class="user-info">
                <strong>
                    {{ Auth::user()->first_name }}
                    {{ Auth::user()->last_name }}
                </strong>

                {{ ucfirst(Auth::user()->role) }}
            </div>

            <div class="nav-title">Main</div>

            <a href="{{ route('dashboard') }}" class="nav-link">
                Dashboard
            </a>

            <div class="nav-title">Management</div>

            <a href="{{ route('products.index') }}" class="nav-link">
                Products
            </a>

            <a href="{{ route('production.index') }}" class="nav-link">
                Production
            </a>

            <a href="{{ route('inventory.index') }}" class="nav-link">
                Inventory
            </a>

            <a href="{{ route('purchases.index') }}" class="nav-link">
                Purchases
            </a>

            <a href="{{ route('sales.create') }}" class="nav-link">
                Sales / POS
            </a>

            <a href="{{ route('customers.index') }}" class="nav-link">
                Customers
            </a>

            @if(Auth::user()->role === 'owner')
            <div class="nav-title">Owner</div>

            <a href="{{ route('orders.index') }}" class="nav-link">
                Online Orders
            </a>

            <a href="{{ route('audit.index') }}" class="nav-link">
                Audit Trail
            </a>

            <a href="{{ route('backup.index') }}" class="nav-link">
                Backup & Recovery
            </a>
            @endif

            <div class="nav-title">Reports</div>

            <a href="{{ route('reports.sales') }}" class="nav-link">
                Sales Reports
            </a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    Log Out
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