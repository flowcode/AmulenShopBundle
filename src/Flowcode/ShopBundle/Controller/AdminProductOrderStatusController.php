<?php

namespace Flowcode\ShopBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\ProductOrderStatus;
use Flowcode\ShopBundle\Form\ProductOrderStatusType;

/**
 * ProductOrderStatus controller.
 *
 * @Route("/admin/orderstatus")
 */
class AdminProductOrderStatusController extends Controller
{

    /**
     * Lists all ProductOrderStatus entities.
     *
     * @Route("/", name="admin_orderstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page', 1);

        $em = $this->getDoctrine()->getManager();

        $filter['q'] = $request->get('q');
        $filter['status'] = $request->get('filter_status');

        /* @var QueryBuilder $qb */
        $qb = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->findAllFilteredQB($filter);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($qb, $page, 40);

        return array(
            'pagination' => $pagination,
            'filter' => $filter
        );
    }

    /**
     * Creates a new ProductOrderStatus entity.
     *
     * @Route("/", name="admin_orderstatus_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:ProductOrderStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductOrderStatus();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_orderstatus_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProductOrderStatus entity.
     *
     * @param ProductOrderStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductOrderStatus $entity)
    {
        $form = $this->createForm($this->get("amulen.shop.form.productorderstatus"), $entity, array(
            'action' => $this->generateUrl('admin_orderstatus_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductOrderStatus entity.
     *
     * @Route("/new", name="admin_orderstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductOrderStatus();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProductOrderStatus entity.
     *
     * @Route("/{id}", name="admin_orderstatus_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOrderStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductOrderStatus entity.
     *
     * @Route("/{id}/edit", name="admin_orderstatus_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOrderStatus entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a ProductOrderStatus entity.
     *
     * @param ProductOrderStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProductOrderStatus $entity)
    {
        $form = $this->createForm($this->get("amulen.shop.form.productorderstatus"), $entity, array(
            'action' => $this->generateUrl('admin_orderstatus_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ProductOrderStatus entity.
     *
     * @Route("/{id}", name="admin_orderstatus_update")
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:ProductOrderStatus:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var ProductOrderStatus $entity */
        $entity = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductOrderStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_orderstatus_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ProductOrderStatus entity.
     *
     * @Route("/{id}", name="admin_orderstatus_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductOrderStatus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_orderstatus'));
    }

    /**
     * Creates a form to delete a ProductOrderStatus entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_orderstatus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete',
                'attr' => array(
                    'onclick' => 'return confirm("Estás seguro?")'
                )))
            ->getForm();
    }

    /**
     * @Route("/api/orderstatus", name="admin_orderstatus_find_all")
     *
     */
    public function findAllAction(Request $request)
    {

        $filter['q'] = $request->get('q');
        $em = $this->getDoctrine()->getManager();
        $statusesQb = $em->getRepository('AmulenShopBundle:ProductOrderStatus')->findAllFilteredQB($filter);
        $statuses = $statusesQb->getQuery()->getResult();

        $data = [];
        foreach ($statuses as $status) {
            array_push($data, [
                'id' => $status->getId(),
                'text' => $status->getName()
            ]);
        }

        return new JsonResponse($data);
    }
}
