<?php

namespace Flowcode\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Amulen\ShopBundle\Entity\ShopSetting;
use Flowcode\ShopBundle\Form\ShopSettingType;
use Doctrine\ORM\QueryBuilder;

/**
 * ShopSetting controller.
 *
 * @Route("/admin/shop/setting")
 */
class ShopSettingController extends Controller
{
    /**
     * Lists all ShopSetting entities.
     *
     * @Route("/", name="admin_amulen_shop_setting")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AmulenShopBundle:ShopSetting')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a ShopSetting entity.
     *
     * @Route("/{id}/show", name="admin_amulen_shop_setting_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ShopSetting $shopSetting)
    {
        $editForm = $this->createForm(new ShopSettingType(), $shopSetting, array(
            'action' => $this->generateUrl('admin_amulen_shop_setting_update', array('id' => $shopSetting->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($shopSetting->getId(), 'admin_amulen_shop_setting_delete');

        return array(
            'shop_setting' => $shopSetting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new ShopSetting entity.
     *
     * @Route("/new", name="admin_amulen_shop_setting_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $shopSetting = new ShopSetting();
        $form = $this->createForm(new ShopSettingType(), $shopSetting);

        return array(
            'shopSetting' => $shopSetting,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ShopSetting entity.
     *
     * @Route("/create", name="admin_amulen_shop_setting_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:ShopSetting:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $shopSetting = new ShopSetting();
        $form = $this->createForm(new ShopSettingType(), $shopSetting);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shopSetting);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_amulen_shop_setting_show', array('id' => $shopSetting->getId())));
        }

        return array(
            'shopSetting' => $shopSetting,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing ShopSetting entity.
     *
     * @Route("/{id}/update", name="admin_amulen_shop_setting_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:ShopSetting:show.html.twig")
     */
    public function updateAction(ShopSetting $shopSetting, Request $request)
    {
        $editForm = $this->createForm(new ShopSettingType(), $shopSetting, array(
            'action' => $this->generateUrl('admin_amulen_shop_setting_update', array('id' => $shopSetting->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_amulen_shop_setting_show', array('id' => $shopSetting->getId())));
        }
        $deleteForm = $this->createDeleteForm($shopSetting->getId(), 'admin_amulen_shop_setting_delete');

        return array(
            'shopSetting' => $shopSetting,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ShopSetting entity.
     *
     * @Route("/{id}/delete", name="admin_amulen_shop_setting_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ShopSetting $shopSetting, Request $request)
    {
        $form = $this->createDeleteForm($shopSetting->getId(), 'admin_amulen_shop_setting_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shopSetting);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_amulen_shop_setting'));
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
