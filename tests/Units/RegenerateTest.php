<?php

namespace Lloricode\LaravelImageable\Tests\Units;

use Illuminate\Support\Facades\File;
use Lloricode\LaravelImageable\Tests\Models\TestModel;
use Lloricode\LaravelImageable\Tests\TestCase;

class RegenerateTest extends TestCase
{
    /** @test
     * @throws \Throwable
     */
    public function regenerate_all()
    {
        $this->modelImageSetUp($this->testModel)->save();

        $fromImage = $this->testModel->getImages('from')->first();
        $toImage = $this->testModel->getImages('to')->first();
        $otherImage = $this->testModel->getImages('other')->first();

        $this->assertFileSourceExistPublic($fromImage);
        $this->assertFileSourceExistPublic($toImage);
        $this->assertFileSourceExistPublic($otherImage);

        File::delete($this->modelFilePathPublic($toImage));
        File::delete($this->modelFilePathPublic($otherImage));

        $this->assertFileSourceExistPublic($fromImage);
        $this->assertFileSourceDoesNotExistPublic($toImage);
        $this->assertFileSourceDoesNotExistPublic($otherImage);

        $this->modelImageSetUp($this->testModel)->regenerate('from');

        $this->assertFileSourceExistPublic($fromImage);
        $this->assertFileSourceExistPublic($toImage);
        $this->assertFileSourceExistPublic($otherImage);
    }

    private function assertFileSourceExistPublic($image)
    {
        $this->assertFileExists($this->modelFilePathPublic($image));
    }

    private function assertFileSourceDoesNotExistPublic($image)
    {
        $this->assertFileDoesNotExist($this->modelFilePathPublic($image));
    }

    private function modelFilePathPublic($image): string
    {
        return public_path(str_replace(config('app.url'), '', $image->source));
    }

    /**
     * @param $testModel
     *
     * @return \Lloricode\LaravelImageable\Uploader
     * @throws \Throwable
     */
    private function modelImageSetUp($testModel)
    {
        /** @var TestModel $testModel */
        return $testModel->uploads(
            [$this->generateFakeFile(1, 'jpg', 120, 300)]

        )->disk('public')->each(
            [
                [
                    'size_name' => 'from',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ],
                [
                    'size_name' => 'to',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ],
                [
                    'size_name' => 'other',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ],
            ]
        );
    }
}