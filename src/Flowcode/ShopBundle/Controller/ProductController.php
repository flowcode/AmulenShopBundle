<?php

namespace Flowcode\ShopBundle\Controller;

use Flowcode\ShopBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Product controller.
 *
 * @Route("/{_locale}/product")
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $page = null)
    {
        $em = $this->getDoctrine()->getManager();

        $category = null;
        $category_slug = $request->get('category', null);
        $category = $em->getRepository('AmulenClassificationBundle:Category')->findOneBySlug($category_slug);

        /* seo metadata */
        $seoPage = $this->container->get('sonata.seo.page');
        $baseTitle = $seoPage->getTitle();
        $title = "Productos - " . $baseTitle;
        $seoPage->setTitle($title);

        /* pagination */
        $pageNumber = $request->get('page') ? $request->get('page') : 1;
        $products = $this->getDoctrine()->getRepository("AmulenShopBundle:Product")->findEnabledByPageAndCategory($category_slug);
        $pagination = $this->get('knp_paginator')->paginate($products, $pageNumber, 4);

        return array(
            'pagination' => $pagination,
            'category' => $category,
            'page' => $page,
        );
    }

    /**
     * Lists all Product entities.
     *
     * @Route("/category/{slug}", name="product_by_category")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:Product:index.html.twig")
     */
    public function listByCategoryAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /* seo metadata */
        $seoPage = $this->container->get('sonata.seo.page');
        $baseTitle = $seoPage->getTitle();
        $title = "Productos - " . $baseTitle;
        $seoPage->setTitle($title);

        /* pagination */
        $pageNumber = $request->get("page", 1);
        $products = $this->getDoctrine()->getRepository("AmulenShopBundle:Product")->findEnabledByPageAndCategory($slug);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($products, $this->get('request')->query->get('page', $pageNumber), 2);

        return array(
            'pagination' => $pagination,
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{slug}", name="product_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $productService = $this->get("amulen.shop.product");
        $productOrderService = $this->get("amulen.shop.order");
        $productOrderItemService = $this->get("amulen.shop.order.item");

        $entity = $em->getRepository('AmulenShopBundle:Product')->findOneBy(array("slug" => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        } else {
            $seoPage = $this->container->get('sonata.seo.page');
            $baseTitle = $seoPage->getTitle();
            $title = ucfirst($entity->getName()) . " - Productos - " . $baseTitle;
            $seoPage->setTitle($title);

            $seoPage->setTitle($title);
            $seoPage->addMeta('property', "og:url", $this->generateUrl('product_show', [
                'slug' => $entity->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL));

            $seoPage->addMeta('property', "og:type", "article");
            $seoPage->addMeta('property', "og:description", $entity->getDescription());

            if ($entity->getImage()) {
                $absoluteImageUrl = $this->generateUrl('base_path', [], UrlGeneratorInterface::ABSOLUTE_URL);
                $absoluteImageUrl .= $entity->getImage()->getPath();
                $seoPage->addMeta('property', "og:image", $absoluteImageUrl);
            }

        }

        /* ProductOrder */
        $session = $request->getSession();
        $productOrderId = $session->get('productOrderId');
        $productOrder = $productOrderService->getProductOrder($productOrderId);
        if (!$productOrderId) {
            $session->set('productOrderId', $productOrder->getId());
        }
        $prodQty = 0;
        if ($productOrderId) {
            $productOrder = $productOrderService->findById($productOrderId);
            if ($productOrder) {
                $prodQty = $request->get('prodQty') ? $request->get('prodQty') : 1;
                $item = $productOrderService->addProduct($entity, $productOrder, $prodQty);
                $prodQty = $item->getQuantity();
            }
        }

        return array(
            'entity' => $entity,
            'productQtyOrder' => $prodQty,
        );
    }
}
