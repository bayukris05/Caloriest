@auth
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dasbor Nutrisi</title>
        <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
        <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- TAMBAHKAN CHART.JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

        <!-- CSS UNTUK CHART -->
        <style>
            .progress-container {
                background: linear-gradient(145deg, #e8f5e8 0%, #d4e8d4 100%);
                border-radius: 20px;
                padding: 30px;
                margin: 20px 0;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .chart-title {
                text-align: center;
                font-size: 24px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 30px;
            }

            .chart-wrapper {
                background: white;
                border-radius: 15px;
                padding: 25px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                position: relative;
                overflow: hidden;
            }

            .chart-wrapper::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            }

            .chart-container {
                position: relative;
                height: 400px;
                width: 100%;
            }

            .legend-custom {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-top: 20px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: #495057;
            }

            .legend-color {
                width: 16px;
                height: 16px;
                border-radius: 3px;
            }

            .legend-calories {
                background: linear-gradient(45deg, #667eea, #764ba2);
            }

            .legend-target {
                background: linear-gradient(45deg, #ffecd2, #fcb69f);
            }

            .stats-container {
                display: flex;
                justify-content: space-around;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 2px solid #f0f0f0;
                flex-wrap: wrap;
                gap: 15px;
            }

            .stat-item {
                text-align: center;
                padding: 15px;
                background: linear-gradient(145deg, #f8f9fa, #e9ecef);
                border-radius: 12px;
                min-width: 120px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
                flex: 1;
                max-width: 200px;
            }

            .stat-value {
                font-size: 20px;
                font-weight: bold;
                color: #495057;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 12px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .daily-breakdown-container {
                background: linear-gradient(145deg, #e8f5e8 0%, #d4e8d4 100%);
                border-radius: 20px;
                padding: 30px;
                margin: 20px 0;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }

            .daily-breakdown-container .chart-title {
                text-align: center;
                font-size: 24px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 30px;
            }

            .daily-breakdown-container .chart-wrapper {
                background: white;
                border-radius: 15px;
                padding: 25px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                position: relative;
                overflow: hidden;
            }

            .daily-breakdown-container .chart-wrapper::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            }

            .daily-breakdown-container .chart-container {
                position: relative;
                height: 400px;
                width: 100%;
            }

            .daily-breakdown-container .legend-custom {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-top: 20px;
            }

            .daily-breakdown-container .legend-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: #495057;
            }

            .daily-breakdown-container .legend-color {
                width: 16px;
                height: 16px;
                border-radius: 3px;
            }

            .daily-breakdown-container .legend-calories {
                background: linear-gradient(45deg, #8b5cf6, #a855f7);
            }

            .daily-breakdown-container .legend-target {
                background: linear-gradient(45deg, #fb7185, #f43f5e);
            }

            .daily-breakdown-container .stats-container {
                display: flex;
                justify-content: space-around;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 2px solid #f0f0f0;
                flex-wrap: wrap;
                gap: 15px;
            }

            .daily-breakdown-container .stat-item {
                text-align: center;
                padding: 15px;
                background: linear-gradient(145deg, #f8f9fa, #e9ecef);
                border-radius: 12px;
                min-width: 120px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
                flex: 1;
                max-width: 200px;
            }

            .daily-breakdown-container .stat-value {
                font-size: 20px;
                font-weight: bold;
                color: #495057;
                margin-bottom: 5px;
            }

            .daily-breakdown-container .stat-label {
                font-size: 12px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            @media (max-width: 768px) {
                .progress-container {
                    padding: 15px;
                    margin: 10px 0;
                }

                .chart-title {
                    font-size: 20px;
                }

                .stats-container {
                    flex-direction: column;
                }

                .legend-custom {
                    flex-wrap: wrap;
                    gap: 15px;
                }

                .daily-breakdown-container {
                    padding: 15px;
                    margin: 10px 0;
                }

                .daily-breakdown-container .chart-title {
                    font-size: 20px;
                }

                .daily-breakdown-container .stats-container {
                    flex-direction: column;
                }

                .daily-breakdown-container .legend-custom {
                    flex-wrap: wrap;
                    gap: 15px;
                }
            }

            /* Profile Completion Overlay & Blur */
            .blurred-content {
                filter: blur(8px);
                pointer-events: none;
                user-select: none;
                overflow: hidden;
            }

            .profile-completion-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 900;
                display: flex;
                justify-content: center;
                align-items: center;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(2px);
            }

            .overlay-card {
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

            .overlay-title {
                font-size: 24px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 15px;
            }

            .overlay-text {
                color: #64748b;
                margin-bottom: 30px;
                line-height: 1.6;
                font-size: 16px;
            }

            .overlay-btn {
                display: inline-block;
                background: linear-gradient(135deg, #7cb342 0%, #8bc34a 100%);
                color: white;
                padding: 14px 35px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(124, 180, 66, 0.4);
            }

            .overlay-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(124, 180, 66, 0.5);
            }
        </style>
    </head>

    <body>
        @include('components.navbar')
        <div class="container {{ isset($isProfileComplete) && !$isProfileComplete ? 'blurred-content' : '' }}">
            <div class="main-content">
                <!-- Header Card -->
                <div class="header-card">
                    <div class="header-date">{{ $currentDateTime }}</div>
                    <div class="header-title">{{ $greeting }}<br>{{ Auth::user()->name }}</div>
                    <div class="header-subtitle">Semoga harimu menyenangkan di hari {{ $currentDay }}!</div>
                    <div class="header-illustration">
                    </div>
                </div>
                <div class="scrollable-content">
                    <!-- Today's Status -->
                    <div class="status-card">
                        <div class="status-header">
                            <div class="status-icon">
                                <img src="{{ asset('images/icon/clipboard.png') }}" alt="">
                            </div>
                            Status hari ini
                        </div>
                        <div class="calorie-number">{{ number_format($calorieConsumed, 0, ',', '.') }}</div>
                        @if ($calorieNeeds)
                            <div class="calorie-subtitle">dari {{ number_format($calorieNeeds, 0, ',', '.') }} kkal per hari
                            </div>
                        @else
                            <div class="calorie-subtitle">Data tidak lengkap untuk menghitung kalori.</div>
                        @endif
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $caloriePercentage }}%"></div>
                        </div>
                        <div class="calories-info">
                            <div class="calories-label">Kurang kalori</div>
                            <div class="calories-value">Kurang {{ $lessCalorie }} kkal</div>
                        </div>
                        <button class="food-recommendations-btn" onclick="window.location.href='/recomend'">Rekomendasi Makanan</button>
                    </div>

                    <!-- Results Assessment -->
                    <div class="results-card">
                        <div class="results-title">Penilaian hasil</div>
                        <div class="bmr-info">
                            <div class="bmr-label">Laju Metabolisme Basal (BMR)</div>
                            <div class="bmr-value">{{ number_format($bmr, 0, ',', '.') }} kkal/hari</div>
                        </div>

                        <div class="bmr-info">
                            <div class="bmr-label">Total Pengeluaran Energi Harian</div>
                            <div class="bmr-value">{{ number_format($tdee, 0, ',', '.') }} kkal/hari</div>
                        </div>

                        <div class="bmr-info">
                            <div class="bmr-label">Indeks Massa Tubuh (BMI) </div>
                            <div class="bmr-value">{{ number_format($bmi, 1, ',', '.') }}</div>
                        </div>
                        <div class="last-title">Berdasarkan Rumus Harris-Benedict dan tingkat aktivitas Anda</div>
                    </div>

                    {{-- GRAFIK - DIPERBAIKI --}}
                    <div class="progress-container">
                        <h2 class="chart-title">Kemajuan Mingguan</h2>

                        <div class="chart-wrapper">
                            <div class="chart-container">
                                <canvas id="progressChart"></canvas>
                            </div>

                            <div class="legend-custom">
                                <div class="legend-item">
                                    <div class="legend-color legend-calories"></div>
                                    <span>Kalori</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color legend-target"></div>
                                    <span>Target</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- dayly breakdown --}}
                    <div class="daily-breakdown-container">
                        <h2 class="chart-title">Rincian Harian</h2>

                        <div class="chart-wrapper">
                            <div class="chart-container">
                                <canvas id="dailyBreakdownChart"></canvas>
                            </div>

                            <div class="legend-custom">
                                <div class="legend-item">
                                    <div class="legend-color legend-calories"></div>
                                    <span>Kalori</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color legend-target"></div>
                                    <span>Target</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Profile --}}
            <div class="sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-title">PROFIL SAYA</div>
                        <div class="profile-icon">

                        </div>
                    </div>
                    <div class="profile-avatar">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="avatar-placeholder" style="background-color: {{ $user->avatar_color }}">
                                {{ $user->initials }}
                            </div>
                        @endif
                    </div>
                    <div class="profile-name">{{ Auth::user()->name }}</div>
                    {{-- <div class="profile-status">Maintain Weight</div> --}}
                    <div class="profile-stats">
                        <span>Usia sekarang</span>
                        <span>Berat sekarang</span>
                        <span>Tinggi sekarang</span>
                    </div>
                    <div class="profile-values">
                        <span>{{ $user->usia }} tahun</span>
                        <span>{{ $user->bb }} kg</span>
                        <span>{{ $user->tb }} cm</span>
                    </div>
                </div>

                <!-- Calendar Card -->
                <div class="calendar-card">
                    <div class="calendar-header">
                        <div class="calendar-title">KALENDER SAYA</div>
                        <div class="calendar-month" id="current-month">June</div>
                    </div>
                    <div class="calendar-grid" id="calendar-grid">
                        <!-- Hari akan diisi oleh JavaScript -->
                    </div>
                    <div class="schedule-info">
                        <div class="schedule-date" id="current-date">17 June 2025</div>
                        <div class="schedule-items" id="schedule-items">
                            <!-- Jadwal akan diisi oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($isProfileComplete) && !$isProfileComplete)
        <div class="profile-completion-overlay">
            <div class="overlay-card">
                <div style="font-size: 48px; margin-bottom: 20px; color: #7cb342;">
                    <i class="bi bi-postcard-heart"></i>
                </div>
                <div class="overlay-title">Profil Belum Lengkap</div>
                <p class="overlay-text">Halo! Untuk mendapatkan analisis nutrisi yang akurat dan personal, mohon lengkapi data profil Anda terlebih dahulu.</p>
                <a href="{{ route('profile.edit') }}" class="overlay-btn">
                    <i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Lengkapi Profil Sekarang
                </a>
            </div>
        </div>
        @endif

        <script>
            // Tunggu sampai DOM dan Chart.js ready
            document.addEventListener('DOMContentLoaded', function() {
                // Pastikan Chart.js sudah loaded
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js belum dimuat!');
                    return;
                }

                const chartData = {
                    labels: ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAT'],
                    datasets: [{
                        label: 'Kalori',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(102, 126, 234, 0.3)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }, {
                        label: 'Target',
                        data: {!! json_encode($targetData) !!},
                        backgroundColor: 'rgba(252, 182, 159, 0.2)',
                        borderColor: 'rgba(252, 182, 159, 1)',
                        borderWidth: 2,
                        fill: false,
                        borderDash: [5, 5],
                        pointBackgroundColor: 'rgba(252, 182, 159, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                };

                // Konfigurasi chart
                const config = {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0,
                                max: 3000,
                                ticks: {
                                    stepSize: 500,
                                    color: '#6c757d',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        elements: {
                            point: {
                                hoverBackgroundColor: '#fff',
                                hoverBorderWidth: 3
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        }
                    }
                };

                // Pastikan canvas ada
                const canvas = document.getElementById('progressChart');
                if (!canvas) {
                    console.error('Canvas dengan ID progressChart tidak ditemukan!');
                    return;
                }

                // Membuat chart
                const ctx = canvas.getContext('2d');
                const progressChart = new Chart(ctx, config);

                // Fungsi untuk update statistik
                function updateStats() {
                    const caloriesData = chartData.datasets[0].data;
                    const targetData = chartData.datasets[1].data[0];

                    const avg = Math.round(caloriesData.reduce((a, b) => a + b, 0) / caloriesData.length);
                    const max = Math.max(...caloriesData);
                    const min = Math.min(...caloriesData);
                    const achieved = Math.round((caloriesData.filter(val => val >= targetData).length / caloriesData
                        .length) * 100);

                    // Update statistik jika element ada
                    const avgElement = document.getElementById('avgCalories');
                    const maxElement = document.getElementById('maxCalories');
                    const minElement = document.getElementById('minCalories');
                    const achievedElement = document.getElementById('targetAchieved');

                    if (avgElement) avgElement.textContent = avg.toLocaleString();
                    if (maxElement) maxElement.textContent = max.toLocaleString();
                    if (minElement) minElement.textContent = min.toLocaleString();
                    if (achievedElement) achievedElement.textContent = achieved + '%';
                }

                // Update statistik saat halaman dimuat
                updateStats();

                // Fungsi untuk update data (bisa dipanggil dari Laravel)
                window.updateChartData = function(newData) {
                    chartData.datasets[0].data = newData;
                    progressChart.update();
                    updateStats();
                }

                console.log('Chart berhasil dimuat!');
            });
        </script>
        {{-- script untuk daily breakdown --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data untuk daily breakdown chart
                const dailyChartData = {
                    labels: ['Sarapan', 'Makan Siang', 'Makan Malam', 'Camilan'],
                    datasets: [{
                        label: 'Kalori',
                        data: [500, 800, 700, 300],
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Target',
                        data: [600, 800, 800, 200],
                        backgroundColor: 'rgba(251, 113, 133, 0.7)',
                        borderColor: 'rgba(251, 113, 133, 1)',
                        borderWidth: 1
                    }]
                };

                const dailyConfig = {
                    type: 'bar',
                    data: dailyChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + ' kkal';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                min: 0,
                                max: 1000,
                                ticks: {
                                    stepSize: 200,
                                    color: '#6c757d',
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        }
                    }
                };

                // Pastikan canvas ada
                const dailyCanvas = document.getElementById('dailyBreakdownChart');
                if (!dailyCanvas) {
                    console.error('Canvas dengan ID dailyBreakdownChart tidak ditemukan!');
                    return;
                }

                // Membuat Daily Breakdown chart
                const dailyCtx = dailyCanvas.getContext('2d');
                const dailyBreakdownChart = new Chart(dailyCtx, dailyConfig);

                // Fungsi untuk update statistik Daily Breakdown
                function updateDailyStats() {
                    const caloriesData = dailyChartData.datasets[0].data;
                    const targetData = dailyChartData.datasets[1].data;

                    const totalCalories = caloriesData.reduce((a, b) => a + b, 0);
                    const totalTarget = targetData.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((totalCalories / totalTarget) * 100);

                    // Update statistik jika element ada
                    const totalElement = document.getElementById('dailyTotalCalories');
                    const targetElement = document.getElementById('dailyTotalTarget');
                    const percentageElement = document.getElementById('dailyPercentage');

                    if (totalElement) totalElement.textContent = totalCalories.toLocaleString();
                    if (targetElement) targetElement.textContent = totalTarget.toLocaleString();
                    if (percentageElement) percentageElement.textContent = percentage + '%';
                }

                // Update statistik saat halaman dimuat
                updateDailyStats();

                // Fungsi untuk update data Daily Breakdown (bisa dipanggil dari Laravel)
                window.updateDailyChartData = function(newData) {
                    dailyChartData.datasets[0].data = newData;
                    dailyBreakdownChart.update();
                    updateDailyStats();
                }

                console.log('Daily Breakdown Chart berhasil dimuat!');
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date();
                const day = today.getDate();
                const month = today.getMonth();
                const year = today.getFullYear();
                const daysOfWeek = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ];

                // Set bulan saat ini
                document.getElementById('current-month').textContent = months[month];

                // Set tanggal saat ini
                document.getElementById('current-date').textContent = `${day} ${months[month]} ${year}`;

                // Buat grid kalender (hanya 5 hari seperti contoh Anda)
                const calendarGrid = document.getElementById('calendar-grid');

                // Tambahkan header hari (hanya 5 hari)
                for (let i = 0; i < 5; i++) {
                    const dayHeader = document.createElement('div');
                    dayHeader.className = 'calendar-day-header';
                    dayHeader.textContent = daysOfWeek[i];
                    calendarGrid.appendChild(dayHeader);
                }

                // Cari tanggal awal untuk menampilkan 5 hari berurutan
                // Kita akan menampilkan 5 hari mulai dari hari ini atau beberapa hari sebelumnya
                // agar hari ini tetap terlihat
                let startDate = new Date(today);
                startDate.setDate(day - today.getDay()); // Mulai dari Minggu minggu ini

                // Sesuaikan jika kita hanya ingin menampilkan 5 hari (Senin-Jumat)
                if (today.getDay() >= 1 && today.getDay() <= 5) {
                    startDate = new Date(today);
                    startDate.setDate(day - (today.getDay() - 1)); // Mulai dari Senin minggu ini
                } else if (today.getDay() === 6) {
                    // Jika hari Sabtu, tampilkan Senin-Jumat minggu depan
                    startDate = new Date(today);
                    startDate.setDate(day + 2);
                } else {
                    // Jika hari Minggu, tampilkan Senin-Jumat minggu ini
                    startDate = new Date(today);
                    startDate.setDate(day + 1);
                }

                // Tambahkan 5 hari berurutan
                for (let i = 0; i < 5; i++) {
                    const currentDate = new Date(startDate);
                    currentDate.setDate(startDate.getDate() + i);

                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';

                    // Tandai hari ini sebagai active
                    if (currentDate.getDate() === day && currentDate.getMonth() === month && currentDate
                        .getFullYear() === year) {
                        dayElement.classList.add('active');
                    }

                    dayElement.textContent = currentDate.getDate();
                    calendarGrid.appendChild(dayElement);
                }

                // Tampilkan jadwal makan dari controller
                const scheduleItems = document.getElementById('schedule-items');
                const mealPlan = @json($mealPlan ?? []);

                if (mealPlan.length > 0) {
                    mealPlan.forEach(item => {
                        const scheduleItem = document.createElement('div');
                        scheduleItem.className = 'schedule-item';
                        scheduleItem.innerHTML = `
                            <span>${item.time}</span>
                            <span>${item.activity}</span>
                        `;
                        scheduleItems.appendChild(scheduleItem);
                    });
                } else {
                    const emptyItem = document.createElement('div');
                    emptyItem.className = 'schedule-item';
                    emptyItem.innerHTML = '<span>-</span><span>Belum ada jadwal makan</span>';
                    scheduleItems.appendChild(emptyItem);
                }
            });
        </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollContainer = document.querySelector('.scrollable-content');
            const navbar = document.querySelector('.navbar-custom');

            if (scrollContainer && navbar) {
                scrollContainer.addEventListener('scroll', function() {
                    if (scrollContainer.scrollTop > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                });
            }
        });
    </script>
    </body>

    </html>
@else
    <script>
        window.location = "/login";
    </script>
@endguest
