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
                    'size_name' => 'banner' , // this will be use in calling image
                    'spatie' => function ($image) {

                        $image // abstract of spatie/image https://github.com/spatie/image
                            ->optimize()
                            ->width(100)
                            -> // ....

                        return $image;
                    },
                ],
                [
                    'size_name' => 'thumbnail', // this will be use in calling image
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
```php
    # .. ->disk('public') 
    $images = $myModel->getImages('banner'); // a size_name use when uploading,
    <img href={{ $images[0]->source }} />
```
- the `images` result:
```
Collection {#3056 ▼
  #items: array:12 [▼
    0 => {#3282 ▼
      +"size_name": "large"
      +"category": null
      +"group": "slide_1"
      +"source": "http://core-boilerplate.test/storage/images/slide/c4ca4238a0b923820dcc509a6f75849b/d49c1886c1582a9372ab8febe263ecc7.jpg"
    }
    1 => {#3283 ▼
      +"size_name": "medium"
      +"category": null
      +"group": "slide_1"
      +"source": "http://core-boilerplate.test/storage/images/slide/c4ca4238a0b923820dcc509a6f75849b/d49c1886c1582a9372ab8febe263ecc7.jpg"
    }
    2 => {#3284 ▼
      +"size_name": "thumbnail"
      +"category": null
      +"group": "slide_1"
      +"source": "http://core-boilerplate.test/storage/images/slide/c4ca4238a0b923820dcc509a6f75849b/d49c1886c1582a9372ab8febe263ecc7.jpg"
    }
    3 => {#3285 ▼
      +"size_name": "large"
      +"category": null
      +"group": "slide_2"
      +"source": "http://core-boilerplate.test/storage/images/slide/c4ca4238a0b923820dcc509a6f75849b/65368aff7a9707782424647b4a3fe8d7.png"
    }
    4 => {#3286 ▼
      +"size_name": "medium"
      +"category": null
      +"group": "slide_2"
      +"source": "http://core-boilerplate.test/storage/images/slide/c4ca4238a0b923820dcc509a6f75849b/65368aff7a9707782424647b4a3fe8d7.png"
    }
    5 => {#3287 ▶}
    6 => {#3288 ▶}
    7 => {#3289 ▶}
    8 => {#3290 ▶}
    9 => {#3291 ▶}
    10 => {#3292 ▶}
    11 => {#3293 ▶}
  ]
}
```
