<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flowcode\ShopBundle\Entity\MeasureUnit;
use Flowcode\ShopBundle\Form\MeasureUnitType;
use Doctrine\ORM\QueryBuilder;

/**
 * MeasureUnit controller.
 *
 * @Route("/admin/shop/measure_unit")
 */
class AdminMeasureUnitController extends Controller
{
    /**
     * Lists all MeasureUnit entities.
     *
     * @Route("/", name="admin_shop_measure_unit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository(MeasureUnit::class)->createQueryBuilder('m');
        $this->addQueryBuilderSort($qb, 'measureunit');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a MeasureUnit entity.
     *
     * @Route("/{id}/show", name="admin_shop_measure_unit_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(MeasureUnit $measureunit)
    {
        $editForm = $this->createForm(MeasureUnitType::class, $measureunit, array(
            'action' => $this->generateUrl('admin_shop_measure_unit_update', array('id' => $measureunit->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($measureunit->getId(), 'admin_shop_measure_unit_delete');

        return array(

            'measureunit' => $measureunit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new MeasureUnit entity.
     *
     * @Route("/new", name="admin_shop_measure_unit_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $measureunit = new MeasureUnit();
        $form = $this->createForm(MeasureUnitType::class, $measureunit);

        return array(
            'measureunit' => $measureunit,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new MeasureUnit entity.
     *
     * @Route("/create", name="admin_shop_measure_unit_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:MeasureUnit:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $measureunit = new MeasureUnit();
        $form = $this->createForm(new MeasureUnitType(), $measureunit);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($measureunit);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_shop_measure_unit_show', array('id' => $measureunit->getId())));
        }

        return array(
            'measureunit' => $measureunit,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing MeasureUnit entity.
     *
     * @Route("/{id}/edit", name="admin_shop_measure_unit_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(MeasureUnit $measureunit)
    {
        $editForm = $this->createForm(new MeasureUnitType(), $measureunit, array(
            'action' => $this->generateUrl('admin_shop_measure_unit_update', array('id' => $measureunit->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($measureunit->getId(), 'admin_shop_measure_unit_delete');

        return array(
            'measureunit' => $measureunit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing MeasureUnit entity.
     *
     * @Route("/{id}/update", name="admin_shop_measure_unit_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:MeasureUnit:edit.html.twig")
     */
    public function updateAction(MeasureUnit $measureunit, Request $request)
    {
        $editForm = $this->createForm(new MeasureUnitType(), $measureunit, array(
            'action' => $this->generateUrl('admin_shop_measure_unit_update', array('id' => $measureunit->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_shop_measure_unit_show', array('id' => $measureunit->getId())));
        }
        $deleteForm = $this->createDeleteForm($measureunit->getId(), 'admin_shop_measure_unit_delete');

        return array(
            'measureunit' => $measureunit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_shop_measure_unit_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('measureunit', $field, $type);

        return $this->redirect($this->generateUrl('admin_shop_measure_unit'));
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
     * Deletes a MeasureUnit entity.
     *
     * @Route("/{id}/delete", name="admin_shop_measure_unit_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(MeasureUnit $measureunit, Request $request)
    {
        $form = $this->createDeleteForm($measureunit->getId(), 'admin_shop_measure_unit_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($measureunit);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_shop_measure_unit'));
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
