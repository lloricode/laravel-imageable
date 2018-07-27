<?php

namespace Lloricode\LaravelImageable\Models;

use Illuminate\Database\Eloquent\Model;
// use Lloricode\Image\Exceptions\DeleteException;
use Storage;
use DB;

class Image extends Model
{

    

    /**
     * Declared Fillables
     */
    protected $fillable = [
        'imageable_id', 'imageable_type', 'user_id', 'filename', 'type', 'group'
    ];

    /** TODO: */
    // public $appends = [
    //     'filename_original',
    // ];

    /**
     * Declared Fillables
     */
    protected $hidden = ['imageable_id', 'imageable_type', 'user_id'];

    // public function getFilenameAttribute($value)
    // {
    //     return config('halcyon-laravel.image.is_storage') ? route('image.show', "public$value") : asset($value);
    // }

    /**
     * TODO:
     */
    // public function getFilenameOriginalAttribute()
    // {
    //     return $this->attributes['filename'];
    // }

    /**
     * Relationship to User
     */
    // public function user()
    // {
    //     return $this->belongsTo(config('halcyon-laravel.image.auth.user.model'));
    // }

    /**
     * Relationship to Morphable content
     */
    // public function imageable()
    // {
    //     return $this->morphTo();
    // }

    // public static function deleteFiles($pathOrModel, $column = 'group', $category = 'default')
    // {
    //     try {
    //         DB::beginTransaction();
    //         $query = self::getQueryCollection($pathOrModel, $column);
    //         if ($column == 'model') {
    //             $query->where('category', $category);
    //         }
    //         if (config('halcyon-laravel.image.can_delete')) {
    //             $files = $query->get();
    //             foreach ($files as $f => $file) {
    //                 if (config('halcyon-laravel.image.is_storage')) {
    //                     Storage::delete('public/' . $file->filename_original);
    //                 } else {
    //                     File::delete($file->filename_original);
    //                 }
    //             }
    //         }
    //         $query->delete();
    //         DB::commit();
    //         return response()->json([
    //             'message' =>  trans('image::messages.success_delete'),
    //         ]);
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }
    // }

    // private static function getQueryCollection($pathOrModel, $column)
    // {
    //     switch ($column) {
    //         case 'model':
    //             $query = self::where('imageable_id', $pathOrModel['id'])
    //                 ->where('imageable_type', $pathOrModel['type']);
    //         break;

    //         default:
    //         case 'group':
    //             $query = self::where($column, 'like', '%' . substr(strrchr($pathOrModel, "-"), 1) . '%');
    //         break;
    //     }
    //     return $query;
    // }

    // /**
    //  * Return other image by size/name
    //  *
    //  * @param int/string $widthOrName
    //  * @param int $height
    //  */
    // public function getOtherImage($widthOrName = null, $height = null)
    // {
    //     $query = self::getQueryCollection($this->filename, 'group');
        
    //     if (!is_null($widthOrName) && is_null($height) && is_string($widthOrName)) {
    //         return $query->where('name', $widthOrName);
    //     } elseif (!is_null($widthOrName) && !is_null($height)) {
    //         return $query->where('type', "{$widthOrName}x{$height}");
    //     }
    //     return $query->whereNull('type');
    // }
}
