<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function forum(Request $request)
    {
        $timeRange = $request->get('timeRange', '');

        // Get forum posts dengan safe access
        $postsQuery = ForumPost::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }
        ])
            ->withCount(['answers', 'likes'])
            ->orderBy('created_at', 'desc');

        if ($timeRange && $timeRange !== 'all') {
            $postsQuery->byTimeRange($timeRange);
        }

        $posts = $postsQuery->paginate(3);

        $posts->getCollection()->transform(function ($post) {
            // Pastikan user object ada
            if (!$post->user) {
                $post->user = (object) [
                    'id' => 0,
                    'name' => 'Deleted User',
                    'avatar' => 'images/default-avatar.png'
                ];
            }

            // Pastikan avatar_url ada
            $post->user->avatar_url = isset($post->user->avatar) && $post->user->avatar
                ? asset('storage/' . $post->user->avatar)
                : asset('images/default-avatar.png');

            return $post;
        });

        // Ambil aktivitas terbaru dari post dan answer
        $recentPosts = ForumPost::with('user')
            ->select('id', 'user_id', 'title', 'created_at', DB::raw("'post' as type"))
            ->where('created_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentAnswers = ForumAnswer::with('user', 'post')
            ->select('id', 'user_id', 'post_id', 'created_at', DB::raw("'answer' as type"))
            ->where('created_at', '<=', now())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Gabungkan kedua koleksi
        $recentActivity = $recentPosts->concat($recentAnswers)
            ->sort(function ($a, $b) {
                $timeA = \Carbon\Carbon::parse($a->created_at);
                $timeB = \Carbon\Carbon::parse($b->created_at);
                return $timeB->getTimestamp() <=> $timeA->getTimestamp();
            })
            ->values() // Reset keys to ensure correct iteration order
            ->take(5); // Ambil 5 aktivitas teratas

        // Active users dengan safe access
        $activeUsers = User::select('id', 'name', 'avatar')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('forum_posts')
                    ->whereColumn('forum_posts.user_id', 'users.id')
                    ->where('forum_posts.created_at', '>=', now()->subDays(7));
            })
            ->orWhereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('forum_answers')
                    ->whereColumn('forum_answers.user_id', 'users.id')
                    ->where('forum_answers.created_at', '>=', now()->subDays(7));
            })
            ->distinct()
            ->limit(10)
            ->limit(10)
            ->get();

        $stats = [
            'total_questions' => ForumPost::count(),
            'total_answers' => ForumAnswer::count(),
        ];

        return view('auth.forum', compact('posts', 'recentActivity', 'activeUsers', 'stats', 'timeRange'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $post = ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        // Redirect kembali ke forum utama setelah membuat post
        return redirect()->route('forum.forum')
            ->with('success', 'Question posted successfully!');
    }

    // Method untuk menangani klik tombol Answer dan increment views
    public function answer($post)
    {
        $post = ForumPost::with(['user', 'answers.user'])
            ->findOrFail($post);

        // Increment views count
        $post->incrementViews();

        // Transform post user untuk safe access
        if (!$post->user) {
            $post->user = (object) [
                'id' => 0,
                'name' => 'Deleted User',
                'avatar' => 'images/default-avatar.png'
            ];
        }

        $post->user->avatar_url = isset($post->user->avatar) && $post->user->avatar
            ? asset('storage/' . $post->user->avatar)
            : asset('images/default-avatar.png');

        // Transform answers untuk safe access
        $post->answers->transform(function ($answer) {
            if (!$answer->user) {
                $answer->user = (object) [
                    'id' => 0,
                    'name' => 'Deleted User',
                    'avatar' => 'images/default-avatar.png'
                ];
            }

            $answer->user->avatar_url = isset($answer->user->avatar) && $answer->user->avatar
                ? asset('storage/' . $answer->user->avatar)
                : asset('images/default-avatar.png');

            return $answer;
        });

        return view('answerquest', compact('post'));
    }

    public function storeAnswer(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:forum_posts,id',
        ]);

        $answer = ForumAnswer::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Update post answers count
        ForumPost::where('id', $request->post_id)->increment('answers_count');

        // Redirect ke halaman answerquest
        return redirect()->route('forum.answer', ['post' => $request->post_id])
            ->with('success', 'Answer posted successfully!');
    }

    public function toggleLike(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:post,answer',
                'id' => 'required|integer'
            ]);

            $type = $request->input('type');
            $id = $request->input('id');
            $user = Auth::user();

            if ($type === 'post') {
                $model = ForumPost::findOrFail($id);
            } else {
                $model = ForumAnswer::findOrFail($id);
            }

            $query = $model->likes();
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', session()->getId());
            }
            $existingLike = $query->first();

            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                $model->likes()->create([
                    'user_id' => $user ? $user->id : null,
                    'session_id' => !$user ? session()->getId() : null,
                ]);
                $liked = true;
            }

            $model->likes_count = $model->likes()->count();
            $model->save();

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $model->likes_count
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update like. Please try again.'], 500);
        }
    }

    public function follow(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }

        if (!$currentUser->isFollowing($user)) {
            $currentUser->following()->attach($user->id);
            return response()->json([
                'success' => true,
                'following' => true,
                'message' => 'You are now following ' . $user->name
            ]);
        }

        return response()->json(['error' => 'Already following this user'], 400);
    }

    public function unfollow(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($currentUser->isFollowing($user)) {
            $currentUser->following()->detach($user->id);
            return response()->json([
                'success' => true,
                'following' => false,
                'message' => 'You unfollowed ' . $user->name
            ]);
        }

        return response()->json(['error' => 'Not following this user'], 400);
    }

    public function filterByTime(Request $request)
    {
        $timeRange = $request->get('timeRange', 'all');
        return $this->forum($request);
    }

    public function storeAjax(Request $request)
    {
        \Log::info('Store AJAX called', ['data' => $request->all()]);

        try {
            // Validasi input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:100',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Buat forum post baru
            $post = ForumPost::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                // Kolom berikut mungkin tidak perlu diisi karena sudah ada default value
                'views_count' => 0,
                'likes_count' => 0,
                'answers_count' => 0
            ]);

            \Log::info('Post created successfully', ['post_id' => $post->id]);

            return response()->json([
                'success' => true,
                'message' => 'Question posted successfully!',
                'post' => $post
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Forum post error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to post question. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function incrementViews(ForumPost $post)
    {
        try {
            // Use the model's incrementViews method instead of Eloquent's increment
            $post->incrementViews();

            return response()->json([
                'success' => true,
                'views_count' => $post->fresh()->views_count
            ]);
        } catch (\Exception $e) {
            \Log::error('Error incrementing views: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to increment views'
            ], 500);
        }
    }
}