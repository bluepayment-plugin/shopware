<?php

declare(strict_types=1);

namespace BlueMedia\ShopwarePayment\Command;

use BlueMedia\ShopwarePayment\Service\GatewayService;
use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeGatewayList extends Command
{
    public static $defaultName = 'blue-media:gateway:sync';

    private GatewayService $gatewayService;

    public function __construct(
        GatewayService $gatewayService
    ) {
        $this->gatewayService = $gatewayService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ShopwareStyle($input, $output);

        $context = new Context(new SystemSource());

        $this->gatewayService->syncGateways($context);

        $io->info('Done. Check logs for more details.');

        return Command::SUCCESS;
    }
}
