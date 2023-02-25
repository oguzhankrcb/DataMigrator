<?php

namespace Oguzhankrcb\DataMigrator\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Oguzhankrcb\DataMigrator\Tests\Models\ModelA;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Oguzhankrcb\DataMigrator\Tests\Models\ModelA>
 */
class ModelAFactory extends Factory
{
    protected $model = ModelA::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data' => [
                'name' => $this->faker->name,
                'category' => $this->faker->word(),
                'alias' => $this->faker->word(),
                'item_code' => $this->faker->numerify('999####'),
                'vat' => $this->faker->numberBetween(0, 18),
                'status' => $this->faker->boolean,
            ],
        ];
    }
}
