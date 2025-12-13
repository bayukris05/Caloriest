@include('components.head')

@include('components.navbar')

<style>
    /* Profile Completion Modal Overlay */
    .profile-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
    }

    .profile-modal-overlay.active {
        display: flex;
    }

    .profile-modal-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        text-align: center;
        max-width: 450px;
        width: 90%;
        animation: slideUp 0.5s ease-out;
        border: 1px solid rgba(0,0,0,0.05);
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .profile-modal-icon {
        font-size: 48px;
        margin-bottom: 20px;
        color: #7cb342;
    }

    .profile-modal-title {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .profile-modal-text {
        color: #64748b;
        margin-bottom: 30px;
        line-height: 1.6;
        font-size: 16px;
    }

    .profile-modal-btn {
        display: inline-block;
        background: linear-gradient(135deg, #7cb342 0%, #8bc34a 100%);
        color: white;
        padding: 14px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(124, 180, 66, 0.4);
        border: none;
        cursor: pointer;
    }

    .profile-modal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 180, 66, 0.5);
        color: white;
    }
</style>

<div class="main-container">
    <!-- Left Column - Food Image -->
    <div class="image-column">
        <img class="food-image-bottom" src="{{ asset('images/image-bg-calc.png') }}" alt="bg-kubis" width="700"
            height="700">
        <img class="food-image-top" src="{{ asset('images/image-left-calc.png') }}" alt="piring-buah" width="400"
            height="400">
    </div>

    <!-- Right Column - Calculator Form -->
    <div class="form-column">
        <div class="calculator-title">
            <h1>Hitung BMR & TDEE Kamu</h1>
        </div>

        <form>
            <!-- Gender Selection -->
            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="gender-options">
                    <div class="gender-option">
                        <input type="radio" id="male" name="gender" value="male">
                        <label for="male">
                            <img src="{{ asset('images/male.png') }}" alt="male" width="30px" height="30px">
                            <p>Pria</p>
                        </label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female">
                            <img src="{{ asset('images/female.png') }}" alt="female" width="30px" height="30px">
                            <p>Wanita</p>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Age Input -->
            <div class="form-group">
                <label for="age" class="form-label">Usia</label>
                <input type="number" id="age" class="form-control" placeholder="Masukkan usia Anda" min="1"
                    max="120">
                <div class="form-text">tahun</div>
            </div>

            <!-- Weight Input -->
            <div class="form-group">
                <label for="weight" class="form-label">Berat Badan</label>
                <input type="number" id="weight" class="form-control" placeholder="Masukkan berat badan Anda" min="1"
                    step="0.1">
                <div class="form-text">Kg</div>
            </div>

            <!-- Height Input -->
            <div class="form-group">
                <label for="height" class="form-label">Tinggi Badan</label>
                <input type="number" id="height" class="form-control" placeholder="Masukkan tinggi badan Anda" min="1"
                    step="0.1">
                <div class="form-text">Cm</div>
            </div>

            <!-- Activity Level -->
            <div class="form-group">
                <label for="activity" class="form-label">Tingkat Aktivitas</label>
                <select id="activity" class="form-control">
                    <option value="" disabled selected>Pilih Tingkat Aktivitas</option>
                    <option value="1.2">Tidak aktif (Sedentary)</option>
                    <option value="1.375">Ringan (Lightly active)</option>
                    <option value="1.55">Cukup aktif (Moderately active)</option>
                    <option value="1.725">Sangat aktif (Very active)</option>
                    <option value="1.9">Ekstra aktif (Extra active)</option>
                </select>
            </div>

            <button type="button" class="btn-calculate" id="calculateBtn">Hitung</button>
        </form>
        <button type="button" class="btn-monitor" id="monitorBtn">Monitor Kalori</button>
    </div>
</div>

<!-- Profile Completion Modal -->
<div id="profileModal" class="profile-modal-overlay">
    <div class="profile-modal-card">
        <div class="profile-modal-icon">
            📋
        </div>
        <div class="profile-modal-title">Profil Belum Lengkap</div>
        <p class="profile-modal-text">Untuk menggunakan Monitor Kalori, mohon lengkapi data profil Anda terlebih dahulu (usia, berat badan, tinggi badan, jenis kelamin, dan tingkat aktivitas).</p>
        <a href="{{ route('profile.edit') }}" class="profile-modal-btn">
            ✏️ Lengkapi Profil Sekarang
        </a>
    </div>
</div>

<div id="calculationModal" class="calculation-modal">
    <div class="modal-content">
        {{-- Character --}}
        <div class="chef-container">
            <div class="chef-avatar">
                <img src="/images/chef_p.png" alt="">
            </div>
        </div>

        {{-- Title --}}
        <h3 class="modal-title">Berikut hasil perhitungannya</h3>

        {{-- Results --}}
        <div class="results-container">
            <div class="result-row">
                <span class="result-label">Laju Metabolisme Basal (BMR)</span>
                <span class="result-value" id="bmrResult">1,514 kkal/hari</span>
            </div>
            <div class="result-row">
                <span class="result-label">Total Pengeluaran Energi Harian (TDEE)</span>
                <span class="result-value" id="tdeeResult">2,347 kkal/hari</span>
            </div>
            <div class="result-row">
                <span class="result-label">Indeks Massa Tubuh (BMI)</span>
                <span class="result-value" id="bmiResult">22.0</span>
            </div>
        </div>

        {{-- OK Button --}}
        <button onclick="closeModal()" class="ok-btn">OK</button>
    </div>
</div>

<script>
    // Profile check - from Laravel
    const isProfileComplete = {{ Auth::check() && Auth::user()->usia && Auth::user()->bb && Auth::user()->tb && Auth::user()->jenis_kelamin && Auth::user()->aktivitas ? 'true' : 'false' }};

    function calculateBMR(weight, height, age, gender) {
        if (gender === 'male') {
            return Math.round(88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age));
        } else {
            return Math.round(447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age));
        }
    }

    function calculateTDEE(bmr, activityMultiplier) {
        return Math.round(bmr * activityMultiplier);
    }

    function calculateBMI(weight, height) {
        const heightInMeters = height / 100;
        return Math.round((weight / (heightInMeters * heightInMeters)) * 10) / 10;
    }

    function showModal(bmr, tdee, bmi) {
        document.getElementById('bmrResult').textContent = bmr.toLocaleString('id-ID') + ' kkal/hari';
        document.getElementById('tdeeResult').textContent = tdee.toLocaleString('id-ID') + ' kkal/hari';
        document.getElementById('bmiResult').textContent = bmi;

        const modal = document.getElementById('calculationModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('calculationModal');
        modal.classList.remove('active');

        setTimeout(() => {
            document.body.style.overflow = 'auto';
        }, 300);
    }

    function showProfileModal() {
        const modal = document.getElementById('profileModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeProfileModal() {
        const modal = document.getElementById('profileModal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function validateInputs() {
        const gender = document.querySelector('input[name="gender"]:checked');
        const age = document.getElementById('age').value;
        const weight = document.getElementById('weight').value;
        const height = document.getElementById('height').value;
        const activity = document.getElementById('activity').value;

        if (!gender) {
            alert('Pilih jenis kelamin terlebih dahulu!');
            return false;
        }

        if (!age || age <= 0 || age > 120) {
            alert('Masukkan umur yang valid (1-120 tahun)!');
            document.getElementById('age').focus();
            return false;
        }

        if (!weight || weight <= 0) {
            alert('Masukkan berat badan yang valid!');
            document.getElementById('weight').focus();
            return false;
        }

        if (!height || height <= 0) {
            alert('Masukkan tinggi badan yang valid!');
            document.getElementById('height').focus();
            return false;
        }

        if (!activity) {
            alert('Pilih tingkat aktivitas!');
            document.getElementById('activity').focus();
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Calculate Button
        const calculateBtn = document.getElementById('calculateBtn');
        if (calculateBtn) {
            calculateBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (!validateInputs()) {
                    return;
                }

                const gender = document.querySelector('input[name="gender"]:checked').value;
                const age = parseInt(document.getElementById('age').value);
                const weight = parseFloat(document.getElementById('weight').value);
                const height = parseFloat(document.getElementById('height').value);
                const activityLevel = document.getElementById('activity').value;

                const activityMapping = {
                    '1.2': 1.2,
                    '1.375': 1.375,
                    '1.55': 1.55,
                    '1.725': 1.725,
                    '1.9': 1.9
                };

                const bmi = calculateBMI(weight, height);
                const bmr = calculateBMR(weight, height, age, gender);
                const activityMultiplier = activityMapping[activityLevel] || 1.2;
                const tdee = calculateTDEE(bmr, activityMultiplier);

                showModal(bmr, tdee, bmi);
            });
        }

        // Monitor Button - check profile first
        const monitorBtn = document.getElementById('monitorBtn');
        if (monitorBtn) {
            monitorBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (isProfileComplete) {
                    // Profile complete, redirect to monitor
                    window.location.href = '{{ route("calculate.monitor") }}';
                } else {
                    // Profile incomplete, show modal
                    showProfileModal();
                }
            });
        }

        // Close modals on click outside
        const calculationModal = document.getElementById('calculationModal');
        if (calculationModal) {
            calculationModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        const profileModal = document.getElementById('profileModal');
        if (profileModal) {
            profileModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeProfileModal();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const calcModal = document.getElementById('calculationModal');
                if (calcModal && calcModal.classList.contains('active')) {
                    closeModal();
                }
                const profModal = document.getElementById('profileModal');
                if (profModal && profModal.classList.contains('active')) {
                    closeProfileModal();
                }
            }
        });
    });
</script>

<script>
    document.getElementById('calculateBtn').addEventListener('click', function() {
        const gender = document.querySelector('input[name="gender"]:checked')?.value;
        const usia = document.getElementById('age').value;
        const bb = document.getElementById('weight').value;
        const tb = document.getElementById('height').value;
        const aktivitas = document.getElementById('activity').selectedOptions[0]?.text;

        if (!gender || !usia || !bb || !tb || !aktivitas) {
            alert('Harap isi semua field');
            return;
        }

        fetch('{{ route('calc.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    jenis_kelamin: gender === 'male' ? 'L' : 'P',
                    usia: parseInt(usia),
                    bb: parseFloat(bb),
                    tb: parseFloat(tb),
                    aktivitas: aktivitas,
                    image_path: ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data berhasil disimpan');
                } else {
                    alert('Gagal menyimpan data');
                }
            })
    });
</script>
