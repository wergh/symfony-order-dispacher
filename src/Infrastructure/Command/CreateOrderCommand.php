<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Order\Command\CreateOrderCommandHandler;
use App\Application\Order\DTO\OrderConceptDTO;
use App\Application\Order\DTO\OrderDTO;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Infrastructure\Persistence\Doctrine\Client\DoctrineClientRepository;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:create-order',
    description: 'Crear una orden desde la consola',
)]
class CreateOrderCommand extends AbstractCommand
{
    private bool $checkStockBeforeAdding;
    public function __construct(
        private DoctrineClientRepository $clientRepository,
        private DoctrineProductRepository $productRepository,
        private CreateOrderCommandHandler $handler,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
        $this->checkStockBeforeAdding = (bool) $params->get('check_stock_before_adding_product');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $clients = $this->clientRepository->all();
        if (empty($clients)) {
            $output->writeln('<error>No hay clientes disponibles.</error>');
            return Command::FAILURE;
        }

        $clientChoices = [];
        foreach ($clients as $client) {
            $clientChoices[$client->getId()] = $client->getName() . ' ' . $client->getSurname();
        }

        $clientQuestion = new ChoiceQuestion('<question>Seleccione un cliente:</question>', $clientChoices);
        $clientName = $helper->ask($input, $output, $clientQuestion);
        $clientId = array_search($clientName, $clientChoices, true);

        $selectedClient = $clients->filter(function ($client) use ($clientId) {
            return $client->getId() === $clientId;
        })->first();

        if (!$selectedClient) {
            $output->writeln('<error>Cliente no válido.</error>');
            return Command::FAILURE;
        }

        $products = $this->productRepository->all();
        if (empty($products)) {
            $output->writeln('<error>No hay productos disponibles.</error>');
            return Command::FAILURE;
        }

        $selectedProducts = [];
        while (true) {
            $productChoices = [];
            foreach ($products as $product) {
                if (!isset($selectedProducts[$product->getId()])) {
                    $productChoices[$product->getId()] = sprintf(
                        '%s (Stock: %d)',
                        $product->getName(),
                        $product->getStock()
                    );
                }
            }

            if (empty($productChoices)) {
                break; // No hay más productos disponibles
            }

            $productChoices[0] = 'Terminar';

            $productQuestion = new ChoiceQuestion('<question>Seleccione un producto:</question>', $productChoices);
            $productName = $helper->ask($input, $output, $productQuestion);

            if ($productName === 'Terminar') {
                break;
            }


            $productId = array_search($productName, $productChoices, true);


            $selectedProduct = $products->filter(function ($product) use ($productId) {
                return $product->getId() === $productId;
            })->first();

            if (!$selectedProduct) {
                $output->writeln('<error>Producto no válido.</error>');
                continue;
            }

            $quantityQuestion = new Question('<question>Ingrese la cantidad:</question>');
            $quantityQuestion->setValidator(function ($value) use ($selectedProduct) {
                if (!is_numeric($value) || (int)$value <= 0) {
                    throw new \RuntimeException('La cantidad debe ser un número positivo.');
                }
                if ($this->checkStockBeforeAdding) {
                    if ((int)$value > $selectedProduct->getStock()) {
                        throw new \RuntimeException('Stock insuficiente.');
                    }
                }
                return (int)$value;
            });

            $quantity = $helper->ask($input, $output, $quantityQuestion);
            $selectedProducts[$selectedProduct->getId()] = ['product' => $selectedProduct, 'quantity' => $quantity];
        }

        if (empty($selectedProducts)) {
            $output->writeln('<error>Debe agregar al menos un producto a la orden.</error>');
            return Command::FAILURE;
        }

        $selectedClientId = $selectedClient->getId();
        $orderConceptDtos = [];

        foreach ($selectedProducts as $product) {
            $orderConceptDtos[] = new OrderConceptDTO(
                $product['product']->getId(),
                $product['quantity']
            );
        }
        $createOrderDto = new OrderDTO($selectedClientId, $orderConceptDtos);

        try {
            $this->handler->handle($createOrderDto);
            $output->writeln('<info>Orden creada con éxito.</info>');
            return Command::SUCCESS;
        } catch (ValidationException $e) {
            $violations = $e->getViolations();
            $output->writeln('<error>'.$violations.'</error>');
            return Command::FAILURE;
        } catch (EntityNotFoundException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return Command::FAILURE;
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return Command::FAILURE;
        }


    }
}
