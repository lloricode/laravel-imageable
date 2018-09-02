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
           ->images($fakeImages)  // instance of \Illuminate\Http\UploadedFile
            ->each([
                [
                    'name' => 'img1' , // this will be use in calling image
                    'spatie' => function ($image) {

                        $image // abstract of spatie/image https://github.com/spatie/image
                            ->optimize()
                            ->width(100)
                            -> // ....

                        return $image;
                    },
                ],
                [
                    'name' => 'img2', // this will be use in calling image
                    'spatie' => function ($image) {
                        
                        $image // abstract of spatie/image https://github.com/spatie/image
                         ->greyscale()
                         -> // ....

                        return $image;
                    },
                ],
            ])
            ->maxCount(2) // maximum upload
            ->disk('public') // any disk in config('filesystem) except cloud
            ->save(); // save mutiple 
```
