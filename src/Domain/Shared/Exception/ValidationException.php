<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Exception thrown when validation fails.
 *
 * This exception is used to indicate that an object or data has failed validation.
 * It holds a list of constraint violations, which can be retrieved to check the
 * specific validation errors that occurred.
 *
 * @package App\Domain\Shared\Exception
 */
class ValidationException extends Exception
{
    private ConstraintViolationListInterface $violations;

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface $violations The list of validation violations.
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct('Validation failed');
        $this->violations = $violations;
    }

    /**
     * Get the list of validation violations.
     *
     * @return ConstraintViolationListInterface The list of violations.
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
