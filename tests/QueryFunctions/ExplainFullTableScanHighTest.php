<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainFullTableScanHigh;

class ExplainFullTableScanHighTest extends TestCase
{

    /**
     * Will test if there are enough rows being scanned to trigger a FullTableScan error (True).
     * We expect this test to return False since we have 19999 rows, which is less than the required 20,000.
     *
     * @return void
     */
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
     *
     * Will test if there are enough rows being scanned to trigger a FullTableScan error (True).
     * We expect this test to return True since we have 20000 rows, which is equal to the required 20,000.
     *
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
     *
     * We will test if the FullTableScan will be triggered when a wrong type is inputted while enough rows are being processed.
     * We expect this test to return false, since it shouldn't react to any other type than 'all'.
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
