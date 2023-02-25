<?php

namespace Oguzhankrcb\DataMigrator\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Oguzhankrcb\DataMigrator\Tests\Models\ModelA;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_example()
    {
        $aa = ModelA::factory()->create();

        $this->assertEquals(true, true);
    }
}
