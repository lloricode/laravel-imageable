<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;
use Spatie\Image\Manipulations;

class TestUploader extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testAllDefault()
    {
        $fakeImage = $this->generateFakeFile();

        $image =  $this->testModel
            ->images([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'test default',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ]
            ])
            ->save();


        $this->assertStorage($image);

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'id' => $image->id,
        ]);

        $this->assertEquals(1, count($image->imageFiles));

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'test default',
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/jpeg',
            // 'size' => 1234000,
            'id' => $image->imageFiles->first()->id,
        ]);
    }

    public function testUploadFile1()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $image =  $this->testModel
            ->images([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'test',
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->fit(Manipulations::FIT_CONTAIN, 120, 300);

                        return $image;
                    },
                ]
            ])
            ->save();


        $this->assertStorage($image);

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'id' => $image->id,
        ]);

        $this->assertEquals(1, count($image->imageFiles));

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/jpeg',
            // 'size' => 1234000,
            'id' => $image->imageFiles->first()->id,
        ]);
    }

    public function testUploadFileContentTypesPNG()
    {
        $fakeImage = $this->generateFakeFile(1, 'png', 120, 300);

        $image =      $this->testModel
            ->images([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'test',
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->width(120)
                        ->height(300);

                        return $image;
                    },
                ]
            ])
            ->contentTypes(['image/png','image/jpg'])
            ->save();

        $this->assertStorage($image);


        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'png',
            'disk' => 'local',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/png',
        ]);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $image =   $this->testModel
            ->images([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'public test',
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->fit(Manipulations::FIT_CONTAIN, 120, 300)
                        ->quality(90);
                        return $image;
                    },
                ]
            ])
            ->disk('public')
            ->save();


        $this->assertStorage($image);


        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'public test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'public',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileGroup()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $image = $this->testModel
            ->images([
                'banner-primary' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'test',
                     'spatie' => function ($image) {
                         $image
                        ->optimize()
                        ->width(120)
                        ->height(300);

                         return $image;
                     },
                ]
            ])
            ->save();
        
        $this->assertStorage($image);
        
        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'banner-primary',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileCategory()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $image =   $this->testModel
            ->images([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'name' => 'test',
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->width(120)
                        ->height(300);

                        return $image;
                    },
                ]
            ])
            ->category('banner')
            ->save();

        $this->assertStorage($image);
        
        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'default_group',
            'category' => 'banner',
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFilesMultiple()
    {
        $fakeImages = $this->generateFakeFile(2, 'jpg', [
            [
                'w'=> 100,
                'h'=> 100,
            ],
            [
                'w'=> 300,
                'h'=> 300,
            ],
        ]);

        $_fakeImages = [];

        foreach ($fakeImages as $key => $fakeImage) {
            $_fakeImages["fake_image_$key"] = $fakeImage;
        }


        $image=  $this->testModel
            ->images($_fakeImages)
            ->each([
                [
                    'name' => 'img1' ,
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->width(100)
                        ->height(100);

                        return $image;
                    },
                ],
                [
                    'name' => 'img2',
                    'spatie' => function ($image) {
                        $image
                        ->optimize()
                        ->width(300)
                        ->height(300);

                        return $image;
                    },
                ],
            ])
            ->save();

        $this->assertStorage($image);

        $this->assertEquals(4, count($image->imageFiles));

        
        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'img1',
            'width' => 100,
            'height' => 100,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'fake_image_0',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'img2',
            'width' => 300,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => 'fake_image_1',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }
}
