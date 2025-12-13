<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Caloriest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        /* Toast Notification Styles - FIXED */
        .toast-container {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 99999 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
            pointer-events: none;
        }

        .toast {
            background: white !important;
            border-left: 4px solid #4caf50 !important;
            padding: 15px 25px !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25) !important;
            display: flex !important;
            align-items: center !important;
            min-width: 320px !important;
            max-width: 400px !important;
            opacity: 1 !important;
            transform: translateX(0) !important;
            animation: toastSlideIn 0.4s ease-out !important;
            pointer-events: auto;
        }

        .toast.error {
            border-left-color: #f44336 !important;
        }

        .toast.hiding {
            animation: toastSlideOut 0.4s ease-in forwards !important;
        }

        .toast-content {
            margin-right: 15px;
            flex: 1;
        }

        .toast-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 4px;
            color: #333;
        }

        .toast-message {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }

        .toast-close {
            margin-left: auto;
            cursor: pointer;
            color: #999;
            font-size: 20px;
            padding: 0 5px;
            pointer-events: auto;
        }

        .toast-close:hover {
            color: #333;
        }

        @keyframes toastSlideIn {
            0% {
                transform: translateX(120%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes toastSlideOut {
            0% {
                transform: translateX(0);
                opacity: 1;
            }
            100% {
                transform: translateX(120%);
                opacity: 0;
            }
        }
    </style></head>

<body>
    {{-- @include('components.navbar') --}}
    
    <!-- Toast Container - HARUS di level body, bukan di dalam div lain -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="login-container">
        <div class="left-section">
            <div class="sign-in-form">
                <h1 class="sign-in-header">Masuk ke akun Anda</h1>

                <form id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Masukkan alamat email Anda" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Masukkan kata sandi Anda" required>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ingat saya</label>
                        </div>
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn-signin">Masuk</button>
                    
                    <div style="display: flex; align-items: center; margin: 20px 0;">
                        <div style="flex: 1; height: 1px; background: #ddd;"></div>
                        <span style="padding: 0 10px; color: #777; font-size: 12px;">ATAU</span>
                        <div style="flex: 1; height: 1px; background: #ddd;"></div>
                    </div>

                    <a href="{{ route('google.login') }}" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: white; border: 1px solid #ddd; border-radius: 6px; padding: 10px; text-decoration: none; color: #555; font-weight: 500; font-size: 14px; margin-bottom: 20px; transition: all 0.3s;">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" style="width: 18px; height: 18px;">
                        Masuk dengan Google
                    </a>

                    <div class="signup-link">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="right-section">
            <div class="image-container">
                <img src="{{ asset('images/bg-kanan.png') }}" alt="Healthy Nutrition" class="food-image">
                <img src="{{ asset('images/plate.png') }}" alt="Healthy Nutrition" class="food-plate">
                <img src="{{ asset('images/apel.png') }}" alt="Apple" class="floating-fruit float1">
                <img src="{{ asset('images/tomat.png') }}" alt="tomat" class="floating-fruit float5">
                <img src="{{ asset('images/paprika.png') }}" alt="paprika" class="floating-fruit float4">
                <img src="{{ asset('images/jeruk.webp') }}" alt="jeruk" class="floating-fruit float2">
            </div>
        </div>
    </div>

    <script>
        // Toast Notification Function - FIXED VERSION
        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) {
                console.error('Toast container not found!');
                return;
            }
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <div class="toast-close" onclick="this.parentElement.remove()">&times;</div>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 5 seconds (5000ms)
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 400);
            }, 5000);
        }

        // Check for flash messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success messages
            @if (session('success'))
                showToast('Berhasil!', '{{ session('success') }}', 'success');
            @endif
            
            @if (session('status'))
                showToast('Berhasil!', '{{ session('status') }}', 'success');
            @endif
            
            // Check for error messages
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showToast('Gagal!', '{{ $error }}', 'error');
                @endforeach
            @endif
        });

        // Form submission with validation
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const remember = document.getElementById('remember').checked;

            const btn = document.querySelector('.btn-signin');
            const originalText = btn.textContent;

            // Basic validation
            if (!email || !password) {
                showToast('Gagal!', 'Harap isi semua kolom yang wajib diisi', 'error');
                return;
            }

            // Disable button and show loading state
            btn.textContent = 'Sedang masuk...';
            btn.disabled = true;

            try {
                console.log('🚀 Sending login request...');
                const response = await fetch("{{ route('login') }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        remember: remember
                    })
                });

                console.log('📡 Response received:', response.status, response.statusText);
                const data = await response.json();
                console.log('📦 Response data:', data);

                if (response.ok && data.success) {
                    console.log('✅ Login successful! Calling showToast...');
                    showToast('Berhasil!', data.message || 'Login berhasil!', 'success');

                    // Small delay for user to see success message
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    // Handle error response
                    console.log('❌ Login failed! Calling showToast with error...');
                    const errorMessage = data.message || 'Email atau password salah';
                    showToast('Gagal!', errorMessage, 'error');

                    btn.textContent = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('🔥 Login error caught:', error);
                showToast('Kesalahan!', 'Terjadi kesalahan. Silakan coba lagi.', 'error');

                btn.textContent = originalText;
                btn.disabled = false;
            }
        });

        // Alternative: Traditional form submission (uncomment if you prefer this approach)
        /*
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                e.preventDefault();
                showMessage('Please fill in all required fields', 'error');
                return;
            }

            // Let the form submit naturally to the server
            // The server will handle the redirect
        });
        */
    </script>
</body>

</html>
