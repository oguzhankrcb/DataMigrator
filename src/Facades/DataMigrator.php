<?php

namespace Oguzhankrcb\DataMigrator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Oguzhankrcb\DataMigrator\DataMigrator
 */
class DataMigrator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Oguzhankrcb\DataMigrator\DataMigrator::class;
    }
}
