<?php

namespace App\SQLite;

use Illuminate\Database\Eloquent\Model;

class GroupSQLite extends Model
{
    protected $connection = 'sqlite';
    protected $table = 'groups';
    protected $fillable = [
        'id',
        'name'
    ];
    public $timestamps = false;
}
