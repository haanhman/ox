<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Youtube.
 *
 * @package namespace App\Entities;
 */
class Youtube extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'youtube_id',
        'cid',
        'id2',
        'src',
        'accent',
        'subtitle',
        's1',
        's2',
        'e1',
        'e2'
    ];
    protected $table = 'youtube';
}
