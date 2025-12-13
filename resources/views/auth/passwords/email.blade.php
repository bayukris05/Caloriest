<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Caloriest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        /* Toast Notification Styles - FIXED */
        .toast-container { position: fixed !important; top: 20px !important; right: 20px !important; z-index: 99999 !important; display: flex !important; flex-direction: column !important; gap: 10px !important; pointer-events: none; }
        .toast { background: white !important; border-left: 4px solid #4caf50 !important; padding: 15px 25px !important; border-radius: 8px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.25) !important; display: flex !important; align-items: center !important; min-width: 320px !important; max-width: 400px !important; opacity: 1 !important; transform: translateX(0) !important; animation: toastSlideIn 0.4s ease-out !important; pointer-events: auto; }
        .toast.error { border-left-color: #f44336 !important; }
        .toast.hiding { animation: toastSlideOut 0.4s ease-in forwards !important; }
        .toast-content { margin-right: 15px; flex: 1; }
        .toast-title { font-weight: bold; font-size: 14px; margin-bottom: 4px; color: #333; }
        .toast-message { font-size: 13px; color: #666; line-height: 1.4; }
        .toast-close { margin-left: auto; cursor: pointer; color: #999; font-size: 20px; padding: 0 5px; pointer-events: auto; }
        .toast-close:hover { color: #333; }
        @keyframes toastSlideIn { 0% { transform: translateX(120%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes toastSlideOut { 0% { transform: translateX(0); opacity: 1; } 100% { transform: translateX(120%); opacity: 0; } }
    </style>
</head>

<body>
    <!-- Toast Container - HARUS di level body -->
    <div class="toast-container" id="toastContainer"></div>
    
    <div class="login-container">
        <div class="left-section">
            <div class="sign-in-form">
                <h1 class="sign-in-header">Reset Kata Sandi</h1>
                <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                    Masukkan email Anda dan kami akan mengirimkan link untuk mereset kata sandi.
                </p>

                <form method="POST" action="{{ route('password.email') }}" id="resetForm">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan alamat email Anda">
                    </div>

                    <button type="submit" class="btn-signin">Kirim Link Reset</button>
                    
                    <div class="signup-link" style="margin-top: 20px;">
                        Kembali ke <a href="{{ route('login') }}">Halaman Login</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="right-section">
            <div class="image-container">
                 <img src="{{ asset('images/bg-kanan.png') }}" alt="Healthy Nutrition" class="food-image">
                 <img src="{{ asset('images/plate.png') }}" alt="Healthy Nutrition" class="food-plate">
            </div>
        </div>
    </div>

    <script>
        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
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
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 400);
            }, 5000);
        }

        // Execute on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showToast('Gagal!', '{{ $error }}', 'error');
                @endforeach
            @endif
            
            @if (session('status'))
                showToast('Berhasil!', '{{ session('status') }}', 'success');
            @endif
        });
    </script>
</body>
</html>
