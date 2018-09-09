# Laravel Imageable 


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
            ->contentTypes(['image/png','image/jpg','image/jpeg'])
            ->disk('public') // any disk in config('filesystem) except cloud
            ->category('banner') // optional
            ->save(); // save mutiple 
```

### Getter
```php
    $images = $myModel->getImages('banner', $category = null, $group = null); // a size_name use when uploading,
    <img href={{ $images->first()->source }} />
```
- the `images` result of `$myModel->getImages()`
```
Illuminate\Support\Collection {#2947
  #items: array:4 [
    0 => {#2948
      +"size_name": "banner"
      +"category": "banner"
      +"group": "first_image"
      +"client_original_name": "avatar.jpg"
      +"source": "http://localhost/storage/imageable/test-model/c4ca4238a0b923820dcc509a6f75849b/banner-43faf04b2c2b6cbf05c49b76cd6b7290.jpg"
    }
    1 => {#2946
      +"size_name": "thumbnail"
      +"category": "banner"
      +"group": "first_image"
      +"client_original_name": "avatar.jpg"
      +"source": "http://localhost/storage/imageable/test-model/c4ca4238a0b923820dcc509a6f75849b/thumbnail-43faf04b2c2b6cbf05c49b76cd6b7290.jpg"
    }
    2 => {#2963
      +"size_name": "banner"
      +"category": "banner"
      +"group": "second_image"
      +"client_original_name": "avatar.jpg"
      +"source": "http://localhost/storage/imageable/test-model/c4ca4238a0b923820dcc509a6f75849b/banner-778f3ef046040aafa2c71d803a5e41ba.jpg"
    }
    3 => {#2994
      +"size_name": "thumbnail"
      +"category": "banner"
      +"group": "second_image"
      +"client_original_name": "avatar.jpg"
      +"source": "http://localhost/storage/imageable/test-model/c4ca4238a0b923820dcc509a6f75849b/thumbnail-778f3ef046040aafa2c71d803a5e41ba.jpg"
    }
  ]
}
```
