<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use Rememberable;

    const UPDATED_AT = null;
    const PATH_FOLDER = 'imageable';

    /**
     * Declared Fillables
     */
    protected $fillable = [
        'user_id',
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

    /**
     * Declared Fillables
     */
    protected $hidden = [
        'imageable_id',
        'imageable_type',
        'user_id'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(Config::get('imageable.migration.table_name', 'images'));
    }
    
    /**
    * Get the route key for the model.
    *
    * @return string
    */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function imageable()
    {
        return $this->morphTo();
    }

  

    public static function boot()
    {
        parent::boot();
        static::creating(function ($image) {
            $image->slug = str_slug("{$image->size_name} {$image->width} {$image->height} {$image->group} {$image->category}");
        });
        static::deleted(function ($image) {
            Storage::disk($image->disk)
                ->delete($image->path);
        });
    }
}
