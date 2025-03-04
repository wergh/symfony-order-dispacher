<?php

namespace App\Tests\Application\Order\UseCase;

use App\Application\Order\DTO\CreateOrderDto;
use App\Application\Order\DTO\OrderConceptDto;
use App\Application\Order\UseCase\CreateOrderUseCase;
use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Tests\Infrastructure\Factory\MockRepositoryFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateOrderUseCaseTest extends TestCase
{
    private MockRepositoryFactory $mockRepositoryFactory;
    private OrderRepositoryInterface $orderRepository;
    private ClientRepositoryInterface $clientRepository;
    private ProductRepositoryInterface $productRepository;
    private ValidatorInterface $mockValidator;
    private CreateOrderUseCase $useCase;

    protected function setUp(): void
    {
        $this->mockRepositoryFactory = new MockRepositoryFactory();
        $this->orderRepository = $this->mockRepositoryFactory->createOrderRepository();
        $this->clientRepository = $this->mockRepositoryFactory->createClientRepository();
        $this->productRepository = $this->mockRepositoryFactory->createProductRepository();
        $this->mockValidator = $this->createMock(ValidatorInterface::class);
        $this->useCase = new CreateOrderUseCase($this->mockRepositoryFactory, $this->mockValidator);
    }


    public function testValidatorException()
    {
        $dto = new CreateOrderDto(-1, []);
        $violations = new ConstraintViolationList([
            $this->createMock(\Symfony\Component\Validator\ConstraintViolation::class)
        ]);

        $this->mockValidator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn($violations);

        $this->expectException(ValidationException::class);

        $this->useCase->execute($dto);

    }

    public function testClientNotFound(): void
    {
        $orderConcept = new OrderConceptDto( 1, 1);
        $dto = new CreateOrderDto(1, [$orderConcept]);


        $this->mockValidator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn(new ConstraintViolationList());

        $this->mockRepositoryFactory->expectClientRepositoryFindById(1, null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Client not found');

        $this->useCase->execute($dto);
    }

    public function testProductNotFound(): void
    {
        $orderConcept = new OrderConceptDto( 1, 1);
        $dto = new CreateOrderDto(1, [$orderConcept]);

        $client = $this->createMock(Client::class);

        $this->mockValidator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn(new ConstraintViolationList());

        $this->mockRepositoryFactory->expectClientRepositoryFindById(1, $client);
        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Product not found');

        $this->useCase->execute($dto);
    }

    public function testSuccessfulOrderCreation(): void
    {
        $orderConcept = new OrderConceptDto( 1, 1);
        $dto = new CreateOrderDto(1, [$orderConcept]);

        $client = $this->createMock(Client::class);
        $product1 = $this->createMock(Product::class);
        $product1->method('getId')->willReturn(1);
        $product1->method('getName')->willReturn('Product 1');
        $product1->method('getPrice')->willReturn(10.0);
        $product1->method('getTax')->willReturn(21);

        $product2 = $this->createMock(Product::class);
        $product2->method('getId')->willReturn(2);
        $product2->method('getName')->willReturn('Product 2');
        $product2->method('getPrice')->willReturn(20.0);
        $product2->method('getTax')->willReturn(21);

        $this->mockValidator->expects($this->once())
            ->method('validate')
            ->with($dto)
            ->willReturn(new ConstraintViolationList());

        $this->mockRepositoryFactory->expectClientRepositoryFindById(1, $client);
        $this->mockRepositoryFactory->expectProductRepositoryFindById(1, $product1);

        $this->mockRepositoryFactory->expectOrderRepositorySave($this->callback(function(Order $order) use ($client) {
            $this->assertSame($client, $order->getClient());
            $this->assertCount(1, $order->getConcepts());
            return true;
        }));

        $result = $this->useCase->execute($dto);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertSame($client, $result->getClient());
        $this->assertCount(1, $result->getConcepts());
    }
}
