<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\ProductItemFieldData;
use Amulen\ShopBundle\Form\Type\ProductItemFieldDataType;
use Doctrine\ORM\QueryBuilder;

/**
 * ProductItemFieldData controller.
 *
 * @Route("/admin/productitemfielddata")
 */
class AdminProductItemFieldDataController extends Controller
{
    /**
     * Lists all ProductItemFieldData entities.
     *
     * @Route("/", name="admin_productitemfielddata")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AmulenShopBundle:ProductItemFieldData')->createQueryBuilder('p');
        $this->addQueryBuilderSort($qb, 'productitemfielddata');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a ProductItemFieldData entity.
     *
     * @Route("/{id}/show", name="admin_productitemfielddata_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ProductItemFieldData $productitemfielddata)
    {
        $editForm = $this->createForm($this->get("amulen.shop.form.product.item.field.data"), $productitemfielddata, array(
            'action' => $this->generateUrl('admin_productitemfielddata_update', array('id' => $productitemfielddata->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productitemfielddata->getId(), 'admin_productitemfielddata_delete');

        return array(

            'productitemfielddata' => $productitemfielddata, 'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new ProductItemFieldData entity.
     *
     * @Route("/new", name="admin_productitemfielddata_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $productitemfielddata = new ProductItemFieldData();
        $form = $this->createForm($this->get("amulen.shop.form.product.item.field.data"), $productitemfielddata);

        return array(
            'productitemfielddata' => $productitemfielddata,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ProductItemFieldData entity.
     *
     * @Route("/create", name="admin_productitemfielddata_create")
     * @Method("POST")
     * @Template("AmulenShopBundle:ProductItemFieldData:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $productitemfielddata = new ProductItemFieldData();
        $form = $this->createForm($this->get("amulen.shop.form.product.item.field.data"), $productitemfielddata);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productitemfielddata);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_productitemfielddata_show', array('id' => $productitemfielddata->getId())));
        }

        return array(
            'productitemfielddata' => $productitemfielddata,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing ProductItemFieldData entity.
     *
     * @Route("/{id}/update", name="admin_productitemfielddata_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("AmulenShopBundle:ProductItemFieldData:edit.html.twig")
     */
    public function updateAction(ProductItemFieldData $productitemfielddata, Request $request)
    {
        $editForm = $this->createForm($this->get("amulen.shop.form.product.item.field.data"), $productitemfielddata, array(
            'action' => $this->generateUrl('admin_productitemfielddata_update', array('id' => $productitemfielddata->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_productitemfielddata_show', array('id' => $productitemfielddata->getId())));
        }
        $deleteForm = $this->createDeleteForm($productitemfielddata->getId(), 'admin_productitemfielddata_delete');

        return array(
            'productitemfielddata' => $productitemfielddata,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_productitemfielddata_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('productitemfielddata', $field, $type);

        return $this->redirect($this->generateUrl('admin_productitemfielddata'));
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
     * Deletes a ProductItemFieldData entity.
     *
     * @Route("/{id}/delete", name="admin_productitemfielddata_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ProductItemFieldData $productitemfielddata, Request $request)
    {
        $form = $this->createDeleteForm($productitemfielddata->getId(), 'admin_productitemfielddata_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productitemfielddata);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_productitemfielddata'));
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
