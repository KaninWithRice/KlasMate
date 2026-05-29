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
            // Also allow the course owner to view files
            if ($file->folder->user_id !== auth()->id()) {
                abort(403);
            }
        }

        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
        $isViewable = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);
        
        $streamUrl = route('files.stream', $file);
        
        // Correctly generate the public URL for Supabase (Handling the double 'reviewers/' prefix)
        $disk = config('filesystems.default');
        if ($disk === 's3') {
            $baseUrl = rtrim(config('filesystems.disks.s3.url'), '/');
            $bucket = config('filesystems.disks.s3.bucket');
            
            // Storage::store() adds the directory prefix, so $file->path is 'reviewers/filename.ext'
            // We need to make sure we don't end up with /reviewers/reviewers/filename.ext
            $path = ltrim($file->path, '/');
            if (str_starts_with($path, $bucket . '/')) {
                $path = substr($path, strlen($bucket) + 1);
            }
            
            $publicUrl = $baseUrl . '/' . $bucket . '/' . $path;
        } else {
            $publicUrl = Storage::url($file->path);
        }

        return view('repository.view', compact('file', 'isViewable', 'streamUrl', 'extension', 'publicUrl'));
    }

    public function stream(File $file)
    {
        if ($file->status !== 'approved' && $file->user_id !== auth()->id()) {
            if ($file->folder->user_id !== auth()->id()) {
                abort(403);
            }
        }

        if (!Storage::exists($file->path)) {
            abort(404, 'File not found in cloud storage.');
        }

        $content = Storage::get($file->path);
        $mime = Storage::mimeType($file->path) ?: 'application/octet-stream';

        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $file->name . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function update(Request $request, File $file)
    {
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not authorized to rename this file!');
        }

        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        return back()->with('success', 'File renamed successfully!');
    }

    public function destroy(File $file)
    {
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id() && !auth()->user()->is_superadmin) {
            return back()->with('error', 'You are not authorized to delete this file!');
        }

        // Delete from cloud storage if it exists
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        $file->delete();
        return back()->with('success', 'File deleted successfully!');
    }
}
