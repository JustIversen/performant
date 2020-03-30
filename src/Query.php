<?php


namespace JustIversen\Performant;

class Query
{
    public $query; //SQL statement
    public $explainCollection; //collection
    public $flushCollection; //created_temp_disk = 1

    /**
     * $arrayOfTests = an array of all parameter-classes that we want to test.
     * Classes are prioritized high->low in regards to criticality/importance.
     */
    private $arrayOfTests = [
        \JustIversen\Performant\QueryFunctions\ExplainFullTableScanHigh::class, 
        \JustIversen\Performant\QueryFunctions\FlushWroteToTempDisk::class,
        \JustIversen\Performant\QueryFunctions\ExplainUsingFileSort::class
    ];
    

    public function analyzeQuery() 
    {
        foreach($this->arrayOfTests as $function){
            $result = (new $function)->validate($this);
            if($result !== false){
                return $result;
            break;
            }
        }
    }

}
