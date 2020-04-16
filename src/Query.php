<?php


namespace JustIversen\Performant;

class Query
{
    public $query; //SQL statement
    public $explainCollection; //collection
    public $flushCollection; //created_temp_disk = 1
    public $explainJsonCollection;

    /**
     * $arrayOfTests = an array of all parameter-classes that we want to test.
     * Classes are prioritized high->low in regards to criticality/importance.
     */
    private $arrayOfTests = [
        \JustIversen\Performant\QueryFunctions\ExplainFullTableScanHigh::class,
        \JustIversen\Performant\QueryFunctions\ExplainUsingIndexCondition::class,
        \JustIversen\Performant\QueryFunctions\FlushWroteToTempDisk::class,
        \JustIversen\Performant\QueryFunctions\ExplainUsingFileSort::class
    ];


    /**
     * Will analyze a query against the sorted $arrayOfTests.
     * If a test is negative (returns false) it will go on the the next test in the array.
     * If a test is positive it will take the solution message and present to the user.
     *
     * @return void
     */
    public function analyzeQuery()
    {
        foreach ($this->arrayOfTests as $function) {
            $result = (new $function)->validate($this);
            if ($result !== false) {
                return $result;
            break;
            }
        }
        return "The analysis we just ran didn't find anything wrong with your query.";
    }
}
