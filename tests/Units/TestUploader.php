<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Tests\TestCase;
use Spatie\Image\Manipulations;

class TestUploader extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testAllDefault()
    {
        $fakeImage = $this->generateFakeFile();

        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test_default',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'test_default',
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertEquals(1, $this->testModel->getImages('test_default')->count());
        $this->assertEquals(1, $this->testModel->getImages()->count());
    }

    public function testUploadFile1()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test',
                'spatie' => function ($image) {
                    $image->optimize()->fit(Manipulations::FIT_CONTAIN, 120, 300);

                    return $image;
                },
            ],
        ])->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertEquals(1, $this->testModel->getImages('test')->count());
        $this->assertEquals(1, $this->testModel->getImages()->count());
    }

    public function testUploadFileContentTypesPNG()
    {
        $fakeImage = $this->generateFakeFile(1, 'png', 120, 300);

        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test',
                'spatie' => function ($image) {
                    $image->optimize()->width(120)->height(300);

                    return $image;
                },
            ],
        ])->contentTypes(['image/png', 'image/jpg'])->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'png',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/png',
        ]);

        $this->assertEquals(1, $this->testModel->getImages('test')->count());
        $this->assertEquals(1, $this->testModel->getImages()->count());
    }

    public function testUploadFileGroup()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test',
                'spatie' => function ($image) {
                    $image->optimize()->width(120)->height(300);

                    return $image;
                },
            ],
        ])->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertEquals(1, $this->testModel->getImages('test')->count());
        $this->assertEquals(1, $this->testModel->getImages()->count());
    }

    public function testUploadFileCategory()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test',
                'spatie' => function ($image) {
                    $image->optimize()->width(120)->height(300);

                    return $image;
                },
            ],
        ])->category('banner')->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => 'banner',
            'content_type' => 'image/jpeg',
        ]);

        $this->assertEquals(1, $this->testModel->getImages('test')->count());
        $this->assertEquals(1, $this->testModel->getImages()->count());
    }

    public function testUploadFilesMultiple()
    {
        $fakeImages = $this->generateFakeFile(2, 'jpg', [
            [
                'w' => 100,
                'h' => 100,
            ],
            [
                'w' => 300,
                'h' => 300,
            ],
        ]);

        $this->testModel->uploads($fakeImages)->each([
            [
                'size_name' => 'img1',
                'spatie' => function ($image) {
                    $image->optimize()->width(100)->height(100);

                    return $image;
                },
            ],
            [
                'size_name' => 'img2',
                'spatie' => function ($image) {
                    $image->optimize()->width(300)->height(300);

                    return $image;
                },
            ],
        ])->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'img1',
            'width' => 100,
            'height' => 100,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'img2',
            'width' => 300,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'img1',
            'width' => 100,
            'height' => 100,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'img2',
            'width' => 300,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertEquals(2, $this->testModel->getImages('img1')->count());
        $this->assertEquals(2, $this->testModel->getImages('img2')->count());
        $this->assertEquals(4, $this->testModel->getImages()->count());
    }
}
