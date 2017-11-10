<?php

namespace spec\Jes490\DParser;

use Jes490\DParser\DParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DParserSpec extends ObjectBehavior
{
    function let()
    {
        mt_srand(0);
    }

    function it_is_initializable()
    {
        $this->beConstructedWith('2');
        $this->shouldHaveType(DParser::class);
    }

    function it_can_handle_empty_string()
    {
        $this->beConstructedWith('');
        $this->getResult()->shouldBe('');
    }

    function it_can_operate_two_operands_plus()
    {
        $this->beConstructedWith('2+3');
        $this->getResult()->shouldBe(5);
    }

    function it_can_operate_two_operands_minus()
    {
        $this->beConstructedWith('5-2');
        $this->getResult()->shouldBe(3);
    }

    function it_can_operate_two_operands_multiply()
    {
        $this->beConstructedWith('5*3');
        $this->getResult()->shouldBe(15);
    }

    function it_can_operate_two_operands_division()
    {
        $this->beConstructedWith('12/4');
        $this->getResult()->shouldBe(3);
    }

    function it_can_handle_plus_and_minus()
    {
        $this->beConstructedWith('5+3-1-2+4-1+3');
        $this->getResult()->shouldBe(11);
    }

    function it_can_handle_operators_in_right_order()
    {
        $this->beConstructedWith('5+3*2-1*4+3+2/2+1-2*1*2/2+3');
        $this->getResult()->shouldBe(13);
    }

    function it_can_handle_dice()
    {
        $this->beConstructedWith('2d6');
        $this->getResult()->shouldBe(7);
        $this->getRolls()->shouldIterateAs([4, 3]);
    }

    function it_can_handle_more_dices()
    {
        $this->beConstructedWith('2d6+2d6-2d6');
        $this->getResult()->shouldBe(6);
        $this->getRolls()->shouldIterateAs([4, 2, 1, 6, 4, 3]);
    }

}
