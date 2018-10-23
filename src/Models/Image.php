<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
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
     * Declared Hidden
     */
    protected $hidden = [
        'imageable_id',
        'imageable_type',
        'user_id',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(Config::get('imageable.migration.table_name', 'images'));
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($image) {
            $slug = str_slug("{$image->size_name} {$image->width} {$image->height} {$image->group} {$image->category}");

            $count = self::where('slug', 'like', $slug.'%')->count() + 1;

            $image->slug = "$slug-$count";
        });
        static::deleted(function ($image) {
            Storage::disk($image->disk)->delete($image->path);
        });
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
}
