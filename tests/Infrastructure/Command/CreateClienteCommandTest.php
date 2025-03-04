<?php

namespace App\Tests\Infrastructure\Command;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\Service\CreateClientService;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Command\CreateClienteCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class CreateClienteCommandTest extends TestCase
{
    private $createClientService;
    private $monitoring;
    private $command;
    private $commandTester;
    private $application;

    public function testExecuteSuccess(): void
    {
        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('John', 'Doe');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->createClientService->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (ClientCreateDto $dto) {
                return $dto->name === 'John' && $dto->surname === 'Doe';
            }));

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Cliente creado con éxito', $this->commandTester->getDisplay());
    }

    public function testExecuteWithAbortedFirstInput(): void
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

        $questionHelper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('John', 'Doe');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $violationList = new ConstraintViolationList([
            new ConstraintViolation(
                'El nombre no puede estar vacío',
                'El nombre no puede estar vacío',
                [],
                null,
                'name',
                ''
            )
        ]);

        $exception = new ValidationException($violationList);
        $this->createClientService->expects($this->once())
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
        $this->createClientService = $this->createMock(CreateClientService::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->command = new CreateClienteCommand($this->createClientService, $this->monitoring);

        $this->application = new Application();
        $this->application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }
}
