<?php

namespace Lloricode\LaravelImageable\Models\HelperClass;

use Lloricode\LaravelImageable\Models\Image as ImageModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Image;
use File;

class Uploader
{
    private const FOLDER_NAME = 'images';

    private $_contentTypes;
    private $_maxCount;
    private $_uploadedFiles;
    private $_formats;
    private $_category;
    private $_group;
    private $_isStorage;
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


    public function upload()
    {
        $uploadedFiles = $this->_uploadedFiles;

        if ($uploadedFiles->count() > $this->_maxCount) {
            dd(__METHOD__, 'must not exceed of of maximum files of ' .$this->_maxCount);
        }

        $user = $this->_getAuthUser();

        $imageModel = ImageModel::create([
            'imageable_id' => $this->_model->id,
            'imageable_type' => get_class($this->_model),
            'user_id' => $user->id,
        ]);
        
        
        $storagePath = $this->_storagePath();

        return $uploadedFiles->map(function ($uploadedFile, $key) use ($storagePath, $imageModel) {
            $filePaths = [];
            foreach ($this->_formats as $format) {
                $filePath =$storagePath .'/'.  md5(
                    implode('', $format).
                    get_class($this->_model) .
                    $this->_model->id .
                    $this->_now->format('Ymdhis') .
                    $this->_category.
                    $this->_group.
                    $key
                );

                $image = Image::make($uploadedFile->getRealPath());
                if ($format['c']) {
                    $image->fit($format['w'], $format['h']);
                } else {
                    $image->resize($format['w'], $format['h'], function ($crop) {
                        $crop->aspectRatio();
                    });
                }
                
                $image->save($filePath, $format['q']);

                $pathToRemove = $this->_isStorage ? storage_path() : public_path();
                $filePath = str_replace(realpath($pathToRemove), '', $filePath);

                $data = [
                    'size_name' => $format['n'],
                    'width' => $format['w'],
                    'height' => $format['h'],
                    'content_type' => $uploadedFile->getClientMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'path' => $filePath,
                    'bytes' => $uploadedFile->getClientSize(),
                    'is_storage' => $this->_isStorage,
                ];

                $imageModel->imageFiles()->create($data);

                $filePaths[] = $data;
            }

            return $filePaths;
        });
    }

    private function _storagePath()
    {
        $modelclass = strtolower(get_class($this->_model));
        $modelClassArray = explode('\\', $modelclass);
        $storage =  self::path($this->_isStorage);
        $storage .= $modelClassArray[count($modelClassArray)-1];
        $storage .= '/' . md5($this->_model->id);

        if (! file_exists($storage)) {
            File::makeDirectory($storage, 0755, $recursive = true);
        }

        return realpath($storage);
    }

    public static function path($isStorage)
    {
        $folder = self::FOLDER_NAME;
        return $isStorage ? storage_path("app/$folder/") : public_path("assets/$folder/");
    }


    private function _resetAttributes()
    {
        $this->_model = null;
        $this->_uploadedFiles = collect([]);
        $this->_contentTypes = null;
        $this->_maxCount = 1;
        $this->_formats = null;
        $this->_category = null;
        $this->_group = null;
        $this->_isStorage = true;
        $this->_now = now();
    }


    public function isStorage(bool $isStorage) :self
    {
        $this->_isStorage = $isStorage;
        return $this;
    }

    public function contentTypes(array $contentTypes) :self
    {
        $this->_contentTypes = $contentTypes;
        return $this;
    }

    public function maxCount(int $maxCount) :self
    {
        if ($maxCount < 1) {
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
