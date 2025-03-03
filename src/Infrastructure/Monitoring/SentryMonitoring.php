<?php

namespace App\Infrastructure\Monitoring;

use App\Domain\Shared\Interface\MonitoringInterface;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Throwable;

/**
 * Sentry implementation of the MonitoringInterface.
 *
 * This class adapts the Sentry error tracking service to the application's
 * monitoring interface, providing exception and message capture capabilities.
 */
class SentryMonitoring implements MonitoringInterface
{
    /**
     * The Sentry Hub instance responsible for sending events to Sentry.
     *
     * @var HubInterface
     */
    private HubInterface $sentryHub;

    /**
     * Constructor.
     *
     * @param HubInterface $sentryHub The Sentry Hub instance to use for sending events
     */
    public function __construct(HubInterface $sentryHub)
    {
        $this->sentryHub = $sentryHub;
    }

    /**
     * Captures an exception and sends it to Sentry.
     *
     * @param Throwable $exception The exception to capture
     * @return void
     */
    public function captureException(Throwable $exception): void
    {
        $this->sentryHub->captureException($exception);
    }

    /**
     * Captures a message and sends it to Sentry with the specified severity level.
     *
     * @param string $message The message to capture
     * @param string $level The severity level (debug, info, warning, error, fatal)
     * @return void
     */
    public function captureMessage(string $message, string $level = 'info'): void
    {
        $severity = match ($level) {
            'debug' => Severity::debug(),
            'info' => Severity::info(),
            'warning' => Severity::warning(),
            'error' => Severity::error(),
            'fatal' => Severity::fatal(),
            default => Severity::info(),
        };

        $this->sentryHub->captureMessage($message, $severity);
    }
}
