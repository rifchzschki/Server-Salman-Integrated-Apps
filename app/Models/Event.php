<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
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
        'cover_image_public_id',
        'poster_publid_id'
    ];

}
