<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            color: white;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            font-size: small;
            font-style: italic;
            font-size: bold;
            width: 150px;
            background: #ffff;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 0px;
        }

        .sidebar h4 {
            font-size: 28px;
            margin-bottom: 20px;
            white-space: nowrap;
            overflow: hidden;
            color: black;
            text-align: center;
            font-family: serif;

        }

        .sidebar a {
            display: block;
            color: #dc3545;
            font-weight: bold;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar a:hover {
            background: #2a2a2a;
            color: #fff;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .toggle-btn {
            background: #0d6efd;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .brand-text {
            display: none;
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100px;
                padding: 10px;
                font-size: xx-small;
            }

            .sidebar h4 {
                font-size: 24px;
            }
            .sidebar a {
                padding: 5px;
                margin-bottom: 0px;
            }
            .toggle-btn {
                padding: 0px 2px;
                border-radius: 4px;
                height: 20px;
            }
            .content{
                width: auto;
            }
            
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="sidebar" id="sidebar">
        <h4><span class="brand-text"> Admin </span></h4>

        <a href="/dashboard"><span class="menu-text">Dashboard</span></a>
        <a href="/news-dashboard"><span class="menu-text">News Dashboard</span></a>
        {{-- <a href="/newsList"><span class="menu-text">NewsList</span></a> --}}
        <a href="/sitedetails"><span class="menu-text">Site Details</span></a>
        {{-- <a href="/add-user"><span class="menu-text">Add User</span></a> --}}
        {{-- <a href="/users"><span class="menu-text">User List</span></a> --}}
        {{-- <a href="/UpdateSites"><span class="menu-text">UpdateSites</span></a> --}}
        <a href="/logout"><span class="menu-text">Logout</span></a>

    </div>

    <div class="content">
        <div class="topbar">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <span>Welcome</span>
        </div>

        @yield('content')
    </div>

</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}
</script>

</body>
</html>