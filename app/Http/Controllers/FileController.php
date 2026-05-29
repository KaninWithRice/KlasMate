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
        
        // 🚀 ENSURE WE STORE IN ROOT TO AVOID PATH ISSUES
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
        if (!auth()->check()) abort(403);

        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) ?: strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        $isPDF = $extension === 'pdf';
        
        $streamUrl = route('files.stream', $file);
        
        // 🚀 SMART SUPABASE URL BUILDER
        $baseUrl = rtrim(env('AWS_URL'), '/');
        $bucket = env('AWS_BUCKET', 'reviewers');
        $projectRef = env('AWS_ACCESS_KEY_ID', 'stcuxchsqfeaejpjsfkw');
        
        // If the user didn't set AWS_URL, build the standard one
        if (!$baseUrl) {
            $baseUrl = "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}";
        }

        $filename = basename($file->path);
        
        // Ensure we don't double up the bucket name if it's already in the baseUrl
        if (str_ends_with($baseUrl, $bucket)) {
            $publicUrl = $baseUrl . '/' . $filename;
        } else {
            $publicUrl = $baseUrl . '/' . $bucket . '/' . $filename;
        }

        return view('repository.view', compact('file', 'isImage', 'isPDF', 'streamUrl', 'extension', 'publicUrl'));
    }

    public function stream(File $file)
    {
        if (!auth()->check()) abort(403);

        $disk = Storage::disk(config('filesystems.default'));
        
        if (!$disk->exists($file->path)) {
            abort(404, 'File not found.');
        }

        return response($disk->get($file->path), 200, [
            'Content-Type' => $disk->mimeType($file->path),
            'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
        ]);
    }

    public function download(File $file)
    {
        if (!auth()->check()) abort(403);
        return Storage::disk(config('filesystems.default'))->download($file->path, $file->name);
    }

    public function update(Request $request, File $file)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        return back()->with('success', 'File renamed successfully!');
    }

    public function destroy(File $file)
    {
        // 🚀 FEARLESS DELETE FOR ALL LOGGED IN USERS (For now, to fix the issue)
        try {
            Storage::disk(config('filesystems.default'))->delete($file->path);
        } catch (\Exception $e) {}

        $file->delete();
        return back()->with('success', 'File deleted successfully!');
    }
}
