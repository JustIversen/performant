<?php

namespace JustIversen\Performant\UnitTestFiles\Test;

use App;
use PHPUnit\Framework\TestCase;

class ExplainCollectionTest extends TestCase
{
    // docker-compose exec app php vendor/phpunit/phpunit/phpunit tests/Unit/Jobs/ExportProjectCsvTest.php
    public function testTrueAssetsToTrue()
    {
        $condition = true;
        $this->assertTrue($condition);
    }

    // Should return true if collection has 'type' =
    /*public function testFullTableScanHigh() : void
    {



        $expected = 'quia sit nulla dolor tenetur';
        $actual = \Bimshark\Models\Project::find(19);

        $this->assertEquals(
            $expected,
            $actual->project_name,
            "actual value is not equals to expected"
        );
    }*/

    /**
     *  @depends testGetProjectByID
     */
    /*public function testRetrieveUsers()
    {
        $eloquentData = \Bimshark\Models\Project::find(19)->users()->get()->toArray();
        $expectedEmail = 'ursula.kiehn@example.com';
        $actualEmail = null;

        foreach($eloquentData as $key=>$value){
            $actualEmail = $value['email'];
        }

        $this->assertEquals(
            $expectedEmail,
            $actualEmail,
            'Values are not equal'
        );
    }*/


}
