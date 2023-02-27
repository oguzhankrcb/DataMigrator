<?php

namespace Oguzhankrcb\DataMigrator\Exceptions;

use Exception;

class KeyNotFoundException extends Exception
{
    public function __construct(string $part)
    {
        parent::__construct("$part key not found in the fromModel.");
    }
}
