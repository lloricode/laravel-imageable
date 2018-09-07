<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    const UPDATED_AT = null;
    const PATH_FOLDER = 'imageable';

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

    public function imageable()
    {
        return $this->morphTo();
    }

    public function delete()
    {
        foreach ($this->imageFiles as $imageFile) {
            $imageFile->delete();
        }
        return parent::delete();
    }
}
