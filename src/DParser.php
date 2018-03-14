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
     * OperatorFactory.
     *
     * @var OperatorFactory
     */
    protected $operatorFactory;

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
    public $numbersStack = [];

    /**
     * Stack of Expression's Operators
     * @var array
     */
    public $operatorsStack = [];

    /**
     * Current Position Index
     * @var int
     */
    public $position = 0;

    /**
     * Current Position Character
     * @var string
     */
    public $lookahead = '';

    /**
     * Original String
     * @var string
     */
    public $sourceString;

    /**
     * Length of Expression
     * @var int
     */
    public $length;

    /**
     * Total Result of Expression
     * @var
     */
    public $result;

    /**
     * Results of all the Rolls
     * @var array
     */
    public $rolls = [];

    /**
     * DParser constructor. Initialize all data and roll expression.
     * @param OperatorFactory $factory
     */
    public function __construct(OperatorFactory $factory)
    {
        $this->operatorFactory = $factory;
    }

    /**
     * Executes Expression.
     */
    public function roll($source)
    {
        $this->sourceString = $source;
        $this->length = strlen($source);

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

        return intval((string) $this);
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
        else if ($operator = $this->operatorFactory->operator($symbol))
        {
            $token['type'] = 'operator';
            $token['precedence'] = $operator->precedence;
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
        $operator = $this->operatorFactory->operator(array_splice($this->operatorsStack, $index, 1)[0]['value']);
        //replace two operands with one calculated
        array_splice($this->numbersStack, $index, 2, $operator->execute(...$couplet));
    }
}
