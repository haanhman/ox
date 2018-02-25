<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Word.
 *
 * @package namespace App\Entities;
 */
class Word extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'words';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'name',
        'url',
        'html',
        'crawler_done',
        'video_data',
        'crawler_video_done',
        'is_ok',
        'word_type',
        'audio',
        'content'
    ];

}
