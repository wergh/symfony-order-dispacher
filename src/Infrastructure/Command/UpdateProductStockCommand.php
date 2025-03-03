<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Product\DTO\CreateProductDto;
use App\Application\Product\DTO\UpdateProductStockDTO;
use App\Application\Product\Service\UpdateProductStockService;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:update-stock',
    description: 'Updatea el stock de un producto',
)]
class UpdateProductStockCommand extends AbstractCommand
{
    private MonitoringInterface $monitoring;

    public function __construct(
        private DoctrineProductRepository $productRepository,
        private UpdateProductStockService $updateProductStockService,
        MonitoringInterface               $monitoring
    )
    {
        parent::__construct();
        $this->monitoring = $monitoring;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $helper = $this->getHelper('question');

        $products = $this->productRepository->all();

        $productChoices = [];

        foreach ($products as $product) {
            $productChoices[$product->getId()] = $product->getName();
        }

        $productQuestion = new ChoiceQuestion('<question>Seleccione un producto:</question>', $productChoices);
        $productName = $helper->ask($input, $output, $productQuestion);
        $productId = array_search($productName, $productChoices, true);

        $selectedProduct = $products->filter(function ($product) use ($productId) {
            return $product->getId() === $productId;
        })->first();

        if (!$selectedProduct) {
            $output->writeln('<error>Producto no válido.</error>');
            return Command::FAILURE;
        }

        $stock = $this->askValidInput($input, $output, 'Nuevo stock del producto', 'int');
        if ($this->abortedByUser($stock, $output)) {
            return Command::FAILURE;
        }

        try {

            $productDTo = new UpdateProductStockDTO($selectedProduct->getId(), $stock);

            $this->updateProductStockService->execute($productDTo);

            $output->writeln('<info>Stock acutalizado con éxito.</info>');

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
