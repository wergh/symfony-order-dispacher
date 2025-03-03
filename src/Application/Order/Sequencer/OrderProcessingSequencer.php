<?php

declare(strict_types=1);

namespace App\Application\Order\Sequencer;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\UseCase\AcceptOrderUseCase;
use App\Application\Order\UseCase\GetOrderToProcessUseCase;
use App\Application\Order\UseCase\RejectOrderUseCase;
use App\Application\Order\UseCase\UpdateStocksUseCase;
use App\Application\Order\UseCase\ValidateStockUseCase;
use App\Domain\Product\Exception\InsufficientStockException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Interface\LoggerInterface;
use App\Domain\Shared\Interface\MonitoringInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class that manages the sequencer for processing orders, including validation and stock updates.
 */
class OrderProcessingSequencer
{
    private MonitoringInterface $monitoring;
    private int $forceOrderProcessError;

    /**
     * OrderProcessingSequencer constructor.
     *
     * @param GetOrderToProcessUseCase $getOrderToProcessUseCase The use case to fetch the order to be processed.
     * @param ValidateStockUseCase $validateStockUseCase The use case to validate stock for the order.
     * @param UpdateStocksUseCase $updateStocksUseCase The use case to update stocks after order processing.
     * @param AcceptOrderUseCase $acceptOrderUseCase The use case to accept the order after processing.
     * @param RejectOrderUseCase $rejectOrderUseCase The use case to reject the order in case of failure.
     * @param EntityManagerInterface $entityManager The entity manager to handle database transactions.
     * @param LoggerInterface $logger The logger to log unexpected errors.
     * @param MonitoringInterface $monitoring The monitoring interface for capturing exceptions.
     * @param ParameterBagInterface $params The parameter bag containing configuration values.
     */
    public function __construct(
        private GetOrderToProcessUseCase $getOrderToProcessUseCase,
        private ValidateStockUseCase     $validateStockUseCase,
        private UpdateStocksUseCase      $updateStocksUseCase,
        private AcceptOrderUseCase       $acceptOrderUseCase,
        private RejectOrderUseCase       $rejectOrderUseCase,
        private EntityManagerInterface   $entityManager,
        private LoggerInterface          $logger,
        MonitoringInterface              $monitoring,
        ParameterBagInterface            $params
    )
    {
        $this->monitoring = $monitoring;
        $this->forceOrderProcessError = $params->get('force_order_process_error');
    }

    /**
     * Process the order based on the given DTO.
     *
     * @param OrderProcessorDto $orderDto The DTO containing the order ID to be processed.
     * @throws Exception If an unexpected error occurs during the order processing.
     */
    public function process(OrderProcessorDto $orderDto): void
    {
        $this->entityManager->beginTransaction();
        try {
            //Hacemos una tirada para ver si hay error o no
            //Si el forceOrderProcessError es 0, dado que el valor minim es 1, nunca podrá ser menor que 0 y nunca fallara
            //Si el forceOrderProcessError es 100, dado que el valor máximo es 100, siempre será igual o menor que 100 y siempre fallará
            //Si el forceOrderProcessError es 50, fallará el 50% de las veces
            if (rand(1, 100) <= $this->forceOrderProcessError) {
                throw new Exception('Forced error');
            }
            $order = $this->getOrderToProcessUseCase->execute($orderDto);
            $order->markAsProcessed();
            $this->validateStockUseCase->execute($order);
            $this->updateStocksUseCase->execute($order);
            $this->acceptOrderUseCase->execute($order);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (InsufficientStockException $e) {
            $this->entityManager->rollback();
            $order->markAsProcessed();
            $this->rejectOrderUseCase->execute($order);
        } catch (EntityNotFoundException $e) {
            $this->entityManager->rollback();
            $this->monitoring->captureException($e);
            throw new Exception($e->getMessage(), 0, $e);
        } catch (Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Unexpected error: ' . $e->getMessage());
            $this->monitoring->captureException($e);
            throw new Exception($e->getMessage(), 0, $e);
        }
    }
}
