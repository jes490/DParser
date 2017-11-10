<?php

namespace Jes490\DParser;

use Jes490\DParser\Exceptions\DParseException;

/**
 * Class DParser
 * @package Jes490\DParser
 */
class DParser
{
    /**
     * List of supported operators.
     * @var array
     */
    protected $operators = [];

    /**
     * Operators Initialization.
     */
    protected function initializeOperators()
    {
        $this->operators['+'] = $this->plusOperator();
        $this->operators['-'] = $this->minusOperator();
        $this->operators['/'] = $this->divideOperator();
        $this->operators['*'] = $this->multiplyOperator();
        $this->operators['d'] = $this->diceOperator();
    }

    /**
     * Plus Operator Settings
     * @return array
     */
    private function plusOperator()
    {
        return [
            "precedence" => 1,
            "exec" => function ($a, $b) { return $a + $b; }
        ];
    }

    /**
     * Minus Operator Settings
     * @return array
     */
    private function minusOperator()
    {
        return [
            "precedence" => 1,
            "exec" => function ($a, $b) { return $a - $b; }
        ];
    }

    /**
     * Multiply Operator Settings
     * @return array
     */
    private function multiplyOperator()
    {
        return [
            "precedence" => 2,
            "exec" => function ($a, $b) { return $a * $b; }
        ];
    }

    /**
     * Division Operator Settings
     * @return array
     */
    private function divideOperator()
    {
        return [
            "precedence" => 2,
            "exec" => function ($a, $b) { return $a / $b; }
        ];
    }

    /**
     * Dice Operator Settings
     * @return array
     */
    private function diceOperator()
    {
        return [
            "precedence" => 3,
            "exec" => function ($rolls, $sides, $instance)
            {
                if ($rolls > 100)
                    throw new DParseException("Maximum allowed throws is 100.");
                $resultTotal = 0;
                while ($rolls--) {
                    $resultRoll = rand(1, $sides);
                    $resultTotal += $resultRoll;
                    $instance->rolls[] = $resultRoll;
                }

                return $resultTotal;
            }
        ];
    }

    /**
     * Ignorable Characters
     * @var array
     */
    protected $whitespaces = [
        ' '     => true,
        '\t'    => true,
    ];

    /**
     * Stack of Expression's Numbers
     * @var array
     */
    private $numbersStack = [];

    /**
     * Stack of Expression's Operators
     * @var array
     */
    private $operatorsStack = [];

    /**
     * Current Position Index
     * @var int
     */
    private $position = 0;

    /**
     * Current Position Character
     * @var string
     */
    private $lookahead = '';

    /**
     * Original String
     * @var string
     */
    private $sourceString;

    /**
     * Length of Expression
     * @var int
     */
    private $length;

    /**
     * Total Result of Expression
     * @var
     */
    private $result;

    /**
     * Results of all the Rolls
     * @var array
     */
    private $rolls = [];

    /**
     * DParser constructor. Initialize all data and roll expression.
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->initializeOperators();
        $this->sourceString = $source;
        $this->length = strlen($source);

        try
        {
            $this->roll();
        }
        catch (DParseException $exception)
        {
            $this->errorHandler($exception);
        }

    }

    /**
     * Returns Total Result of Expression
     * @return string
     */
    public function __toString() : string
    {
        return $this->getResult();
    }


    /**
     * Get expression result
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get all rolls
     * @return array
     */
    public function getRolls()
    {
        return array_reverse($this->rolls);
    }

    /**
     * Parser Exception Handler.
     * @param $exception
     */
    protected function errorHandler($exception)
    {
        $this->result = $exception->getMessage();
    }

    /**
     * Tokenize all characters. Populates numbers stack and operators stack.
     * @throws DParseException
     */
    private function tokenize()
    {
        //check all symbols
        for ( ; $this->position < $this->length; $this->position++)
        {
            //currentSymbol
            $this->lookahead = $this->sourceString[$this->position];
            //skip whitespaces
            if (array_key_exists($this->lookahead, $this->whitespaces))
                continue;
            //get token
            $token = $this->nextToken();
            //push to numbers stack
            if ($token['type'] === 'number')
                $this->numbersStack[] = $token['value'];
            else if ($token['type'] === 'operator')
            {
                //check if there are wrong chains
                if (count($this->operatorsStack) > 0)
                {
                    $last = $this->operatorsStack[count($this->operatorsStack)-1];
                    if ($token['value'] === 'd' && $last['value'] === 'd')
                        throw new DParseException("Can't create chains of 'd'.");
                }
                //push to operators stack
                $this->operatorsStack[] = $token;
            }
        }
    }

    /**
     * Returns next token from the expression string.
     * @return array
     * @throws DParseException
     */
    private function nextToken()
    {
        $symbol     = $this->lookahead;
        $token      = ['value' => $symbol];
        //input overflow
        if ($this->position >= $this->length)
            throw new DParseException("Unexpected end of input.");
        //check if symbol is numeric
        if (is_numeric($symbol))
        {
            //fetch number and set token type to number
            $number = $this->fetchNumber();
            $token['type'] = 'number';
            $token['value'] = $number['value'];
            //add number length to position
            $this->position += $number['length'];
        }
        //then it's operator
        else if (array_key_exists($symbol, $this->operators))
        {
            $token['type'] = 'operator';
            $token['precedence'] = $this->operators[$symbol]['precedence'];
        }
        //if it's neither, then throw exception
        else
            throw new DParseException("Wrong symbol '{$symbol}'' at position {$this->position}.");

        return $token;
    }

    /**
     * Fetches number from the expression string.
     * @return array
     * @throws DParseException
     */
    private function fetchNumber()
    {
        $offset = 0;
        $number = '';
        //while digits
        while (is_numeric(($symbol = $this->sourceString[$this->position+$offset])))
        {
            $number .= $symbol;
            $offset++;
            //if end of input
            if ($this->position+$offset > $this->length-1)
                break;
        }
        //something went wrong
        if (strlen($number) === 0)
            throw new DParseException("Unfinished operation. Number needed at position {$this->position}.");

        //return number value and number length
        return ['value' => intval($number), 'length' => $offset-1];
    }

    /**
     * Executes Expression.
     */
    private function roll()
    {
        $this->tokenize();
        //while stack is not empty
        while ( $idx = count($this->operatorsStack) )
        {
            //cycle each operator in stack from right to left
            while ( --$idx >= 0 )
            {
                $this->tryExecute($this->operatorsStack[$idx], $idx);
            }
        }
        if (count($this->numbersStack) > 0)
            $this->result = $this->numbersStack[0];
        else
            $this->result = '';
    }

    /**
     * Tries to execute given operator.
     * @param $operator
     * @param $index
     */
    private function tryExecute($operator, $index)
    {
        //if not last operator
        if ($index+1 < count($this->operatorsStack))
        {
            //compare pair of operators, if right one's precedence bigger, execute it
            //else skip it to the first index and execute first
            $last = $this->operatorsStack[$index+1];
            if ($last['precedence'] > $operator['precedence'])
                $this->execute($index+1);
            else if ($index == 0)
                $this->execute($index);
        }
        //else just execute it
        else if ($index == 0)
            $this->execute($index);
    }

    /**
     * Executes given operator with corresponding operands.
     * @param $index
     */
    private function execute($index)
    {
        //find corresponding operands [first operand, second operand, this object]
        $couplet = array_slice($this->numbersStack, $index, 2);
        $couplet[] = $this;
        //remove operator from stack and return corresponding closure
        $operator = $this->operators[array_splice($this->operatorsStack, $index, 1)[0]['value']];
        //replace two operands with one calculated
        array_splice($this->numbersStack, $index, 2, $operator['exec'](...$couplet));
    }
}