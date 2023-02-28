<?php

namespace Oguzhankrcb\DataMigrator;

use Exception;
use Illuminate\Support\Facades\DB;
use Oguzhankrcb\DataMigrator\Exceptions\ClassNotFoundException;
use Oguzhankrcb\DataMigrator\Exceptions\KeyNotFoundException;
use Oguzhankrcb\DataMigrator\Traits\FieldTokenizer;
use Throwable;

class DataMigrator
{
    use FieldTokenizer;

    /**
     * @return array
     *
     * @throws \Oguzhankrcb\DataMigrator\Exceptions\KeyNotFoundException
     *
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transforms_data_with_concatenate_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transforms_data_with_nested_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transforms_data_with_static_keys()
     */
    public function transformData(array $toModelPrototype, array $fromModel)
    {
        $toModel = [];

        foreach ($toModelPrototype as $newField => $fromField) {
            if (is_array($fromField)) {
                $toModel[$newField] = $this->transformData($fromField, $fromModel);

                continue;
            }

            $fieldParts = $this->tokenizeField($fromField);

            foreach ($fieldParts as $fieldValue) {
                $fromValue = $fromModel;

                if (strpos($fieldValue, '.') === false && strpos($fieldValue, '->') === false) {
                    if (isset($toModel[$newField])) {
                        $toModel[$newField] .= $fromValue[$fieldValue] ?? $fieldValue;
                    } else {
                        $toModel[$newField] = $fromValue[$fieldValue] ?? $fieldValue;
                    }

                    continue;
                }

                $fromFieldParts = explode('->', $fieldValue);
                foreach ($fromFieldParts as $part) {
                    if (strpos($part, '.') !== false) {
                        $nestedParts = explode('.', $part);
                        $nestedValue = '';
                        foreach ($nestedParts as $nestedPart) {
                            if (! isset($fromValue[$nestedPart])) {
                                throw new KeyNotFoundException($nestedPart);
                            }
                            $nestedValue .= $fromValue[$nestedPart];
                        }
                        $fromValue = $nestedValue;

                        continue;
                    }

                    if (! isset($fromValue[$part])) {
                        throw new KeyNotFoundException($part);
                    }

                    $fromValue = $fromValue[$part];
                }

                if ($fromValue !== null && $fromValue !== '') {
                    if (isset($toModel[$newField])) {
                        $toModel[$newField] .= $fromValue;
                    } else {
                        $toModel[$newField] = $fromValue;
                    }
                }
            }
        }

        return $toModel;
    }

    /**
     * @throws \Oguzhankrcb\DataMigrator\Exceptions\ClassNotFoundException
     *
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_data_from_model_to_model_with_concatenate_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_data_from_model_to_model_with_nested_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_data_from_model_to_model_with_static_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_throws_exception_while_transfering_data_from_model_to_model_with_empty_model()
     */
    public function transferDataModelToModel(
        string $transferToModel,
        array $toModelPrototype,
        Model|array $transferFromModel
    ): Model|null {
        if (! class_exists($transferToModel)) {
            throw new ClassNotFoundException($transferToModel);
        }

        try {
            DB::beginTransaction();

            if ($transferFromModel instanceof Model) {
                $transferFromModel = $transferFromModel->toArray();
            }

            $toModel = $this->transformData($toModelPrototype, $transferFromModel);

            $createdModel = $transferToModel::create($toModel);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return $createdModel;
    }
     * @throws \Oguzhankrcb\DataMigrator\Exceptions\ClassNotFoundException
     *
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_all_data_from_model_to_model_with_concatenate_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_all_data_from_model_to_model_with_nested_keys()
     * @see \Oguzhankrcb\DataMigrator\Tests\Unit\DataMigratorTest::it_transfers_all_data_from_model_to_model_with_static_keys()
     */
    public function transferAllDataFromModelToModel(
        string $transferToModel,
        array $toModelPrototype,
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
