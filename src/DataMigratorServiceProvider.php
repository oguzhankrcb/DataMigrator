<?php

namespace Oguzhankrcb\DataMigrator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Oguzhankrcb\DataMigrator\Commands\DataMigratorCommand;

class DataMigratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('datamigrator');
    }
}
