<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Tests\TestCase;
use Spatie\Image\Manipulations;

class TestSaveDifferentFormat extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFilePublicStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'png', 120, 300);

        $this->testModel->uploads([
                'default_group' => $fakeImage,
        ])->each([
                [
                    'size_name' => 'public_test',
                    'spatie' => function ($image) {
                        $image->optimize()->format(Manipulations::FORMAT_JPG)->fit(Manipulations::FIT_CONTAIN, 120, 300)->quality(90);

                        return $image;
                    },
                ],
        ])->disk('public')->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'public_test',
            'extension' => 'jpg', // <---------------
            'disk' => 'public',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);
    }
}
