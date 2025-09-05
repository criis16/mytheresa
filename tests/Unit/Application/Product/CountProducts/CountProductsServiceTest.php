<?php

namespace Tests\Unit\Application\Product\CountProducts;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\CountProducts\CountProductsService;

class CountProductsServiceTest extends TestCase
{
    private CountProductsService $sut;

    /** @var RepositoryInterface&MockObject */
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->sut = new CountProductsService(
            $this->repository
        );
    }

    public function testExecuteWorksCorrectly(): void
    {
        $criteria = ['a criteria'];
        $expectedResult = 5;

        $this->repository->expects(self::once())
            ->method('countProducts')
            ->with($criteria)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->sut->execute($criteria));
    }
}
