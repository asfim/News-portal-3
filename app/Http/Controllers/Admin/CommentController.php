<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query = Comment::with(['news', 'user'])->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $comments = $query->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments', 'status'));
    }

    public function approve(Comment $comment)
    {
        $comment->update([
            'status' => 'approved',
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'মন্তব্যটি অনুমোদন করা হয়েছে।');
    }

    public function reject(Request $request, Comment $comment)
    {
        $comment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason', 'নীতিমালা পরিপন্থী'),
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'মন্তব্যটি প্রত্যাখ্যান করা হয়েছে।');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'মন্তব্যটি মুছে ফেলা হয়েছে।');
    }
}
