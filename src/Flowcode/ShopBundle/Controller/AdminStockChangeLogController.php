<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\StockChangeLog;
use Flowcode\ShopBundle\Form\Type\StockChangeLogType;
use Doctrine\ORM\QueryBuilder;

/**
 * StockChangeLog controller.
 *
 * @Route("/admin/shop/changelog")
 */
class AdminStockChangeLogController extends Controller
{
    /**
     * Lists all StockChangeLog entities.
     *
     * @Route("/", name="admin_shop_changelog")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository(StockChangeLog::class)->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'stockchangelog');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a StockChangeLog entity.
     *
     * @Route("/{id}/show", name="admin_shop_changelog_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(StockChangeLog $stockchangelog)
    {
        $editForm = $this->createForm(new StockChangeLogType(), $stockchangelog, array(
            'action' => $this->generateUrl('admin_shop_changelog_update', array('id' => $stockchangelog->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stockchangelog->getId(), 'admin_shop_changelog_delete');

        return array(

            'stockchangelog' => $stockchangelog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new StockChangeLog entity.
     *
     * @Route("/new", name="admin_shop_changelog_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $stockchangelog = new StockChangeLog();
        $form = $this->createForm(new StockChangeLogType(), $stockchangelog);

        return array(
            'stockchangelog' => $stockchangelog,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new StockChangeLog entity.
     *
     * @Route("/create", name="admin_shop_changelog_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:StockChangeLog:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $stockchangelog = new StockChangeLog();
        $form = $this->createForm(new StockChangeLogType(), $stockchangelog);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stockchangelog);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_shop_changelog_show', array('id' => $stockchangelog->getId())));
        }

        return array(
            'stockchangelog' => $stockchangelog,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StockChangeLog entity.
     *
     * @Route("/{id}/edit", name="admin_shop_changelog_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(StockChangeLog $stockchangelog)
    {
        $editForm = $this->createForm(new StockChangeLogType(), $stockchangelog, array(
            'action' => $this->generateUrl('admin_shop_changelog_update', array('id' => $stockchangelog->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($stockchangelog->getId(), 'admin_shop_changelog_delete');

        return array(
            'stockchangelog' => $stockchangelog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing StockChangeLog entity.
     *
     * @Route("/{id}/update", name="admin_shop_changelog_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:StockChangeLog:edit.html.twig")
     */
    public function updateAction(StockChangeLog $stockchangelog, Request $request)
    {
        $editForm = $this->createForm(new StockChangeLogType(), $stockchangelog, array(
            'action' => $this->generateUrl('admin_shop_changelog_update', array('id' => $stockchangelog->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_shop_changelog_show', array('id' => $stockchangelog->getId())));
        }
        $deleteForm = $this->createDeleteForm($stockchangelog->getId(), 'admin_shop_changelog_delete');

        return array(
            'stockchangelog' => $stockchangelog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_shop_changelog_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('stockchangelog', $field, $type);

        return $this->redirect($this->generateUrl('admin_shop_changelog'));
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
     * Deletes a StockChangeLog entity.
     *
     * @Route("/{id}/delete", name="admin_shop_changelog_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(StockChangeLog $stockchangelog, Request $request)
    {
        $form = $this->createDeleteForm($stockchangelog->getId(), 'admin_shop_changelog_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stockchangelog);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_shop_changelog'));
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
