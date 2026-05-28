<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get recently uploaded approved files
        $recentFiles = File::where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        // Get all top-level folders joined with visit activity
        // Sorted by most recently visited first, then by creation date
        $allFolders = Folder::leftJoin('folder_visits', function($join) {
                $join->on('folders.id', '=', 'folder_visits.folder_id')
                     ->where('folder_visits.user_id', '=', auth()->id());
            })
            ->whereNull('folders.parent_id')
            ->orderByRaw('folder_visits.visited_at DESC NULLS LAST')
            ->orderByDesc('folders.created_at')
            ->select('folders.*', 'folder_visits.visited_at')
            ->get();

        return view('dashboard', compact('recentFiles', 'allFolders'));
    }
}
