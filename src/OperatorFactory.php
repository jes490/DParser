<?php

namespace Jes490\DParser;

use Jes490\DParser\Exceptions\DParseException;
use Jes490\DParser\Operators\BaseOperator;
use Jes490\DParser\Operators\DivideOperator;
use Jes490\DParser\Operators\DOperator;
use Jes490\DParser\Operators\MinusOperator;
use Jes490\DParser\Operators\MultiplyOperator;
use Jes490\DParser\Operators\PlusOperator;

class OperatorFactory
{
    /**
     * Return Corresponding Operator.
     *
     * @param string $operator
     * @return BaseOperator
     * @throws DParseException
     */
    public function operator($operator)
    {
        switch ($operator)
        {
            case '+': return new PlusOperator();
            case '-': return new MinusOperator();
            case '/': return new DivideOperator();
            case '*': return new MultiplyOperator();
            case 'd': return new DOperator();
            default:
                throw new DParseException("Operator '{$operator}' is not supported");
        }
    }
}
