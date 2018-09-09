<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Watson\Rememberable\Rememberable;
use Illuminate\Support\Facades\Config;

class Image extends Model
{
    use HasSlug;
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

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['size_name', 'width', 'height'])
            ->saveSlugsTo('slug');
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    public function delete()
    {
        Storage::disk($this->disk)
            ->delete($this->path);

        return parent::delete();
    }
}
