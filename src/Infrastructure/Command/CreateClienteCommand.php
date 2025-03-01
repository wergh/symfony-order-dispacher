<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Client\DTO\ClientDTO;
use App\Application\Client\Service\CreateClientService;
use Cassandra\Exception\ValidationException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:create-cliente',
    description: 'Crear un cliente desde la consola',
)]
class CreateClienteCommand extends AbstractCommand
{
    public function __construct(private CreateClientService $createClientService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nombre = $this->askValidInput($input, $output, 'Nombre del cliente', 'string');
        if ($this->abortedByUser($nombre, $output)) {
            return Command::FAILURE;
        }

        $apellido = $this->askValidInput($input, $output, 'Apellido del cliente', 'string');
        if ($this->abortedByUser($apellido, $output)) {
            return Command::FAILURE;
        }

        try {
            // Crear el DTO
            $clientDTO = new ClientDTO($nombre, $apellido);

            // Llamar al servicio de aplicación
            $this->createClientService->execute($clientDTO);

            $output->writeln('<info>Cliente creado con éxito.</info>');
        } catch (ValidationException $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}

