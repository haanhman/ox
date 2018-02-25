<?php

namespace App\SQLite;

use Illuminate\Database\Eloquent\Model;

class WordSQLite extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'words';
    protected $fillable = [
        'group_id',
        'name',
        'word_type',
        'use',
        'video_data',
        'audio_data'
    ];
    public $timestamps = false;
}
