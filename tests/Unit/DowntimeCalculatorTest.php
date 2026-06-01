<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DowntimeCalculator;

class DowntimeCalculatorTest extends TestCase
{
    public function testCalculateReturnsMinutes(): void
    {
        $result = DowntimeCalculator::calculate('2024-01-15', '08:00', '2024-01-15', '10:30');
        $this->assertEquals(150, $result);
    }

    public function testCalculateReturnsZeroWhenNoEndDate(): void
    {
        $result = DowntimeCalculator::calculate('2024-01-15', '08:00', null, null);
        $this->assertEquals(0, $result);
    }

    public function testCalculateReturnsZeroWhenEndBeforeStart(): void
    {
        $result = DowntimeCalculator::calculate('2024-01-15', '10:00', '2024-01-15', '08:00');
        $this->assertEquals(0, $result);
    }

    public function testCalculateSameDayMultipleHours(): void
    {
        $result = DowntimeCalculator::calculate('2024-01-15', '08:00', '2024-01-15', '17:00');
        $this->assertEquals(540, $result);
    }

    public function testCalculateDifferentDays(): void
    {
        $result = DowntimeCalculator::calculate('2024-01-15', '08:00', '2024-01-16', '08:00');
        $this->assertEquals(1440, $result);
    }

    public function testFormatHours(): void
    {
        $this->assertEquals('0h 0min', DowntimeCalculator::formatHours(0));
        $this->assertEquals('1h 0min', DowntimeCalculator::formatHours(60));
        $this->assertEquals('2h 30min', DowntimeCalculator::formatHours(150));
        $this->assertEquals('8h 0min', DowntimeCalculator::formatHours(480));
    }

    public function testClassifyCorto(): void
    {
        $this->assertEquals('corto', DowntimeCalculator::classify(30));
        $this->assertEquals('corto', DowntimeCalculator::classify(60));
    }

    public function testClassifyMedio(): void
    {
        $this->assertEquals('medio', DowntimeCalculator::classify(61));
        $this->assertEquals('medio', DowntimeCalculator::classify(480));
    }

    public function testClassifyLargo(): void
    {
        $this->assertEquals('largo', DowntimeCalculator::classify(481));
        $this->assertEquals('largo', DowntimeCalculator::classify(1440));
    }

    public function testClassifyCritico(): void
    {
        $this->assertEquals('critico', DowntimeCalculator::classify(1441));
        $this->assertEquals('critico', DowntimeCalculator::classify(2880));
    }
}
