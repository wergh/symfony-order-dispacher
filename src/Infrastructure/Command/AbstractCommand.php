<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends Command
{
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

    private function castToType(mixed $value, string $expectedType): mixed
    {
        return match ($expectedType) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => in_array(strtolower((string)$value), ['true', '1', 'yes'], true),
            default => $value // string queda igual
        };
    }


    protected function abortedByUser(mixed $value, OutputInterface $output): bool
    {
        if (is_null($value)) {
            $output->writeln('<comment>Proceso cancelado.</comment>');
            return true;
        }
        return false;
    }
}
