<?php

namespace Lloricode\LaravelImageable\Models\Traits;

use Illuminate\Http\UploadedFile;

trait ImagesTrait
{
    private $_type;
    private $_maxCount;
    private $_files;
    private $_sizes;
    private $_category;
    private $_group;

    public function type(array $type) :self
    {
        $this->_type = $type;
        return $this;
    }
    public function maxCount(int $maxCount) :self
    {
        $this->_maxCount = $maxCount;
        return $this;
    }
    public function files($files):self
    {
        $this->_files = $files;
        // if ($file instanceof UploadedFile) {
        // }

        return $this;
    }
    public function sizes(array $sizes):self
    {
        $this->_sizes = $sizes;
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
