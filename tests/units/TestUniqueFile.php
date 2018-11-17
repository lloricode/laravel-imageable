<?php
/**
 *
 * Created by PhpStorm.
 * User: Lloric Mayuga Garcia <lloricode@gmail.com>
 * Date: 10/31/18
 * Time: 2:14 PM
 */

namespace Lloricode\LaravelImageable\Tests\units;

use App\Models\TestModel;
use Lloricode\LaravelImageable\Exceptions\FileNotUniqueException;
use Lloricode\LaravelImageable\Tests\TestCase;

/**
 * Class TestUniqueFile
 *
 * @package Lloricode\LaravelImageable\Tests\units
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class TestUniqueFile extends TestCase
{
    public function testUniqueUpload()
    {
        $this->expectException(FileNotUniqueException::class);

        $fakeImage = $this->generateFakeFile();
        $testModel = TestModel::create([
            'name' => 'test',
        ]);

        $testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test_image',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->save();

        $testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test_image',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->save();
    }

    public function testUniqueUploadNotThrow()
    {

        $fakeImage = $this->generateFakeFile();
        $testModel = TestModel::create([
            'name' => 'test',
        ]);

        $testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test_image',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->save();

        $testModel->uploads([
            $fakeImage,
        ])->each([
            [
                'size_name' => 'test_image',
                'spatie' => function ($image) {
                    return $image;
                },
            ],
        ])->category('test')->save();

        $this->assertTrue(true);
    }
}