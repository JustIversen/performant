<?php

namespace JustIversen\Performant\QueryFunctions;

interface QueryInterface
{
    public function validate($query);
    public function solution();
    public function test($collection);
}
