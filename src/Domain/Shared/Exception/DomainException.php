<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use Exception;

/**
 * Base exception for domain-related errors.
 *
 * This exception serves as a base for all exceptions that occur within the domain layer.
 * It can be used to represent business logic violations or other issues that are
 * specific to the domain model and its invariants.
 */
class DomainException extends Exception
{
}
