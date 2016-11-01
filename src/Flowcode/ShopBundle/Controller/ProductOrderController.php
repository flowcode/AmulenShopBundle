<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\ProductOrder;
use Flowcode\ShopBundle\Form\ProductOrderType;

/**
 * ProductOrder controller.
 *
 * @Route("/{_locale}/order")
 */
class ProductOrderController extends Controller
{

    /**
     * Lists all ProductOrder entities.
     *
     * @Route("/", name="order")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request, $page = null)
    {
        $session = $request->getSession();

        $productOrderService = $this->get("amulen.shop.order");

        if ($productOrderId = $session->get('productOrderId')) {
            $productOrder = $productOrderService->getProductOrder($productOrderId);
        } else {
            return $this->redirectToRoute('product');
        }

        return array(
            'productOrder' => $productOrder,
            'page' => $page,
        );
    }

    /**
     * Lists all ProductOrder entities.
     *
     * @Route("/add", name="order_add")
     * @Method("GET")
     * @Template()
     */
    public function orderAddAction(Request $request, $page = null)
    {
        $session = $request->getSession();

        $productService = $this->get("amulen.shop.product");
        $productOrderService = $this->get("amulen.shop.order");
        $productOrderItemService = $this->get("amulen.shop.order.item");
        $product = null;

        $productOrderId = $session->get('productOrderId');
        $productOrder = $productOrderService->getProductOrder($productOrderId);
        if (!$productOrderId) {
            $session->set('productOrderId', $productOrder->getId());
        }
        if ($productId = $request->get('product')) {
            $product = $productService->findById($productId);
            $productOrderItem = $productOrderService->addProduct($product, $productOrder);
            $productOrderItemService->create($productOrderItem);
        }

        return array(
            'productQtyOrder' => $productOrderItem->getQuantity(),
            'product' => $product,
            'productOrder' => $productOrder,
            'page' => $page,
        );
    }

}
