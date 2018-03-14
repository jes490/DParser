<?php
/**
 * Created by PhpStorm.
 * User: Jesus
 * Date: 14.03.2018
 * Time: 20:06
 */

namespace Jes490\DParser\Operators;


use Jes490\DParser\DParser;

abstract class BaseOperator
{
    public $precedence = 1;

    /**
     * Execute operator
     *
     * @param integer $operand1
     * @param integer $operand2
     * @param DParser $parser
     *
     * @return integer
     */
    public function execute($operand1, $operand2, DParser $parser)
    {

    }
}
