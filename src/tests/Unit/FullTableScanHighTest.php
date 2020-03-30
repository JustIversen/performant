<?php

namespace JustIversen\Performant\Test;

use JustIversen\Performant;

use PHPUnit\Framework\TestCase;

class FullTableScanHighTest extends TestCase
{
    // docker-compose exec app php vendor/bin/phpunit tests/Unit/FullTableScanHighTest.php
    
    
    public function testToFewRowsToBeTriggered() : bool
    {      
        $testData = new Collection([
            ['id' => 1],
            ['select_type' => 'SIMPLE'],
            ['table' => 'airlines'],
            ['partitions' => NULL],
            ['type' => 'all'],
            ['possible_keys' => 'PRIMARY'],
            ['key' => 'PRIMARY'],
            ['key_len' => '6'],
            ['ref' =>'const'],
            ['rows' => 18000],
            ['filtered' => 100],
            ['Extra' => NULL],
        ]);
        
        $testClass = ExplainFullTableScanHigh::class;

        $expected = false;
        $actual = $this->testClass()->validate($testData);

        $this->assertEquals( 
            $expected, 
            $actual, 
            "actual value is not equals to expected"
        ); 
    }    
}