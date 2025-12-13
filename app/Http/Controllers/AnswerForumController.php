<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumAnswer;
use Illuminate\Support\Facades\Auth;

class AnswerForumController extends Controller
{
    public function answer($postId)
    {
        // Find the post and increment views
        $post = ForumPost::find($postId);

        if ($post) {
            $post->views_count = $post->views_count + 1;
            $post->save();
        }

        // Continue with your existing code
        $post = ForumPost::with(['user', 'answers.user'])
            ->findOrFail($postId);

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

        return view('auth.answerquest', compact('post'));
    }

    // Method untuk menyimpan answer baru - DIPERBAIKI
    public function storeAnswer(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
            'content' => 'required|string|max:1000',
        ]);

        try {
            // Gunakan user_id dari user yang sedang login
            ForumAnswer::create([
                'post_id' => $request->post_id,
                'user_id' => Auth::id(), // Menggunakan ID user yang login
                'content' => $request->content,
            ]);

            // Redirect dengan session flash untuk mencegah form resubmission
            return redirect()->route('forum.answer', $request->post_id)
                ->with('success', 'Answer posted successfully!');
        } catch (\Exception $e) {
            // Redirect back dengan input kecuali content untuk menghindari text yang kembali
            return redirect()->back()
                ->with('error', 'Failed to post answer. Please try again.')
                ->withInput(['post_id' => $request->post_id]); // Hanya kirim post_id, bukan content
        }
    }
}
