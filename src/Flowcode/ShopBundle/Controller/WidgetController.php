<?php

namespace Flowcode\ShopBundle\Controller;

use Amulen\ShopBundle\Repository\ProductOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Widget controller.
 */
class WidgetController extends Controller
{

    /**
     * Last n product orders.
     * @Template()
     */
    public function mostRecentOrdersAction($max = 20)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ProductOrderRepository $productOrderRepo */
        $productOrderRepo = $em->getRepository('AmulenShopBundle:ProductOrder');
        $orders = $productOrderRepo->recentOrders($max);

        return [
            'orders' => $orders,
        ];
    }

}
