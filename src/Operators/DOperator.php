<?php

namespace Jes490\DParser\Operators;

use Jes490\DParser\DParser;
use Jes490\DParser\Exceptions\DParseException;

class DOperator extends BaseOperator
{
    public $precedence = 3;

    public function execute($operand1, $operand2, DParser $parser)
    {
        if ($operand1 > 100)
            throw new DParseException("Maximum allowed throws is 100.");
        $resultTotal = 0;
        while ($operand1--) {
            $resultRoll = rand(1, $operand2);
            $resultTotal += $resultRoll;
            $parser->rolls[] = $resultRoll;
        }

        return $resultTotal;
    }
}
