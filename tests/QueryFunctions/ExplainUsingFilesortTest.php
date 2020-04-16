<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainUsingFilesort;

class ExplainUsingFilesortTest extends TestCase
{

    /**
     * We're testing if the UsingFileSort class will be triggered by the Extra field containing 'Using filesort'.
     * We're expecting the test to return True, since the Extra field does contain 'Using filesort'.
     *
     * @return void
     */
    public function testExtraContainsUsingFileSort()
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
            ['Extra' => 'Using filesort'],
        ]);

        $class = new ExplainUsingFilesort;

        $this->assertTrue($class->test($testData));
    }

    /**
     * @depends testExtraContainsUsingFileSort
     *
     * We're testing if the UsingFileSort class will be triggered by the Extra field containing 'Using filesort' amongst other values.
     * We're expecting the test to return True, since the Extra field does contain 'Using filesort'.
     *
     */
    public function testExtraContainsUsingFilesortSeveralValues()
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
            ['Extra' => 'Using where; Using index condition; Using filesort'],
        ]);

        $class = new ExplainUsingFilesort;

        $this->assertTrue($class->test($testData));
    }

    /**
     * @depends testExtraContainsUsingFilesortSeveralValues
     *
     * We're testing if the UsingFileSort class will be triggered when the Extra field doesn't contain the sentence 'Using filesort'.
     * We're expecting the test to return False, since the Extra field does NOT contain 'Using filesort'.
     *
     */
    public function testExtraDoesNotContainsUsingFilesort()
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

        $class = new ExplainUsingFilesort;

        $this->assertFalse($class->test($testData));
    }
}
