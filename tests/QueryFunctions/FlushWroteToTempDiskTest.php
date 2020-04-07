<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\FlushWroteToTempDisk;

class FlushWroteToTempDiskTest extends TestCase
{
    // docker-compose exec app php vendor/phpunit/phpunit/phpunit packages/JustIversen/tests/QueryFunctions/FlushWroteToTempDiskTest.php

    public function testDiskNotFound()
    {
        $testSetTwo = new \stdClass();
        $testSetTwo->Variable_name = "Ssl_server_not_after";
        $testSetTwo->Value = "Jan 26 09:13:46 2030 GMT";

        $testSetThree = new \stdClass();
        $testSetThree->Variable_name = "Table_open_cache_hits";
        $testSetThree->Value = "3";

        $testSetFour = new \stdClass();
        $testSetFour->Variable_name = "Uptime";
        $testSetFour->Value = "108286";

        $testArray = [
            1 => $testSetTwo,
            2 => $testSetThree,
            3 => $testSetFour
        ];

        $class = new FlushWroteToTempDisk;

        $this->assertFalse($class->test($testArray));
    }

    public function testDiskNotUsed()
    {
        $testSetOne = new \stdClass();
        $testSetOne->Variable_name = "Created_tmp_disk_tables";
        $testSetOne->Value = "0";

        $testSetTwo = new \stdClass();
        $testSetTwo->Variable_name = "Ssl_server_not_after";
        $testSetTwo->Value = "Jan 26 09:13:46 2030 GMT";

        $testSetThree = new \stdClass();
        $testSetThree->Variable_name = "Table_open_cache_hits";
        $testSetThree->Value = "3";

        $testSetFour = new \stdClass();
        $testSetFour->Variable_name = "Uptime";
        $testSetFour->Value = "108286";

        $testArray = [
            0 => $testSetOne,
            1 => $testSetTwo,
            2 => $testSetThree,
            3 => $testSetFour
        ];

        $class = new FlushWroteToTempDisk;

        $this->assertFalse($class->test($testArray));
    }

    public function testDiskUsed()
    {
        $testSetOne = new \stdClass();
        $testSetOne->Variable_name = "Created_tmp_disk_tables";
        $testSetOne->Value = "1";

        $testSetTwo = new \stdClass();
        $testSetTwo->Variable_name = "Ssl_server_not_after";
        $testSetTwo->Value = "Jan 26 09:13:46 2030 GMT";

        $testSetThree = new \stdClass();
        $testSetThree->Variable_name = "Table_open_cache_hits";
        $testSetThree->Value = "3";

        $testSetFour = new \stdClass();
        $testSetFour->Variable_name = "Uptime";
        $testSetFour->Value = "108286";

        $testArray = [
            0 => $testSetOne,
            1 => $testSetTwo,
            2 => $testSetThree,
            3 => $testSetFour
        ];

        $class = new FlushWroteToTempDisk;

        $this->assertTrue($class->test($testArray));
    }
}
