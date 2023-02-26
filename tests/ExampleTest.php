<?php

namespace Oguzhankrcb\DataMigrator\Tests;

use Oguzhankrcb\DataMigrator\Tests\Models\ModelA;

class ExampleTest extends TestCase
{
    public function test_example()
    {
        $aa = ModelA::factory()->create();

        $this->assertEquals(true, true);
    }
}
