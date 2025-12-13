@include('components.head')
@include('components.navbar')

<style>
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
</style>


<div class="toast-container" id="toastContainer"></div>
<div class="container main-body">
    <!-- Main Content -->
    <main>
        <section class="welcome-section">
            <div class="welcome-header">
                @auth
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="profile-image" class="welcome-avatar">
                    @else
                        <div class="welcome-avatar" style="background-color: {{ Auth::user()->avatar_color }}">
                            {{ Auth::user()->initials }}
                        </div>
                    @endif
                @else
                    <img src="{{ asset('images/pisang.png') }}" alt="profile-image" class="welcome-avatar">
                @endauth

                <div class="welcome-content">
                    <h1 class="welcome-title">
                        @auth
                            Selamat datang {{ Auth::user()->name }} di forum Caloriest!
                        @else
                            Selamat datang di forum Caloriest!
                        @endauth
                    </h1>
                    <p class="welcome-description">
                        Ini adalah ruang untuk bertanya, berbagi pengalaman, dan berdiskusi tentang kalori,
                        nutrisi, diet sehat, dan kesehatan sehari-hari.
                    </p>
                </div>
            </div>
        </section>

        <!-- Topic Selector Section -->
        <div class="topic-selector-section">
            <div class="topic-selector-container">
                <form method="GET" action="{{ route('forum.filterByTime') }}" id="timeRangeForm">
                    <select class="topic-selector" id="topicSelector" name="timeRange"
                        onchange="document.getElementById('timeRangeForm').submit()">
                        <option value="">Pilih rentang waktu diskusi</option>
                        <option value="today" {{ $timeRange == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $timeRange == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $timeRange == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ $timeRange == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="all" {{ $timeRange == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    </select>

                    <div class="ask-mobile">
                        <a onclick="openModal()" class="ask-question-btn">+ Ajukan Pertanyaan</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Forum Posts -->
        <div class="forum-posts">
            @foreach ($posts as $post)
                <div class="post" data-id="{{ $post->id }}">
                    <!-- Post Header -->
                    <div class="post-header">
                        @php
                            $userAvatar = isset($post->user->avatar_url)
                                ? $post->user->avatar_url
                                : asset('images/default-avatar.png');
                            $userName = isset($post->user->name) ? $post->user->name : 'Pengguna Dihapus';
                        @endphp

                        @if($post->user && $post->user->avatar_url)
                            <img class="post-avatar" src="{{ $post->user->avatar_url }}" alt="{{ $userName }}">
                        @elseif($post->user)
                            <div class="post-avatar dynamic-avatar" style="background-color: {{ $post->user->avatar_color }}">
                                {{ $post->user->initials }}
                            </div>
                        @else
                             <img class="post-avatar" src="{{ asset('images/default-avatar.png') }}" alt="Deleted User">
                        @endif

                        <div class="post-meta-info">
                            <div class="post-author-name">{{ $userName }}</div>
                            <div class="post-date-category">
                                <span>Ditanyakan {{ $post->created_at->translatedFormat('d F Y') }}</span>
                                @php
                                    $categoryMap = [
                                        'Weight Loss' => 'Penurunan Berat Badan',
                                        'Weight Gain' => 'Kenaikan Berat Badan',
                                        'Maintain Weight' => 'Menjaga Berat Badan',
                                        'Nutrition' => 'Nutrisi',
                                        'Exercise' => 'Olahraga',
                                        'General Health' => 'Kesehatan Umum',
                                    ];
                                    $translatedCategory = $categoryMap[$post->category] ?? $post->category;
                                @endphp
                                <span class="post-category-tag">{{ $translatedCategory }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="post-content-section">
                        <h3 class="post-title">{{ $post->title }}</h3>
                        <p class="post-content">{{ Str::limit($post->content, 150) }}</p>
                    </div>

                    <!-- Post Footer -->
                    <div class="post-footer">
                        <div class="post-stats">
                            <div class="answers-stat">
                                <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <span>{{ $post->answers_count }} Jawaban</span>
                            </div>

                            <div class="views-stat">
                                <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <span id="views-count-{{ $post->id }}">{{ number_format($post->views_count ?? 0) }} 
                                    Dilihat</span>
                            </div>

                            <div class="likes-stat">
                                <svg class="like-button"
                                    style="color: {{ $post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF' }}; 
                                        fill: {{ $post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF' }}; 
                                        cursor: pointer; width: 20px; height: 20px;"
                                    data-post-id="{{ $post->id }}" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                                <span class="likes-count" data-post-id="{{ $post->id }}">{{ $post->likes_count }}
                                    Suka</span>
                            </div>
                        </div>

                        <a href="javascript:void(0)" onclick="goToAnswer({{ $post->id }})" class="btn-answer">
                            Jawab
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $posts->links() }}
            </div>
        </div>
    </main>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="ask-question-section">
            <a onclick="openModal()" class="ask-question-btn">+ Ajukan Pertanyaan</a>
        </div>

        <!-- Stats Card -->
        <div class="stats-card">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Pertanyaan</div>
                    <span class="stat-number">{{ number_format($stats['total_questions']) }}</span>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Jawaban</div>
                    <span class="stat-number">{{ number_format($stats['total_answers']) }}</span>
                </div>
            </div>
        </div>

        <h3 class="activity-title">
            <img src="{{ asset('images/activity-image.png') }}" alt="activity-image">
            Aktivitas Terbaru
        </h3>

        <div class="recent-activity">
            @if ($recentActivity->count() > 0)
                @foreach ($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="activity-avatar">
                            @if ($activity->user && $activity->user->avatar_url)
                                <img src="{{ $activity->user->avatar_url }}"
                                    alt="profile-image" class="activity-img-avatar">
                            @elseif ($activity->user)
                                <div class="activity-img-avatar dynamic-avatar" style="background-color: {{ $activity->user->avatar_color }}; font-size: 12px;">
                                    {{ $activity->user->initials }}
                                </div>
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" alt="profile-image"
                                    class="activity-img-avatar">
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                @if ($activity->type === 'post')
                                    <span class="highlight">{{ $activity->user->name ?? 'Pengguna Tidak Dikenal' }}</span> membuat pertanyaan baru: 
                                    <a href="{{ route('forum.answer', ['post' => $activity->id]) }}" class="activity-link">{{ Str::limit($activity->title, 30) }}</a>
                                @elseif ($activity->type === 'answer')
                                    <span class="highlight">{{ $activity->user->name ?? 'Pengguna Tidak Dikenal' }}</span>
                                    menjawab pertanyaan: 
                                    <a href="{{ route('forum.answer', ['post' => $activity->post_id]) }}" class="activity-link">{{ Str::limit($activity->post->title, 30) }}</a>
                                @endif
                            </div>
                            <div class="activity-time">
                                {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="activity-item">
                    <div class="activity-content">
                        <div class="activity-text">Tidak ada aktivitas terbaru</div>
                    </div>
                </div>
            @endif
        </div>

        <h3 class="users-title">
            <span>👥</span>
            Pengguna Aktif
        </h3>

        <!-- Active Users -->
        <div class="online-users">
            <div class="active-users-display">
                <div class="user-avatars-row">
                    @foreach ($activeUsers->take(5) as $user)
                        <div class="user-avatar-large">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="profile-image"
                                    class="activity-user-avatar" title="{{ $user->name ?? 'Pengguna Tidak Dikenal' }}">
                            @else
                                <div class="activity-user-avatar dynamic-avatar" style="background-color: {{ $user->avatar_color }}; font-size: 12px;" title="{{ $user->name ?? 'Pengguna Tidak Dikenal' }}">
                                    {{ $user->initials }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($activeUsers->count() > 5)
                    <div class="others-count-large">+{{ $activeUsers->count() - 5 }} lainnya</div>
                @endif
            </div>
        </div>
    </aside>
</div>

<div id="askQuestionModal" class="modal-overlay" style="display: none;">
    <div class="modal-container-forum">
        <div class="modal-content-forum">
            <div class="modal-character">
                <div class="character-avatar">
                    <img src="{{ asset('images/avatar-question.png') }}" alt="">
                </div>
            </div>

            <h2 class="modal-title">Ajukan Pertanyaan</h2>

            <form id="questionForm" class="question-form">
                @csrf
                <div class="form-group">
                    <input type="text" id="questionTitle" class="form-control" placeholder="Judul Pertanyaan"
                        required>
                </div>

                <div class="form-group">
                    <select id="questionCategory" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Weight Loss">Penurunan Berat Badan</option>
                        <option value="Weight Gain">Kenaikan Berat Badan</option>
                        <option value="Maintain Weight">Menjaga Berat Badan</option>
                        <option value="Nutrition">Nutrisi</option>
                        <option value="Exercise">Olahraga</option>
                        <option value="General Health">Kesehatan Umum</option>
                    </select>
                </div>

                <div class="form-group">
                    <textarea id="questionContent" class="form-control" placeholder="Tulis detail pertanyaan kamu di sini!" rows="4"
                        required></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="back-button" onclick="closeModal()">Batal</button>
                    <button type="submit" class="submit-button">Posting Pertanyaan</button>
                </div>

                <div id="formError" class="error-message" style="color: red; display: none;"></div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('askQuestionModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('askQuestionModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('questionForm').reset();
        document.getElementById('formError').style.display = 'none';
    }

    document.getElementById('askQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('questionForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const title = document.getElementById('questionTitle').value.trim();
        const category = document.getElementById('questionCategory').value;
        const content = document.getElementById('questionContent').value.trim();
        const errorElement = document.getElementById('formError');
        const submitButton = this.querySelector('button[type="submit"]');

        errorElement.style.display = 'none';
        errorElement.textContent = '';

        if (!title) {
            showError('Harap masukkan judul pertanyaan');
            return;
        }

        if (!category) {
            showError('Harap pilih kategori');
            return;
        }

        if (!content) {
            showError('Harap isi detail pertanyaan');
            return;
        }

        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Memposting...';

        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('category', category);
            formData.append('content', content);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route('forum.store.ajax') }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeModal();
                showToast('Berhasil!', 'Pertanyaan berhasil diposting!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                let errorMsg = 'Gagal memposting pertanyaan';

                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    errorMsg = data.message;
                }

                showError(errorMsg);
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Terjadi kesalahan jaringan. Periksa koneksi Anda dan coba lagi.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    function showError(message) {
        const errorElement = document.getElementById('formError');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    async function goToAnswer(postId) {
        try {
            const response = await fetch(`/forum/posts/${postId}/increment-views`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const viewsElement = document.getElementById(`views-count-${postId}`);
                if (viewsElement) {
                    viewsElement.textContent = `${data.views_count} Dilihat`;
                }
            }
        } catch (error) {
            console.error('Error incrementing views:', error);
        } finally {
            window.location.href = `/forum/answer/${postId}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const postId = this.getAttribute('data-post-id');
                toggleLike(postId, this);
            });
        });
    });

    async function toggleLike(postId, buttonElement) {
        console.log('Toggle like called for post:', postId);

        if (buttonElement.classList.contains('processing')) {
            return;
        }

        buttonElement.classList.add('processing');

        try {
            const response = await fetch('{{ route('forum.toggleLike') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: "post",
                    id: parseInt(postId)
                })
            });

            console.log('Response status:', response.status);

            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                const likesCountElement = document.querySelector(`.likes-count[data-post-id="${postId}"]`);
                if (likesCountElement) {
                    likesCountElement.textContent = `${data.likes_count} Suka`;
                }

                if (data.liked) {
                    buttonElement.style.color = '#EC4899';
                    buttonElement.style.fill = '#EC4899';
                } else {
                    buttonElement.style.color = '#9CA3AF';
                    buttonElement.style.fill = '#9CA3AF';
                }
            } else {
                showToast('Gagal!', 'Gagal memperbarui suka. Silakan coba lagi.', 'error');
            }
        } catch (error) {
            showToast('Kesalahan!', 'Kesalahan jaringan. Silakan coba lagi.', 'error');
        } finally {
            buttonElement.classList.remove('processing');
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
