<?php

namespace App\Controllers;

use App\Services\SyncService;
use Illuminate\Http\Request;

class BookmarkSyncController extends AppController
{
    public function __construct(
        private readonly SyncService $syncService
    ) {}

    public function sync(Request $request)
    {
        $request->validate([
            'tree' => 'required|array',
            'tree.*.firefox_id' => 'required|string',
            'tree.*.type' => 'required|in:folder,bookmark',
        ]);

        $tree = $request->input('tree');

        $result = $this->syncService->sync($request->user(), $tree);

        return $this->json($result);
    }
}
