<?php
namespace Lloricode\LaravelImageable\Tests;

use Illuminate\Database\Schema\Blueprint;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        
        $this->setUpDatabase($this->app);
    }
    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
    }
    protected function getPackageAliases($app)
    {
        return [
            
        ];
    }
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
    protected function getPackageProviders($app)
    {
        return ['Lloricode\LaravelImageable\Providers\LaravelImageableProvider'];
    }
}
