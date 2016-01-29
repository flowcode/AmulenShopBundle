<?php

namespace Flowcode\ShopBundle\Controller;

use Amulen\MediaBundle\Entity\Gallery;
use Amulen\MediaBundle\Entity\GalleryItem;
use Amulen\MediaBundle\Entity\Media;
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
use Symfony\Component\Finder\SplFileInfo;

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
     * @Route("/{id}/gallery", name="admin_product_gallery_show")
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

    /**
     * MEDIA
    */
    /**
     * Add media.
     *
     * @Route("/{id}/addmedia/{type}", name="admin_product_new_media")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:AdminProduct:media_new.html.twig")
     */
    public function addMediaProductAction(Product $product, $type = null)
    {
        if($type == 'type_image_file') {
            return $this->redirectToRoute('admin_product_new_image', array('id' => $product->getId()));
        }
        $entity = new Media();
        $entity->setMediaType($type);
        $form = $this->mediaCreateCreateForm($entity, $product);

        return array(
            'type' => $type,
            'entity' => $entity,
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Media entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function mediaCreateCreateForm(Media $entity, $product)
    {
        $types = $this->container->getParameter('flowcode_media.media_types');
        $class = $types[$entity->getMediaType()]["class_type"];

        $form = $this->createForm(new $class(), $entity, array(
            'action' => $this->generateUrl('admin_product_media_create', array("product" => $product->getId(), "type" => $entity->getMediaType())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a new Media entity.
     *
     * @Route("/{product}/media/{type}", name="admin_product_media_create")
     * @Method("POST")
     * @Template("FlowcodeShopBundle:AdminProduct:media_new.html.twig")
     */
    public function createMediaAction(Product $product,Request $request, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $media = new Media();
        $media->setMediaType($type);
        $form = $this->mediaCreateCreateForm($media, $product);
        $form->handleRequest($request);

        if($type == 'type_video_youtube'){
            if (is_null($product->getVideoGallery())) {
                /* create media gallery */
                $gallery = new Gallery();
                $gallery->setName($product->getName());
                $gallery->setEnabled(true);
                $em->persist($gallery);

                /* set video gallery */
                $product->setVideoGallery($gallery);
            }
        } else {
            $gallery = $product->getMediaGallery();
        }

        if ($form->isValid()) {
            $galleryItem = new GalleryItem();
            $galleryItem->setGallery($gallery);
            $position = $gallery->getGalleryItems()->count() + 1;
            $galleryItem->setPosition($position);
            $galleryItem->setMedia($media);


            $em = $this->getDoctrine()->getManager();
            $em->persist($media);
            $em->persist($galleryItem);
            $em->flush();

            return $this->redirectToRoute('admin_product_show', array('id' => $product->getId()));
        }

        return array(
            'entity' => $media,
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/{product}/media/{id}/{type}/edit", name="admin_product_media_edit")
     * @Method("GET")
     * @Template("FlowcodeShopBundle:AdminProduct:media_edit.html.twig")
     */
    public function editMediaAction(Product $product, Media $entity, $type, Request $request)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $editForm = $this->mediaCreateEditForm($entity, $product, $type);

        return array(
            'entity' => $entity,
            'type' => $type,
            'product' => $product,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Media entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function mediaCreateEditForm(Media $entity, $product, $type)
    {
        $types = $this->container->getParameter('flowcode_media.media_types');
        $class = $types[$entity->getMediaType()]["class_type"];

        $form = $this->createForm(new $class(), $entity, array(
            'action' => $this->generateUrl('admin_product_media_update', array("product" => $product->getId(), 'entity' => $entity->getId(), "type" => $entity->getMediaType())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Media entity.
     *
     * @Route("/{product}/media/{entity}", name="admin_product_media_update")
     * @Method("PUT")
     * @Template("FlowcodeShopBundle:AdminProduct:media_edit.html.twig")
     */
    public function updateMediaAction(Request $request, Product $product, Media $entity,$type)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $editForm = $this->createEditForm($entity, $product, $type);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_product_media_edit', array("product" => $product->getId(), 'entity' => $entity->getId(), "type" => $entity->getMediaType())));
        }

        return array(
            'entity' => $entity,
            'type' => $type,
            'product' => $product,
            'edit_form' => $editForm->createView(),
        );
    }
}
