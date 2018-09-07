<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;
use Spatie\Image\Manipulations;

class TestUploaderAccessPrivate extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFilePrivateStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);
        $this->testModel
            ->uploads([
                'default_p_group' => $fakeImage,
            ])
            ->each([
                [
                    'size_name' => 'private_test',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ]
            ])
            ->disk('local')
            ->save();


        $images = $this->testModel->getImages('private_test');
       
        $this->get($images->first()->source)
        ->assertStatus(200);
    }
}
