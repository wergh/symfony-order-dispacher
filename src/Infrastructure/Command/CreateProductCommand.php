<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Product\DTO\ProductDTO;
use App\Application\Product\Service\CreateProductService;
use App\Domain\Shared\Exception\ValidationException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:create-producto',
    description: 'Crear un producto desde la consola',
)]
class CreateProductCommand extends AbstractCommand
{
    public function __construct(private CreateProductService $createProductService)
    {
        parent::__construct();
    }

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

            $productDTO = new ProductDTO($nombre, $precio, $impuesto, $stock);

            $this->createProductService->execute($productDTO);

            $output->writeln('<info>Producto creado con éxito.</info>');

        } catch (ValidationException $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
