<?php

namespace Lloricode\LaravelImageable\Tests;

use Lloricode\LaravelImageable\Tests\Models\TestModel;
use Lloricode\LaravelImageable\Tests\Models\User;
use Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use Lloricode\LaravelImageable\Models\Image;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use Functions;

    protected $testModel;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
        Artisan::call('storage:link');
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        include_once __DIR__ . '/../database/migrations/migration.stub';
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

    public function tearDown(): void
    {
        $folder = Image::PATH_FOLDER . '/';
        Storage::disk('local')->deleteDirectory($folder);
        Storage::disk('public')->deleteDirectory($folder);

        parent::tearDown();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageAliases($app)
    {
        return [];
    }

    protected function getPackageProviders($app)
    {
        return [
            'Lloricode\LaravelImageable\Providers\LaravelImageableProvider',
            "Lloricode\\LaravelImageable\\Providers\\LaravelImageableRouteServiceProvider",
        ];
    }
}
