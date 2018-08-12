<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;

class ImageFile extends Model
{
    const UPDATED_AT = null;

    /**
     * Declared Fillables
     */
    protected $fillable = [
        'group',
        'category',
        'size_name',
        'width',
        'height',
        'content_type',
        'extension',
        'path',
        'bytes',
        'storage_driver',
    ];
}
