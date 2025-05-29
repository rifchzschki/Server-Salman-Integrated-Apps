<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class News extends Model
{
    protected $table = 'news';
    protected $primaryKey = 'news_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'author',
        'poster',
        'cover',
        'link',
        'description',
        'poster_public_id',
        'cover_public_id'
    ];
    protected $casts = [
        'author' => 'array',
    ];
}
