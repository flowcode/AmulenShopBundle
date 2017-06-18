<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\StockLevel;
use Flowcode\ShopBundle\Form\Type\StockLevelType;
use Doctrine\ORM\QueryBuilder;

/**
 * StockLevel controller.
 *
 * @Route("/stock/stocklevel")
 */
class StockLevelController extends Controller
{
    /**
     * Lists all StockLevel entities.
     *
     * @Route("/", name="stock_stocklevel")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:StockLevel')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'stocklevel');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a StockLevel entity.
     *
     * @Route("/{id}/show", name="stock_stocklevel_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(StockLevel $stocklevel)
    {
        $editForm = $this->createForm(new StockLevelType(), $stocklevel, array(
            'action' => $this->generateUrl('stock_stocklevel_update', array('id' => $stocklevel->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stocklevel->getId(), 'stock_stocklevel_delete');

        return array(

        'stocklevel' => $stocklevel,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new StockLevel entity.
     *
     * @Route("/new", name="stock_stocklevel_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $stocklevel = new StockLevel();
        $form = $this->createForm(new StockLevelType(), $stocklevel);

        return array(
            'stocklevel' => $stocklevel,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new StockLevel entity.
     *
     * @Route("/create", name="stock_stocklevel_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:StockLevel:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $stocklevel = new StockLevel();
        $form = $this->createForm(new StockLevelType(), $stocklevel);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stocklevel);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_stocklevel_show', array('id' => $stocklevel->getId())));
        }

        return array(
            'stocklevel' => $stocklevel,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StockLevel entity.
     *
     * @Route("/{id}/edit", name="stock_stocklevel_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(StockLevel $stocklevel)
    {
        $editForm = $this->createForm(new StockLevelType(), $stocklevel, array(
            'action' => $this->generateUrl('stock_stocklevel_update', array('id' => $stocklevel->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stocklevel->getId(), 'stock_stocklevel_delete');

        return array(
            'stocklevel' => $stocklevel,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing StockLevel entity.
     *
     * @Route("/{id}/update", name="stock_stocklevel_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:StockLevel:edit.html.twig")
     */
    public function updateAction(StockLevel $stocklevel, Request $request)
    {
        $editForm = $this->createForm(new StockLevelType(), $stocklevel, array(
            'action' => $this->generateUrl('stock_stocklevel_update', array('id' => $stocklevel->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_stocklevel_show', array('id' => $stocklevel->getId())));
        }
        $deleteForm = $this->createDeleteForm($stocklevel->getId(), 'stock_stocklevel_delete');

        return array(
            'stocklevel' => $stocklevel,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_stocklevel_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('stocklevel', $field, $type);

        return $this->redirect($this->generateUrl('stock_stocklevel'));
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
     * Deletes a StockLevel entity.
     *
     * @Route("/{id}/delete", name="stock_stocklevel_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(StockLevel $stocklevel, Request $request)
    {
        $form = $this->createDeleteForm($stocklevel->getId(), 'stock_stocklevel_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stocklevel);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_stocklevel'));
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
