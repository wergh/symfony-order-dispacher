<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\Service\CreateClientService;
use App\Domain\Shared\Interface\MonitoringInterface;
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
    private MonitoringInterface $monitoring;

    public function __construct(private CreateClientService $createClientService, MonitoringInterface $monitoring)
    {
        parent::__construct();
        $this->monitoring = $monitoring;
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
            $clientDTO = new ClientCreateDto($nombre, $apellido);

            $this->createClientService->execute($clientDTO);

            $output->writeln('<info>Cliente creado con éxito.</info>');
        } catch (ValidationException $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            $this->monitoring->captureException($e);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

}

