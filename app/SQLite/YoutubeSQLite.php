<?php

namespace App\SQLite;

use Illuminate\Database\Eloquent\Model;

class YoutubeSQLite extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'youtube';
    protected $fillable = [
        'youtube_id',
        'subtitle'
    ];
    public $timestamps = false;
}
