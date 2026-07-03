<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\BookmarkFolder;
use App\Models\Bookmark;
use Illuminate\Routing\Controller;

class PublicPageController extends Controller
{
    public function home()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('public.home', ['users' => $users]);
    }

    public function browse(string $email, ?string $path = null)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->view('errors.404', ['message' => 'User not found.'], 404);
        }

        $segments = $path ? explode('/', $path) : [];
        $currentFolder = null;
        $ancestors = [];
        $parentId = null;

        foreach ($segments as $segment) {
            $decoded = rawurldecode($segment);
            $query = BookmarkFolder::where('user_id', $user->id)
                ->where('name', $decoded);

            if ($parentId === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }

            $folder = $query->first();
            if (!$folder) {
                return response()->view('errors.404', ['message' => 'Folder not found.'], 404);
            }

            $ancestors[] = $folder;
            $parentId = $folder->id;
            $currentFolder = $folder;
        }

        if ($currentFolder) {
            $folders = BookmarkFolder::where('parent_id', $currentFolder->id)
                ->orderBy('position')->get();
            $bookmarks = Bookmark::where('folder_id', $currentFolder->id)
                ->orderBy('position')->paginate(50);
        } else {
            $folders = BookmarkFolder::where('user_id', $user->id)->whereNull('parent_id')
                ->orderBy('position')->get();
            $bookmarks = Bookmark::where('user_id', $user->id)->whereNull('folder_id')
                ->orderBy('position')->paginate(50);
        }

        $ancestorSegments = [];
        $running = '';
        foreach ($ancestors as $a) {
            $running .= '/' . rawurlencode($a->name);
            $ancestorSegments[] = ['folder' => $a, 'url' => $running];
        }

        return view('public.browse', [
            'user' => $user,
            'folder' => $currentFolder,
            'ancestors' => $ancestorSegments,
            'folders' => $folders,
            'bookmarks' => $bookmarks,
        ]);
    }
}
