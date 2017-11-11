<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\ProductItemField;
use Amulen\ShopBundle\Form\Type\ProductItemFieldType;
use Doctrine\ORM\QueryBuilder;

/**
 * ProductItemField controller.
 *
 * @Route("/admin/productitemfield")
 */
class AdminProductItemFieldController extends Controller
{
    /**
     * Lists all ProductItemField entities.
     *
     * @Route("/", name="admin_productitemfield")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AmulenShopBundle:ProductItemField')->createQueryBuilder('p');
        $this->addQueryBuilderSort($qb, 'productitemfield');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a ProductItemField entity.
     *
     * @Route("/{id}/show", name="admin_productitemfield_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ProductItemField $productitemfield)
    {
        $editForm = $this->createForm($this->get("amulen.shop.form.product.item.field"), $productitemfield, array(
            'action' => $this->generateUrl('admin_productitemfield_update', array('id' => $productitemfield->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productitemfield->getId(), 'admin_productitemfield_delete');

        return array(

            'productitemfield' => $productitemfield, 'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new ProductItemField entity.
     *
     * @Route("/new", name="admin_productitemfield_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $productitemfield = new ProductItemField();
        $form = $this->createForm($this->get("amulen.shop.form.product.item.field"), $productitemfield);

        return array(
            'productitemfield' => $productitemfield,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ProductItemField entity.
     *
     * @Route("/create", name="admin_productitemfield_create")
     * @Method("POST")
     * @Template("AmulenShopBundle:ProductItemField:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $productitemfield = new ProductItemField();
        $form = $this->createForm($this->get("amulen.shop.form.product.item.field"), $productitemfield);
        if ($form->handleRequest($request)->isValid()) {
            $productitemSrv = $this->get('amulen.shop.product.item.field');
            $productitemSrv->uploadImage($productitemfield);
            $em = $this->getDoctrine()->getManager();
            $em->persist($productitemfield);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_productitemfield_show', array('id' => $productitemfield->getId())));
        }

        return array(
            'productitemfield' => $productitemfield,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing ProductItemField entity.
     *
     * @Route("/{id}/update", name="admin_productitemfield_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("AmulenShopBundle:ProductItemField:edit.html.twig")
     */
    public function updateAction(ProductItemField $productitemfield, Request $request)
    {
        $editForm = $this->createForm($this->get("amulen.shop.form.product.item.field"), $productitemfield, array(
            'action' => $this->generateUrl('admin_productitemfield_update', array('id' => $productitemfield->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $productitemSrv = $this->get('amulen.shop.product.item.field');
            $productitemSrv->uploadImage($productitemfield);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_productitemfield_show', array('id' => $productitemfield->getId())));
        }
        return $this->redirect($this->generateUrl('admin_productitemfield_show', array('id' => $productitemfield->getId())));
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_productitemfield_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('productitemfield', $field, $type);

        return $this->redirect($this->generateUrl('admin_productitemfield'));
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
     * Deletes a ProductItemField entity.
     *
     * @Route("/{id}/delete", name="admin_productitemfield_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ProductItemField $productitemfield, Request $request)
    {
        $form = $this->createDeleteForm($productitemfield->getId(), 'admin_productitemfield_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productitemfield);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_productitemfield'));
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
