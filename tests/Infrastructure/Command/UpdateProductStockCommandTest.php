<?php

namespace App\Tests\Infrastructure\Command;

use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Application\Product\Service\UpdateProductStockService;
use App\Domain\Product\Entity\Product;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Command\UpdateProductStockCommand;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateProductStockCommandTest extends TestCase
{
    private $productRepository;
    private $updateProductStockService;
    private $monitoring;
    private $command;
    private $commandTester;
    private $application;

    public function testExecuteSuccess(): void
    {
        $product1 = $this->createMock(Product::class);
        $product1->method('getId')->willReturn(1);
        $product1->method('getName')->willReturn('Product 1');

        $products = new ArrayCollection([$product1]);

        $this->productRepository->method('all')
            ->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('Product 1', 20);

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->updateProductStockService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (UpdateProductStockDTO $dto) {
                return $dto->id === 1 && $dto->stock === 20;
            }));

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Stock acutalizado con éxito', $this->commandTester->getDisplay());
    }

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(DoctrineProductRepository::class);
        $this->updateProductStockService = $this->createMock(UpdateProductStockService::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->command = new UpdateProductStockCommand(
            $this->productRepository,
            $this->updateProductStockService,
            $this->monitoring
        );

        $this->application = new Application();
        $this->application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }
}
