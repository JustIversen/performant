<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Check if a query does a full-table scan and scans more than 20.000 rows.
 */
class ExplainUsingFilesort implements QueryInterface
{

    public function validate($query)
    {
        $extraString = '';
        foreach ($query->explainCollection as $item) {
            foreach ($item as $key => $value) {
                if ($key === 'Extra') {
                    $extraString = $value;
                }
            }
        }
        if (strpos($extraString, 'Using filesort') !== false) {
            return $this->solution();
        } else {
            return false;
        }
    }

    public function solution()
    {
        return "Your query is using (filesort) which means it's sorting your data in a temporary table before returning it to you.
        It can be improved by creating an index for your query.";
    }

    public function test($collection)
    {
        $extraString = '';
        foreach ($collection as $item) {
            foreach ($item as $key => $value) {
                if ($key === 'Extra') {
                    $extraString = $value;
                }
            }
        }
        if (strpos($extraString, 'Using filesort') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
