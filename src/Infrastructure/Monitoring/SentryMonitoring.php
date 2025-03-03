<?php

namespace App\Infrastructure\Monitoring;

use App\Domain\Shared\Interface\MonitoringInterface;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Throwable;

class SentryMonitoring implements MonitoringInterface
{

    private HubInterface $sentryHub;

    public function __construct(HubInterface $sentryHub)
    {
        $this->sentryHub = $sentryHub;
    }

    public function captureException(Throwable $exception): void
    {
        $this->sentryHub->captureException($exception);
    }

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
