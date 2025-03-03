<?php

declare(strict_types=1);

namespace App\Application\Order\Service;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Sequencer\OrderProcessingSequencer;
use Exception;

/**
 * Service class for processing an order through a sequencer.
 */
class OrderProcessorService
{
    /**
     * OrderProcessorService constructor.
     *
     * @param OrderProcessingSequencer $orderProcessingSequencer The sequencer responsible for processing the order.
     */
    public function __construct(private OrderProcessingSequencer $orderProcessingSequencer)
    {
    }

    /**
     * Executes the order processing using the order processing sequencer.
     *
     * @param OrderProcessorDto $orderProcessorDTO The DTO containing the order details to be processed.
     * @throws Exception
     */
    public function execute(OrderProcessorDto $orderProcessorDTO): void
    {
        $this->orderProcessingSequencer->process($orderProcessorDTO);
    }
}
