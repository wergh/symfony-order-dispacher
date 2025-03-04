<?php

namespace App\Tests\Infrastructure\Command;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\Service\CreateProductService;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Command\CreateProductCommand;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class CreateProductCommandTest extends TestCase
{
    private $createProductService;
    private $monitoring;
    private $command;
    private $commandTester;
    private $application;

    public function testExecuteSuccess(): void
    {
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(4))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('Test Product', '99.99', '21', '10');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->createProductService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (CreateProductDto $dto) {
                return $dto->name === 'Test Product' &&
                    $dto->price === 99.99 &&
                    $dto->tax === 21 &&
                    $dto->stock === 10;
            }));

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Producto creado con éxito', $this->commandTester->getDisplay());
    }

    public function testExecuteWithAbortedInput(): void
    {
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('exit');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Proceso cancelado', $this->commandTester->getDisplay());
    }

    public function testExecuteWithValidationException(): void
    {
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(4))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('Test Product', '99.99', '21', '10');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $violationList = new ConstraintViolationList([
            new ConstraintViolation(
                'El precio debe ser mayor que cero',
                'El precio debe ser mayor que cero',
                [],
                null,
                'price',
                -1
            )
        ]);

        $exception = new ValidationException($violationList);
        $this->createProductService->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Error', $this->commandTester->getDisplay());
    }

    public function testExecuteWithGenericException(): void
    {
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(4))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('Test Product', '99.99', '21', '10');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $exception = new Exception('Something went wrong');
        $this->createProductService->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Error', $this->commandTester->getDisplay());
    }

    protected function setUp(): void
    {
        $this->createProductService = $this->createMock(CreateProductService::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->command = new CreateProductCommand($this->createProductService, $this->monitoring);

        $this->application = new Application();
        $this->application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }
}
