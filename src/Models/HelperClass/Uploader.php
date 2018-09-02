<?php

namespace Lloricode\LaravelImageable\Models\HelperClass;

use Lloricode\LaravelImageable\Models\Image as ImageModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Exception;
use Illuminate\Support\Facades\File;

class Uploader
{
    private const FOLDER_NAME = 'images';

    private $_contentTypes;
    private $_maxCount;
    private $_uploadedFiles;
    private $_formats;
    private $_category;
    private $_group;
    private $_storageDriver;
    private $_model;
    private $_now;
    
    public function __construct(Model $model, $uploadedFiles)
    {
        $checkFile = function ($uploadedFile) {
            if (!($uploadedFile instanceof UploadedFile)) {
                dd(__METHOD__, 'must UploadedFile');
            }
        };

        if (is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $uploadedFile) {
                $checkFile($uploadedFile);
            }
        } else {
            $checkFile($uploadedFiles);
            $uploadedFiles = [$uploadedFiles];
        }

        $this->_resetAttributes();

        $this->_model = $model;
        $this->_uploadedFiles = collect($uploadedFiles);
    }

    private function _getAuthUser()
    {
        return auth()->user(); // TODO: config
    }


    public function upload() :ImageModel
    {
        $uploadedFiles = $this->_uploadedFiles;

        // ignore if zero for no limit
        if ($this->_maxCount !== 0 && $uploadedFiles->count() > $this->_maxCount) {
            dd(__METHOD__, 'must not exceed of of maximum files of ' .$this->_maxCount);
        }

        $user = $this->_getAuthUser();

        $imageModel = ImageModel::create([
            'imageable_id' => $this->_model->id,
            'imageable_type' => get_class($this->_model),
            'user_id' => $user->id,
        ]);
        
        
        $storagePath = $this->_storagePath();

        // check content types
        if (!is_null($this->_contentTypes)) {
            $uploadedFiles->map(function ($uploadedFile, $key) {
                if (!in_array($uploadedFile->getClientMimeType(), $this->_contentTypes)) {
                    dd(__METHOD__, 'invalid content type it must [ '. implode(', ', $this->_contentTypes) .' ].');
                }
            });
        }

        $uploadedFiles->map(function ($uploadedFile, $key) use ($storagePath, $imageModel) {
            foreach ($this->_formats as $format) {
                $filePath = $storagePath .'/'.  md5(
                    implode('', $format).
                    get_class($this->_model) .
                    $this->_model->id .
                    $this->_now->format('Ymdhis') .
                    $this->_category.
                    $this->_group.
                    $key
                ) . '.' . $uploadedFile->getClientOriginalExtension();
                
                // TODO:
                $crop = Manipulations::FIT_CONTAIN;
                $pathToSave = Image::load($uploadedFile)
                    ->optimize()
                    ->fit($crop, $format['w'], $format['h'])
                    ->quality($format['q'])
                    ->save($filePath);
                  
                $imageModel->imageFiles()->create([
                    'size_name' => $format['n'],
                    'width' => $format['w'],
                    'height' => $format['h'],
                    'content_type' => $uploadedFile->getClientMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'path' => str_replace(storage_path('/'), '', $filePath),
                    'bytes' => $uploadedFile->getClientSize(),
                    'storage_driver' => $this->_storageDriver,
                    'category' => $this->_category,
                    'group' => $this->_group,
                ]);
                ;
            }
        });

        return $imageModel;
    }

    private function _storagePath()
    {
        $modelclass = strtolower(get_class($this->_model));

        $modelClassArray = explode('\\', $modelclass);

        $path =  ImageModel::PATH_FOLDER . '/' . $modelClassArray[count($modelClassArray)-1] . '/' . md5($this->_model->id);

        $storage =  config("filesystems.disks.{$this->_storageDriver}.root");
        $path = $storage . '/' . $path;

        if (! file_exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        
        return   $path;
    }

    // public static function path($isStorage)
    // {
    //     $folder = self::FOLDER_NAME;
    //     return $isStorage ? storage_path("app/$folder/") : public_path("assets/$folder/");
    // }


    private function _resetAttributes()
    {
        $this->_model = null;
        $this->_uploadedFiles = collect([]);
        $this->_contentTypes = null;
        $this->_maxCount = 1;
        $this->_formats = null;
        $this->_category = null;
        $this->_group = null;
        $this->_storageDriver = 'local';
        $this->_now = now();
    }


    public function storageDriver(string $storageDriver) :self
    {
        $drivers = ['public', 'local'];

        throw_if(!in_array($storageDriver, $drivers), Exception::class, 'Invalid storage parameter in ' . get_class($this) . '->storageDriver($storageDriver)');

        $this->_storageDriver = $storageDriver;
        return $this;
    }

    public function contentTypes(array $contentTypes) :self
    {
        $this->_contentTypes = $contentTypes;
        return $this;
    }

    public function maxCount(int $maxCount) :self
    {
        if ($maxCount < 0) {
            dd(__METHOD__, 'invalid maxCount.');
        }
        $this->_maxCount = $maxCount;
        return $this;
    }

    public function formats(array $formats):self
    {
        $validated = [];
        foreach ($formats as $format) {
            if ((!array_key_exists('w', $format)) or
            (!array_key_exists('h', $format)) or
            (!array_key_exists('n', $format))) {
                dd(__METHOD__, 'invalid formats parameters.');
            }

            $crop = array_key_exists('c', $format)? $format['c'] : false;
            if (!is_bool($crop)) {
                dd(__METHOD__, 'invalid crop parameters.');
            }
            $validated[] = [
                'n' => $format['n'],
                'w' => $format['w'],
                'h' => $format['h'],
                'c' => $crop,
                'q' => 90, // TODO: quality
                'b' => 5000000, // 5mb TODO: max byte
            ];
        }

        $this->_formats = $validated;
        return $this;
    }

    public function category(string $category):self
    {
        $this->_category = $category;
        return $this;
    }

    public function group(string $group):self
    {
        $this->_group = $group;
        return $this;
    }
}
