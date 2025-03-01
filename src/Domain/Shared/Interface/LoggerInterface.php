<?php

declare(strict_types=1);

namespace App\Domain\Shared\Logger;

interface LoggerInterface
{
    public function info(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}
