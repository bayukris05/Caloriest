<nav class="navbar-custom">
    <div class="logo">
        <img src="<?php echo e(asset('images/logogizi.png')); ?>" alt="logo" class="logo-icon">
        Caloriest
    </div>
    <div class="nav-center">
        <a href="<?php echo e(route('homepage')); ?>" class="<?php echo e(request()->routeIs('homepage') ? 'active' : ''); ?>">
            Beranda
        </a>
        <a href="<?php echo e(route('calculator')); ?>" class="<?php echo e(request()->routeIs('calculator*') ? 'active' : ''); ?>">
            Kalkulator
        </a>
        <a href="<?php echo e(route('recomend')); ?>" class="<?php echo e(request()->routeIs('recomend*') ? 'active' : ''); ?>">
            Rekomendasi
        </a>
        <a href="<?php echo e(Auth::check() ? route('forum.forum') : route('login')); ?>" class="<?php echo e(request()->routeIs('forum*') ? 'active' : ''); ?>">
            Forum
        </a>
        <a href="<?php echo e(route('profile.edit')); ?>" class="<?php echo e(request()->routeIs('profile*') ? 'active' : ''); ?>">
            Profil
        </a>
    </div>
    <?php if(auth()->guard()->guest()): ?>
        <button class="get_started" onclick="location.href='<?php echo e(route('login')); ?>'">Mulai</button>
    <?php endif; ?>
    <?php if(auth()->guard()->check()): ?>
        <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="get_started">Logout</button>
        </form>
    <?php endif; ?>
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
<?php /**PATH D:\lomba\TrackCalorie\resources\views/components/navbar.blade.php ENDPATH**/ ?>