<nav class="navbar">
    <a href="{{ url('/') }}" class="navbar__logo">Wisata<span>Tomohon</span></a>

    <!-- Navbar Nav (Desktop) -->
    <div class="navbar__nav">
        <a href="{{ route ('home') }}">Home</a>
        <a href="{{ route ('all__des') }}">Destination</a>
        <a href="{{ route ('all__events') }}">Event</a>
        
    </div>

    <div class="nav__extra">
        @auth
            <!-- User Menu Desktop -->
            <div class="user-menu desktop-only">
                <div class="user-profile">
                    <div class="user-avatar" style="background-color: {{ Auth::user()->avatar_color }};">
                        <span class="avatar-initials">{{ Auth::user()->initials }}</span>
                    </div>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                <div class="dropdown-menu">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item" style="background-color: transparent; padding: 8px; margin-left: 8px;font-size: 15px;">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"><i data-feather="user"></i></a>
        @endauth

        <!-- Humburger -->
        <a href="#" id="humburger__menu"><i data-feather="menu"></i></a>
    </div>
</nav>

<!-- Mobile Dropdown Menu -->
<div class="mobile__dropdown" id="mobileDropdown">
    <div class="navbar__nav">
        <a href="{{ route ('home') }}">Home</a>
        <a href="{{ route ('all__des') }}">Destination</a>
        <a href="{{ route ('all__events') }}">Event</a>
        
    </div>

    @auth
        <!-- User Info + Logout langsung di dropdown mobile -->
        <div class="user-menu mobile-only">
            <div class="user-profile">
                <div class="user-avatar" style="background-color: {{ Auth::user()->avatar_color }};">
                    <span class="avatar-initials">{{ Auth::user()->initials }}</span>
                </div>
                <span class="user-name">{{ Auth::user()->name }}</span>
            </div>
            <hr class="navbar__divider">
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="dropdown-item">
                    Sign out
                </button>
            </form>
        </div>
    @else
        <a href="{{ route('login') }}" style="font-size : 15px;">Login</a>
    @endauth
</div>

<style>
:root {
    --primary: #fdfdfd;
    --sec: #710eff;
    --txt: #1f1f1f;
}

/* Divider tipis */
.navbar__divider {
    border: 0;
    border-top: 1px solid #e5e5e5; /* abu tipis */
    margin: 6px 0; /* beri jarak atas bawah */
}


/* ===== NAVBAR BASE ===== */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: #fdfdfd;
    border-bottom: 1px solid #eee;
    position: sticky;
    top: 0;
    z-index: 9999;
}

.navbar__logo {
    font-size: 1.5rem;
    font-weight: 800;
    text-decoration: none;
    color: #1f1f1f;
}
.navbar__logo span {
    color: #710eff;
    font-style: italic;
}

/* ===== NAV LINKS ===== */
.navbar__nav {
    display: none; /* mobile default hidden */
    flex-direction: column;
    gap: 12px;
}
.navbar__nav a {
    text-decoration: none;
    color: #1f1f1f;
    font-size: 1rem;
    transition: color 0.2s;
}
.navbar__nav a:hover {
    color: #710eff;
    font-weight: bold;
}

/* ===== EXTRA RIGHT ===== */
.nav__extra {
    display: flex;
    align-items: center;
    gap: 15px;
}
.nav__extra a {
    color: #1f1f1f;
    margin-left: 0.5rem;
}
.nav__extra a:hover {
    color: #710eff;
}

/* ===== HAMBURGER ===== */
#humburger__menu {
    display: block;
    cursor: pointer;
    font-size: 1.5rem;
    color: #1f1f1f;
}

:root {
    --navbar-height: 50px; /* tinggi navbar */
}

/* ===== MOBILE DROPDOWN ===== */
.mobile__dropdown {
    position: fixed;  /* ✅ supaya muncul tepat di bawah navbar */
    top: var(--navbar-height);
    right: -100%;   /* sembunyi default */
    height: calc(100vh - var(--navbar-height));
    width: 250px;
    background: #fff;
    box-shadow: -2px 0 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    padding: 1.5rem 20px;
    transition: right 0.3s ease-in-out;
    z-index: 9998;
}
.mobile__dropdown.show {
    right: 0;
}

/* Mobile user-menu */
.mobile__dropdown .user-menu {
    margin-top: 15px;
    border-top: 1px solid #eee;
    padding-top: 10px;
    flex-direction: column;
    align-items: flex-start;
}
.mobile__dropdown .user-profile {
    display: flex;
    align-items: center;
    padding: 6px 0;
}
.mobile__dropdown .user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #1f1f1f;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 6px;
    font-weight: bold;
    font-size: 15px;
}
.mobile__dropdown .user-name {
    font-size: 15px;
    font-weight: 500;
    color: #1f1f1f;
}
.mobile__dropdown .dropdown-item {
    margin-top: 6px;
    padding: 8px 0;
    color: #1f1f1f;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    cursor: pointer;
    width: 100%;
    text-align: left;
}
.mobile__dropdown .dropdown-item:hover {
    color: #710eff;
}

/* ==== MOBILE NAV STYLE ==== */
.mobile__dropdown .navbar__nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
}
.mobile__dropdown .navbar__nav a {
    color: var(--txt);
    font-size: 15px;
    text-decoration: none;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}
.mobile__dropdown .navbar__nav a:last-child {
    border-bottom: none;
}

/* ===== DESKTOP USER MENU ===== */
.user-menu {
    position: relative;
    display: inline-block;
}
.user-profile {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
}
.user-profile:hover {
    color: #710eff;
}
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #1f1f1f;
    color: #fdfdfd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}
.user-name {
    font-size: 14px;
    font-weight: 500;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #1f1f1f;
}
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: #fdfdfd;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 6px;
    min-width: 180px;
    z-index: 1000;
    padding: 8px 0;
    margin-top: 5px;
}
.user-menu:hover .dropdown-menu {
    display: block;
}
.dropdown-item i {
    margin-right: 8px;
    width: 16px;
    height: 16px;
    background-color: transparent;
}

/* ===== RESPONSIVE ===== */
.desktop-only { display: none; }
.mobile-only { display: flex; }

@media (min-width: 992px) {
    .navbar__nav {
        display: flex;
        flex-direction: row;
        gap: 20px;
    }
    #humburger__menu { display: none; }
    .mobile__dropdown { display: none !important; }
    .desktop-only { display: flex; }
    .mobile-only { display: none; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const humburger = document.getElementById("humburger__menu");
    const mobileDropdown = document.getElementById("mobileDropdown");

    if (humburger) {
        humburger.addEventListener("click", function(e) {
            e.preventDefault();
            mobileDropdown.classList.toggle("show");
            if (typeof feather !== "undefined") feather.replace();
        });
    }
});
</script>
