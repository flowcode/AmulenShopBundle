<?php

namespace Flowcode\ShopBundle\Controller;

use Amulen\MediaBundle\Entity\Gallery;
use Amulen\MediaBundle\Entity\GalleryItem;
use Amulen\MediaBundle\Form\GalleryItemType;
use Amulen\MediaBundle\Form\ImageGalleryType;
use Amulen\ShopBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Product controller.
 *
 * @Route("/admin/product")
 */
class AdminProductController extends Controller {

    /**
     * Lists all Product entities.
     *
     * @Route("/", name="admin_product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmulenShopBundle:Product')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/", name="admin_product_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:AdminProduct:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Product();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /* persist product */
            $em->persist($entity);

            if (is_null($entity->getMediaGallery())) {
                /* create media gallery */
                $mediaGallery = new Gallery();
                $mediaGallery->setName($entity->getName());
                $mediaGallery->setEnabled(true);
                $em->persist($mediaGallery);

                /* set media gallery */
                $entity->setMediaGallery($mediaGallery);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                    'success', $this->get('translator')->trans('save_success')
            );

            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $entity->getId())));
        } else {
            $this->get('session')->getFlashBag()->add(
                    'warning', $this->get('translator')->trans('save_fail')
            );
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Product $entity) {
        $form = $this->createForm($this->get("amulen.shop.form.product"), $entity, array(
            'action' => $this->generateUrl('admin_product_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="admin_product_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Product();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="admin_product_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Product $product) {
        
        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($product->getId());

        return array(
            'entity' => $product,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="admin_product_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
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
     * Creates a form to edit a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Product $entity) {
        $form = $this->createForm($this->get("amulen.shop.form.product"), $entity, array(
            'action' => $this->generateUrl('admin_product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}", name="admin_product_update")
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:AdminProduct:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmulenShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="admin_product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AmulenShopBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_product'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_product_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete',
                            'attr' => array(
                                    'onclick' => 'return confirm("Estás seguro?")'
                            )))
                        ->getForm()
        ;
    }


    /**
     * GALLERY 
     *
     */

    /**
     * Displays a form to create a new Gallery entity.
     *
     * @Route("/{id}/addimage", name="admin_product_new_image")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:AdminProduct:gallery_add_item.html.twig")
     */
    public function addimageAction(Product $product) {
        $em = $this->getDoctrine()->getManager();

        $gallery = $product->getMediaGallery();
        $entity = new GalleryItem();
        $entity->setGallery($gallery);
        $position = $gallery->getGalleryItems()->count() + 1;
        $entity->setPosition($position);

        $form = $this->createForm($this->get("amulen.shop.form.product.gallery"), $entity, array(
            'action' => $this->generateUrl('admin_product_gallery_item_create', array('id' => $product->getId())),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));

        return array(
            'entity' => $entity,
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Media entity.
     *
     * @Route("/{id}/creategalleryitem", name="admin_product_gallery_item_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:AdminProduct:gallery_add_item.html.twig")
     */
    public function createGalleryItemAction(Product $product, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $entity = new \Amulen\MediaBundle\Entity\GalleryItem();

        $form = $this->createForm($this->get("amulen.shop.form.product.gallery"), $entity, array(
            'action' => $this->generateUrl('admin_product_gallery_item_create', array('id' => $product->getId())),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $gallery = $product->getMediaGallery();
            $gallery->addGalleryItem($entity);
            $entity->setGallery($gallery);


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $product->getId())));
        }

        return array(
            'entity' => $entity,
            'product' => $product,
            'form' => $form->createView(),
        );
    }



    /**
     * Finds and displays a Gallery entity.
     *
     * @Route("/gallery/{id}", name="admin_product_gallery_show")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:AdminProduct:gallery_show.html.twig")
     */
    public function showGalleryItemAction(Product $product) {
        $entity = $product->getMediaGallery();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery entity.');
        }

        $deleteForm = $this->createDeleteForm($product->getId());

        return array(
            'entity' => $entity,
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing GalleryItem entity.
     *
     * @Route("/{product}/galleryitem/{id}/edit", name="admin_product_gallery_edit")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:AdminProduct:gallery_edit_item.html.twig")
     */
    public function editGalleryItemAction(GalleryItem $entity, Product $product, Request $request) {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GalleryItem entity.');
        }

        $editForm = $this->createEditGalleryItemForm($entity, $product);
        $deleteForm = $this->createDeleteGalleryItemForm($entity->getId());

        return array(
            'entity' => $entity,
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing GalleryItem entity.
     *
     * @Route("/{id}/galleryitem/{entity}", name="admin_product_gallery_update")
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:AdminProduct:gallery_edit_item.html.twig")
     */
    public function updateGalleryItemAction(Request $request, GalleryItem $entity, Product $product) {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GalleryItem entity.');
        }

        $deleteForm = $this->createDeleteGalleryItemForm($entity->getId());
        $editForm = $this->createEditGalleryItemForm($entity, $product);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_show', array('id' => $product->getId())));
        }

        return array(
            'entity' => $entity,
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a GalleryItem entity.
     *
     * @Route("/galleryitem/{id}", name="admin_product_gallery_delete")
     * @Method("DELETE")
     */
    public function deleteGalleryItemAction(Request $request, GalleryItem $entity) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GalleryItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_product'));
    }

    /**
     * Displays a form to create a new Gallery entity.
     *
     * @Route("/{product}/galleryitem/{id}/remove", name="admin_product_gallery_item_remove")
     */
    public function removeitemAction(Product $product, Request $request, GalleryItem $entity) {
        $em = $this->getDoctrine()->getManager();

        $galleryItem = $em->getRepository('AmulenMediaBundle:GalleryItem')->find($entity->getId());

        $gallery = $galleryItem->getGallery();

        $em->remove($galleryItem);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_product_show', array('id' => $product->getId())));
    }

    /**
     * Creates a form to edit a GalleryItem entity.
     *
     * @param GalleryItem $entity The entity
     *
     * @return Form The form
     */
    private function createEditGalleryItemForm(GalleryItem $entity, $product) {
        $form = $this->createForm($this->get("amulen.shop.form.product.gallery"), $entity, array(
            'action' => $this->generateUrl('admin_product_gallery_update', array('entity' => $entity->getId(), 'id' => $product->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a GalleryItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteGalleryItemForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_product_gallery_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Actualizar la posicion de cada imagen.
     *
     * @Route("/updatePosItem", name="admin_product_gallery_update_drag_drop")
     * @Method("POST")
    */
    public function updateGalleryItemPosition(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $posArray = $request->get('data');

        $i = 0;
        foreach ($posArray as $item) {
            $entity = $em->getRepository('AmulenMediaBundle:GalleryItem')->find($item);
            $entity->setPosition($i);
            $i++;
        }
        $em->flush();
        return new Response('ok');
    }
}
