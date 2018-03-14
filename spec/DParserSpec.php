<?php

namespace spec\Jes490\DParser;

use Jes490\DParser\DParser;
use PhpSpec\ObjectBehavior;
use Jes490\DParser\OperatorFactory;

class DParserSpec extends ObjectBehavior
{
    function let()
    {
        mt_srand(0);
    }

    function it_is_initializable()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->shouldHaveType(DParser::class);
    }

    function it_can_handle_empty_string()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('')->shouldBe(0);
    }

    function it_can_operate_two_operands_plus()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('2+3')->shouldBe(5);
    }

    function it_can_operate_two_operands_minus()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('5-2')->shouldBe(3);
    }

    function it_can_operate_two_operands_multiply()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('5*3')->shouldBe(15);
    }

    function it_can_operate_two_operands_division()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('12/4')->shouldBe(3);
    }

    function it_can_handle_plus_and_minus()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('5+3-1-2+4-1+3')->shouldBe(11);
    }

    function it_can_handle_operators_in_right_order()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('5+3*2-1*4+3+2/2+1-2*1*2/2+3')->shouldBe(13);
    }

    function it_can_handle_dice()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('2d6')->shouldBe(7);
        //$this->getRolls()->shouldIterateAs([4, 3]);
    }

    function it_can_handle_more_dices()
    {
        $this->beConstructedWith(new OperatorFactory());
        $this->roll('2d6+2d6-2d6')->shouldBe(6);
        //$this->getRolls()->shouldIterateAs([4, 2, 1, 6, 4, 3]);
    }

}
