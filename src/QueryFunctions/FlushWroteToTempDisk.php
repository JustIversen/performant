<?php

namespace JustIversen\Performant\QueryFunctions;

/**
 * Checks if a query takes up more memory than allocated, in which case data will be
 * temporarily written to a disk, slowing the performance down drastically.
 */
class FlushWroteToTempDisk implements QueryInterface
{

    public function validate($query)
    {
        foreach ($query->flushCollection as $object) {

            $array = (array) $object;

            foreach ($array as $key => $value) {
                if (in_array('Created_tmp_disk_tables', $array)) {
                    if ($key == 'Value' and $value === 1) {
                        return $this->solution();
                    }
                }
            }
        }
        return false;
    }

    public function solution()
    {
        return "Your query uses more memory than allocated, forcing it to write part of the temporary data to a disk.\n
                This is slowing down the query substantially.\n
                You may want to increase the (tmp_table_size) or (max_heap_table_size) value to lessen the likelihood
                that internal temporary tables in memory will be converted to on-disk tables. \n
                More info -> https://dev.mysql.com/doc/refman/8.0/en/server-status-variables.html#statvar_Created_tmp_disk_tables";

    }

    public function test($collection)
    {
        foreach ($collection as $object) {

            $array = (array) $object;

            foreach ($array as $key => $value) {
                if (in_array('Created_tmp_disk_tables', $array)) {
                    if ($key == 'Value' and $value == 1) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
