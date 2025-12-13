<nav class="navbar-custom">
    <div class="logo">
        <img src="{{ asset('images/logogizi.png') }}" alt="logo" class="logo-icon">
        Caloriest
    </div>
    <div class="nav-center">
        <a href="{{ route('homepage') }}" class="{{ request()->routeIs('homepage') ? 'active' : '' }}">
            Beranda
        </a>
        <a href="{{ route('calculator') }}" class="{{ request()->routeIs('calculator*') ? 'active' : '' }}">
            Kalkulator
        </a>
        <a href="{{ route('recomend') }}" class="{{ request()->routeIs('recomend*') ? 'active' : '' }}">
            Rekomendasi
        </a>
        <a href="{{ Auth::check() ? route('forum.forum') : route('login') }}" class="{{ request()->routeIs('forum*') ? 'active' : '' }}">
            Forum
        </a>
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile*') ? 'active' : '' }}">
            Profil
        </a>
    </div>
    @guest
        <button class="get_started" onclick="location.href='{{ route('login') }}'">Mulai</button>
    @endguest
    @auth
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="get_started">Logout</button>
        </form>
    @endauth
    <button class="mobile-menu-toggle">☰</button>
</nav>

<script>
    // Mobile menu toggle functionality
    document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
        const navCenter = document.querySelector('.nav-center');
        navCenter.classList.toggle('show');
        this.textContent = this.textContent === '☰' ? '✕' : '☰';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar-custom');

        function updateNavbar() {
            // Check scroll position from various possible sources
            const scrollValues = [
                window.scrollY,
                window.pageYOffset,
                document.documentElement.scrollTop,
                document.body.scrollTop
            ];
            
            // Get the maximum scroll value found
            const currentScroll = Math.max(...scrollValues);

            if (currentScroll > 50) {
                if (!navbar.classList.contains('scrolled')) {
                    navbar.classList.add('scrolled');
                }
            } else {
                if (navbar.classList.contains('scrolled')) {
                    navbar.classList.remove('scrolled');
                }
            }
        }

        // Listen to scroll on window and document components
        window.addEventListener('scroll', updateNavbar, { passive: true });
        document.addEventListener('scroll', updateNavbar, { passive: true });
        document.body.addEventListener('scroll', updateNavbar, { passive: true });

        // Initial check
        updateNavbar();
    });
</script>
