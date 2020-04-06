<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Check if a query does a full-table scan and scans more than 20.000 rows.
 */
class FlushWroteToTempDisk implements QueryInterface
{

    public function validate($query)
    {
        if ($query->flushCollection->contains('type', 'const')) {
            return $this->solution();
        }
        return false;
    }

    public function solution()
    {
        return "Your query writes to the disk instead of only your memory";
        // TODO write a better error message and potentially add an option that allows the
        // program to create the solution/index for them.
    }

    public function test($collection)
    {
    }
}
