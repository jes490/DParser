<?php

namespace Jes490\DParser;

class DParser
{
    protected $operators = [];

    protected function initializeOperators()
    {
        $this->operators['+'] = $this->plusOperator();
        $this->operators['-'] = $this->minusOperator();
        $this->operators['/'] = $this->divideOperator();
        $this->operators['*'] = $this->multiplyOperator();
        $this->operators['d'] = $this->diceOperator();
    }

    protected function plusOperator()
    {
        return [
            "precedence" => 1, "exec" => function ($a, $b) { return $a + $b; }
        ];
    }

    protected function minusOperator()
    {
        return [
            "precedence" => 1, "exec" => function ($a, $b) { return $a - $b; }
        ];
    }

    protected function multiplyOperator()
    {
        return [
            "precedence" => 2, "exec" => function ($a, $b) { return $a * $b; }
        ];
    }

    protected function divideOperator()
    {
        return [
            "precedence" => 2, "exec" => function ($a, $b) { return $a / $b; }
        ];
    }

    protected function diceOperator()
    {
        return [
            "precedence" => 3, "exec" => function ($rolls, $sides, $roll)
            {
                if ($rolls > 100)
                    throw new DParseException("Maximum allowed throws is 100.");
                $resultTotal = 0;
                while ($rolls--) {
                    $resultRoll = rand(1, $sides);
                    $resultTotal += $resultRoll;
                    array_push($roll->rolls, $resultRoll);
                }

                return $resultTotal;
            }
        ];
    }

    protected $whitespaces = [
        ' '     => true,
        '\t'    => true,
    ];

    protected $numbersStack = [];

    protected $operatorsStack = [];

    protected $position = 0;

    protected $lookahead = '';

    protected $sourceString;

    protected $length;

    public $rolls = [];

    public $result;

    public function __construct(string $source)
    {
        $this->initializeOperators();
        $this->sourceString = $source;
        $this->length = strlen($source);
        $this->roll();
    }

    public function __toString() : string
    {
        return $this->numberStack[0];
    }

    protected function roll()
    {
        $this->tokenize();
        $this->executeAll();
        $this->result = $this->numbersStack[0];
    }

    protected function tokenize()
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

    protected function nextToken()
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

    protected function fetchNumber()
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

    protected function executeAll()
    {
        //while stack is not empty
        while ( $idx = count($this->operatorsStack) )
        {
            //cycle each operator in stack from right to left
            while ( --$idx >= 0 )
            {
                $this->execute($this->operatorsStack[$idx], $idx);
            }
        }
    }

    protected function execute($token, $index)
    {
        //if not last operator
        if ($index+1 < count($this->operatorsStack))
        {
            //compare pair of operators, if right one's precedence bigger, execute it
            //else skip it to the first index and execute first
            $last = $this->operatorsStack[$index+1];
            if ($last['precedence'] > $token['precedence'])
                $this->operate($index+1);
            else if ($index == 0)
                $this->operate($index);
        }
        //else just execute it
        else if ($index == 0)
            $this->operate($index);
        else
            throw DParseException("Internal Error.");
    }

    protected function operate($index)
    {
        //find corresponding operands [first operand, second operand, this object]
        $couplet = array_slice($this->numbersStack, $index, $index+2);
        $couplet[] = $this;
        //remove operator from stack and return corresponding closure
        $operator = $this->operators[array_splice($this->operatorsStack, $index, 1)[0]['value']];
        //replace two operands with one calculated
        array_splice($this->numbersStack, $index, 2, $operator['exec'](...$couplet));
    }
}