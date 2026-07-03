<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\BookmarkFolder;
use App\Models\Bookmark;
use Illuminate\Routing\Controller;

class PublicPageController extends Controller
{
    public function root(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->view('errors.404', ['message' => 'User not found.'], 404);
        }

        $folders = BookmarkFolder::where('user_id', $user->id)->whereNull('parent_id')
            ->orderBy('position')->get();
        $bookmarks = Bookmark::where('user_id', $user->id)->whereNull('folder_id')
            ->orderBy('position')->paginate(50);

        return view('public.root', [
            'user' => $user,
            'folders' => $folders,
            'bookmarks' => $bookmarks,
        ]);
    }

    public function folder(string $email, string $folderName)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->view('errors.404', ['message' => 'User not found.'], 404);
        }

        $folder = BookmarkFolder::where('user_id', $user->id)
            ->where('name', $folderName)
            ->whereNull('parent_id')
            ->first();

        if (!$folder) {
            return response()->view('errors.404', ['message' => 'Folder not found.'], 404);
        }

        return $this->renderFolder($user, $folder);
    }

    public function subfolder(string $email, string $folderName, string $subfolderName)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->view('errors.404', ['message' => 'User not found.'], 404);
        }

        $parent = BookmarkFolder::where('user_id', $user->id)
            ->where('name', $folderName)
            ->whereNull('parent_id')
            ->first();

        if (!$parent) {
            return response()->view('errors.404', ['message' => 'Folder not found.'], 404);
        }

        $folder = BookmarkFolder::where('user_id', $user->id)
            ->where('name', $subfolderName)
            ->where('parent_id', $parent->id)
            ->first();

        if (!$folder) {
            return response()->view('errors.404', ['message' => 'Folder not found.'], 404);
        }

        return $this->renderFolder($user, $folder);
    }

    private function renderFolder(User $user, BookmarkFolder $folder)
    {
        $subfolders = BookmarkFolder::where('parent_id', $folder->id)
            ->orderBy('position')->get();
        $bookmarks = Bookmark::where('folder_id', $folder->id)
            ->orderBy('position')->paginate(50);

        return view('public.folder', [
            'user' => $user,
            'folder' => $folder,
            'subfolders' => $subfolders,
            'bookmarks' => $bookmarks,
        ]);
    }
}
