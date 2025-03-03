<?php

declare(strict_types=1);

namespace App\Infrastructure\Logger;

use App\Domain\Shared\Interface\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Adapter class for the Monolog logger.
 *
 * This class implements the application's LoggerInterface and adapts it to use
 * a PSR-compatible logger implementation.
 */
class MonologAdapter implements LoggerInterface
{
    /**
     * Constructor.
     *
     * @param PsrLoggerInterface $logger The PSR-compatible logger to adapt
     */
    public function __construct(private PsrLoggerInterface $logger)
    {
    }

    /**
     * Log an informational message.
     *
     * @param string $message The message to log
     * @param array $context Additional context data to include in the log
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message The error message to log
     * @param array $context Additional context data to include in the log
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
