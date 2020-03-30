<?php 

namespace JustIversen\Performant\QueryFunctions;

/**
 * Check if a query does a full-table scan and scans more than 20.000 rows.
 */
class ExplainUsingFileSort implements QueryInterface {

    public function validate($query)
    {
        if($query->explainCollection->contains('type','const'))
        {
            return $this->solution();
        }
        return false;
    }

    public function solution()
    {
        return "Your query writes to the disk instead of only your memory";
    }

}