<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainUsingIndexCondition;

class ExplainUsingIndexConditionTest extends TestCase
{
    // docker-compose exec app php vendor/phpunit/phpunit/phpunit packages/JustIversen/tests/QueryFunctions/ExplainUsingIndexConditionTest.php

    public function testExtraContainsUsingIndexConditionSingleValue()
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
            ['Extra' => 'Using index condition'],
        ]);

        $class = new ExplainUsingIndexCondition;

        $this->assertTrue($class->test($testData));
    }

    /**
     * @depends testExtraContainsUsingIndexConditionSingleValue
     */
    public function testExtraContainsUsingIndexConditionSeveralValues()
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
            ['Extra' => 'Using where; Using index condition; Using index'],
        ]);

        $class = new ExplainUsingIndexCondition;

        $this->assertTrue($class->test($testData));
    }

    /**
     * @depends testExtraContainsUsingIndexConditionSeveralValues
     */
    public function testExtraDoesNotContainsUsingIndexCondition()
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
            ['Extra' => 'Using where; Using index'],
        ]);

        $class = new ExplainUsingIndexCondition;

        $this->assertFalse($class->test($testData));
    }

}
