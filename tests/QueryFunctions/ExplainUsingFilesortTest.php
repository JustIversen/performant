<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\ExplainUsingFilesort;

class ExplainUsingFilesortTest extends TestCase
{
    // docker-compose exec app php vendor/phpunit/phpunit/phpunit packages/JustIversen/tests/QueryFunctions/ExplainUsingFilesortTest.php

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
