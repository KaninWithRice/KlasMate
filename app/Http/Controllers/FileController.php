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
            'file' => 'required|file|max:20480',
            'folder_id' => 'required|exists:folders,id',
            'type' => 'nullable|string|in:image,file,link',
        ]);

        $file = $request->file('file');
        $disk = config('filesystems.default');
        
        // 🚀 STORE AT ROOT to avoid 'reviewers/reviewers' path issues
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
        if (!auth()->check()) abort(403);
        return Storage::disk(config('filesystems.default'))->download($file->path, $file->name);
    }

    public function view(File $file)
    {
        if (!auth()->check()) abort(403);

        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) ?: strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        $isPDF = $extension === 'pdf';
        
        // 🚀 ULTIMATE CLEAN URL BUILDER
        $projectRef = 'stcuxchsqfeaejpjsfkw';
        $bucket = 'reviewers';
        $filename = basename($file->path);
        
        // This is the guaranteed public URL format for Supabase
        $publicUrl = "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$filename}";
        
        $streamUrl = route('files.stream', $file);

        return view('repository.view', compact('file', 'isImage', 'isPDF', 'streamUrl', 'extension', 'publicUrl'));
    }

    public function stream(File $file)
    {
        if (!auth()->check()) abort(403);

        $disk = Storage::disk(config('filesystems.default'));
        
        if (!$disk->exists($file->path)) {
            abort(404, 'File not found in cloud storage.');
        }

        return response($disk->get($file->path), 200, [
            'Content-Type' => $disk->mimeType($file->path),
            'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
        ]);
    }

    public function update(Request $request, File $file)
    {
        if (!auth()->check()) abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        return back()->with('success', 'File renamed successfully!');
    }

    public function destroy(File $file)
    {
        $folderId = $file->folder_id;

        // FEARLESS DELETE: Always clean up database
        try {
            Storage::disk(config('filesystems.default'))->delete($file->path);
        } catch (\Exception $e) {}

        $file->delete();

        return redirect()->route('repository.index', $folderId)->with('success', 'File deleted.');
    }
}
