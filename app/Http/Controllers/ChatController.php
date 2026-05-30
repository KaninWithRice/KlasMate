<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(User $friend)
    {
        $currentId = auth()->id();
        
        $messages = Message::with(['folder', 'file', 'sender'])
            ->where(function($q) use ($currentId, $friend) {
                $q->where('sender_id', $currentId)->where('receiver_id', $friend->id);
            })
            ->orWhere(function($q) use ($currentId, $friend) {
                $q->where('sender_id', $friend->id)->where('receiver_id', $currentId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('sender_id', $friend->id)
            ->where('receiver_id', $currentId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat', compact('friend', 'messages'));
    }

    public function share(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|in:folder,file',
            'item_id' => 'required'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'type' => $request->type,
            'folder_id' => $request->type === 'folder' ? $request->item_id : null,
            'file_id' => $request->type === 'file' ? $request->item_id : null,
            'message' => 'Shared a ' . $request->type . ' with you.'
        ]);

        return response()->json(['success' => true, 'message' => 'Shared successfully!']);
    }

    public function store(Request $request, User $friend)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $friend->id,
            'message' => $request->message,
            'type' => 'text'
        ]);

        return back();
    }
}
