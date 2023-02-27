<?php

namespace Oguzhankrcb\DataMigrator\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Oguzhankrcb\DataMigrator\Tests\Models\ModelB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Oguzhankrcb\DataMigrator\Tests\Models\NewProduct>
 */
class ModelBFactory extends Factory
{
    protected $model = ModelB::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => $this->faker->name,
            'category'  => $this->faker->word(),
            'alias'     => $this->faker->word(),
            'item_code' => $this->faker->numerify('999####'),
            'vat'       => $this->faker->numberBetween(0, 18),
            'status'    => $this->faker->boolean,
        ];
    }
}
