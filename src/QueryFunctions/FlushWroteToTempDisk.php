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
        return "Your query uses more memory than allocated, forcing it to write part of the tmeporary data to a disk.
        This is slowing down the task substantially. You may want to increase the (tmp_table_size) or (max_heap_table_size)
        value to lessen the likelihood that internal temporary tables in memory will be converted to on-disk tables.";
        // TODO write a better error message and potentially add an option that allows the
        // program to create the solution/index for them.
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
