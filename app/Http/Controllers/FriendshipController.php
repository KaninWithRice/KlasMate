<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function sendRequest(User $user)
    {
        $currentUserId = auth()->id();

        // Check if request already exists
        $exists = Friendship::where(function($q) use ($currentUserId, $user) {
            $q->where('user_id', $currentUserId)->where('friend_id', $user->id);
        })->orWhere(function($q) use ($currentUserId, $user) {
            $q->where('user_id', $user->id)->where('friend_id', $currentUserId);
        })->exists();

        if (!$exists) {
            Friendship::create([
                'user_id' => $currentUserId,
                'friend_id' => $user->id,
                'status' => 'pending'
            ]);
            return back()->with('success', 'Friend request sent!');
        }

        return back()->with('error', 'Friendship already exists or is pending.');
    }

    public function acceptRequest(User $user)
    {
        $currentUserId = auth()->id();

        $friendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', $currentUserId)
            ->where('status', 'pending')
            ->first();

        if ($friendship) {
            $friendship->update(['status' => 'accepted']);
            
            // Create reciprocal accepted friendship for easy query
            Friendship::updateOrCreate(
                ['user_id' => $currentUserId, 'friend_id' => $user->id],
                ['status' => 'accepted']
            );

            return back()->with('success', 'Friend request accepted!');
        }

        return back()->with('error', 'Request not found.');
    }
}
