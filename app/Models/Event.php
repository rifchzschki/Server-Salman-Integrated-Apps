<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    // Field yang boleh diisi secara mass-assignment
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_time',
        'end_time',
        'organizer',
        'is_online',
        'link',
        'cover_image',
        'poster',
    ];

    // Tipe data untuk casting otomatis
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_online' => 'boolean',
    ];
}
