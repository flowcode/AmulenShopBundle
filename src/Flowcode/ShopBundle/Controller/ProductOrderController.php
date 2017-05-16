<?php

namespace Flowcode\ShopBundle\Controller;

use Amulen\ShopBundle\Entity\ProductOrderStatus;
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
        $session->set('_security.frontend.target_path', 'order');
        $productOrderService = $this->get("amulen.shop.order");

        if ($productOrderId = $session->get('productOrderId')) {
            $productOrder = $productOrderService->getProductOrder($productOrderId);
        } else {
            return $this->redirectToRoute('products_index');
        }
        $shipping = $productOrderService->hasShipping($productOrder);

        return array(
            'productOrder' => $productOrder,
            'page' => $page,
            'shipping' => $shipping,
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

        // Get Draft status
        $em = $this->getDoctrine()->getManager();
        $draftStatus = $em->getRepository(ProductOrderStatus::class)->findOneBy([
            'name' => ProductOrderStatus::STATUS_DRAFT
        ]);
        $productOrder->setStatus($draftStatus);

        if (!$productOrderId) {
            $session->set('productOrderId', $productOrder->getId());
        }
        if ($productId = $request->get('product')) {
            $quantity = $request->get('prodQty', 1);
            $product = $productService->findById($productId);
            $productOrderItem = $productOrderService->addProduct($product, $productOrder, $quantity);
            $productOrderItemService->create($productOrderItem);
        }

        $session->set('productOrderCount', $productOrder->getItemTotalCount());

        return array(
            'productQtyOrder' => $productOrderItem->getQuantity(),
            'product' => $product,
            'productOrder' => $productOrder,
            'page' => $page,
        );
    }

    /**
     * Order item remove
     *
     * @Route("/update", name="order_item_update")
     * @Method("GET")
     * @Template()
     */
    public function orderItemUpdateAction(Request $request)
    {
        $session = $request->getSession();

        $productOrderService = $this->get("amulen.shop.order");
        $productOrderItemService = $this->get("amulen.shop.order.item");

        $productOrderId = $session->get('productOrderId');
        $productOrder = $productOrderService->getProductOrder($productOrderId);
        if (!$productOrderId || !$productOrder) {
            /* No hay order en session redirect a listado de productos */
            return $this->redirectToRoute('products_index');
        }
        if ($itemId = $request->get('item')) {
            $quantity = $request->get('prodQty') ? $request->get('prodQty') : 0;
            $item = $productOrderItemService->findById($itemId);
            $item->setQuantity($quantity);
            $productOrderItemService->update($item);
            $productOrderService->updateOrderAmount($productOrder);
        }

        return $this->redirectToRoute('order');
    }

    /**
     * Order item remove
     *
     * @Route("/remove", name="order_item_remove")
     * @Method("GET")
     * @Template()
     */
    public function orderItemRemoveAction(Request $request)
    {
        $session = $request->getSession();

        $productOrderService = $this->get("amulen.shop.order");
        $productOrderItemService = $this->get("amulen.shop.order.item");

        $productOrderId = $session->get('productOrderId');
        $productOrder = $productOrderService->getProductOrder($productOrderId);
        if (!$productOrderId || !$productOrder) {
            /* No hay order en session redirect a listado de productos */
            return $this->redirectToRoute('products_index');
        }
        if ($itemId = $request->get('item')) {
            $item = $productOrderItemService->findById($itemId);
            $productOrderItemService->delete($item);
            $productOrder->removeItem($item);
            $productOrderService->update($productOrder);
            $productOrderService->updateOrderAmount($productOrder);
        }

        $session->set('productOrderCount', $productOrder->getItemTotalCount());

        return $this->redirectToRoute('order');
    }

    /**
     * Order item remove
     *
     * @Route("/{id}/checkout", name="order_checkout")
     * @Method("GET")
     * @Template()
     */
    public function orderCheckoutAction(ProductOrder $productOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $user = $this->getUser();
        $session->remove('productOrderId');

        if(!$user){
            return $this->redirectToRoute('amulen_user_login');
        }
        if($user && !$productOrder->getUser()){
            $productOrder->setUser($user);
            $em->flush();
        }

        return $this->redirectToRoute('homepage');
    }

}
