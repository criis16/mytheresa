<?php

namespace Tests\Unit\Domain\Shared;

use PHPUnit\Framework\TestCase;
use App\Domain\Shared\ConvertPriceToCentsService;

class ConvertPriceToCentsServiceTest extends TestCase
{
    private ConvertPriceToCentsService $sut;

    protected function setUp(): void
    {
        $this->sut = new ConvertPriceToCentsService();
    }

    /**
     * @dataProvider priceProvider
     */
    public function testExecuteConvertsPriceToCents(
        float $price,
        int $expectedCents
    ): void {
        $result = $this->sut->execute($price);
        $this->assertSame($expectedCents, $result);
    }

    public static function priceProvider(): array
    {
        return [
            'whole number' => [10.0, 1000],
            'fractional price' => [10.55, 1055],
            'round down' => [10.554, 1055],
            'round up' => [10.556, 1056],
            'zero' => [0.0, 0],
            'small value' => [0.01, 1],
        ];
    }
}
