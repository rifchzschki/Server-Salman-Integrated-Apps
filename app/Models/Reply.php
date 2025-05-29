<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = [
        'discussion_id',
        'author_id',
        'content'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
