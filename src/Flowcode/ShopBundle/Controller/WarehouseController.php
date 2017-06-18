<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\Warehouse;
use Flowcode\ShopBundle\Form\Type\WarehouseType;
use Doctrine\ORM\QueryBuilder;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse")
 */
class WarehouseController extends Controller
{
    /**
     * Lists all Warehouse entities.
     *
     * @Route("/", name="warehouse")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:Warehouse')->createQueryBuilder('w');
        $this->addQueryBuilderSort($qb, 'warehouse');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }
    /**
     * Lists all Warehouse entities.
     *
     * @Route("/transactions", name="warehouse_transactions")
     * @Method("GET")
     * @Template()
     */
    public function transactionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $warehouses = $em->getRepository('FlowerStockBundle:Warehouse')->findAll();
        $products = $em->getRepository('FlowerModelBundle:Stock\Product')->findAll();

        $filter = array(
            'product' => $request->get('filter_product'),
            'warehouse' => $request->get('filter_warehouse')
        );
        $qb = $em->getRepository('FlowerStockBundle:StockChangeLog')->findAllFilteredQB($filter);
        $qb->addOrderBy('scl.id', "DESC");

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
            'warehouses' => $warehouses,
            'products' => $products,
            'filter' => $filter,
        );
    }

    /**
     * Finds and displays a Warehouse entity.
     *
     * @Route("/{id}/show", name="warehouse_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Warehouse $warehouse)
    {
        $editForm = $this->createForm(new WarehouseType(), $warehouse, array(
            'action' => $this->generateUrl('warehouse_update', array('id' => $warehouse->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($warehouse->getId(), 'warehouse_delete');

        return array(

        'warehouse' => $warehouse,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Warehouse entity.
     *
     * @Route("/new", name="warehouse_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(new WarehouseType(), $warehouse);

        return array(
            'warehouse' => $warehouse,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Warehouse entity.
     *
     * @Route("/create", name="warehouse_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Warehouse:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(new WarehouseType(), $warehouse);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($warehouse);
            $em->flush();

            return $this->redirect($this->generateUrl('warehouse_show', array('id' => $warehouse->getId())));
        }

        return array(
            'warehouse' => $warehouse,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Warehouse entity.
     *
     * @Route("/{id}/edit", name="warehouse_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Warehouse $warehouse)
    {
        $editForm = $this->createForm(new WarehouseType(), $warehouse, array(
            'action' => $this->generateUrl('warehouse_update', array('id' => $warehouse->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($warehouse->getId(), 'warehouse_delete');

        return array(
            'warehouse' => $warehouse,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Warehouse entity.
     *
     * @Route("/{id}/update", name="warehouse_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Warehouse:edit.html.twig")
     */
    public function updateAction(Warehouse $warehouse, Request $request)
    {
        $editForm = $this->createForm(new WarehouseType(), $warehouse, array(
            'action' => $this->generateUrl('warehouse_update', array('id' => $warehouse->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('warehouse_show', array('id' => $warehouse->getId())));
        }
        $deleteForm = $this->createDeleteForm($warehouse->getId(), 'warehouse_delete');

        return array(
            'warehouse' => $warehouse,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="warehouse_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('warehouse', $field, $type);

        return $this->redirect($this->generateUrl('warehouse'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a Warehouse entity.
     *
     * @Route("/{id}/delete", name="warehouse_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Warehouse $warehouse, Request $request)
    {
        $form = $this->createDeleteForm($warehouse->getId(), 'warehouse_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($warehouse);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('warehouse'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
