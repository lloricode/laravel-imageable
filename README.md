# Laravel Imageable 
### [STILL ON DEVELOPING]


[![Latest Version on Packagist](https://img.shields.io/packagist/v/lloricode/laravel-imageable.svg)](https://packagist.org/packages/lloricode/laravel-imageable) [![Build Status](https://travis-ci.org/lloricode/laravel-imageable.svg?branch=develop)](https://travis-ci.org/lloricode/laravel-imageable) [![Total Downloads](https://img.shields.io/packagist/dt/lloricode/laravel-imageable.svg)](https://packagist.org/packages/lloricode/laravel-imageable)


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

        // files to be upload 
        $imageFiles = [
            // group => instance of \Illuminate\Http\UploadedFile
            [
                'first_image' => $imageFile1,
            ],
            [
                'second_image' => $imageFile2,
            ],
        ];

       $myModel
           ->uploads($imageFiles) // must array, 
            ->each([
                [
                    'name' => 'banner' , // this will be use in calling image
                    'spatie' => function ($image) {

                        $image // abstract of spatie/image https://github.com/spatie/image
                            ->optimize()
                            ->width(100)
                            -> // ....

                        return $image;
                    },
                ],
                [
                    'name' => 'thumbnail', // this will be use in calling image
                    'spatie' => function ($image) {
                        
                        $image // abstract of spatie/image https://github.com/spatie/image
                         ->greyscale()
                         -> // ....

                        return $image;
                    },
                ],
            ])
            ->contentTypes(['image/png','image/jpg'])
            ->disk('public') // any disk in config('filesystem) except cloud
            ->category('banner') // optional
            ->save(); // save mutiple 
```

### Getter
- when `disk` is set to `public`, so meaning image is accessesible to the public forder.
```php
    # .. ->disk('public') 
    $image = $myModel->getImages('public_test');
    <img href={{ $image->src }} />
```
- on private, it will need to make response
- TODO:
