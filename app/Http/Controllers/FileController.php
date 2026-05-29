<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB limit
            'folder_id' => 'required|exists:folders,id',
            'type' => 'nullable|string|in:image,file,link',
        ]);

        $file = $request->file('file');
        
        // Use the configured disk (public for local, s3 for Supabase/Production)
        $disk = config('filesystems.default');
        $path = $file->store('reviewers', $disk);

        $type = $request->type;
        if (!$type) {
            $extension = $file->getClientOriginalExtension();
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $type = 'image';
            } else {
                $type = 'file';
            }
        }

        File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'folder_id' => $request->folder_id,
            'user_id' => auth()->id(),
            'status' => 'approved',
            'share_token' => Str::random(40),
            'type' => $type,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function show(File $file)
    {
        if ($file->status !== 'approved' && $file->user_id !== auth()->id()) {
            abort(403);
        }

        $file->load('comments.user', 'user');
        
        return view('repository.show', compact('file'));
    }

    public function download(File $file)
    {
        if ($file->status !== 'approved' && $file->user_id !== auth()->id()) {
            abort(403);
        }

        return Storage::download($file->path, $file->name);
    }

    public function view(File $file)
    {
        if ($file->status !== 'approved' && $file->user_id !== auth()->id()) {
            abort(403);
        }

        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
        $isViewable = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
        
        // We provide a direct stream URL for the viewer
        $streamUrl = route('files.stream', $file);

        return view('repository.view', compact('file', 'isViewable', 'streamUrl', 'extension'));
    }

    public function stream(File $file)
    {
        if ($file->status !== 'approved' && $file->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->stream(function () use ($file) {
            echo Storage::get($file->path);
        }, 200, [
            'Content-Type' => Storage::mimeType($file->path),
            'Content-Disposition' => 'inline; filename="' . $file->name . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function update(Request $request, File $file)
    {
        if ($file->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not the owner of this file!');
        }

        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        return back()->with('success', 'File renamed successfully!');
    }

    public function destroy(File $file)
    {
        if ($file->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not the owner of this file!');
        }

        $file->delete();
        return back()->with('success', 'File deleted successfully!');
    }
}
