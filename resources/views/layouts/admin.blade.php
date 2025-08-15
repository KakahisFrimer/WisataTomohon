<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
    
    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

  <div class="admin-dashboard">
    @include('components.sidebar')

    <main class="main">
        @yield('dashboard')
    </main>
  </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const adminDashboard = document.querySelector('.admin-dashboard');

    if (toggleBtn && sidebar && adminDashboard) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            adminDashboard.classList.toggle('sidebar-collapsed'); // tambahkan class di parent

            // Simpan status collapse di localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Restore state saat reload
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            adminDashboard.classList.add('sidebar-collapsed');
        }
    }

    // Inisialisasi Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>


</body>
</html>
