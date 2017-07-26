<?php

namespace Flowcode\ShopBundle\Command;

use Amulen\UserBundle\Entity\User;
use Amulen\UserBundle\Entity\UserAddress;
use Flowcode\DashboardBundle\Command\AmulenCommand;
use Flowr\AmulenSyncBundle\Entity\Setting;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of UpgradeCommand
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class ClearDraftOrdersCommand extends AmulenCommand
{
    const COMMAND_NAME = 'amulen:shop:cleardraftorders';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Amulen Shop, clear orders.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    function task(InputInterface $input, OutputInterface $output)
    {

        $productOrderService = $this->getContainer()->get('amulen.shop.order');

        $processedOrders = $productOrderService->clearDrafts();

        $output->write("Processed order count: ");
        $output->writeln($processedOrders['processed']);

        return true;
    }
}
