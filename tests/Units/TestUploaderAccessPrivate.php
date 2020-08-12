<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;

class TestUploaderAccessPrivate extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testUploadFilePrivateStorage()
    {
        $fakeImage = $this->generateFakeFile(1, 'jpg', 120, 300);
        $this->testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'private_test',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->disk('local')->save();

        $images = $this->testModel->getImages('private_test');

        $this->get($images->first()->source)->assertStatus(200);
    }
}
