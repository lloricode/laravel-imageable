<?php

namespace Lloricode\LaravelImageable;

use Lloricode\LaravelImageable\Models\Image as ImageModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Image\Image;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class Uploader
{
    private $_contentTypes;
    private $_uploadedFiles;
    private $_each;
    private $_category;
    private $_disk;
    private $_model;
    private $_now;

    private $_configFilesystem;
    
    public function __construct(Model $model, array $uploadedFiles)
    {
        foreach ($uploadedFiles as $group => $uploadedFile) {
            throw_if(empty($group) or !is_string($group), Exception::class, 'Must have group in key, and must string.');

            throw_if(!($uploadedFile instanceof UploadedFile), Exception::class, 'Must instance of ' . UploadedFile::class);
        }
       
        $this->_configFileSystem = Config::get('filesystems');

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

        $user = $this->_getAuthUser();

        $imageModel = $this->_model->images()->create([
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

        $uploadedFiles->map(function ($uploadedFile, $group) use ($storagePath, $imageModel) {
            foreach ($this->_each as $each) {
                $filePath = $storagePath .'/'.  md5(
                    // implode('', $format).
                    get_class($this->_model) .
                    $this->_model->id .
                    $this->_now->format('Ymdhis') .
                    $this->_category.
                    $group
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
                    'path' => str_replace($this->_storageDiskPath(), '', $filePath),
                    'bytes' => $uploadedFile->getClientSize(),
                    'disk' => $this->_disk,
                    'category' => $this->_category,
                    'group' => $group,
                ]);
            }
        });

        return $imageModel;
    }

    private function _storagePath()
    {
        $path =  ImageModel::PATH_FOLDER . '/' . kebab_case(class_basename($this->_model)) . '/' . md5($this->_model->id);

        $path = $this->_storageDiskPath() . $path;

        if (! file_exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        
        return   $path;
    }

    private function _storageDiskPath()
    {
        return $this->_configFileSystem['disks'][$this->_disk]['root'] . '/';
    }


    private function _resetAttributes()
    {
        $this->_model = null;
        $this->_uploadedFiles = collect([]);
        $this->_contentTypes = null;
        $this->_each = null;
        $this->_category = null;
        $this->_group = null;
        $this->_now = now();

        $this->_disk =
            $this->_configFileSystem['default'] == $this->_configFileSystem['cloud']
            ? $this->_availableDisks()[0]
            : $this->_configFileSystem['default'];
    }

    private function _availableDisks() :array
    {
        $configFileSystem = $this->_configFileSystem;
        array_forget($configFileSystem['disks'], $configFileSystem['cloud']);
        return array_keys($configFileSystem['disks']);
    }

    public function disk(string $disk) :self
    {
        $disks = $this->_availableDisks();
       
        throw_if(!in_array($disk, $disks), Exception::class, 'Invalid disk parameter in ' . get_class($this) . '->disk($disk)');

        $this->_disk = $disk;
        return $this;
    }

    public function contentTypes(array $contentTypes) :self
    {
        $this->_contentTypes = $contentTypes;
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
}
