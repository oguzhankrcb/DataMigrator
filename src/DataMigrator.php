<?php

namespace Oguzhankrcb\DataMigrator;

use Exception;
use Illuminate\Support\Facades\DB;
use Oguzhankrcb\DataMigrator\Concerns\BasePropertyDefiner;
use Oguzhankrcb\DataMigrator\Exceptions\ClassNotFoundException;
use Throwable;

class DataMigrator
{
    public function transformData(BasePropertyDefiner|array $toModelPrototype, BasePropertyDefiner|array $fromModel)
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
                $toModel[$newField] = $this->transformData($fromField, $fromModel);

                continue;
            }

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

        return $toModel;
    }

    public function transferAllDataFromModelToModel(
        string $transferToModel,
        BasePropertyDefiner|array $toModelPrototype,
        string $transferFromModel
    ) {
        if (! class_exists($transferFromModel)) {
            throw new ClassNotFoundException($transferFromModel);
        }

        if (! class_exists($transferToModel)) {
            throw new ClassNotFoundException($transferToModel);
        }

        try {
            DB::beginTransaction();

            $queryAllDataFromFromModel = $transferFromModel::all()->toArray();
            foreach ($queryAllDataFromFromModel as $model) {
                $toModel = $this->transformData($toModelPrototype, $model);

                $transferToModel::create($toModel);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
