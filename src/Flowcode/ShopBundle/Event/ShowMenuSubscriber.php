<?php

namespace Flowcode\ShopBundle\Event;

use Flowcode\DashboardBundle\Event\ShowMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Created by PhpStorm.
 * User: juanma
 * Date: 5/28/16
 * Time: 12:20 PM
 */
class ShowMenuSubscriber implements EventSubscriberInterface
{
    protected $router;
    protected $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ShowMenuEvent::NAME => array('handler', 3),
        );
    }


    public function handler(ShowMenuEvent $event)
    {
        $menuOptions = $event->getMenuOptions();

        /* add default */
        $menuOptions[] = array(
            "icon" => "fa fa-shopping-cart",
            "url" => '#',
            "title" => $this->translator->trans('shop'),
            "submenu" => array(
                array(
                    "url" => $this->router->generate('admin_product'),
                    "title" => $this->translator->trans('products'),
                ),
                array(
                    "url" => $this->router->generate('admin_shop_category'),
                    "title" => $this->translator->trans('categories'),
                ),
                array(
                    "url" => $this->router->generate('admin_order'),
                    "title" => $this->translator->trans('orders'),
                ),
                array(
                    "url" => $this->router->generate('admin_order_delivery'),
                    "title" => $this->translator->trans('deliveries'),
                ),
                array(
                    "url" => $this->router->generate('admin_amulen_shop_setting'),
                    "title" => $this->translator->trans('settings'),
                ),
            ),
        );

        $event->setMenuOptions($menuOptions);

    }
}