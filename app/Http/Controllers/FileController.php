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
        $disk = config('filesystems.default');
        
        // Store in root to keep paths simple
        $path = $file->store('', $disk);

        $type = $request->type;
        if (!$type) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
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

    public function view(File $file)
    {
        // Permission: Uploader OR Course Owner
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id()) {
            abort(403);
        }

        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
        if (!$extension) {
            $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        }

        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) || $file->type === 'image';
        $isPDF = $extension === 'pdf';
        
        $streamUrl = route('files.stream', $file);
        
        // 🚀 ULTIMATE SUPABASE URL BUILDER
        $projectRef = env('AWS_ACCESS_KEY_ID', 'stcuxchsqfeaejpjsfkw');
        $bucket = env('AWS_BUCKET', 'reviewers');
        $filename = basename($file->path);
        
        // Use the official public object URL
        $publicUrl = "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$filename}";
        
        return view('repository.view', compact('file', 'isImage', 'isPDF', 'streamUrl', 'extension', 'publicUrl'));
    }

    public function stream(File $file)
    {
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id()) {
            abort(403);
        }

        $disk = Storage::disk(config('filesystems.default'));
        
        if (!$disk->exists($file->path)) {
            abort(404, 'File not found in cloud storage.');
        }

        $content = $disk->get($file->path);
        $mime = $disk->mimeType($file->path) ?: 'application/octet-stream';

        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function update(Request $request, File $file)
    {
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized.');
        }

        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        return back()->with('success', 'File renamed successfully!');
    }

    public function destroy(File $file)
    {
        // Course owner or uploader can delete
        if ($file->user_id !== auth()->id() && $file->folder->user_id !== auth()->id()) {
            return back()->with('error', 'You are not authorized to delete this file!');
        }

        try {
            $disk = Storage::disk(config('filesystems.default'));
            if ($disk->exists($file->path)) {
                $disk->delete($file->path);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to delete physical file: " . $e->getMessage());
        }

        $file->delete();
        return back()->with('success', 'File deleted successfully!');
    }
}
