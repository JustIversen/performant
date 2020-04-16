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
        if ($query->explainCollection->contains('type', 'ALL') and $query->explainCollection->contains(
            function ($array) {
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
     *
     * @return void
     */
    public function solution()
    {
        return "The query scanned the entire table/s records (More than {$this->minRows} records in this case) to meet the given requirements.\n
                This usually results in suboptimal performance and can in most cases be fixed by adding an index.\n
                If an index exists but isn't used, try adding FORCE INDEX to your query.\n
                More info -> https://dev.mysql.com/doc/refman/8.0/en/table-scan-avoidance.html";
    }

    /**
     * Test Function made for testing the validate function.
     * Instead of returning an error message, will instead return true if triggered or false if not.
     *
     * @param [type] $collection
     * @return boolean
     */
    public function test($collection): bool
    {
        if ($collection->contains('type', 'ALL') and $collection->contains(
            function ($array) {
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
