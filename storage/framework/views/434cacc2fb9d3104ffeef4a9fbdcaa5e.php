<?php echo $__env->make('components.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                <?php if(auth()->guard()->check()): ?>
                    <?php if(Auth::user()->avatar_url): ?>
                        <img src="<?php echo e(Auth::user()->avatar_url); ?>" alt="profile-image" class="welcome-avatar">
                    <?php else: ?>
                        <div class="welcome-avatar" style="background-color: <?php echo e(Auth::user()->avatar_color); ?>">
                            <?php echo e(Auth::user()->initials); ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="<?php echo e(asset('images/pisang.png')); ?>" alt="profile-image" class="welcome-avatar">
                <?php endif; ?>

                <div class="welcome-content">
                    <h1 class="welcome-title">
                        <?php if(auth()->guard()->check()): ?>
                            Selamat datang <?php echo e(Auth::user()->name); ?> di forum Caloriest!
                        <?php else: ?>
                            Selamat datang di forum Caloriest!
                        <?php endif; ?>
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
                <form method="GET" action="<?php echo e(route('forum.filterByTime')); ?>" id="timeRangeForm">
                    <select class="topic-selector" id="topicSelector" name="timeRange"
                        onchange="document.getElementById('timeRangeForm').submit()">
                        <option value="">Pilih rentang waktu diskusi</option>
                        <option value="today" <?php echo e($timeRange == 'today' ? 'selected' : ''); ?>>Hari Ini</option>
                        <option value="week" <?php echo e($timeRange == 'week' ? 'selected' : ''); ?>>Minggu Ini</option>
                        <option value="month" <?php echo e($timeRange == 'month' ? 'selected' : ''); ?>>Bulan Ini</option>
                        <option value="year" <?php echo e($timeRange == 'year' ? 'selected' : ''); ?>>Tahun Ini</option>
                        <option value="all" <?php echo e($timeRange == 'all' ? 'selected' : ''); ?>>Semua Waktu</option>
                    </select>

                    <div class="ask-mobile">
                        <a onclick="openModal()" class="ask-question-btn">+ Ajukan Pertanyaan</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Forum Posts -->
        <div class="forum-posts">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="post" data-id="<?php echo e($post->id); ?>">
                    <!-- Post Header -->
                    <div class="post-header">
                        <?php
                            $userAvatar = isset($post->user->avatar_url)
                                ? $post->user->avatar_url
                                : asset('images/default-avatar.png');
                            $userName = isset($post->user->name) ? $post->user->name : 'Pengguna Dihapus';
                        ?>

                        <?php if($post->user && $post->user->avatar_url): ?>
                            <img class="post-avatar" src="<?php echo e($post->user->avatar_url); ?>" alt="<?php echo e($userName); ?>">
                        <?php elseif($post->user): ?>
                            <div class="post-avatar dynamic-avatar" style="background-color: <?php echo e($post->user->avatar_color); ?>">
                                <?php echo e($post->user->initials); ?>

                            </div>
                        <?php else: ?>
                             <img class="post-avatar" src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="Deleted User">
                        <?php endif; ?>

                        <div class="post-meta-info">
                            <div class="post-author-name"><?php echo e($userName); ?></div>
                            <div class="post-date-category">
                                <span>Ditanyakan <?php echo e($post->created_at->translatedFormat('d F Y')); ?></span>
                                <?php
                                    $categoryMap = [
                                        'Weight Loss' => 'Penurunan Berat Badan',
                                        'Weight Gain' => 'Kenaikan Berat Badan',
                                        'Maintain Weight' => 'Menjaga Berat Badan',
                                        'Nutrition' => 'Nutrisi',
                                        'Exercise' => 'Olahraga',
                                        'General Health' => 'Kesehatan Umum',
                                    ];
                                    $translatedCategory = $categoryMap[$post->category] ?? $post->category;
                                ?>
                                <span class="post-category-tag"><?php echo e($translatedCategory); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="post-content-section">
                        <h3 class="post-title"><?php echo e($post->title); ?></h3>
                        <p class="post-content"><?php echo e(Str::limit($post->content, 150)); ?></p>
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
                                <span><?php echo e($post->answers_count); ?> Jawaban</span>
                            </div>

                            <div class="views-stat">
                                <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                <span id="views-count-<?php echo e($post->id); ?>"><?php echo e(number_format($post->views_count ?? 0)); ?> 
                                    Dilihat</span>
                            </div>

                            <div class="likes-stat">
                                <svg class="like-button"
                                    style="color: <?php echo e($post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF'); ?>; 
                                        fill: <?php echo e($post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF'); ?>; 
                                        cursor: pointer; width: 20px; height: 20px;"
                                    data-post-id="<?php echo e($post->id); ?>" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                                <span class="likes-count" data-post-id="<?php echo e($post->id); ?>"><?php echo e($post->likes_count); ?>

                                    Suka</span>
                            </div>
                        </div>

                        <a href="javascript:void(0)" onclick="goToAnswer(<?php echo e($post->id); ?>)" class="btn-answer">
                            Jawab
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <!-- Pagination -->
            <div class="pagination-container">
                <?php echo e($posts->links()); ?>

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
                    <span class="stat-number"><?php echo e(number_format($stats['total_questions'])); ?></span>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Jawaban</div>
                    <span class="stat-number"><?php echo e(number_format($stats['total_answers'])); ?></span>
                </div>
            </div>
        </div>

        <h3 class="activity-title">
            <img src="<?php echo e(asset('images/activity-image.png')); ?>" alt="activity-image">
            Aktivitas Terbaru
        </h3>

        <div class="recent-activity">
            <?php if($recentActivity->count() > 0): ?>
                <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="activity-item">
                        <div class="activity-avatar">
                            <?php if($activity->user && $activity->user->avatar_url): ?>
                                <img src="<?php echo e($activity->user->avatar_url); ?>"
                                    alt="profile-image" class="activity-img-avatar">
                            <?php elseif($activity->user): ?>
                                <div class="activity-img-avatar dynamic-avatar" style="background-color: <?php echo e($activity->user->avatar_color); ?>; font-size: 12px;">
                                    <?php echo e($activity->user->initials); ?>

                                </div>
                            <?php else: ?>
                                <img src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="profile-image"
                                    class="activity-img-avatar">
                            <?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                <?php if($activity->type === 'post'): ?>
                                    <span class="highlight"><?php echo e($activity->user->name ?? 'Pengguna Tidak Dikenal'); ?></span> membuat pertanyaan baru: 
                                    <a href="<?php echo e(route('forum.answer', ['post' => $activity->id])); ?>" class="activity-link"><?php echo e(Str::limit($activity->title, 30)); ?></a>
                                <?php elseif($activity->type === 'answer'): ?>
                                    <span class="highlight"><?php echo e($activity->user->name ?? 'Pengguna Tidak Dikenal'); ?></span>
                                    menjawab pertanyaan: 
                                    <a href="<?php echo e(route('forum.answer', ['post' => $activity->post_id])); ?>" class="activity-link"><?php echo e(Str::limit($activity->post->title, 30)); ?></a>
                                <?php endif; ?>
                            </div>
                            <div class="activity-time">
                                <?php echo e(\Carbon\Carbon::parse($activity->created_at)->diffForHumans()); ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="activity-item">
                    <div class="activity-content">
                        <div class="activity-text">Tidak ada aktivitas terbaru</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h3 class="users-title">
            <span>👥</span>
            Pengguna Aktif
        </h3>

        <!-- Active Users -->
        <div class="online-users">
            <div class="active-users-display">
                <div class="user-avatars-row">
                    <?php $__currentLoopData = $activeUsers->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="user-avatar-large">
                            <?php if($user->avatar_url): ?>
                                <img src="<?php echo e($user->avatar_url); ?>" alt="profile-image"
                                    class="activity-user-avatar" title="<?php echo e($user->name ?? 'Pengguna Tidak Dikenal'); ?>">
                            <?php else: ?>
                                <div class="activity-user-avatar dynamic-avatar" style="background-color: <?php echo e($user->avatar_color); ?>; font-size: 12px;" title="<?php echo e($user->name ?? 'Pengguna Tidak Dikenal'); ?>">
                                    <?php echo e($user->initials); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($activeUsers->count() > 5): ?>
                    <div class="others-count-large">+<?php echo e($activeUsers->count() - 5); ?> lainnya</div>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>

<div id="askQuestionModal" class="modal-overlay" style="display: none;">
    <div class="modal-container-forum">
        <div class="modal-content-forum">
            <div class="modal-character">
                <div class="character-avatar">
                    <img src="<?php echo e(asset('images/avatar-question.png')); ?>" alt="">
                </div>
            </div>

            <h2 class="modal-title">Ajukan Pertanyaan</h2>

            <form id="questionForm" class="question-form">
                <?php echo csrf_field(); ?>
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
            formData.append('_token', '<?php echo e(csrf_token()); ?>');

            const response = await fetch('<?php echo e(route('forum.store.ajax')); ?>', {
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
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
            const response = await fetch('<?php echo e(route('forum.toggleLike')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
<?php /**PATH D:\lomba\TrackCalorie\resources\views/auth/forum.blade.php ENDPATH**/ ?>