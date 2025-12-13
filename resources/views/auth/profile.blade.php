<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ time() }}">
</head>

<body>
    @include('components.navbar')
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    <div class="profile-container">
        <div class="profile-section">
            <h1 class="section-title">Edit Profil</h1>

            @if ($loading)
                <div class="loading">Memuat data profil...</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            <div id="profileContent" @if ($loading) style="display: none;" @endif>
                <div class="profile-photo">
                    <div class="photo-circle" id="profilePhotoDisplay" 
                         @if(!$user->avatar_url) style="background: {{ $user->avatar_color }};" @endif>
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}?v={{ time() }}" alt="Foto Profil">
                        @else
                            <span id="photoInitial">{{ $user->initials }}</span>
                        @endif
                    </div>
                    <div class="upload-container">
                        <form action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data"
                            id="photoUploadForm">
                            @csrf
                            <div class="upload-btn">
                                Unggah foto baru
                                <input type="file" id="photoUpload" name="photo"
                                    accept="image/jpeg,image/png,image/jpg"
                                    onchange="document.getElementById('photoUploadForm').submit()">
                            </div>
                        </form>
                        <div class="photo-info">Minimal 800 x 800 disarankan<br>JPG atau PNG diperbolehkan</div>
                    </div>
                </div>

                <div class="personal-info-header">
                    <h2 style="font-size: 18px; color: #333; font-weight: 600; margin: 0;">Informasi Pribadi</h2>
                    <button type="button" class="edit-toggle-btn" id="editToggleBtn" onclick="toggleAllEdit()">
                        <span class="edit-icon">✎</span>
                        <span id="editBtnText">Edit</span>
                    </button>
                </div>

                <form class="form-grid" id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input editable-field" id="name" name="name"
                            value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap Anda" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tinggi (cm)</label>
                        <input type="number" class="form-input editable-field" id="tb" name="tb"
                            value="{{ old('tb', $user->tb) }}" placeholder="Masukkan tinggi dalam cm" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input editable-field" id="email" name="email"
                            value="{{ old('email', $user->email) }}" placeholder="Masukkan email Anda" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" class="form-input editable-field" id="bb" name="bb"
                            value="{{ old('bb', $user->bb) }}" placeholder="Masukkan berat dalam kg" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Usia</label>
                        <input type="number" class="form-input editable-field" id="usia" name="usia"
                            value="{{ old('usia', $user->usia) }}" placeholder="Masukkan usia Anda" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Sandi</label>
                        <div class="password-container">
                            <input type="password" class="form-input editable-field" id="password" name="password"
                                value="********" placeholder="********" readonly
                                data-has-password="{{ $user->password ? 'true' : 'false' }}">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                    <div class="form-group">
                        <label class="form-label">Tingkat Aktivitas</label>
                        <select class="form-input editable-field" id="aktivitas" name="aktivitas" disabled>
                            <option value="">Pilih tingkat aktivitas</option>
                            <option value="Sedentary"
                                {{ old('aktivitas', $user->aktivitas) == 'Sedentary' ? 'selected' : '' }}>Sedenter
                            </option>
                            <option value="Lightly Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Lightly Active' ? 'selected' : '' }}>Aktif Ringan</option>
                            <option value="Moderately Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Moderately Active' ? 'selected' : '' }}>
                                Cukup Aktif</option>
                            <option value="Very Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Very Active' ? 'selected' : '' }}>Sangat Aktif
                            </option>
                            <option value="Extra Active"
                                {{ old('aktivitas', $user->aktivitas) == 'Extra Active' ? 'selected' : '' }}>
                                Ekstra Aktif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-input editable-field" id="jenis_kelamin" name="jenis_kelamin" disabled>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>

                    <button class="save-btn" id="saveBtn" type="submit" disabled>Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="progress-section">
            <h2 class="section-title">Lengkapi profil Anda</h2>

            <div class="progress-circle">
                <div class="circle-progress" id="progressCircle">
                    <span class="progress-text" id="progressText">{{ $completionPercentage }}%</span>
                </div>
            </div>

            <ul class="checklist" id="progressChecklist">
                <li class="checklist-item">
                    <span class="check-icon {{ $user->email ? '' : 'incomplete' }}" id="check-account">✓</span>
                    <span>Pengaturan akun</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->avatar ? '' : 'incomplete' }}" id="check-photo">✓</span>
                    <span>Unggah foto</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->aktivitas ? '' : 'incomplete' }}"
                        id="check-assessment">✓</span>
                    <span>Penilaian selesai</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $user->name && $user->usia ? '' : 'incomplete' }}"
                        id="check-info">✓</span>
                    <span>Informasi pribadi</span>
                </li>
                <li class="checklist-item">
                    <span class="check-icon {{ $hasForumActivity ? '' : 'incomplete' }}" id="check-forum">✓</span>
                    <span>Bergabung di forum</span>
                </li>
            </ul>
        </div>

    <script>
        let isEditMode = false;

        function toggleAllEdit() {
            const editableFields = document.querySelectorAll('.editable-field:not(#password)');
            const editBtn = document.getElementById('editToggleBtn');
            const editBtnText = document.getElementById('editBtnText');
            const saveBtn = document.getElementById('saveBtn');

            isEditMode = !isEditMode;

            editableFields.forEach(field => {
                if (isEditMode) {
                    if (field.tagName === 'SELECT') {
                        field.disabled = false;
                    } else {
                        field.removeAttribute('readonly');
                    }
                    field.style.background = 'white';
                    field.style.borderColor = '#e9ecef';
                } else {
                    if (field.tagName === 'SELECT') {
                        field.disabled = true;
                    } else {
                        field.setAttribute('readonly', true);
                    }
                    field.style.background = '#f8f9fa';
                    field.style.borderColor = '#e9ecef';
                }
            });

            const passwordField = document.getElementById('password');
            if (isEditMode) {
                passwordField.value = '';
                passwordField.placeholder = 'Masukkan password baru';
                passwordField.removeAttribute('readonly');
            } else {
                passwordField.value = '********';
                passwordField.setAttribute('readonly', true);
            }

            if (isEditMode) {
                editBtnText.textContent = 'Selesai';
                editBtn.style.color = '#667eea';
                saveBtn.disabled = false;
            } else {
                editBtnText.textContent = 'Edit';
                editBtn.style.color = '#999';
                saveBtn.disabled = true;
            }
        }

        // Initialize progress circle
        document.addEventListener('DOMContentLoaded', function() {
            const percentage = {{ $completionPercentage }};
            const circle = document.getElementById('progressCircle');
            circle.style.background =
                `conic-gradient(#7cb342 0deg ${percentage * 3.6}deg, #e9ecef ${percentage * 3.6}deg 360deg)`;
        });

        function togglePasswordVisibility(button) {
            const container = button.closest('.password-container');
            const input = container.querySelector('input');

            if (input.type === 'password') {
                input.type = 'text';
                input.value = 'Password tersimpan (terenkripsi)';
                button.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                input.type = 'password';
                input.value = '********';
                button.innerHTML = '<i class="bi bi-eye"></i>';
            }
        }

        // Toast Notification Function
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
    </script>

    <style>
        /* Toast Notification Styles */
        .toast-container {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 99999 !important;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast {
            background: white;
            border-left: 4px solid #4CAF50;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            min-width: 320px;
            max-width: 400px;
            pointer-events: auto;
            position: relative;
            overflow: hidden;
            animation: toastSlideIn 0.4s ease-out forwards;
            transition: all 0.3s ease;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast-content {
            flex: 1;
            margin-right: 12px;
        }

        .toast-title {
            font-weight: 700;
            font-size: 15px;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        .toast-close {
            color: #9ca3af;
            cursor: pointer;
            font-size: 20px;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
            line-height: 1;
        }

        .toast-close:hover {
            background: #f3f4f6;
            color: #4b5563;
        }

        .toast.hiding {
            animation: toastSlideOut 0.4s ease-in forwards;
        }

        @keyframes toastSlideIn {
            0% {
                transform: translateX(100%);
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
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Use existing showToast function from global scripts
            showToast('Berhasil!', '{{ session('success') }}', 'success');
        });
    </script>
    @endif
</body>

</html>
