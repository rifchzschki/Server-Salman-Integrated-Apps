<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $dates = ['date'];
    protected $fillable = ['date', 'amount'];
}