<?php

namespace Flowcode\ShopBundle\Controller;

use Flowcode\ShopBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction(Request $request, $parameter_bag = null, $page = null)
    {
        $em = $this->getDoctrine()->getManager();

        $category = null;
        $category_slug = null;
        if(isset($parameter_bag["category"])){
            $category_slug = $parameter_bag["category"];
            $category = $em->getRepository('AmulenClassificationBundle:Category')->findOneBySlug($category_slug);
        }
        if(isset($parameter_bag["page"])){
            $pageNumber = $parameter_bag["page"];
        } else {
            $pageNumber = 1;
        }

        /* seo metadata */
        $seoPage = $this->container->get('sonata.seo.page');
        $baseTitle = $seoPage->getTitle();
        $title = "Productos - " . $baseTitle;
        $seoPage->setTitle($title);

        /* pagination */
        $products = $this->getDoctrine()->getRepository("AmulenShopBundle:Product")->findEnabledByPageAndCategory($category_slug);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($products, $pageNumber, 4);

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
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:Product')->findOneBy(array("slug" => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        } else {
            $seoPage = $this->container->get('sonata.seo.page');
            $baseTitle = $seoPage->getTitle();
            $title = ucfirst($entity->getName()) . " - Productos - " . $baseTitle;
            $seoPage->setTitle($title);
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Lists all Product entities.
     *
     * @Route("/", name="menushowcategory")
     * @Method("GET")
     * @Template()
     */
    public function menuShowCategoryAction(Request $request, $parameter_bag = null, $page = null)
    {
        $em = $this->getDoctrine()->getManager();

        $category = null;
        $category_slug = null;
        if(isset($parameter_bag["category"])){
            $category_slug = $parameter_bag["category"];
            $category = $em->getRepository('AmulenClassificationBundle:Category')->findOneBySlug($category_slug);
            if (count($category->getChildren()) > 0){
                /* Aca cargar nueva vista para vista de subcategorias. */
            } else {
                $this->indexAction($request, $parameter_bag = null, $page = null);
            }

        }
        if(isset($parameter_bag["page"])){
            $pageNumber = $parameter_bag["page"];
        } else {
            $pageNumber = 1;
        }

        return array(
            'pagination' => $pagination,
            'category' => $category,
            'page' => $page,
        );
    }
}
