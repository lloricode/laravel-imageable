<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;
use Illuminate\Http\UploadedFile;
use Storage;

class TestUploader extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    private function _generateFakeFile(int $count = 1, $ext = 'jpg')
    {
        if ($count === 1) {
            return UploadedFile::fake()->image("avatar.$ext");
        }

        $files = [];

        for ($i = 0; $i < $count; $i++) {
            $files[] = UploadedFile::fake()->image("avatar$i.$ext");
        }

        return $files;
    }

    private function _assertStorage(Image $image)
    {
        foreach ($image->imageFiles as $imageFile) {
            if ($imageFile->is_storage) {
                Storage::disk('local')->assertExists(str_replace('app/', '', $imageFile->path));
            } else {
                $this->assertTrue(file_exists(public_path($imageFile->path)));
            }
        }
    }


    public function testUploadFile1()
    {
        $fakeImage = $this->_generateFakeFile();

        $image =  $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->upload();


        $this->_assertStorage($image);

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
            'is_storage' => true,
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileContentTypesPNG()
    {
        $fakeImage = $this->_generateFakeFile(1, 'png');

        $image =      $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->contentTypes(['image/png','image/jpg'])
            ->upload();

        $this->_assertStorage($image);


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
            'is_storage' => true,
            'group' => null,
            'category' => null,
            'content_type' => 'image/png',
        ]);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->_generateFakeFile();

        $image =   $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->isStorage(false)
            ->upload();


        $this->_assertStorage($image);


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
            'is_storage' => false,
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileGroup()
    {
        $fakeImage = $this->_generateFakeFile();

        $image = $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->group('banner-primary')
            ->upload();
        
        $this->_assertStorage($image);
        
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
            'is_storage' => true,
            'group' => 'banner-primary',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFileCategory()
    {
        $fakeImage = $this->_generateFakeFile();

        $image =   $this->testModel
            ->images($fakeImage)
            ->formats([['n' => 'test', 'w' => 120, 'h' => 300, 'c' => true]])
            ->maxCount(1)
            ->category('banner')
            ->upload();

        $this->_assertStorage($image);
        
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
            'is_storage' => true,
            'group' => null,
            'category' => 'banner',
            'content_type' => 'image/jpeg',
        ]);
    }

    public function testUploadFilesMultiple()
    {
        $fakeImages = $this->_generateFakeFile(2);

        $image=  $this->testModel
            ->images($fakeImages)
            ->formats([
                ['n' => 'img1' ,'w' => 100, 'h' => 100],
                ['n' => 'img2', 'w' => 300, 'h' => 300],
            ])
            ->maxCount(2)
            ->upload();

        $this->_assertStorage($image);

        
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
            'is_storage' => true,
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $this->assertDatabaseHas((new ImageFile)->getTable(), [
            'size_name' => 'img2',
            'width' => 300,
            'height' => 300,
            'extension' => 'jpg',
            'is_storage' => true,
            'group' => null,
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }
}