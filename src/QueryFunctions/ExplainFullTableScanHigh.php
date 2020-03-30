<?php 

namespace JustIversen\Performant\QueryFunctions;

/**
 * Check if a query does a full-table scan and scans more than 20.000 rows.
 */
class ExplainFullTableScanHigh implements QueryInterface {

    private $minRows = 20000;

    public function validate($query)
    {
        if($query->explainCollection->contains('type','all') and $query->explainCollection->contains(function ($array,$index)
        {
            foreach($array as $parameter=>$value){
                if($parameter == 'rows'){
                    if($value<$this->minRows){
                        return true;
                    }
                }
            }
            return false;
        }))
        {
            return $this->solution();
        }
        return false;
    }

    public function solution()
    {
        return 'The query scanned the entire table/s to find matching rows for the join. This is the worst performing join. Try adding a index to your table/s ';
    }

}