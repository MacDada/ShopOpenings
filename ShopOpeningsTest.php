<?php

namespace MacDada;

class ShopOpeningsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConfigNeedsToHave7DaysDefined()
    {
        // 6 days configured
        new ShopOpenings([
            [],
            [],
            [],
            [],
            [],
            [],
        ]);
    }

    /**
     * @dataProvider provideInvalidDaysOfWeek
     * @expectedException \InvalidArgumentException
     */
    public function testGetForDayOfWeekValidatesTheArgument($invalidDayOfWeek)
    {
        $openings = new ShopOpenings([
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);

        $openings->getForDayOfWeek($invalidDayOfWeek);
    }

    public function testGetForDayOfWeekWhenNoOpenings()
    {
        // 7 days configured as no openings
        $openings = new ShopOpenings([
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);

        $this->assertSame([], $openings->getForDayOfWeek(1));
        $this->assertSame([], $openings->getForDayOfWeek(2));
        $this->assertSame([], $openings->getForDayOfWeek(3));
        $this->assertSame([], $openings->getForDayOfWeek(4));
        $this->assertSame([], $openings->getForDayOfWeek(5));
        $this->assertSame([], $openings->getForDayOfWeek(6));
        $this->assertSame([], $openings->getForDayOfWeek(7));
    }


    public function testGetForDayOfWeekWithExistingOpenings()
    {
        $openings = new ShopOpenings([
            [],
            [[8, 16]],
            [[10, 18]],
            [[12, 14], [14, 16]], // two openings with a break between
            [],
            [],
            [],
        ]);

        $this->assertSame([], $openings->getForDayOfWeek(1));
        $this->assertSame([[8, 16]], $openings->getForDayOfWeek(2));
        $this->assertSame([[10, 18]], $openings->getForDayOfWeek(3));
        $this->assertSame([[12, 14], [14, 16]], $openings->getForDayOfWeek(4));
        $this->assertSame([], $openings->getForDayOfWeek(5));
        $this->assertSame([], $openings->getForDayOfWeek(5));
        $this->assertSame([], $openings->getForDayOfWeek(6));
        $this->assertSame([], $openings->getForDayOfWeek(7));
    }

    /**
     * @dataProvider provideInvalidDaysOfWeek
     * @expectedException \InvalidArgumentException
     */
    public function testIsOpenOnDayOfWeekValidatesTheArgument($invalidDayOfWeek)
    {
        $openings = new ShopOpenings([
            [],
            [],
            [],
            [],
            [],
            [],
            [],
        ]);

        $openings->isOpenOnDayOfWeek($invalidDayOfWeek);
    }

    public function testIsOpenOnDayOfWeek()
    {
        $openings = new ShopOpenings([
            [],
            [[8, 16]],
            [[10, 18]],
            [[12, 14], [14, 16]], // two openings with a break between
            [],
            [],
            [],
        ]);

        $this->assertFalse($openings->isOpenOnDayOfWeek(1));
        $this->assertTrue($openings->isOpenOnDayOfWeek(2));
        $this->assertTrue($openings->isOpenOnDayOfWeek(3));
        $this->assertTrue($openings->isOpenOnDayOfWeek(4));
        $this->assertFalse($openings->isOpenOnDayOfWeek(5));
        $this->assertFalse($openings->isOpenOnDayOfWeek(6));
        $this->assertFalse($openings->isOpenOnDayOfWeek(7));
    }

    public function provideInvalidDaysOfWeek()
    {
        return [
            [-17],
            [0],
            [8],
        ];
    }
}