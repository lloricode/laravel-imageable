<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class Image
 *
 * @package Lloricode\LaravelImageable\Models
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class Image extends Model
{
    public const UPDATED_AT = null;

    public const PATH_FOLDER = 'imageable';

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
        'order',
    ];

    /**
     * Declared Hidden
     */
    protected $hidden = [
        'imageable_id',
        'imageable_type',
        'user_id',
    ];

    /**
     * Image constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(Config::get('imageable.migration.table_name', 'images'));
    }

    public static function boot()
    {
        parent::boot();
        static::creating(
            function ($image) {
                $slug = Str::slug(
                    "{$image->size_name} {$image->width} {$image->height} {$image->group} {$image->category}"
                );

                $count = self::where('slug', 'like', $slug.'%')->count() + 1;

                $image->slug = "$slug-$count";
            }
        );
        static::deleted(
            function ($image) {
                Storage::disk($image->disk)->delete($image->path);
            }
        );
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
