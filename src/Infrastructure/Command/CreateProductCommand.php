<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\Service\CreateProductService;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Command to create a product via the console.
 *
 * This command prompts the user for product details, such as the product name, price,
 * tax percentage, and stock. It validates the inputs, creates a product using the
 * `CreateProductService`, and provides feedback to the user.
 * If any error occurs during the product creation process, it is captured and reported.
 *
 * @package App\Infrastructure\Command
 */
#[AsCommand(
    name: 'app:create-producto',
    description: 'Crear un producto desde la consola',
)]
class CreateProductCommand extends AbstractCommand
{
    private MonitoringInterface $monitoring;

    /**
     * CreateProductCommand constructor.
     *
     * @param CreateProductService $createProductService Service to create the product.
     * @param MonitoringInterface  $monitoring           Monitoring service to capture errors.
     */
    public function __construct(private CreateProductService $createProductService, MonitoringInterface $monitoring)
    {
        parent::__construct();
        $this->monitoring = $monitoring;
    }

    /**
     * Executes the product creation process.
     *
     * This method prompts the user to input the product's name, price, tax percentage,
     * and stock. It validates the input data and creates the product using the
     * `CreateProductService`. If any error occurs, it will be captured and shown to the user.
     *
     * @param InputInterface  $input  The console input interface.
     * @param OutputInterface $output The console output interface.
     *
     * @return int The command exit status: Command::SUCCESS on success, Command::FAILURE on failure.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nombre = $this->askValidInput($input, $output, 'Nombre del producto');
        if ($this->abortedByUser($nombre, $output)) {
            return Command::FAILURE;
        }

        $precio = $this->askValidInput($input, $output, 'Precio unitario (sin impuestos)', 'float');
        if ($this->abortedByUser($precio, $output)) {
            return Command::FAILURE;
        }

        $impuesto = $this->askValidInput($input, $output, 'Impuesto del producto en %', 'int');
        if ($this->abortedByUser($impuesto, $output)) {
            return Command::FAILURE;
        }

        $stock = $this->askValidInput($input, $output, 'Stock del producto', 'int');
        if ($this->abortedByUser($stock, $output)) {
            return Command::FAILURE;
        }

        try {
            $productDTO = new CreateProductDto($nombre, $precio, $impuesto, $stock);
            $this->createProductService->execute($productDTO);
            $output->writeln('<info>Producto creado con éxito.</info>');
        } catch (ValidationException $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            $this->monitoring->captureException($e);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
