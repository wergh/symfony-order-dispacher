<?php

declare(strict_types=1);

namespace App\Application\Order\Service;

use App\Application\Order\DTO\OrderProcessorDto;
use App\Application\Order\Sequencer\OrderProcessingSequencer;

class OrderProcessorService
{

    public function __construct(private OrderProcessingSequencer $orderProcessingSequencer)
    {
    }

    public function execute(OrderProcessorDto $orderProcessorDTO): void
    {
        $this->orderProcessingSequencer->process($orderProcessorDTO);
    }
}
