<?php

namespace Oguzhankrcb\DataMigrator;

use Exception;
use Oguzhankrcb\DataMigrator\Concerns\BasePropertyDefiner;

class DataMigrator
{
    public function transferData(BasePropertyDefiner|array $toModelPrototype, BasePropertyDefiner|array $fromModel)
    {
        if ($toModelPrototype instanceof BasePropertyDefiner) {
            $toModelPrototype = $toModelPrototype->getProperty();
        }

        if ($fromModel instanceof BasePropertyDefiner) {
            $fromModel = $fromModel->getProperty();
        }

        $toModel = [];

        foreach ($toModelPrototype as $newField => $fromField) {
            if (is_array($fromField)) {
                $toModel[$newField] = $this->transferData($fromField, $fromModel);
            } else {
                $fromFieldParts = explode('->', $fromField);
                $fromValue = $fromModel;
                foreach ($fromFieldParts as $part) {
                    if (strpos($part, '.') !== false) {
                        $nestedParts = explode('.', $part);
                        $nestedValue = '';
                        foreach ($nestedParts as $nestedPart) {
                            if (! isset($fromValue[$nestedPart])) {
                                throw new Exception("$nestedPart key not found in the model.");
                            }
                            $nestedValue .= $fromValue[$nestedPart];
                        }
                        $fromValue = $nestedValue;

                        continue;
                    }

                    if (! isset($fromValue[$part])) {
                        throw new Exception("$part key not found in the model.");
                    }
                    $fromValue = $fromValue[$part];
                }
                $toModel[$newField] = $fromValue;
            }
        }

        return $toModel;
    }
}
