<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Checks if a Explain Query returns 'Using index condition' in which case only parts of the request has a index.
 */
class ExplainUsingIndexCondition implements QueryInterface
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
        if (strpos($extraString, 'Using index condition') !== false) {
            return $this->solution();
        } else {
            return false;
        }
    }

    public function solution()
    {
        return "Your query didn't have an index covering all your requirements. \n
                This resulted in an 'Index Push-down' (ICP) instead of a covering index. \n
                We recommend creating an index containing all columns used in your 'SELECT' and 'WHERE' clauses. \n
                More info -> https://dev.mysql.com/doc/refman/5.6/en/index-condition-pushdown-optimization.html";
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
        if (strpos($extraString, 'Using index condition') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
