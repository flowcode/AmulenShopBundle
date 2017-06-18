<?php

namespace Flowcode\ShopBundle\Controller;

use Flowcode\ShopBundle\Entity\StockTransferItem;
use Flowcode\ShopBundle\Service\StockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\StockTransfer;
use Flowcode\ShopBundle\Form\Type\StockTransferType;
use Doctrine\ORM\QueryBuilder;

/**
 * StockTransfer controller.
 *
 * @Route("/stock/transfers")
 */
class StockTransferController extends Controller
{
    /**
     * Lists all StockTransfer entities.
     *
     * @Route("/", name="stock_transfers")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:StockTransfer')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'stocktransfer');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a StockTransfer entity.
     *
     * @Route("/{id}/show", name="stock_transfers_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(StockTransfer $stocktransfer)
    {
        $editForm = $this->createForm(new StockTransferType(), $stocktransfer, array(
            'action' => $this->generateUrl('stock_transfers_update', array('id' => $stocktransfer->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stocktransfer->getId(), 'stock_transfers_delete');

        return array(

            'stocktransfer' => $stocktransfer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new StockTransfer entity.
     *
     * @Route("/new", name="stock_transfers_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $stocktransfer = new StockTransfer();

        $nextCode = $this->get('flower.stock.service.stock_transfer')->getNextCode();
        $stocktransfer->setCode($nextCode);
        $stocktransfer->setUser($this->getUser());

        $form = $this->createForm(new StockTransferType(), $stocktransfer);

        return array(
            'stocktransfer' => $stocktransfer,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new StockTransfer entity.
     *
     * @Route("/create", name="stock_transfers_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:StockTransfer:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $stocktransfer = new StockTransfer();
        $stocktransfer->setUser($this->getUser());
        $form = $this->createForm(new StockTransferType(), $stocktransfer);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stocktransfer);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_transfers_show', array('id' => $stocktransfer->getId())));
        }

        return array(
            'stocktransfer' => $stocktransfer,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StockTransfer entity.
     *
     * @Route("/{id}/edit", name="stock_transfers_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(StockTransfer $stocktransfer)
    {
        $editForm = $this->createForm(new StockTransferType(), $stocktransfer, array(
            'action' => $this->generateUrl('stock_transfers_update', array('id' => $stocktransfer->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stocktransfer->getId(), 'stock_transfers_delete');

        return array(
            'stocktransfer' => $stocktransfer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing StockTransfer entity.
     *
     * @Route("/{id}/update", name="stock_transfers_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:StockTransfer:edit.html.twig")
     */
    public function updateAction(StockTransfer $stocktransfer, Request $request)
    {
        $editForm = $this->createForm(new StockTransferType(), $stocktransfer, array(
            'action' => $this->generateUrl('stock_transfers_update', array('id' => $stocktransfer->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_transfers_show', array('id' => $stocktransfer->getId())));
        }
        $deleteForm = $this->createDeleteForm($stocktransfer->getId(), 'stock_transfers_delete');

        return array(
            'stocktransfer' => $stocktransfer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a Inventory entity.
     *
     * @Route("/{id}/confirm", name="stock_transfers_confirm", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function confirmAction(StockTransfer $stocktransfer)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var StockService $stockService */
        $stockService = $this->get('flower.stock.service.stock');

        /* @var StockTransferItem $item */
        foreach ($stocktransfer->getItems() as $item) {

            /* Decrease on target warehouse */
            $stockService->decreaseProduct(
                $stocktransfer->getWarehouseFrom(),
                $item->getProduct(),
                $item->getUnits(),
                null,
                "Transferencia entre depósitos: " . $stocktransfer->getCode(),
                $stocktransfer->getDate()
            );

            /* Increase on target warehouse */
            $stockService->increaseProduct(
                $stocktransfer->getWarehouseTo(),
                $item->getProduct(),
                $item->getUnits(),
                "Transferencia entre depósitos: " . $stocktransfer->getCode(),
                $stocktransfer->getDate()
            );

        }

        $stocktransfer->setStatus(StockTransfer::STATUS_CONFIRMED);
        $em->flush();

        return $this->redirect($this->generateUrl('stock_transfers_show', array('id' => $stocktransfer->getId())));
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_transfers_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('stocktransfer', $field, $type);

        return $this->redirect($this->generateUrl('stock_transfers'));
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
     * Deletes a StockTransfer entity.
     *
     * @Route("/{id}/delete", name="stock_transfers_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(StockTransfer $stocktransfer, Request $request)
    {
        $form = $this->createDeleteForm($stocktransfer->getId(), 'stock_transfers_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stocktransfer);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_transfers'));
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
