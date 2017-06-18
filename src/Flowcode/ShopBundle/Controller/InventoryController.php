<?php

namespace Flowcode\ShopBundle\Controller;


use Flowcode\ShopBundle\Entity\Inventory;
use Flowcode\ShopBundle\Entity\InventoryItem;
use Flowcode\ShopBundle\Entity\WarehouseProduct;
use Flowcode\ShopBundle\Form\Type\InventoryItemsType;
use Flowcode\ShopBundle\Form\Type\InventoryType;
use Flowcode\ShopBundle\Service\InventoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\QueryBuilder;

/**
 * Inventory controller.
 *
 * @Route("/stock/inventory")
 */
class InventoryController extends Controller
{

    /**
     * @var InventoryService
     */
    private $inventoryService;

    /**
     * Lists all Inventory entities.
     *
     * @Route("/", name="stock_inventory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:Inventory')->createQueryBuilder('i');
        $qb->orderBy("i.created", "DESC");
        $this->addQueryBuilderSort($qb, 'inventory');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Inventory entity.
     *
     * @Route("/{id}/show", name="stock_inventory_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Inventory $inventory)
    {
        $products = $inventory->getItems();
        $editForm = $this->createForm(new InventoryType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));
        $editItemsForm = $this->createForm(new InventoryItemsType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_item_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($inventory->getId(), 'stock_inventory_delete');

        return array(

            'inventory' => $inventory,
            'products' => $products,
            'edit_form' => $editForm->createView(),
            'edit_items_form' => $editItemsForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Finds and displays a Inventory entity.
     *
     * @Route("/{id}/confirm", name="stock_inventory_confirm", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function confirmAction(Inventory $inventory)
    {
        $em = $this->getDoctrine()->getManager();

        $warehouseProductRepo = $em->getRepository('FlowerStockBundle:WarehouseProduct');
        foreach ($inventory->getItems() as $item) {
            $warehouseProduct = $warehouseProductRepo->findOneBy(array(
                'product' => $item->getProduct(),
                'warehouse' => $inventory->getWarehouse(),
            ));

            if (!$warehouseProduct) {
                $warehouseProduct = new WarehouseProduct();
                $warehouseProduct->setProduct($item->getProduct());
                $warehouseProduct->setWarehouse($item->getWarehouse());
                $em->persist($warehouseProduct);
            }
            $warehouseProduct->setStock($item->getStock());
        }


        $inventory->setStatus(Inventory::STATUS_CONFIRMED);
        $em->flush();

        return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventory->getId())));
    }

    /**
     * @Route("{id}/add_inventory_item", name="stock_inventory_add_item", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function addInventoryItemsAction(Inventory $inventory, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $var = $request->get('addItems');
        $products = $this->get('flower.stock.service.inventory')->getItems($var);

        foreach ($products as $product) {
            $inventory->addItem($product);
        }
        $em->persist($inventory);
        $em->flush();
        return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventory->getId())));

    }

    /**
     * Finds and displays a Inventory entity.
     *
     * @Route("/item/{id}/set_inventory_stock", name="stock_inventory_product_set_inventory_stock", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function setProductInventoryStockAction(InventoryItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var WarehouseProductService $warehouseProductService */
        $warehouseProductService = $this->get('flower.stock.service.warehouse_product');
        $warehouseProduct = $warehouseProductService->getByWarehouseProduct($item->getInventory()->getWarehouse(), $item->getProduct());
        $warehouseProduct->setStock($item->getStock());
        $em->flush();
        return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $item->getInventory()->getId())));
    }

    /**
     * Finds and displays a Inventory entity.
     *
     * @Route("/item/{id}/set_warehouse_stock", name="stock_inventory_product_set_warehouse_stock", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function setProductWarehouseStockAction(InventoryItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var WarehouseProductService $warehouseProductService */
        $warehouseProductService = $this->get('flower.stock.service.warehouse_product');
        $warehouseProduct = $warehouseProductService->getByWarehouseProduct($item->getInventory()->getWarehouse(), $item->getProduct());
        $item->setStock($warehouseProduct->getStock());
        $em->flush();
        return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $item->getInventory()->getId())));
    }

    /**
     * Displays a form to create a new Inventory entity.
     *
     * @Route("/new", name="stock_inventory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $inventory = new Inventory();

        $today = new \DateTime();;
        $inventory->setDate($today);
        $inventory->setUser($this->getUser());
        $inventory->setStatus(Inventory::STATUS_DRAFT);

        $defaultCode = $this->getInventoryService()->getNextCode();
        $inventory->setCode($defaultCode);

        $defaultName = $this->get('translator')->trans('default_name', [
            "%date%" => $today->format('d/m/y'),
        ], 'Inventory');
        $inventory->setName($defaultName);

        $form = $this->createForm(new InventoryType(), $inventory);

        return array(
            'inventory' => $inventory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Inventory entity.
     *
     * @Route("/create", name="stock_inventory_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Inventory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $inventory = new Inventory();
        $inventory->setStatus(Inventory::STATUS_DRAFT);
        $form = $this->createForm(new InventoryType(), $inventory);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('flower.stock.service.inventory')->fillInventory($inventory);
            $em->persist($inventory);
            $em->flush();


            return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventory->getId())));
        }

        return array(
            'inventory' => $inventory,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Inventory entity.
     *
     * @Route("/{id}/edit", name="stock_inventory_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Inventory $inventory)
    {
        $editForm = $this->createForm(new InventoryType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($inventory->getId(), 'stock_inventory_delete');

        return array(
            'inventory' => $inventory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Inventory entity.
     *
     * @Route("/{id}/saveitems", name="stock_inventory_item_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template()
     */
    public function saveItemsAction(Inventory $inventory, Request $request)
    {
        $editItemsForm = $this->createForm(new InventoryItemsType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_item_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));

        if ($editItemsForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventory->getId())));
        }
        $deleteForm = $this->createDeleteForm($inventory->getId(), 'stock_inventory_delete');
        $editForm = $this->createForm(new InventoryType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));

        return array(
            'inventory' => $inventory,
            'edit_form' => $editForm->createView(),
            'editItemsForm' => $editItemsForm,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Inventory entity.
     *
     * @Route("/{id}/update", name="stock_inventory_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Inventory:edit.html.twig")
     */
    public function updateAction(Inventory $inventory, Request $request)
    {
        $editForm = $this->createForm(new InventoryType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));

        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_inventory_show', array('id' => $inventory->getId())));
        }
        $deleteForm = $this->createDeleteForm($inventory->getId(), 'stock_inventory_delete');
        $editItemsForm = $this->createForm(new InventoryItemsType(), $inventory, array(
            'action' => $this->generateUrl('stock_inventory_item_update', array('id' => $inventory->getid())),
            'method' => 'PUT',
        ));

        return array(
            'inventory' => $inventory,
            'editItemsForm' => $editItemsForm,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Inventory entity.
     *
     * @Route("/stock/inventory/inventoryItem/{id}/save", name="stock_inventory_save")
     * @Method("POST")
     * @Template()
     */
    public function saveStock(InventoryItem $inventoryItem, Request $request)
    {
        $answer = $this->get('flower.stock.service.inventory')->itemIsValid($inventoryItem);

        if ($answer == true) {
            $inventoryItem->setStock($request->get('stock'));
            return true;
        }
        return false;
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_inventory_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('inventory', $field, $type);

        return $this->redirect($this->generateUrl('stock_inventory'));
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
     * Deletes a Inventory entity.
     *
     * @Route("/{id}/delete", name="stock_inventory_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Inventory $inventory, Request $request)
    {
        $form = $this->createDeleteForm($inventory->getId(), 'stock_inventory_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_inventory'));
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

    /**
     * @return InventoryService
     */
    public function getInventoryService(): InventoryService
    {
        if (!$this->inventoryService) {
            $this->inventoryService = $this->get('flower.stock.service.inventory');
        }
        return $this->inventoryService;
    }

    /**
     * @Route("/print/{id}/pdf", name="inventory_pdf_export", requirements={"id"="\d+"})
     * @Template()
     */
    public function printPDFAction(Inventory $inventory)
    {
        $em = $this->getDoctrine()->getManager();
        $tenant = $this->getUser()->getTenant();
        return array(
            'inventory' => $inventory,
            'tenant' => $tenant,

        );
    }

}
