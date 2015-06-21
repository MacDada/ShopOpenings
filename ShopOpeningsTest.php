<?php

namespace MacDada;

use DateTime;

class ShopOpeningsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConfigNeedsToHave7DaysDefined()
    {
        // 6 days configured (7 are required)
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
     * @dataProvider provideInvalidDayConfigs
     * @expectedException \InvalidArgumentException
     */
    public function testOpeningMustHaveAHoursRangeDefined($invalidDayConfig)
    {
        new ShopOpenings([
            [],
            [],
            [],
            $invalidDayConfig,
            [],
            [],
            [],
        ]);
    }

    public function provideInvalidDayConfigs()
    {
        return [
            ['', 'day is string while array should be expected'],
            [[''], 'opening is string while array should be expected'],
            [[[]], 'opening is empty array while hours range should be expected'],
            [[[17]], 'opening contains only one hour while a range should be expected'],
            [[[17, 18, 19]], 'opening contains too many hours while a range should be expected'],

            [[[-1, 17]], 'single opening: invalid hour'],
            [[[7, 25]], 'single opening: invalid hour'],
            [[[20, 10]], 'single opening: closing before opening'],
            [[[12, 12]], 'single opening: opening and closing at the same time'],
            [[['', 12]], 'single opening: not numeric hour'],
            [[[12, '']], 'single opening: not numeric hour'],

            [[[12, 14], [-1, 17]], 'multiple openings: invalid hour'],
            [[[12, 14], [7, 25]], 'multiple openings: invalid hour'],
            [[[12, 14], [18, 17]], 'multiple openings: closing before opening'],
            [[[12, 14], [12, 12]], 'multiple openings: opening and closing at the same time'],
            [[[12, 14], ['', 12]], 'multiple openings: not numeric hour'],
            [[[12, 14], [12, '']], 'multiple openings: not numeric hour'],

            [[[8, 14], [14, 16]], 'first opening closing when second is starting'],
            [[[8, 14], [12, 16]], 'first opening closing after second is starting'],
            [[[14, 16], [8, 12]], 'first opening after second opening'],
        ];
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
            [[12, 14], [15, 16]], // two openings with a break between
            [],
            [],
            [],
        ]);

        $this->assertSame([], $openings->getForDayOfWeek(1));
        $this->assertSame([[8, 16]], $openings->getForDayOfWeek(2));
        $this->assertSame([[10, 18]], $openings->getForDayOfWeek(3));
        $this->assertSame([[12, 14], [15, 16]], $openings->getForDayOfWeek(4));
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
            [[12, 14], [17, 21]], // two openings with a break between
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

    /**
     * @dataProvider provideForIsOpenOn
     */
    public function testIsOpenOn($dateTimeString, $isOpen)
    {
        $openings = new ShopOpenings([
            [],
            [[8, 16]],
            [[10, 18]],
            [[12, 14], [17, 21]],
            [],
            [],
            [[13, 15]],
        ]);

        $this->assertSame($isOpen, $openings->isOpenOn(new DateTime($dateTimeString)));
    }

    public function provideForIsOpenOn()
    {
        return [
            ['15-06-2015 17:00', false, 'monday - no openings defined'],
            ['19-06-2015 17:00', false, 'friday - no openings defined'],
            ['20-06-2015 17:00', false, 'saturday - no openings defined'],

            ['16-06-2015 07:59', false, 'tuesday - before opening'],
            ['17-06-2015 09:59', false, 'wednesday - before opening'],
            ['18-06-2015 09:59', false, 'thursday - before opening'],
            ['21-06-2015 12:59', false, 'sunday - before opening'],

            ['16-06-2015 16:01', false, 'tuesday - after opening'],
            ['17-06-2015 18:01', false, 'wednesday - after opening'],
            ['18-06-2015 21:01', false, 'thursday - after opening'],
            ['21-06-2015 15:01', false, 'sunday - after opening'],

            ['18-06-2015 14:01', false, 'thursday - between openings'],
            ['18-06-2015 16:59', false, 'thursday - between openings'],

            ['16-06-2015 08:00', true, 'tuesday'],
            ['16-06-2015 13:00', true, 'tuesday'],
            ['16-06-2015 16:00', true, 'tuesday'],

            ['17-06-2015 10:00', true, 'wednesday'],
            ['17-06-2015 14:00', true, 'wednesday'],
            ['17-06-2015 18:00', true, 'wednesday'],

            ['17-06-2015 10:00', true, 'wednesday'],
            ['17-06-2015 14:00', true, 'wednesday'],
            ['17-06-2015 18:00', true, 'wednesday'],

            ['18-06-2015 12:00', true, 'thursday first opening'],
            ['18-06-2015 13:00', true, 'thursday first opening'],
            ['18-06-2015 14:00', true, 'thursday first opening'],
            ['18-06-2015 17:00', true, 'thursday second opening'],
            ['18-06-2015 20:00', true, 'thursday second opening'],
            ['18-06-2015 21:00', true, 'thursday second opening'],
        ];
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
