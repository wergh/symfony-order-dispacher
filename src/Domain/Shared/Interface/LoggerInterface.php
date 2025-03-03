<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

/**
 * Interface for logging messages.
 *
 * This interface defines methods for logging messages with different levels of severity.
 * It can be implemented by any logging service to ensure consistent logging behavior across the application.
 *
 * @package App\Domain\Shared\Interface
 */
interface LoggerInterface
{
    /**
     * Logs an informational message.
     *
     * @param string $message The message to log.
     * @param array $context An array of context data to include with the message.
     */
    public function info(string $message, array $context = []): void;

    /**
     * Logs an error message.
     *
     * @param string $message The message to log.
     * @param array $context An array of context data to include with the message.
     */
    public function error(string $message, array $context = []): void;
}
