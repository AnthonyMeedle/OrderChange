<?php
namespace OrderChange\Hook\Admin;

use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\OrderQuery;
use Thelia\Tools\URL;

class OrderChangeHook extends BaseHook {
	var $orderId;
	public function onOrderEditOrderProductTableHeader(HookRenderEvent $event){
        $event->add($this->render('order-edit-order-product-table-header.html'));
    }
	public function onOrderEditOrderProductList(HookRenderEvent $event){
		$this->order_id = $event->getArgument('order_id', null);
        $event->add($this->render('order-edit-order-product-list.html'));
    }
	public function onOrderEditOrderProductTableRow(HookRenderEvent $event){
		$order = OrderQuery::create()->findPk($this->order_id);
        $event->add($this->render('order-edit-order-product-table-row.html', array('order_id' => $this->order_id, 'order_status_id'=> $order->getStatusId(), 'order_product_id' => $event->getArgument('order_product_id', null))));
    }
	public function onOrderEditAfterOrderProductList(HookRenderEvent $event){
        $event->add($this->render('order-edit-after-order-product-list.html', array('order_id' => $event->getArgument('order_id', null))));
    }
	
	public function onOrderEditBottom(HookRenderEvent $event){
		$order = OrderQuery::create()->findPk($event->getArgument('order_id', null));
        $event->add($this->render('order-edit-bottom.html', array('order_id' => $event->getArgument('order_id', null), 'order_discount' => $order->getDiscount(), 'order_postage' => $order->getPostage())));
    }
	
    public function onOrderEditJs(HookRenderEvent $event){
		$event->add($this->addJS("/assets/js/order-edit.js"));
    }
	/*
	public function onOrderEditJs(HookRenderEvent $event){
        $event->add($this->render('order-edit.js', array('order_id' => $event->getArgument('order_id', null))));
    }*/
}
?>