<?php

namespace App\Services;

use App\Models\User;
use App\Models\BookmarkFolder;
use App\Models\Bookmark;
use Illuminate\Support\Facades\DB;

class SyncService
{
    public function sync(User $user, array $tree): array
    {
        $created = 0;
        $updated = 0;
        $deleted = 0;

        DB::transaction(function () use ($user, $tree, &$created, &$updated, &$deleted) {
            $incomingFolderIds = [];
            $incomingBookmarkIds = [];

            $this->processNodes($user, $tree, null, $incomingFolderIds, $incomingBookmarkIds, $created, $updated);

            $deleted += Bookmark::where('user_id', $user->id)
                ->whereNotIn('firefox_id', $incomingBookmarkIds)
                ->delete();

            $deleted += BookmarkFolder::where('user_id', $user->id)
                ->whereNotIn('firefox_id', $incomingFolderIds)
                ->delete();
        });

        $folderCount = BookmarkFolder::where('user_id', $user->id)->count();
        $bookmarkCount = Bookmark::where('user_id', $user->id)->count();

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'folders' => $folderCount,
            'bookmarks' => $bookmarkCount,
        ];
    }

    private function processNodes(
        User $user,
        array $nodes,
        ?int $parentId,
        array &$incomingFolderIds,
        array &$incomingBookmarkIds,
        int &$created,
        int &$updated,
    ): void {
        foreach ($nodes as $node) {
            $firefoxId = $node['firefox_id'];
            $type = $node['type'] ?? 'bookmark';

            if ($type === 'folder') {
                $incomingFolderIds[] = $firefoxId;

                $folder = BookmarkFolder::updateOrCreate(
                    ['user_id' => $user->id, 'firefox_id' => $firefoxId],
                    [
                        'user_id' => $user->id,
                        'parent_id' => $parentId,
                        'name' => $node['title'],
                        'position' => $node['position'] ?? 0,
                    ]
                );

                if ($folder->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }

                if (!empty($node['children'])) {
                    $this->processNodes(
                        $user,
                        $node['children'],
                        $folder->id,
                        $incomingFolderIds,
                        $incomingBookmarkIds,
                        $created,
                        $updated,
                    );
                }
            } else {
                $url = $node['url'] ?? '';

                if (!preg_match('#^https?://#i', $url)) {
                    continue;
                }

                $incomingBookmarkIds[] = $firefoxId;

                $bookmark = Bookmark::updateOrCreate(
                    ['user_id' => $user->id, 'firefox_id' => $firefoxId],
                    [
                        'user_id' => $user->id,
                        'folder_id' => $parentId,
                        'title' => $node['title'],
                        'url' => $url,
                        'position' => $node['position'] ?? 0,
                    ]
                );

                if ($bookmark->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
        }
    }
}
