<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Models\ImageFile;
use Spatie\Image\Manipulations;

class TestUploaderPublic extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);

        $image =   $this->testModel
            ->uploads([
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
}
