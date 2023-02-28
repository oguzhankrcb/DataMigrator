<?php

namespace Oguzhankrcb\DataMigrator\Tests\Unit;

use Exception;
use Oguzhankrcb\DataMigrator\Facades\DataMigrator;
use Oguzhankrcb\DataMigrator\Tests\Models\ModelA;
use Oguzhankrcb\DataMigrator\Tests\Models\ModelB;
use Oguzhankrcb\DataMigrator\Tests\TestCase;

class DataMigratorTest extends TestCase
{
    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transformData()
     */
    public function it_transforms_data_with_all_type_of_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $toModelPrototype = [
            'id'         => '[id]',
            'unique_id'  => '[unique_number.id]',
            'name'       => '[data->name]',
            'categories' => [
                'first_category'  => '[data->categories->category_2]',
                'second_category' => '[data->categories->category_3]',
            ],
            'alias_with_item_code' => '[data->alias][data->item->code]',
            'alias'                => '[data->alias]',
            'item_code'            => '[data->item->code]',
            'status'               => '[data->status]',
        ];

        $fromModel = (new ModelA([
            'id'            => 1,
            'unique_number' => $this->faker->shuffleString('abcdefghi'),
            'data'          => [
                'name'       => $this->faker->name,
                'alias'      => $this->faker->name,
                'categories' => [
                    'category_1' => $this->faker->word,
                    'category_2' => $this->faker->word,
                    'category_3' => $this->faker->word,
                ],
                'item' => [
                    'code' => $this->faker->numerify('######'),
                ],
                'status' => $this->faker->boolean,
            ],
        ]))->toArray();
        // 2️⃣ Act 🏋🏻‍
        $toModel = DataMigrator::transformData($toModelPrototype, $fromModel);

        // 3️⃣ Assert ✅
        $this->assertEquals(
            [
                'id'         => $fromModel['id'],
                'unique_id'  => $fromModel['unique_number'].$fromModel['id'],
                'name'       => $fromModel['data']['name'],
                'categories' => [
                    'first_category'  => $fromModel['data']['categories']['category_2'],
                    'second_category' => $fromModel['data']['categories']['category_3'],
                ],
                'alias_with_item_code' => $fromModel['data']['alias'].$fromModel['data']['item']['code'],
                'alias'                => $fromModel['data']['alias'],
                'item_code'            => $fromModel['data']['item']['code'],
                'status'               => $fromModel['data']['status'],
            ],
            $toModel
        );
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transformData()
     */
    public function it_transforms_data_with_nested_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $toModelPrototype = [
            'id'         => '[id]',
            'name'       => '[data->name]',
            'categories' => [
                'first_category'  => '[data->categories->category_2]',
                'second_category' => '[data->categories->category_3]',
            ],
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'status'    => '[data->status]',
        ];

        $fromModel = (new ModelA([
            'id'            => 1,
            'unique_number' => $this->faker->shuffleString('abcdefghi'),
            'data'          => [
                'name'       => $this->faker->name,
                'alias'      => $this->faker->name,
                'categories' => [
                    'category_1' => $this->faker->word,
                    'category_2' => $this->faker->word,
                    'category_3' => $this->faker->word,
                ],
                'item' => [
                    'code' => $this->faker->numerify('######'),
                ],
                'status' => $this->faker->boolean,
            ],
        ]))->toArray();
        // 2️⃣ Act 🏋🏻‍
        $toModel = DataMigrator::transformData($toModelPrototype, $fromModel);

        // 3️⃣ Assert ✅
        $this->assertEquals(
            [
                'id'         => $fromModel['id'],
                'name'       => $fromModel['data']['name'],
                'categories' => [
                    'first_category'  => $fromModel['data']['categories']['category_2'],
                    'second_category' => $fromModel['data']['categories']['category_3'],
                ],
                'alias'     => $fromModel['data']['alias'],
                'item_code' => $fromModel['data']['item']['code'],
                'status'    => $fromModel['data']['status'],
            ],
            $toModel
        );
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transformData()
     */
    public function it_transforms_data_with_concatenate_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $toModelPrototype = [
            'unique_id'   => '[id.unique_number]',
            'unique_name' => '[data->name]-lorke-[unique_number]',
        ];

        $fromModel = (new ModelA([
            'id'            => 1,
            'unique_number' => $this->faker->shuffleString('abcdefghi'),
            'data'          => [
                'name' => $this->faker->name,
            ],
        ]))->toArray();
        // 2️⃣ Act 🏋🏻‍
        $toModel = DataMigrator::transformData($toModelPrototype, $fromModel);

        // 3️⃣ Assert ✅
        $this->assertEquals(
            [
                'unique_id'   => $fromModel['id'].$fromModel['unique_number'],
                'unique_name' => $fromModel['data']['name'].'-lorke-'.$fromModel['unique_number'],
            ],
            $toModel
        );
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transformData()
     */
    public function it_transforms_data_with_static_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $toModelPrototype = [
            'unique_id' => $unique_id = $this->faker->uuid.'-unique-id',
        ];

        // 2️⃣ Act 🏋🏻‍
        $toModel = DataMigrator::transformData($toModelPrototype, []);

        // 3️⃣ Assert ✅
        $this->assertEquals(
            [
                'unique_id' => $unique_id,
            ],
            $toModel
        );
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferDataModelToModel()
     */
    public function it_transfers_data_from_model_to_model_with_nested_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory(random_int(5, 10))->create();

        $randomInstance = $model_a_s->random()->first();

        $toModelPrototype = [
            'id'        => '[id]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => '[data->vat]',
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferDataModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: $randomInstance);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, 1);

        $this->assertDatabaseHas(ModelB::class, [
            'id'        => $randomInstance->id,
            'name'      => $randomInstance->data['name'],
            'category'  => $randomInstance->data['category'],
            'alias'     => $randomInstance->data['alias'],
            'item_code' => $randomInstance->data['item']['code'],
            'vat'       => $randomInstance->data['vat'],
            'status'    => $randomInstance->data['status'],
        ]);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferDataModelToModel()
     */
    public function it_transfers_data_from_model_to_model_with_concatenate_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory(random_int(5, 10))->create();

        $randomInstance = $model_a_s->random()->first();

        $toModelPrototype = [
            'id'        => '[id]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => '[data->vat]',
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferDataModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: $randomInstance);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, 1);

        $this->assertDatabaseHas(ModelB::class, [
            'id'        => $randomInstance->id,
            'new_key'   => $randomInstance->id.'_'.$randomInstance->unique_key,
            'name'      => $randomInstance->data['name'],
            'category'  => $randomInstance->data['category'],
            'alias'     => $randomInstance->data['alias'],
            'item_code' => $randomInstance->data['item']['code'],
            'vat'       => $randomInstance->data['vat'],
            'status'    => $randomInstance->data['status'],
        ]);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferDataModelToModel()
     */
    public function it_transfers_data_from_model_to_model_with_static_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory(random_int(5, 10))->create();

        $randomInstance = $model_a_s->random()->first();

        $toModelPrototype = [
            'id'        => '[id]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => $randomVat = $this->faker->numberBetween(0, 18),
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferDataModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: $randomInstance);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, 1);

        $this->assertDatabaseHas(ModelB::class, [
            'id'        => $randomInstance->id,
            'new_key'   => $randomInstance->id.'_'.$randomInstance->unique_key,
            'name'      => $randomInstance->data['name'],
            'category'  => $randomInstance->data['category'],
            'alias'     => $randomInstance->data['alias'],
            'item_code' => $randomInstance->data['item']['code'],
            'vat'       => $randomVat,
            'status'    => $randomInstance->data['status'],
        ]);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferDataModelToModel()
     */
    public function it_throws_exception_while_transfering_data_from_model_to_model_with_empty_model(): void
    {
        // 1️⃣ Arrange 🏗
        ModelA::factory(random_int(5, 10))->create();

        $toModelPrototype = [
            'id'        => '[id]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => $this->faker->numberBetween(0, 18),
            'status'    => '[data->status]',
        ];

        $this->expectException(Exception::class);

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferDataModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: []);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, 0);
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferAllDataFromModelToModel()
     */
    public function it_transfers_all_data_from_model_to_model_with_nested_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory($count = random_int(5, 10))->create();

        $toModelPrototype = [
            'id'        => '[id]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => '[data->vat]',
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferAllDataFromModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: ModelA::class);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, $count);

        $model_a_s->each(function (ModelA $modelA) {
            $this->assertDatabaseHas(ModelB::class, [
                'id'        => $modelA->id,
                'name'      => $modelA->data['name'],
                'category'  => $modelA->data['category'],
                'alias'     => $modelA->data['alias'],
                'item_code' => $modelA->data['item']['code'],
                'vat'       => $modelA->data['vat'],
                'status'    => $modelA->data['status'],
            ]);
        });
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferAllDataFromModelToModel()
     */
    public function it_transfers_all_data_from_model_to_model_with_concatenate_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory($count = random_int(5, 10))->create();

        $toModelPrototype = [
            'id'        => '[id]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => '[data->vat]',
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferAllDataFromModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: ModelA::class);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, $count);

        $model_a_s->each(function (ModelA $modelA) {
            $this->assertDatabaseHas(ModelB::class, [
                'id'        => $modelA->id,
                'new_key'   => $modelA->id.'_'.$modelA->unique_key,
                'name'      => $modelA->data['name'],
                'category'  => $modelA->data['category'],
                'alias'     => $modelA->data['alias'],
                'item_code' => $modelA->data['item']['code'],
                'vat'       => $modelA->data['vat'],
                'status'    => $modelA->data['status'],
            ]);
        });
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferAllDataFromModelToModel()
     */
    public function it_transfers_all_data_from_model_to_model_with_static_keys(): void
    {
        // 1️⃣ Arrange 🏗
        $model_a_s = ModelA::factory($count = random_int(5, 10))->create();

        $toModelPrototype = [
            'id'        => '[id]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => $randomVat = $this->faker->numberBetween(0, 18),
            'status'    => '[data->status]',
        ];

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferAllDataFromModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: ModelA::class);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, $count);

        $model_a_s->each(function (ModelA $modelA) use ($randomVat) {
            $this->assertDatabaseHas(ModelB::class, [
                'id'        => $modelA->id,
                'new_key'   => $modelA->id.'_'.$modelA->unique_key,
                'name'      => $modelA->data['name'],
                'category'  => $modelA->data['category'],
                'alias'     => $modelA->data['alias'],
                'item_code' => $modelA->data['item']['code'],
                'vat'       => $randomVat,
                'status'    => $modelA->data['status'],
            ]);
        });
    }

    /**
     * @test
     *
     * @see \Oguzhankrcb\DataMigrator\Facades\DataMigrator::transferAllDataFromModelToModel()
     */
    public function it_throws_exception_while_transfering_all_data_from_model_to_model_with_empty_model(): void
    {
        // 1️⃣ Arrange 🏗
        ModelA::factory(random_int(5, 10))->create();

        $toModelPrototype = [
            'id'        => '[id_not_exists]',
            'new_key'   => '[id]_[unique_key]',
            'name'      => '[data->name]',
            'category'  => '[data->category]',
            'alias'     => '[data->alias]',
            'item_code' => '[data->item->code]',
            'vat'       => $this->faker->numberBetween(0, 18),
            'status'    => '[data->status]',
        ];

        $this->expectException(Exception::class);

        // 2️⃣ Act 🏋🏻‍
        DataMigrator::transferAllDataFromModelToModel(
            transferToModel: ModelB::class,
            toModelPrototype: $toModelPrototype,
            transferFromModel: ModelA::class);

        // 3️⃣ Assert ✅
        $this->assertDatabaseCount(ModelB::class, 0);
    }
}
