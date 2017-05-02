<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\OrderDeliveryOption;

/**
 * OrderDeliveryOption controller.
 *
 * @Route("/admin/order/delivery")
 */
class AdminOrderDeliveryOptionController extends Controller
{

    /**
     * Lists all Brand entities.
     *
     * @Route("/", name="admin_order_delivery")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmulenShopBundle:OrderDeliveryOption')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OrderDeliveryOption entity.
     *
     * @Route("/", name="admin_order_delivery_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:OrderDeliveryOption:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new OrderDeliveryOption();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_order_delivery_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a OrderDeliveryOption entity.
     *
     * @param OrderDeliveryOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrderDeliveryOption $entity)
    {
        $form = $this->createForm($this->get("amulen.shop.form.delivery.option"), $entity, array(
            'action' => $this->generateUrl('admin_order_delivery_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new OrderDeliveryOption entity.
     *
     * @Route("/new", name="admin_order_delivery_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OrderDeliveryOption();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OrderDeliveryOption entity.
     *
     * @Route("/{id}", name="admin_order_delivery_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:OrderDeliveryOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderDeliveryOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderDeliveryOption entity.
     *
     * @Route("/{id}/edit", name="admin_order_delivery_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:OrderDeliveryOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderDeliveryOption entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a OrderDeliveryOption entity.
    *
    * @param OrderDeliveryOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrderDeliveryOption $entity)
    {
        $form = $this->createForm($this->get("amulen.shop.form.delivery.option"), $entity, array(
            'action' => $this->generateUrl('admin_order_delivery_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing OrderDeliveryOption entity.
     *
     * @Route("/{id}", name="admin_order_delivery_update")
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:OrderDeliveryOption:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:OrderDeliveryOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderDeliveryOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_order_delivery_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderDeliveryOption entity.
     *
     * @Route("/{id}", name="admin_order_delivery_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AmulenShopBundle:OrderDeliveryOption')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderDeliveryOption entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_order_delivery'));
    }

    /**
     * Creates a form to delete a OrderDeliveryOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_order_delivery_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
