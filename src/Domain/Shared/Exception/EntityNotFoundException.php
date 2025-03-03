<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use Exception;

/**
 * Exception thrown when an entity is not found.
 *
 * This exception is used when an attempt to retrieve an entity by its identifier
 * fails, indicating that the entity does not exist in the repository or data store.
 * It can be used across different entities in the domain layer.
 */
class EntityNotFoundException extends Exception
{
}
