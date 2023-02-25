<?php

namespace Oguzhankrcb\DataMigrator\Exceptions;

use Exception;

class ClassNotFoundException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct("Could not found class named: {$class}");
    }
}
