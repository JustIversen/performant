<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Check if a query does a full-table scan and scans more than 20.000 rows.
 * The 20,000 rows is a abitrary number we've selected.
 * Can be changed in $minRows.
 */
class ExplainFullTableScanHigh implements QueryInterface
{

    private $minRows = 20000;

    /**
     * Checks if query scans entire index AND scans more than 20,000 Rows.
     *
     * @param [type] $query
     * @return void
     */
    public function validate($query)
    {
        if ($query->explainCollection->contains('type', 'all') and $query->explainCollection->contains(
            function ($array, $index) {
                foreach ($array as $parameter => $value) {
                    if ($parameter == 'rows') {
                        if ($value >= $this->minRows) {
                            return true;
                        }
                    }
                }
                return false;
            }
        )) {
            return $this->solution();
        } else {
            return false;
        }
    }

    /**
     * Error/Optimization message.
     * Will be returned in case this is triggered "worst" error/point of optimization. 
     *
     * @return void
     */
    public function solution()
    {
        return 'The query scanned the entire table/s to find matching rows for the join.
        This is the worst performing join. Try adding a index to your table/s ';
        // TODO write a better error message and potentially add an option that allows the
        // program to create the solution/index for them.
    }

    /**
     * Test Function made for testing the validate function.
     * Instead of returning an error message, will instead return true if triggered or false if not.
     *
     * @param [type] $collection
     * @return boolean
     */
    public function test($collection) : bool
    {
        if ($collection->contains('type', 'all') and $collection->contains(
            function ($array, $index) {
                foreach ($array as $parameter => $value) {
                    if ($parameter == 'rows') {
                        if ($value >= $this->minRows) {
                            return true;
                        }
                    }
                }
                return false;
            }
        )) {
            return true;
        } else {
            return false;
        }
    }
}
