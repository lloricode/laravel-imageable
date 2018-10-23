<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Tests\TestCase;
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

        $this->testModel->uploads([
                'default_group' => $fakeImage,
        ])->each([
                [
                    'size_name' => 'public_test',
                    'spatie' => function ($image) {
                        $image->optimize()->fit(Manipulations::FIT_CONTAIN, 120, 300)->quality(90);

                        return $image;
                    },
                ],
        ])->disk('public')->save();

        $this->assertDatabaseHas((new Image)->getTable(), [
            'imageable_id' => $this->testModel->id,
            'imageable_type' => get_class($this->testModel),
            'user_id' => $this->user->id,
            'size_name' => 'public_test',
            'width' => 120,
            'height' => 300,
            'extension' => 'jpg',
            'disk' => 'public',
            'group' => 'default_group',
            'category' => null,
            'content_type' => 'image/jpeg',
        ]);

        $images = $this->testModel->getImages('public_test');

        // check keys
        $this->assertTrue(is_null($images->first()->category));
        $this->assertEquals('public_test', $images->first()->size_name);
        $this->assertEquals('default_group', $images->first()->group);
        $this->assertEquals('avatar.jpg', $images->first()->client_original_name);

        $this->assertFileExists(public_path(str_replace(config('app.url'), '', $images->first()->source)));
        // $this->get($images->first()->source)
        // ->assertStatus(200);

        $response = $this->call('DELETE', $images->first()->source_delete);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertFileNotExists(public_path(str_replace(config('app.url'), '', $images->first()->source)));
    }
}
