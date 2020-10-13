<?php

namespace Lloricode\LaravelImageable;

use Cache;
use Config;
use DB;
use Exception;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Lloricode\LaravelImageable\Exceptions\FileNotUniqueException;
use Lloricode\LaravelImageable\Exceptions\InvalidMimeTypeException;
use Lloricode\LaravelImageable\Models\Image as ImageModel;
use Spatie\Image\Image as SpatieImage;

/**
 * Class Uploader
 *
 * @package Lloricode\LaravelImageable
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class Uploader
{
    /**
     * @var
     */
    private $_contentTypes;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $_uploadedFiles;

    /**
     * @var
     */
    private $_each;

    /**
     * @var
     */
    private $_category;

    /**
     * @var
     */
    private $_disk;

    /**
     * @var \Illuminate\Database\Eloquent\Model|\Lloricode\LaravelImageable\Models\Traits\ImageableTrait
     */
    private $_model;

    /**
     * @var
     */
    private $_now;

    /*
     *
     */
    private $_configFileSystem;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $uploadedFiles
     * @throws \Throwable
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function prepare(Model $model, array $uploadedFiles)
    {
        foreach ($uploadedFiles as $uploadedFile) {
            throw_if(!($uploadedFile instanceof UploadedFile), Exception::class,
                'Must instance of ' . UploadedFile::class);
        }

        $this->_configFileSystem = Config::get('filesystems');

        $this->_resetAttributes();

        $this->_model = $model;
        $this->_uploadedFiles = collect(array_values($uploadedFiles));
    }

    /**
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _resetAttributes()
    {
        $this->_model = null;
        $this->_uploadedFiles = collect([]);
        $this->_contentTypes = null;
        $this->_each = null;
        $this->_category = null;
        $this->_group = null;
        $this->_now = now();

        $this->_disk = $this->_configFileSystem['default'] == $this->_configFileSystem['cloud'] ? $this->_availableDisks()[0] : $this->_configFileSystem['default'];
    }

    /**
     * @return array
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _availableDisks(): array
    {
        $configFileSystem = $this->_configFileSystem;
        Arr::forget($configFileSystem['disks'], $configFileSystem['cloud']);

        return array_keys($configFileSystem['disks']);
    }

    public function regenerate(string $fromSizeName)
    {
        $each = collect($this->_each);

        $from = $each->where('size_name', $fromSizeName);

        if ($from->isEmpty()) {
            abort(500, "size_name `$fromSizeName` not found from ".$this->_model->getMorphClass().'.');
        }

        $fromFilePath = $this->_model
            ->images()
            ->where('size_name', $fromSizeName)
            ->first()->path;

        $other = $each->reject(
            function ($each1) use ($fromSizeName) {
                return $each1['size_name'] == $fromSizeName;
            }
        );

        $other->map(
            function ($each1) use ($fromFilePath) {
                $finalPath = $imageModel = $this->_model
                    ->images()
                    ->where('size_name', $each1['size_name'])
                    ->first()->path;
                $each1['spatie'](SpatieImage::load($this->_storageDiskPath().$fromFilePath))
                    ->save($this->_storageDiskPath().$finalPath);
            }
        );
    }

    /**
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function save()
    {
        $uploadedFiles = $this->_uploadedFiles;

        $storagePath = $this->_storagePath();

        // check content types
        if (!empty($this->_contentTypes)) {
            $uploadedFiles->map(function ($uploadedFile, $key) {
                /** @var \Illuminate\Http\UploadedFile $uploadedFile */
                throw_if(!in_array($uploadedFile->getMimeType(), $this->_contentTypes), InvalidMimeTypeException::class,
                    'Invalid content type it must [' . implode(', ',
                        $this->_contentTypes) . '], ' . $uploadedFile->getMimeType() . ' given.');
            });
        }

        DB::transaction(function () use ($uploadedFiles, $storagePath) {
            $uploadedFiles->map(function ($uploadedFile, $group) use ($storagePath) {
                /** @var \Illuminate\Http\UploadedFile $uploadedFile */
                $group = md5($this->_now->addSeconds($group + 1)->format('Ymdhis') . get_class($this->_model) . $this->_model->getKey() . $this->_category);
                $this->_model->getImages();
                $order = 1;
                foreach ($this->_each as $each) {
                    throw_if(ImageModel::where([
                            'size_name' => $each['size_name'],
                            'group' => $group,
                            'category' => $this->_category,
                            'imageable_id' => $this->_model->getKey(),
                            'imageable_type' => $this->_model->getMorphClass(),
                        ])->count() > 0, FileNotUniqueException::class, 'File upload needs to be unique.');

                    $filePath = $storagePath.'/'.
                                explode(
                                    '.',
                                    $uploadedFile->getClientOriginalName()
                                )[0].
                                '-'.
                                $each['size_name'].
                                '.';

                    $toBeUpload = $each['spatie'](SpatieImage::load($uploadedFile));

                    $manipulations = $toBeUpload->getManipulationSequence();

                    $mimeType = $uploadedFile->getMimeType();
                    $fileExtension = $uploadedFile->getClientOriginalExtension();

                    $isCustomFormat = false;
                    if (!empty($manipulations->toArray()[0]['format'])) {
                        $fileExtension = $manipulations->toArray()[0]['format'];
                        $isCustomFormat = true;
                    }

                    $fullFilePath = $filePath . $fileExtension;

                    $toBeUpload->save($fullFilePath);

                    if ($isCustomFormat) {
                        $mimeType = mime_content_type($fullFilePath);
                    }

                    $image = SpatieImage::load($fullFilePath);

                    $this->_model->images()->create([
                        'user_id' => optional($this->_getAuthUser())->getKey(),
                        'client_original_name' => $uploadedFile->getClientOriginalName(),
                        'size_name' => $each['size_name'],
                        'width' => $image->getWidth(),
                        'height' => $image->getHeight(),
                        'content_type' => $mimeType,
                        'extension' => $fileExtension,
                        'path' => str_replace($this->_storageDiskPath(), '', $fullFilePath),
                        'bytes' => $uploadedFile->getSize() ?: 0,
                        'disk' => $this->_disk,
                        'category' => $this->_category,
                        'group' => $group,
                        'order' => $order,
                    ]);
                }
            });
            if (Config::get('imageable.cache.enable') === true) {
                // ImageModel::flushCache($this->_model->getCachePrefix());
                Cache::tags($this->_model->getCachePrefix())->flush();
            }
        });
    }

    /**
     * @return string
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _storagePath()
    {
        $path = ImageModel::PATH_FOLDER . '/' . kebab_case(class_basename($this->_model)) . '/' . md5($this->_model->id);

        $path = $this->_storageDiskPath() . $path;

        if (!file_exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        return $path;
    }

    /**
     * @return string
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _storageDiskPath()
    {
        return $this->_configFileSystem['disks'][$this->_disk]['root'] . '/';
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    private function _getAuthUser()
    {
        return auth()->check() ? auth()->user() : null; // TODO: config
    }

    /**
     * @param string $disk
     * @return \Lloricode\LaravelImageable\Uploader
     * @throws \Throwable
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function disk(string $disk): self
    {
        $disks = $this->_availableDisks();

        throw_if(!in_array($disk, $disks), Exception::class,
            'Invalid disk parameter in ' . get_class($this) . '->disk($disk)');

        $this->_disk = $disk;

        return $this;
    }

    /**
     * @param array $contentTypes
     * @return \Lloricode\LaravelImageable\Uploader
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function contentTypes(array $contentTypes): self
    {
        $this->_contentTypes = $contentTypes;

        return $this;
    }

    /**
     * @param array $each
     * @return \Lloricode\LaravelImageable\Uploader
     * @throws \Throwable
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function each(array $each): self
    {
        foreach ($each as $each_) {
            foreach (['size_name', 'spatie'] as $key) {
                throw_if(!array_key_exists($key, $each_), Exception::class,
                    'Invalid each parameter in ' . get_class($this) . '->each($each)');
            }
        }

        $this->_each = $each;

        return $this;
    }

    /**
     * @param string $category
     * @return \Lloricode\LaravelImageable\Uploader
     * @author Lloric Mayuga Garcia <lloricode@gmail.com>
     */
    public function category(string $category): self
    {
        $this->_category = $category;

        return $this;
    }
}
