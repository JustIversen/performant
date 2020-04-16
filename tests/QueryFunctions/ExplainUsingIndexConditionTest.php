<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainUsingIndexCondition;

class ExplainUsingIndexConditionTest extends TestCase
{

    /**
     * We're testing if the UsingIndexCondition class will be triggered by the Extra field containing 'Using index condition'.
     * We're expecting the test to return True, since the Extra field does contain 'Using index condition'.
     *
     * @return void
     */
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
     *
     * We're testing if the UsingIndexCondition class will be triggered by the Extra field containing 'Using index condition' amongst other values.
     * We're expecting the test to return True, since the Extra field does contain 'Using index condition'.
     *
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
     *
     * We're testing if the UsingIndexCondition class will be triggered when the Extra field doesn't contain the sentence 'Using index condition'.
     * We're expecting the test to return False, since the Extra field does NOT contain 'Using index condition'.
     *
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
