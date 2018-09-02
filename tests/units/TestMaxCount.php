<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;

class TestMaxCount extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }


    public function testUploadFileNoLimit()
    {
        $fileCount = 20;
        $fakeImage = $this->generateFakeFile($fileCount);

        $image =  $this->testModel
            ->images($fakeImage)
            ->each([
                [
                    'name' => 'test',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ],
            ])
            ->maxCount(0)
            ->save();

        $this->assertEquals($fileCount, count($image->imageFiles));

        $this->assertStorage($image);
    }
}
