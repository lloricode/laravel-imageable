<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    const UPDATED_AT = null;
    const PATH_FOLDER = 'images';

    /**
     * Declared Fillables
     */
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'user_id',
    ];

    /**
     * Declared Fillables
     */
    protected $hidden = [
        'imageable_id',
        'imageable_type',
        'user_id'
    ];

    public function imageFiles()
    {
        return $this->hasMany(ImageFile::class);
    }
}
