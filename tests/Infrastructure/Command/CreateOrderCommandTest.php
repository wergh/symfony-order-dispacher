<?php

namespace App\Tests\Infrastructure\Command;

use App\Application\Order\Command\CreateOrderCommandHandler;
use App\Application\Order\DTO\CreateOrderDto;
use App\Domain\Client\Entity\Client;
use App\Domain\Product\Entity\Product;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Command\CreateOrderCommand;
use App\Infrastructure\Persistence\Doctrine\Client\DoctrineClientRepository;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class CreateOrderCommandTest extends TestCase
{
    private $clientRepository;
    private $productRepository;
    private $handler;
    private $params;
    private $monitoring;
    private $command;
    private $commandTester;
    private $application;

    public function testExecuteWithNoClients(): void
    {

        $this->clientRepository->method('all')
            ->willReturn(new ArrayCollection());

        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $output->expects($this->once())
            ->method('writeln')
            ->with('<error>No hay clientes disponibles.</error>');

        $result = $this->invokeMethod($this->command, 'execute', [$input, $output]);

        $this->assertEquals(Command::FAILURE, $result);
    }

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    public function testExecuteWithNoProducts(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $this->clientRepository->method('all')
            ->willReturn($clients);

        $this->productRepository->method('all')
            ->willReturn(new ArrayCollection());

        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('John Doe');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $output->expects($this->once())
            ->method('writeln')
            ->with('<error>No hay productos disponibles.</error>');

        $result = $this->invokeMethod($this->command, 'execute', [$input, $output]);

        $this->assertEquals(Command::FAILURE, $result);
    }

    public function testExecuteWithInvalidClient(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $this->clientRepository->method('all')
            ->willReturn($clients);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('Invalid Client');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Cliente no válido', $this->commandTester->getDisplay());
    }

    public function testExecuteWithNoSelectedProducts(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Product 1');
        $product->method('getStock')->willReturn(10);

        $products = new ArrayCollection([$product]);

        $this->clientRepository->method('all')->willReturn($clients);
        $this->productRepository->method('all')->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('John Doe', 'Terminar');

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Debe agregar al menos un producto a la orden', $this->commandTester->getDisplay());
    }

    public function testExecuteSuccess(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $product1 = $this->createMock(Product::class);
        $product1->method('getId')->willReturn(1);
        $product1->method('getName')->willReturn('Product 1');
        $product1->method('getStock')->willReturn(10);

        $products = new ArrayCollection([$product1]);

        $this->clientRepository->method('all')->willReturn($clients);
        $this->productRepository->method('all')->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(3))
            ->method('ask')
            ->willReturnOnConsecutiveCalls(
                'John Doe',
                'Product 1 (Stock: 10)',
                5,
            );

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (CreateOrderDto $dto) {
                return $dto->clientId === 1 &&
                    count($dto->concepts) === 1 &&
                    $dto->concepts[0]->productId === 1 &&
                    $dto->concepts[0]->quantity === 5;
            }));

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Orden creada con éxito', $this->commandTester->getDisplay());
    }

    public function testExecuteWithValidationException(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Product 1');
        $product->method('getStock')->willReturn(10);

        $products = new ArrayCollection([$product]);

        $this->clientRepository->method('all')->willReturn($clients);
        $this->productRepository->method('all')->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(3))
            ->method('ask')
            ->willReturnOnConsecutiveCalls(
                'John Doe',
                'Product 1 (Stock: 10)',
                5,
            );

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $violationList = new ConstraintViolationList([
            new ConstraintViolation(
                'La cantidad debe ser mayor que cero',
                'La cantidad debe ser mayor que cero',
                [],
                null,
                'concepts[0].quantity',
                0
            )
        ]);

        $exception = new ValidationException($violationList);
        $this->handler->expects($this->once())
            ->method('handle')
            ->willThrowException($exception);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('La cantidad debe ser mayor que cero', $this->commandTester->getDisplay());
    }

    public function testExecuteWithEntityNotFoundException(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Product 1');
        $product->method('getStock')->willReturn(10);

        $products = new ArrayCollection([$product]);

        $this->clientRepository->method('all')->willReturn($clients);
        $this->productRepository->method('all')->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(3))
            ->method('ask')
            ->willReturnOnConsecutiveCalls(
                'John Doe',
                'Product 1 (Stock: 10)',
                5,
            );

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $exception = new EntityNotFoundException('Entity not found');
        $this->handler->expects($this->once())
            ->method('handle')
            ->willThrowException($exception);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Entity not found', $this->commandTester->getDisplay());
    }

    public function testExecuteWithGenericException(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);
        $client->method('getName')->willReturn('John');
        $client->method('getSurname')->willReturn('Doe');

        $clients = new ArrayCollection([$client]);

        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Product 1');
        $product->method('getStock')->willReturn(10);

        $products = new ArrayCollection([$product]);

        $this->clientRepository->method('all')->willReturn($clients);
        $this->productRepository->method('all')->willReturn($products);

        $questionHelper = $this->getMockBuilder(QuestionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $questionHelper->expects($this->exactly(3))
            ->method('ask')
            ->willReturnOnConsecutiveCalls(
                'John Doe',
                'Product 1 (Stock: 10)',
                5,
            );

        $helperSet = $this->createMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSet->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $this->command->setHelperSet($helperSet);

        $exception = new Exception('Something went wrong');
        $this->handler->expects($this->once())
            ->method('handle')
            ->willThrowException($exception);

        $this->monitoring->expects($this->once())
            ->method('captureException')
            ->with($exception);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Something went wrong', $this->commandTester->getDisplay());
    }

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(DoctrineClientRepository::class);
        $this->productRepository = $this->createMock(DoctrineProductRepository::class);
        $this->handler = $this->createMock(CreateOrderCommandHandler::class);
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->monitoring = $this->createMock(MonitoringInterface::class);

        $this->params->method('get')
            ->with('check_stock_before_adding_product')
            ->willReturn(true);

        $this->command = new CreateOrderCommand(
            $this->clientRepository,
            $this->productRepository,
            $this->handler,
            $this->params,
            $this->monitoring
        );

        $this->application = new Application();
        $this->application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }


}
