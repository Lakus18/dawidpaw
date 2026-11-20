<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(string $celsius, string $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);

        $this->assertEqualsWithDelta((float) $expectedFahrenheit, $measurement->getFahrenheit(), 1e-9);
    }

    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['0.5', '32.9'],
            ['-0.5', '31.1'],
            ['1.25', '34.25'],
            ['-1.25', '29.75'],
            ['10.5', '50.9'],
            ['-40', '-40'],
            ['37', '98.6'],
        ];
    }
}
