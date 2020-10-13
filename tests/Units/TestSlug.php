<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Models\Image;
use Lloricode\LaravelImageable\Tests\Models\TestModel;
use Lloricode\LaravelImageable\Tests\TestCase;

class TestSlug extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testSlug()
    {
        $fakeImage = $this->generateFakeFile();
        foreach (range(1, 10) as $index) {
            TestModel::create(
                [
                    'name' => 'test',
                ]
            )->uploads(
                [
                    $fakeImage,
                ]
            )->each(
                [
                    [
                        'size_name' => 'test_image',
                        'spatie' => function ($image) {
                            return $image;
                        },
                    ],
                    [
                        'size_name' => 'test_image_2',
                        'spatie' => function ($image) {
                            return $image;
                        },
                    ],
                ]
            )->save();
        }

        $this->assertCount(20, Image::all());
    }
}
