<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Order\DTO\CreateOrderDto;
use App\Application\Order\DTO\OrderConceptDto;
use App\Application\Order\Service\CreateOrderService;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\Interface\MonitoringInterface;
use App\Infrastructure\Persistence\Doctrine\Client\DoctrineClientRepository;
use App\Infrastructure\Persistence\Doctrine\Product\DoctrineProductRepository;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Command to create an order via the console.
 *
 * This command will prompt the user to select a client and add products to an order.
 * It checks if stock is sufficient before adding products and allows the user to finalize the order.
 * If any validation or entity not found errors occur, appropriate messages are shown.
 * The order creation process is handled by the `CreateOrderCommandHandler`.
 *
 * @package App\Infrastructure\Command
 */
#[AsCommand(
    name: 'app:create-order',
    description: 'Crear una orden desde la consola',
)]
class CreateOrderCommand extends AbstractCommand
{
    private bool $checkStockBeforeAdding;
    private MonitoringInterface $monitoring;

    /**
     * CreateOrderCommand constructor.
     *
     * @param DoctrineClientRepository $clientRepository Repository to fetch clients.
     * @param DoctrineProductRepository $productRepository Repository to fetch products.
     * @param CreateOrderService $service Handler to process order creation.
     * @param ParameterBagInterface $params Parameter bag for configuration.
     * @param MonitoringInterface $monitoring Monitoring service for error capturing.
     */
    public function __construct(
        private DoctrineClientRepository  $clientRepository,
        private DoctrineProductRepository $productRepository,
        private CreateOrderService        $service,
        private ParameterBagInterface     $params,
        MonitoringInterface               $monitoring
    )
    {
        parent::__construct();
        $this->checkStockBeforeAdding = (bool)$params->get('check_stock_before_adding_product');
        $this->monitoring = $monitoring;
    }

    /**
     * Executes the order creation process.
     *
     * This method interacts with the user to select a client and products,
     * validate stock, and creates an order. If any error occurs during
     * the creation process, it will be captured and reported.
     *
     * @param InputInterface $input The console input interface.
     * @param OutputInterface $output The console output interface.
     *
     * @return int The command exit status: Command::SUCCESS on success, Command::FAILURE on failure.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $clients = $this->clientRepository->all();
        if ($clients->count() === 0) {
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
        if ($products->count() === 0) {
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
                break;
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
                    throw new RuntimeException('La cantidad debe ser un número positivo.');
                }
                if ($this->checkStockBeforeAdding) {
                    if ((int)$value > $selectedProduct->getStock()) {
                        throw new RuntimeException('Stock insuficiente.');
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
            $orderConceptDtos[] = new OrderConceptDto(
                $product['product']->getId(),
                $product['quantity']
            );
        }
        $createOrderDto = new CreateOrderDto($selectedClientId, $orderConceptDtos);

        try {
            $this->service->execute($createOrderDto);
            $output->writeln('<info>Orden creada con éxito.</info>');
            return Command::SUCCESS;
        } catch (ValidationException $e) {
            $violations = $e->getViolations();
            $output->writeln('<error>' . $violations . '</error>');
            return Command::FAILURE;
        } catch (EntityNotFoundException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $this->monitoring->captureException($e);
            return Command::FAILURE;
        }
    }
}
