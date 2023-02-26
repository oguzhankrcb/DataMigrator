<?php

namespace Oguzhankrcb\DataMigrator\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Oguzhankrcb\DataMigrator\DataMigratorServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Oguzhankrcb\\DataMigrator\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [DataMigratorServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();
    }
}
