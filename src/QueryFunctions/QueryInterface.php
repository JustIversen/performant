<?php

namespace JustIversen\Performant\QueryFunctions;

interface QueryInterface
{
    // The validate function should evaluate a parameter and return either
    // the solution in case something is wrong, or false to say that all's good.
    public function validate($query);
    public function solution();
    public function test($collection);
}
