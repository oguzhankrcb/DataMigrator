<?php

namespace Oguzhankrcb\DataMigrator\Concerns;

use Illuminate\Database\Eloquent\Model;

abstract class BasePropertyDefiner
{
    protected array $property;

    public function setProperty(Model|array $property)
    {
        if ($property instanceof Model) {
            $this->property = $property->toArray();

            return;
        }

        $this->property = $property;
    }

    public function getProperty(): array
    {
        return $this->property;
    }
}
