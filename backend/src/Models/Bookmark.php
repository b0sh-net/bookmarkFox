<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'folder_id',
        'firefox_id',
        'title',
        'url',
        'position',
    ];

    protected $table = 'bookmarks';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(BookmarkFolder::class, 'folder_id');
    }
}
