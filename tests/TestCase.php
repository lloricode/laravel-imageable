<?php

namespace Lloricode\LaravelImageable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Lloricode\LaravelImageable\Models\HelperClass\Uploader;
use Illuminate\Database\Schema\Blueprint;
use App\Models\TestModel;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelImageable\Models\Image;

class TestCase extends Orchestra
{
    use Functions;
    
    protected $testModel;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        
        $this->setUpDatabase($this->app);
        \Artisan::call('storage:link');
    }

    public function tearDown()
    {
        $folder = Image::PATH_FOLDER .'/';
        Storage::disk('local')->deleteDirectory($folder);
        Storage::disk('public')->deleteDirectory($folder);

        parent::tearDown();
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

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        include_once __DIR__.'/../database/migrations/migration.stub';
        (new \CreateImageablesTable())->up();

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });

        $this->testModel = TestModel::create([
            'name' => 'test',
        ]);

        $this->user = User::create([
            'first_name' => 'Basic',
            'last_name' => 'User',
        ]);
    }

    protected function getPackageAliases($app)
    {
        return [
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            'Lloricode\LaravelImageable\Providers\LaravelImageableProvider',
        ];
    }
}
