<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Spatie\Image\Manipulations;

class TestUploaderPublicDelete extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $this->testModel
            ->uploads([
                'default_group' => $fakeImage,
            ])
            ->each([
                [
                    'size_name' => 'public_test',
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


        $images = $this->testModel->getImages('public_test');


        $this->assertFileExists(public_path(str_replace(config('app.url'), '', $images->first()->source)));


        $this->testModel->deleteImages();
        
        $this->assertCount(0, $this->testModel->getImages());
    }
}
