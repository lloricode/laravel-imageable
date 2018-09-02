<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;

class TestUploader extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFile1()
    {
        $fakeImage = $this->generateFakeFile();

        $image =  $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->upload();


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
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
            // 'size' => 1234000,
            'id' => $image->imageFiles->first()->id,
        ]);
    }

    public function testUploadFileContentTypesPNG()
    {
        $fakeImage = $this->generateFakeFile(1, 'png');

        $image =      $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->contentTypes(['image/png','image/jpg'])
            ->upload();

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
            'group' => null,
            'category' => null,
            'content_type' => 'image/png',
        ]);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->generateFakeFile();

        $image =   $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'public test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->disk('public')
            ->upload();


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
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileGroup()
    {
        $fakeImage = $this->generateFakeFile();

        $image = $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->group('banner-primary')
            ->upload();
        
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
        $fakeImage = $this->generateFakeFile();

        $image =   $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->category('banner')
            ->upload();

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
            'group' => null,
            'category' => 'banner',
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFilesMultiple()
    {
        $fakeImages = $this->generateFakeFile(2);

        $image=  $this->testModel
            ->images($fakeImages)
            ->formats([
                ['n' => 'img1' ,'w' => 100, 'h' => 100],
                ['n' => 'img2', 'w' => 300, 'h' => 300],
            ])
            ->maxCount(2)
            ->upload();

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
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'img2',
            'width' => 300,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'local',
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }
}
