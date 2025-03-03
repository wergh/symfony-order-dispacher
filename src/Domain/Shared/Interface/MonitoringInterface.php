<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interface;

use Throwable;

interface MonitoringInterface
{
    public function captureException(Throwable $exception): void;

    public function captureMessage(string $message, string $level = 'info'): void;
}
