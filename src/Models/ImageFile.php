<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'disk',
        'client_original_name',
    ];


    public function delete()
    {
        Storage::disk($this->disk)
            ->delete($this->path);

        return parent::delete();
    }
}
