@auth
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Menu Makanan</title>
        <link rel="stylesheet" href="{{ asset('css/recomend.css') }}">
        <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
        <style>
            .main-container {
                background: transparent !important;
            }

            .food-cards-container {
                background: transparent !important;
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2em;
                color: #666;
                z-index: 10;
            }

            .section {
                position: relative;
            }

            /* Toast Notification Styles */
            .toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .toast {
                background: white;
                border-left: 4px solid #4caf50;
                padding: 15px 25px;
                border-radius: 4px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                animation: slideIn 0.3s ease-out forwards;
                min-width: 300px;
            }

            .toast.error {
                border-left-color: #f44336;
            }

            .toast-content {
                margin-right: 15px;
            }

            .toast-title {
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 2px;
            }

            .toast-message {
                font-size: 13px;
                color: #666;
            }

            .toast-close {
                margin-left: auto;
                cursor: pointer;
                color: #999;
                font-size: 18px;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }

            /* Confirmation Modal Styles */
            .confirm-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .confirm-modal-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .confirm-modal {
                background: white;
                padding: 25px;
                border-radius: 12px;
                width: 90%;
                max-width: 400px;
                transform: scale(0.9);
                transition: transform 0.3s ease;
                text-align: center;
            }

            .confirm-modal-overlay.active .confirm-modal {
                transform: scale(1);
            }

            .confirm-modal-icon {
                font-size: 40px;
                margin-bottom: 15px;
                display: block;
            }

            .confirm-modal h3 {
                margin: 0 0 10px 0;
                color: #333;
            }

            .confirm-modal p {
                color: #666;
                margin-bottom: 20px;
                line-height: 1.5;
            }

            .confirm-modal-buttons {
                display: flex;
                gap: 10px;
                justify-content: center;
            }

            .confirm-btn {
                padding: 10px 24px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.2s;
            }

            .confirm-btn.cancel {
                background: #f5f5f5;
                color: #666;
            }

            .confirm-btn.cancel:hover {
                background: #e0e0e0;
            }

            .confirm-btn.delete {
                background: #f44336;
                color: white;
            }

            .confirm-btn.delete:hover {
                background: #d32f2f;
            }
            }
        </style>
    </head>

    <body>
        <div class="toast-container" id="toastContainer"></div>
        @include('components.navbar')

        <header class="header">
            <h1 class="greeting">Halo {{ Auth::user()->name }}</h1>
            <div class="underline"></div>
            <p class="subtitle">Pilih satu kategori untuk menyaring makanan atau cari berdasarkan nama</p>

            <div class="search-container">
                <div class="search-box">
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari makanan Anda">
                    <button class="search-btn" id="searchBtn">🔍</button>
                </div>
                <p>atau</p>
                <select class="dropdown" id="categoryFilter">
                    <option value="all">Pilih preferensi makan Anda</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
                <button class="my-list-btn" type="button">Menu pilihan saya</button>
            </div>
        </header>

        <main class="main-container">
            <section class="section" style="background: transparent;" id="recommendationsSection">
                <div class="food-cards-container" style="background: transparent;" id="foodCardsContainer">
                    @foreach ($pageRows as $row)
                        <div class="food-cards-row">
                            @foreach ($row as $item)
                                <div class="food-card">
                                    <div class="food-card-image">
                                        @if ($item->image_path)
                                            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}"
                                                class="food-card-img"
                                                onerror="this.onerror=null; this.src='{{ asset('images/fallback.jpg') }}'">
                                        @else
                                            <div class="food-card-fallback"
                                                style="background: linear-gradient(45deg, {{ $item->image_color ?? '#4caf50' }});">
                                                <span>{{ substr($item->name, 0, 15) }}</span>
                                            </div>
                                        @endif
                                        <div class="food-card-badge">
                                            <span>⭐</span> {{ $item->calorie_range }}
                                        </div>
                                    </div>
                                    <div class="food-card-content">
                                        <h3>{{ $item->name }}</h3>
                                        <p>{{ Str::limit($item->description, 100) }}</p>
                                        @if ($item->category)
                                            <div style="margin: 10px 0;">
                                                <span
                                                    style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 0.8em;">
                                                    {{ $item->category }}
                                                </span>
                                            </div>
                                        @endif

                                        <button class="food-card-button" onclick="saveMenu('{{ $item->name }}', '{{ $item->calorie_range }}')">Simpan Menu</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <!-- Confirmation Modal -->
        <div class="confirm-modal-overlay" id="deleteConfirmModal">
            <div class="confirm-modal">
                <span class="confirm-modal-icon">⚠️</span>
                <h3>Hapus Menu?</h3>
                <p>Apakah Anda yakin ingin menghapus menu ini dari daftar pilihan Anda?</p>
                <div class="confirm-modal-buttons">
                    <button class="confirm-btn cancel" onclick="closeDeleteModal()">Batal</button>
                    <button class="confirm-btn delete" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div class="popup-overlay" id="menuListPopup">
            <div class="popup-container">
                <img src="{{ asset('images/chef.png') }}" alt="Menu Header" class="popup-header-image">
                <div class="popup-content">
                    <h3 class="popup-title">Selamat datang di menu Anda!</h3>
                    <p class="popup-message">Berikut adalah daftar menu yang telah Anda simpan</p>

                    <div class="menu-list" id="userMenuList">
                        <!-- Data akan diisi oleh JavaScript -->
                    </div>

                    <button class="popup-ok-btn" type="button">OK</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize filter functionality
                initializeFilters();
                initializePopup();
            });

            function initializeFilters() {
                const searchInput = document.getElementById('searchInput');
                const searchBtn = document.getElementById('searchBtn');
                const categoryFilter = document.getElementById('categoryFilter');

                let searchTimeout;

                // Search functionality
                function performSearch() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filterRecommendations();
                    }, 500); // Delay 500ms untuk menghindari terlalu banyak request
                }

                searchInput.addEventListener('input', performSearch);
                searchBtn.addEventListener('click', filterRecommendations);

                // Category filter
                categoryFilter.addEventListener('change', filterRecommendations);

                // Enter key untuk search
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        filterRecommendations();
                    }
                });
            }

            function initializePopup() {
                // Event listener untuk tombol My List
                const myListBtn = document.querySelector('.my-list-btn');

                if (myListBtn) {
                    myListBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const popup = document.getElementById('menuListPopup');
                        if (popup) {
                            popup.classList.add('active');
                            loadUserMenus();
                        }
                    });
                }

                // Event listener untuk close popup
                const popupOkBtn = document.querySelector('.popup-ok-btn');
                if (popupOkBtn) {
                    popupOkBtn.addEventListener('click', function() {
                        document.getElementById('menuListPopup').classList.remove('active');
                    });
                }

                // Close popup ketika klik overlay
                const popupOverlay = document.getElementById('menuListPopup');
                if (popupOverlay) {
                    popupOverlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            this.classList.remove('active');
                        }
                    });
                }
            }

            async function filterRecommendations() {
                const searchValue = document.getElementById('searchInput').value;
                const categoryValue = document.getElementById('categoryFilter').value;
                const container = document.getElementById('foodCardsContainer');
                const section = document.getElementById('recommendationsSection');

                // Show loading
                if (!section.querySelector('.loading-overlay')) {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'loading-overlay';
                    loadingDiv.innerHTML = 'Memuat rekomendasi...';
                    section.appendChild(loadingDiv);
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const params = new URLSearchParams();
                    params.append('filter', categoryValue);
                    if (searchValue.trim() !== '') {
                        params.append('search', searchValue.trim());
                    } else {
                        // Explicitly send empty search or don't send at all?
                        // Controller logic: if ($search) ...
                        // If we skip 'search' param, $search is null -> false.
                        // If we send 'search=', $search is "" -> false.
                        // Both work. But let's be explicit in our intent.
                        params.append('search', '');
                    }

                    const response = await fetch(`/recomend?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        renderRecommendations(result.data);
                    } else {
                        throw new Error('Failed to filter recommendations');
                    }

                } catch (error) {
                    console.error('Error filtering recommendations:', error);
                    container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <p>Gagal memuat rekomendasi</p>
                        <button onclick="location.reload()" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Muat Ulang Halaman
                        </button>
                    </div>
                `;
                } finally {
                    // Hide loading
                    const loading = section.querySelector('.loading-overlay');
                    if (loading) {
                        loading.remove();
                    }
                }
            }

            function renderRecommendations(pageRows) {
                const container = document.getElementById('foodCardsContainer');

                if (!pageRows || pageRows.length === 0) {
                    container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <p>Tidak ada rekomendasi ditemukan</p>
                        <button onclick="clearFilters()" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Hapus Filter
                        </button>
                    </div>
                `;
                    return;
                }

                let html = '';
                pageRows.forEach(row => {
                    html += '<div class="food-cards-row">';
                    row.forEach(item => {
                        const imageHtml = item.image_path ?
                            `<img src="/${item.image_path}" alt="${item.name}" class="food-card-img" onerror="this.onerror=null; this.src='/images/fallback.jpg'">` :
                            `<div class="food-card-fallback" style="background: linear-gradient(45deg, ${item.image_color || '#4caf50'});"><span>${item.name.substr(0, 15)}</span></div>`;

                        const categoryBadge = item.category ?
                            `<div style="margin: 10px 0;"><span style="background: #e8f5e8; color: #2e7d32; padding: 4px 8px; border-radius: 12px; font-size: 0.8em;">${item.category}</span></div>` :
                            '';

                        html += `
                        <div class="food-card">
                            <div class="food-card-image">
                                ${imageHtml}
                                <div class="food-card-badge">
                                    <span>⭐</span> ${item.calorie_range}
                                </div>
                            </div>
                            <div class="food-card-content">
                                <h3>${item.name}</h3>
                                <p>${item.description.length > 100 ? item.description.substr(0, 100) + '...' : item.description}</p>
                                ${categoryBadge}

                                <button class="food-card-button" onclick="saveMenu('${escapeHtml(item.name)}', '${item.calorie_range}')">Simpan Menu</button>
                            </div>
                        </div>
                    `;
                    });
                    html += '</div>';
                });

                container.innerHTML = html;
            }

            function clearFilters() {
                document.getElementById('searchInput').value = '';
                document.getElementById('categoryFilter').value = 'all';
                location.reload(); // Simple reload to show all items
            }

            // User menu functions (unchanged)
            async function loadUserMenus() {
                const menuList = document.getElementById('userMenuList');

                if (!menuList) {
                    console.error('Menu list container not found!');
                    return;
                }

                menuList.innerHTML = '<div class="loading-message">Memuat menu...</div>';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const response = await fetch('/recomend', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    const menus = await response.json();

                    if (Array.isArray(menus) && menus.length > 0) {
                        menuList.innerHTML = menus.map(menu => `
                        <div class="menu-item">
                            <span class="menu-name">${escapeHtml(menu.name)}</span>
                            <div>
                                <span class="menu-calories">${menu.calories} Kcal</span>
                                <button class="delete-btn" data-id="${menu.id}" onclick="deleteMenu(${menu.id})">×</button>
                            </div>
                        </div>
                    `).join('');
                    } else {
                        menuList.innerHTML = '<div class="empty-message">Belum ada menu yang disimpan</div>';
                    }

                } catch (error) {
                    console.error('Error loading menus:', error);
                    menuList.innerHTML = `
                    <div class="error-message">
                        Gagal memuat menu: ${error.message}
                        <br><button onclick="loadUserMenus()" style="margin-top: 10px; padding: 5px 10px; background: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer;">Coba lagi</button>
                    </div>
                `;
                }
            }

            let menuToDeleteId = null;

            function deleteMenu(menuId) {
                menuToDeleteId = menuId;
                const modal = document.getElementById('deleteConfirmModal');
                modal.classList.add('active');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteConfirmModal');
                modal.classList.remove('active');
                menuToDeleteId = null;
            }

            // Setup delete confirmation listener
            document.addEventListener('DOMContentLoaded', function() {
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', async function() {
                        if (!menuToDeleteId) return;
                        
                        // Store ID locally before clearing global state
                        const idToDelete = menuToDeleteId;

                        // Close modal immediately for better UX
                        closeDeleteModal();
                        
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                            const response = await fetch(`/recomend/${idToDelete}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            if (response.ok) {
                                showToast('Berhasil!', 'Menu berhasil dihapus dari daftar.', 'success');
                                loadUserMenus();
                            } else {
                                const errData = await response.json().catch(() => ({}));
                                const errMsg = errData.error || `Status: ${response.status}`;
                                throw new Error(errMsg);
                            }
                        } catch (error) {
                            console.error('Error deleting menu:', error);
                            showToast('Error!', `Gagal menghapus menu: ${error.message}`, 'error');
                        }
                    });
                }
                
                // Close modal on click outside
                const deleteOverlay = document.getElementById('deleteConfirmModal');
                if (deleteOverlay) {
                    deleteOverlay.addEventListener('click', function(e) {
                         if (e.target === this) {
                             closeDeleteModal();
                         }
                    });
                }
            });

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }

            async function saveMenu(name, calories) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const response = await fetch('/recomend', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            name: name,
                            calories: calories
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast('Berhasil!', 'Menu berhasil disimpan ke daftar pilihan Anda.', 'success');
                        
                        // Auto open popup to show updated list
                        const popup = document.getElementById('menuListPopup');
                        if (popup) {
                            if (popup.classList.contains('active')) {
                                loadUserMenus();
                            }
                        }
                    } else {
                        showToast('Gagal!', 'Gagal menyimpan menu: ' + (result.error || 'Terjadi kesalahan'), 'error');
                    }
                } catch (error) {
                    console.error('Error saving menu:', error);
                    showToast('Error!', 'Terjadi kesalahan koneksi saat menyimpan menu', 'error');
                }
            }

            // Toast Notification Function
            function showToast(title, message, type = 'success') {
                const container = document.getElementById('toastContainer');
                
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
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease-in forwards';
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }, 3000);
            }
        </script>
    </body>

    </html>
@else
    <script>
        window.location = "/login";
    </script>
@endguest
