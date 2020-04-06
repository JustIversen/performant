<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainFullTableScanHigh;

class ExplainFullTableScanHighTest extends TestCase
{
    // docker-compose exec app php vendor/phpunit/phpunit/phpunit packages/JustIversen/tests/QueryFunctions/ExplainFullTableScanHighTest.php

    public function testTooFewRowsToBeTriggered()
    {
        $testData = new \Illuminate\Database\Eloquent\Collection([
            ['id' => 1],
            ['select_type' => 'SIMPLE'],
            ['table' => 'airlines'],
            ['partitions' => NULL],
            ['type' => 'all'],
            ['possible_keys' => 'PRIMARY'],
            ['key' => 'PRIMARY'],
            ['key_len' => '6'],
            ['ref' => 'const'],
            ['rows' => 19999],
            ['filtered' => 100],
            ['Extra' => NULL],
        ]);

        $class = new ExplainFullTableScanHigh;

        $this->assertFalse($class->test($testData));
    }

    /**
     * @depends testTooFewRowsToBeTriggered
     */
    public function testEnoughRowsToBeTriggered()
    {
        $testData = new \Illuminate\Database\Eloquent\Collection([
            ['id' => 1],
            ['select_type' => 'SIMPLE'],
            ['table' => 'airlines'],
            ['partitions' => NULL],
            ['type' => 'all'],
            ['possible_keys' => 'PRIMARY'],
            ['key' => 'PRIMARY'],
            ['key_len' => '6'],
            ['ref' => 'const'],
            ['rows' => 20000],
            ['filtered' => 100],
            ['Extra' => NULL],
        ]);

        $class = new ExplainFullTableScanHigh;

        $this->assertTrue($class->test($testData));
    }

    /**
     * @depends testEnoughRowsToBeTriggered
     */
    public function testWontBeTriggeredWithWrongType()
    {
        $testData = new \Illuminate\Database\Eloquent\Collection([
            ['id' => 1],
            ['select_type' => 'SIMPLE'],
            ['table' => 'airlines'],
            ['partitions' => NULL],
            ['type' => 'const'],
            ['possible_keys' => 'PRIMARY'],
            ['key' => 'PRIMARY'],
            ['key_len' => '6'],
            ['ref' => 'const'],
            ['rows' => 25000],
            ['filtered' => 100],
            ['Extra' => NULL],
        ]);

        $class = new ExplainFullTableScanHigh;

        $this->assertFalse($class->test($testData));
    }
}
