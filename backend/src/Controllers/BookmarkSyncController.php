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
        $validated = $request->validate([
            'tree' => 'required|array',
            'tree.*.firefox_id' => 'required|string',
            'tree.*.type' => 'required|in:folder,bookmark',
        ]);

        $result = $this->syncService->sync($request->user(), $validated['tree']);

        return $this->json($result);
    }
}
