<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

use Throwable;

/**
 * Interface for monitoring and capturing exceptions and messages.
 *
 * This interface defines methods for capturing exceptions and messages for monitoring purposes.
 * It allows for consistent monitoring behavior across the application.
 *
 * @package App\Domain\Shared\Interface
 */
interface MonitoringInterface
{
    /**
     * Captures an exception for monitoring purposes.
     *
     * @param Throwable $exception The exception to capture.
     */
    public function captureException(Throwable $exception): void;

    /**
     * Captures a message for monitoring purposes.
     *
     * @param string $message The message to capture.
     * @param string $level The severity level of the message (e.g., 'info', 'warning', 'error').
     *                      Default is 'info'.
     */
    public function captureMessage(string $message, string $level = 'info'): void;
}
