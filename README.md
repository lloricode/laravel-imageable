# Laravel Imageable 
### [STILL ON DEVELOPING]
[![Build Status](https://travis-ci.org/lloricode/laravel-imageable.svg?branch=develop)](https://travis-ci.org/lloricode/laravel-imageable)

Painless uploading/getting images to your eloquent models.



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
       ->images($fakeImage)
       ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
       ->maxCount(1)
       ->upload();
```
