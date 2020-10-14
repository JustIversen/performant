<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Checks if a query's ORDER BY requirement is fulfilled by using filesort.
 * If this is the case, the process can usually be improved by using an index for sorting the query data.
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
        return "Your query isn't using an index to obtain your 'ORDER BY' requirement. \n
                Instead, it's using the slower filesort to satisfy your ORDER by.\n
                This means that it's going through and sorting your data in a temporary table before returning it to you.\n
                This can be a lot of extra work which could be eliminated by having an index for your query.\n
                More info -> https://dev.mysql.com/doc/refman/8.0/en/order-by-optimization.html";
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
