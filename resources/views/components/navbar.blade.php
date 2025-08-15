<nav class="navbar">
    <a href="{{ url('/') }}" class="navbar__logo">Wisata<span>Tomohon</span></a>

    <div class="navbar__nav">
        <a href="{{ route ('home') }}">Home</a>
        <a href="{{ route ('all__des') }}">Destination</a>
        <a href="{{ url('contact') }}">Contact</a>
    </div>

    <div class="nav__extra">
        @auth
            <div class="user-menu">
                <div class="user-profile">
                   <div class="user-avatar" style="background-color: {{ Auth::user()->avatar_color }};">
                    <span class="avatar-initials">{{ Auth::user()->initials }}</span>
                  </div>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </div>
                <div class="dropdown-menu">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i data-feather="log-out"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"><i data-feather="user"></i></a>
        @endauth
        <a href="#" id="humburger__menu"><i data-feather="menu"></i></a>
    </div>
</nav>