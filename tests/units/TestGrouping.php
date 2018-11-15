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
use Lloricode\LaravelImageable\Tests\TestCase;

/**
 * Class TestUniqueFile
 *
 * @package Lloricode\LaravelImageable\Tests\units
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class TestGrouping extends TestCase
{
    public function testGrouping1()
    {
        $this->_grouping();
    }

    public function _grouping(int $count = 1)
    {
        $testModel = TestModel::create([
            'name' => 'test',
        ]);
        for ($i = 0; $i < $count; $i++) {

            $testModel->uploads([
                $this->generateFakeFile(),
            ])->each([
                [
                    'size_name' => 'test_image',
                    'spatie' => function ($image) {
                        return $image;
                    },
                ],
            ])->category('category_'.($i + 1))->save();
        }

        $this->assertCount($count, $testModel->getImages()->unique('group'));
    }

    public function testGrouping2()
    {
        $this->_grouping(2);
    }

    public function testGrouping3()
    {
        $this->_grouping(3);
    }
}