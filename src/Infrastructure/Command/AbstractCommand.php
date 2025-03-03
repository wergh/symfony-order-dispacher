<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Abstract base class for Symfony console commands.
 *
 * This class provides common methods for interacting with the user via the console,
 * such as asking validated inputs and handling process cancellation by the user.
 * Commands extending this class can use these methods to efficiently ask the user for data.
 *
 * @package App\Infrastructure\Command
 */
abstract class AbstractCommand extends Command
{
    /**
     * Asks for a valid user input.
     *
     * This method performs a validation loop to ensure that the entered value
     * matches the expected type. If the value is invalid, it will ask the user again.
     * The method also allows for process cancellation via specific commands.
     *
     * @param InputInterface  $input       The console input object.
     * @param OutputInterface $output      The console output object.
     * @param string          $questionText The question text presented to the user.
     * @param string          $expectedType The expected data type (string, int, float, bool).
     * @param bool            $nullable    Whether the value can be null.
     *
     * @return mixed The value entered by the user, cast to the expected type, or null if the user cancels.
     */
    protected function askValidInput(
        InputInterface  $input,
        OutputInterface $output,
        string          $questionText,
        string          $expectedType = 'string',
        bool            $nullable = false
    ): mixed
    {
        $helper = $this->getHelper('question');

        do {
            $question = new Question("<question>{$questionText} (Escribe 'x', 'quit' o 'exit' para salir): </question>");
            $value = $helper->ask($input, $output, $question);

            if (in_array(strtolower((string)$value), ['x', 'quit', 'exit'], true)) {
                return null;
            }

            if ($nullable && ($value === null || trim((string)$value) === '')) {
                return null;
            }

            if (!$this->isValidType($value, $expectedType)) {
                $output->writeln("<error>El valor ingresado no es un {$expectedType} válido. Inténtalo de nuevo.</error>");
                $value = null;
                continue;
            }

        } while ($value === null);

        return $this->castToType($value, $expectedType);
    }

    /**
     * Validates if the entered value matches the expected type.
     *
     * @param mixed  $value         The value entered by the user.
     * @param string $expectedType  The expected data type (string, int, float, bool).
     *
     * @return bool True if the value matches the expected type, false otherwise.
     */
    private function isValidType(mixed $value, string $expectedType): bool
    {
        return match ($expectedType) {
            'string' => is_string($value) && trim($value) !== '',
            'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT) !== false,
            'bool' => in_array(strtolower((string)$value), ['true', 'false', '1', '0', 'yes', 'no'], true),
            default => false
        };
    }

    /**
     * Casts the value to the expected type.
     *
     * @param mixed  $value         The value entered by the user.
     * @param string $expectedType  The expected data type (string, int, float, bool).
     *
     * @return mixed The value casted to the expected type.
     */
    private function castToType(mixed $value, string $expectedType): mixed
    {
        return match ($expectedType) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => in_array(strtolower((string)$value), ['true', '1', 'yes'], true),
            default => $value
        };
    }

    /**
     * Checks if the process was aborted by the user.
     *
     * If the value is null (which indicates a cancellation), a message is shown informing the user
     * that the process has been canceled.
     *
     * @param mixed $value   The value entered by the user.
     * @param OutputInterface $output The console output object.
     *
     * @return bool True if the process was canceled, false otherwise.
     */
    protected function abortedByUser(mixed $value, OutputInterface $output): bool
    {
        if (is_null($value)) {
            $output->writeln('<comment>Proceso cancelado.</comment>');
            return true;
        }
        return false;
    }
}
