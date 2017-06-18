<?php

namespace Flowcode\ShopBundle\Controller;

use Flowcode\ShopBundle\Entity\StockTransfer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\StockTransferItem;
use Flowcode\ShopBundle\Form\Type\StockTransferItemType;
use Doctrine\ORM\QueryBuilder;

/**
 * StockTransferItem controller.
 *
 * @Route("/stock/transfersitem")
 */
class StockTransferItemController extends Controller
{

    /**
     * Displays a form to create a new StockTransferItem entity.
     *
     * @Route("/{id}/new", name="stock_transfersitem_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(StockTransfer $stockTransfer)
    {
        $stocktransferitem = new StockTransferItem();
        $stocktransferitem->setStockTransfer($stockTransfer);
        $form = $this->createForm(new StockTransferItemType(), $stocktransferitem);

        return array(
            'stocktransferitem' => $stocktransferitem,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new StockTransferItem entity.
     *
     * @Route("/create", name="stock_transfersitem_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:StockTransferItem:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $stocktransferitem = new StockTransferItem();
        $form = $this->createForm(new StockTransferItemType(), $stocktransferitem);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stocktransferitem);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_transfers_show', array(
                'id' => $stocktransferitem->getStockTransfer()->getId(),
            )));
        }

        return array(
            'stocktransferitem' => $stocktransferitem,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StockTransferItem entity.
     *
     * @Route("/{id}/edit", name="stock_transfersitem_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(StockTransferItem $stocktransferitem)
    {
        $editForm = $this->createForm(new StockTransferItemType(), $stocktransferitem, array(
            'action' => $this->generateUrl('stock_transfersitem_update', array('id' => $stocktransferitem->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stocktransferitem->getId(), 'stock_transfersitem_delete');

        return array(
            'stocktransferitem' => $stocktransferitem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing StockTransferItem entity.
     *
     * @Route("/{id}/update", name="stock_transfersitem_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:StockTransferItem:edit.html.twig")
     */
    public function updateAction(StockTransferItem $stocktransferitem, Request $request)
    {
        $editForm = $this->createForm(new StockTransferItemType(), $stocktransferitem, array(
            'action' => $this->generateUrl('stock_transfersitem_update', array('id' => $stocktransferitem->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_transfersitem_show', array('id' => $stocktransferitem->getId())));
        }
        $deleteForm = $this->createDeleteForm($stocktransferitem->getId(), 'stock_transfersitem_delete');

        return array(
            'stocktransferitem' => $stocktransferitem,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_transfersitem_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('stocktransferitem', $field, $type);

        return $this->redirect($this->generateUrl('stock_transfersitem'));
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
     * Deletes a StockTransferItem entity.
     *
     * @Route("/{id}/delete", name="stock_transfersitem_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(StockTransferItem $stocktransferitem, Request $request)
    {
        $form = $this->createDeleteForm($stocktransferitem->getId(), 'stock_transfersitem_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stocktransferitem);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_transfersitem'));
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
