<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Client\DTO\ClientCreateDto;
use App\Application\Client\Service\CreateClientService;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to create a client via the console.
 *
 * This command will prompt the user for the client's first and last name,
 * then attempt to create a new client by invoking the `CreateClientService`.
 * If successful, the client is created and a success message is displayed.
 * If an error occurs, it is captured and reported.
 *
 * @package App\Infrastructure\Command
 */
#[AsCommand(
    name: 'app:create-cliente',
    description: 'Crear un cliente desde la consola',
)]
class CreateClienteCommand extends AbstractCommand
{
    private MonitoringInterface $monitoring;

    /**
     * CreateClienteCommand constructor.
     *
     * @param CreateClientService $createClientService Service to create clients.
     * @param MonitoringInterface $monitoring Interface for monitoring services like exception capturing.
     */
    public function __construct(private CreateClientService $createClientService, MonitoringInterface $monitoring)
    {
        parent::__construct();
        $this->monitoring = $monitoring;
    }

    /**
     * Executes the command to create a client.
     *
     * This method interacts with the user to get the client's name and surname,
     * and then delegates the creation process to the `CreateClientService`. If
     * the creation is successful, a success message is displayed; otherwise,
     * the error is captured and reported.
     *
     * @param InputInterface  $input  The console input interface.
     * @param OutputInterface $output The console output interface.
     *
     * @return int The command exit status: Command::SUCCESS on success, Command::FAILURE on failure.
     */
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
