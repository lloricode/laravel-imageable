# Laravel Imageable 
### [STILL ON DEVELOPING]
[![Build Status](https://travis-ci.org/lloricode/laravel-imageable.svg?branch=develop)](https://travis-ci.org/lloricode/laravel-imageable)

Painless uploading and managing images to your eloquent laravel models.



## Installation

```
composer require lloricode/laravel-imageable
```
- add to your model
```php
// ...
use Illuminate\Database\Eloquent\Model;
use Lloricode\LaravelImageable\Models\Traits\ImageableTrait;

class MyModel extends Model
{
    use ImageableTrait;
    
    
    // ...
    

```
## Usage
```php
       $myModel
       ->images($image) // instance of \Illuminate\Http\UploadedFile
       ->formats([
           [
               'n' => 'test', // this will be use in calling image
               'w' => 120, // width
               'h' => 300, // height
               'f' => Manipulations::FIT_CONTAIN // any value of \Spatie\Image\Manipulations, default is FIT_CONTAIN
            ]
        ])
       ->maxCount(1)
       ->upload();
```
