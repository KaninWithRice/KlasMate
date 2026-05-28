<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, File $file)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'file_id' => $file->id,
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comment added.');
    }
}
