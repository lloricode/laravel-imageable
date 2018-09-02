<?php

namespace Lloricode\LaravelImageable\Models\HelperClass;

use Lloricode\LaravelImageable\Models\Image as ImageModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;
use Exception;
use Illuminate\Support\Facades\File;

class Uploader
{
    private $_contentTypes;
    private $_maxCount;
    private $_uploadedFiles;
    private $_each;
    private $_category;
    private $_group;
    private $_disk;
    private $_model;
    private $_now;
    
    public function __construct(Model $model, $uploadedFiles)
    {
        $checkFile = function ($uploadedFile) {
            throw_if(!($uploadedFile instanceof UploadedFile), Exception::class, 'Must instance of ' . UploadedFile::class);
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


    public function save() :ImageModel
    {
        $uploadedFiles = $this->_uploadedFiles;

        // ignore if zero for no limit
        throw_if(
            $this->_maxCount !== 0 && $uploadedFiles->count() > $this->_maxCount,
            Exception::class,
            'Must not exceed of of maximum files of ' .$this->_maxCount
        );

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
                throw_if(
                    !in_array($uploadedFile->getClientMimeType(), $this->_contentTypes),
                    Exception::class,
                    'Invalid content type it must [ '. implode(', ', $this->_contentTypes) .' ].'
                );
            });
        }

        $uploadedFiles->map(function ($uploadedFile, $key) use ($storagePath, $imageModel) {
            foreach ($this->_each as $each) {
                $filePath = $storagePath .'/'.  md5(
                    // implode('', $format).
                    get_class($this->_model) .
                    $this->_model->id .
                    $this->_now->format('Ymdhis') .
                    $this->_category.
                    $this->_group.
                    $key
                ) . '.' . $uploadedFile->getClientOriginalExtension();
                                
                $each['spatie'](Image::load($uploadedFile))
                    ->save($filePath);

                $image = Image::load($filePath);
                
                $imageModel->imageFiles()->create([
                    'size_name' => $each['name'],
                    'width' => $image->getWidth(),
                    'height' =>  $image->getHeight(),
                    'content_type' => $uploadedFile->getClientMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'path' => str_replace(storage_path() . '/', '', $filePath),
                    'bytes' => $uploadedFile->getClientSize(),
                    'disk' => $this->_disk,
                    'category' => $this->_category,
                    'group' => $this->_group,
                ]);
            }
        });

        return $imageModel;
    }

    private function _storagePath()
    {
        $modelclass = strtolower(get_class($this->_model));

        $modelClassArray = explode('\\', $modelclass);

        $path =  ImageModel::PATH_FOLDER . '/' . $modelClassArray[count($modelClassArray)-1] . '/' . md5($this->_model->id);

        $path = $this->storagePath($path);

        if (! file_exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        
        return   $path;
    }

    private function storagePath($path)
    {
        return config("filesystems.disks.{$this->_disk}.root") . '/' . $path;
    }


    private function _resetAttributes()
    {
        $this->_model = null;
        $this->_uploadedFiles = collect([]);
        $this->_contentTypes = null;
        $this->_maxCount = 1;
        $this->_each = null;
        $this->_category = null;
        $this->_group = null;
        $this->_disk = 'local';
        $this->_now = now();
    }


    public function disk(string $disk) :self
    {
        $disks = ['public', 'local'];

        throw_if(!in_array($disk, $disks), Exception::class, 'Invalid disk parameter in ' . get_class($this) . '->disk($disk)');

        $this->_disk = $disk;
        return $this;
    }

    public function contentTypes(array $contentTypes) :self
    {
        $this->_contentTypes = $contentTypes;
        return $this;
    }

    public function maxCount(int $maxCount) :self
    {
        throw_if($maxCount < 0, Exception::class, 'Invalid maxCount');

        $this->_maxCount = $maxCount;
        return $this;
    }

    public function each(array $each):self
    {
        foreach ($each as $each_) {
            foreach (['name', 'spatie'] as $key) {
                throw_if(!array_key_exists($key, $each_), Exception::class, 'Invalid each parameter in ' . get_class($this) . '->each($each)');
            }
        }

        $this->_each = $each;
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
