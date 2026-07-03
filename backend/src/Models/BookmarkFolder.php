<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookmarkFolder extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'firefox_id',
        'name',
        'position',
    ];

    protected $table = 'bookmark_folders';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'folder_id');
    }
}
