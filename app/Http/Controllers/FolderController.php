<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FolderController extends Controller
{
    public function index(Folder $folder = null)
    {
        // Access control: if folder is private and user is not owner and no valid token
        if ($folder && !$folder->is_public && $folder->user_id !== auth()->id()) {
             if (request('token') !== $folder->invite_token) {
                 abort(403, 'This course is private. You need an invite link to access it.');
             }
        }

        // Record visit if authenticated
        if ($folder && auth()->check()) {
            DB::table('folder_visits')->updateOrInsert(
                ['user_id' => auth()->id(), 'folder_id' => $folder->id],
                ['visited_at' => now()]
            );
        }

        $folders = Folder::where('parent_id', $folder?->id)->get();
        $files = $folder ? $folder->files()->where('status', 'approved')->get() : [];
        $friends = auth()->user()->friends;
        
        return view('repository.index', compact('folders', 'files', 'folder', 'friends'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'code' => 'nullable|string|max:255',
            'semester' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        $is_public = $request->has('is_public') ? (bool)$request->is_public : true;

        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'code' => $request->code,
            'semester' => $request->semester,
            'color' => $request->color ?? 'bg-[#f5c32f]',
            'user_id' => auth()->id(),
            'is_public' => $is_public,
            'invite_token' => !$is_public ? Str::random(32) : null,
        ]);

        return back()->with('success', 'Folder created successfully.');
    }

    public function update(Request $request, Folder $folder)
    {
        if ($folder->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not the owner of this course!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'semester' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);

        $is_public = $request->has('is_public') ? (bool)$request->is_public : true;
        
        $data = $request->only('name', 'code', 'semester', 'color');
        $data['is_public'] = $is_public;

        if (!$is_public && !$folder->invite_token) {
            $data['invite_token'] = Str::random(32);
        } elseif ($is_public) {
            $data['invite_token'] = null;
        }

        $folder->update($data);

        return back()->with('success', 'Folder updated successfully.');
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not the owner of this course!');
        }

        $folder->delete();
        return back()->with('success', 'Folder deleted successfully!');
    }
}
