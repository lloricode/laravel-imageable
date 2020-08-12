<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;

class TestOrder extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testOrder()
    {
        $fakeImage = $this->generateFakeFile();
        $this->testModel->uploads([
            $fakeImage,
        ])->each([
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
        ])->save();

        $this->assertEquals(1, $this->testModel->getImages()->first()->order);
        $this->assertEquals(1, $this->testModel->getImages()->last()->order);
    }
}
