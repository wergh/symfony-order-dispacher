<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use App\Domain\Shared\Interface\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class MonologAdapter implements LoggerInterface
{
    public function __construct(private PsrLoggerInterface $logger) {}

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
