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
            ->formats([
                [
                    'n' => 'test',
                    'w' => 120,
                    'h' => 300,
                    'c' => true
                ],
            ])
            ->maxCount(0)
            ->upload();

        $this->assertEquals($fileCount, count($image->imageFiles));

        $this->assertStorage($image);
    }
}
