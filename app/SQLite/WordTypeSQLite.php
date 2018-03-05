<?php

namespace App\SQLite;

use Illuminate\Database\Eloquent\Model;

class WordTypeSQLite extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'word_type';
    protected $fillable = [        
        'name',
        'weight',
    ];
    public $timestamps = false;
}
