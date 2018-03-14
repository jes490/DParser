<?php

namespace Jes490\DParser\Operators;

use Jes490\DParser\DParser;

class MultiplyOperator extends BaseOperator
{
    public $precedence = 2;

    public function execute($operand1, $operand2, DParser $parser)
    {
        return $operand1 * $operand2;
    }
}

