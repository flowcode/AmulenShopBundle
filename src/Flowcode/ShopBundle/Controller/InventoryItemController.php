<?php

namespace Flowcode\ShopBundle\Controller;

use Flowcode\ShopBundle\Entity\Inventory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\InventoryItem;
use Flowcode\ShopBundle\Form\Type\InventoryItemType;
use Doctrine\ORM\QueryBuilder;

/**
 * InventoryItem controller.
 *
 * @Route("/stock/inventoryitem")
 */
class InventoryItemController extends Controller
{
    /**
     * Lists all InventoryItem entities.
     *
     * @Route("/", name="stock_inventoryitem")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:InventoryItem')->createQueryBuilder('i');
        $this->addQueryBuilderSort($qb, 'inventoryitem');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a InventoryItem entity.
     *
     * @Route("/{id}/show", name="stock_inventoryitem_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(InventoryItem $inventoryitem)
    {
        $editForm = $this->createForm(new InventoryItemType(), $inventoryitem, array(
            'action' => $this->generateUrl('stock_inventoryitem_update', array('id' => $inventoryitem->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($inventoryitem->getId(), 'stock_inventoryitem_delete');

        return array(

            'inventoryitem' => $inventoryitem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new InventoryItem entity.
     *
     * @Route("/inventory/{id}/new", name="stock_inventoryitem_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Inventory $inventory)
    {
        $inventoryitem = new InventoryItem();
        $inventoryitem->setInventory($inventory);
        $form = $this->createForm(new InventoryItemType(), $inventoryitem);

        return array(
            'inventoryitem' => $inventoryitem,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new InventoryItem entity.
     *
     * @Route("/create", name="stock_inventoryitem_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:InventoryItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $inventoryitem = new InventoryItem();
        $form = $this->createForm(new InventoryItemType(), $inventoryitem);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventoryitem);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventoryitem->getInventory()->getId())));
        }

        return array(
            'inventoryitem' => $inventoryitem,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing InventoryItem entity.
     *
     * @Route("/{id}/edit", name="stock_inventoryitem_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(InventoryItem $inventoryitem)
    {
        $editForm = $this->createForm(new InventoryItemType(), $inventoryitem, array(
            'action' => $this->generateUrl('stock_inventoryitem_update', array('id' => $inventoryitem->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($inventoryitem->getId(), 'stock_inventoryitem_delete');

        return array(
            'inventoryitem' => $inventoryitem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing InventoryItem entity.
     *
     * @Route("/{id}/update", name="stock_inventoryitem_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:InventoryItem:edit.html.twig")
     */
    public function updateAction(InventoryItem $inventoryitem, Request $request)
    {
        $editForm = $this->createForm(new InventoryItemType(), $inventoryitem, array(
            'action' => $this->generateUrl('stock_inventoryitem_update', array('id' => $inventoryitem->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_inventoryitem_show', array('id' => $inventoryitem->getId())));
        }
        $deleteForm = $this->createDeleteForm($inventoryitem->getId(), 'stock_inventoryitem_delete');

        return array(
            'inventoryitem' => $inventoryitem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_inventoryitem_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('inventoryitem', $field, $type);

        return $this->redirect($this->generateUrl('stock_inventoryitem'));
    }

    /**
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
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
     * @param string $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a InventoryItem entity.
     *
     * @Route("/{id}/delete", name="stock_inventoryitem_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(InventoryItem $inventoryitem, Request $request)
    {
        $form = $this->createDeleteForm($inventoryitem->getId(), 'stock_inventoryitem_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventoryitem);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_inventoryitem'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}
