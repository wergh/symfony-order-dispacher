<?php

namespace App\Tests\Infrastructure\Command;

use App\Infrastructure\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommandTest extends TestCase
{
    private $concreteCommand;

    public function testAskValidInputWithValidString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('valid string');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->concreteCommand->setHelperSet($helperSet);

        $result = $this->concreteCommand->publicAskValidInput(
            $input,
            $output,
            'Test question',
            'string'
        );

        $this->assertEquals('valid string', $result);
    }

    public function testAskValidInputWithInvalidString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $output->expects($this->once())
            ->method('writeln')
            ->with('<error>El valor ingresado no es un string válido. Inténtalo de nuevo.</error>');

        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('', 'valid string');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->concreteCommand->setHelperSet($helperSet);

        $result = $this->concreteCommand->publicAskValidInput(
            $input,
            $output,
            'Test question',
            'string'
        );

        $this->assertEquals('valid string', $result);
    }

    public function testAskValidInputWithValidInt(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('123');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->concreteCommand->setHelperSet($helperSet);

        $result = $this->concreteCommand->publicAskValidInput(
            $input,
            $output,
            'Test question',
            'int'
        );

        $this->assertEquals(123, $result);
        $this->assertIsInt($result);
    }

    public function testAskValidInputWithExitCommand(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('exit');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->concreteCommand->setHelperSet($helperSet);

        $result = $this->concreteCommand->publicAskValidInput(
            $input,
            $output,
            'Test question',
            'string'
        );

        $this->assertNull($result);
    }

    public function testAbortedByUserWithNull(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $output->expects($this->once())
            ->method('writeln')
            ->with('<comment>Proceso cancelado.</comment>');

        $result = $this->concreteCommand->publicAbortedByUser(null, $output);

        $this->assertTrue($result);
    }

    public function testAbortedByUserWithValue(): void
    {
        $output = $this->createMock(OutputInterface::class);

        $output->expects($this->never())
            ->method('writeln');

        $result = $this->concreteCommand->publicAbortedByUser('some value', $output);

        $this->assertFalse($result);
    }

    protected function setUp(): void
    {
        $this->concreteCommand = new class extends AbstractCommand {
            protected static $defaultName = 'app:test-command';

            protected function configure(): void
            {
                $this
                    ->setName('app:test-command')
                    ->setDescription('Test command');
            }

            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                return Command::SUCCESS;
            }

            public function publicAskValidInput(
                InputInterface  $input,
                OutputInterface $output,
                string          $question,
                string          $type = 'string',
                bool            $nullable = false
            )
            {
                return $this->askValidInput($input, $output, $question, $type, $nullable);
            }

            public function publicAbortedByUser($value, OutputInterface $output): bool
            {
                return $this->abortedByUser($value, $output);
            }
        };
    }
}
