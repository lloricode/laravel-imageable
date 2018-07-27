<?php
namespace Lloricode\LaravelImageable\Tests\Units;

use Lloricode\LaravelImageable\Tests\TestCase;

class TestSample extends TestCase
{
    public function testSample()
    {
        (new \App\Models\TestModel)->type(['sss']);
        $this->assertTrue(true);
    }
}
