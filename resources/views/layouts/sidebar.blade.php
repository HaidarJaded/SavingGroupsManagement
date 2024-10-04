<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your App</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
        }
        .sidebar h3 {
            color: white;
            margin-bottom: 30px;
        }
        .sidebar a {
            color: white;
            padding: 10px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px; /* Sidebar width */
            padding: 20px;
            flex-grow: 1;
        }
        .logout-btn {
            margin-top: 20px;
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
        }
        .logout-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Al Kamal Phone</h3>
            <a href="{{ route('saving_groups.index') }}">الجمعيات</a>
            <a href="{{ route('transactions.index') }}">الحوالات</a>
            <a href="{{ route('payments.index') }}">المدفوعات</a>
            
            <!-- Logout Button -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="btn logout-btn">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>

        <!-- Content Area -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
