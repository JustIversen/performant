<?php

namespace JustIversen\Performant\Test;

use PHPUnit\Framework\TestCase;
use \JustIversen\Performant\QueryFunctions\FlushWroteToTempDisk;

class FlushWroteToTempDiskTest extends TestCase
{
    /**
     * We're testing if the WroteToTempDisk class will be triggered if there's no
     * 'Created_tmp_disk_tables' found in the Flush status.
     * We're expecting this test to return False since it should only return True
     * if 'Created_tmp_disk_tables' is 1.
     *
     * @return void
     */
    public function testTmpDiskNotPresent()
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

    /**
     * We're testing if the WroteToTempDisk class will be triggered when
     * 'Created_tmp_disk_tables' holds the value 0.
     * We're expecting this test to return False since it should only return True
     * if 'Created_tmp_disk_tables' is 1.
     *
     * @return void
     */
    public function testTmpDiskNotUsed()
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

    /**
     * We're testing if the WroteToTempDisk class will be triggered when
     * 'Created_tmp_disk_tables' holds the value 1.
     * We're expecting this test to return True since it should return True
     * if 'Created_tmp_disk_tables' is 1. 
     *
     * @return void
     */
    public function testTmpDiskUsed()
    {
        $testSetOne = new \stdClass();
        $testSetOne->Variable_name = "Created_tmp_disk_tables";
        $testSetOne->Value = 1;

        $testSetTwo = new \stdClass();
        $testSetTwo->Variable_name = "Ssl_server_not_after";
        $testSetTwo->Value = "Jan 26 09:13:46 2030 GMT";

        $testSetThree = new \stdClass();
        $testSetThree->Variable_name = "Table_open_cache_hits";
        $testSetThree->Value = 3;

        $testSetFour = new \stdClass();
        $testSetFour->Variable_name = "Uptime";
        $testSetFour->Value = 108286;

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
