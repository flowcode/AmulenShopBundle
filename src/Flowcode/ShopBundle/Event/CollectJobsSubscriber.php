<?php

namespace Flowcode\ShopBundle\Event;


use Flowcode\DashboardBundle\Event\CollectJobsEvent;
use Flowcode\ShopBundle\Command\ClearDraftOrdersCommand;
use Flowr\AmulenSyncBundle\Command\SyncOrdersCommand;
use Flowr\AmulenSyncBundle\Command\SyncProductsCommand;
use Flowr\AmulenSyncBundle\Command\SyncServicesCommand;
use Flowr\AmulenSyncBundle\Command\SyncUsersCommand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CollectJobsSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            CollectJobsEvent::NAME => array('handler', 1000),
        );
    }

    public function handler(CollectJobsEvent $event)
    {
        $clearDraftsJob = [
            'name' => ClearDraftOrdersCommand::COMMAND_NAME,
            'command' => ClearDraftOrdersCommand::COMMAND_NAME,
        ];
        $event->pushJob($clearDraftsJob);

    }
}