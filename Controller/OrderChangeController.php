<?php
namespace OrderChange\Controller;

use OrderChange\Model\OrderProductStatusQuery;
use OrderChange\Model\OrderProductStatus;
use OrderChange\OrderChange as Objoc;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderProductQuery;
use Thelia\Model\OrderProductTaxQuery;
use Thelia\Model\TaxRuleI18nQuery;
use Thelia\TaxEngine\Calculator;

class OrderChangeController extends BaseAdminController
{
    public function __construct(){}

	public function config(){
		Objoc::setConfigValue('module-orderchange-affiche_img', $_REQUEST['affiche_img']);
		Objoc::setConfigValue('module-orderchange-edit_prix', $_REQUEST['edit_prix']);
		Objoc::setConfigValue('module-orderchange-edit_quantity', $_REQUEST['edit_quantity']);
		Objoc::setConfigValue('module-orderchange-edit_status', $_REQUEST['edit_status']);
		Objoc::setConfigValue('module-orderchange-edit_remise', $_REQUEST['edit_remise']);
		Objoc::setConfigValue('module-orderchange-edit_fdp', $_REQUEST['edit_fdp']);
		
		if(!empty($_REQUEST["success_url"])) return $response = $this->generateRedirect($_REQUEST["success_url"]);
	}
	public function updateOrder(){
		if(null !== $order = OrderQuery::create()->findPk($_REQUEST['change_order_id'])){
			if(isset($_REQUEST['order_change_discount'])) $order->setDiscount($_REQUEST['order_change_discount']);
			if(isset($_REQUEST['order_change_postage'])) $order->setPostage($_REQUEST['order_change_postage']);
			$order->save();
		}
		if(!empty($_REQUEST["stop"])) exit;
		if(!empty($_REQUEST["success_url"])) return $response = $this->generateRedirect($_REQUEST["success_url"]);
	}
	public function updateProductOrder(){
		if(null !== $orderProduct = OrderProductQuery::create()->findPk($_REQUEST['change_order_product_id'])){
			if(isset($_REQUEST['order_change_quantity'])) $orderProduct->setQuantity($_REQUEST['order_change_quantity']);
			if(isset($_REQUEST['order_change_price'])){ 
				if($orderProduct->getWasInPromo())
					$orderProduct->setPromoPrice($_REQUEST['order_change_price']);
				else
					$orderProduct->setPrice($_REQUEST['order_change_price']);
			}
			$orderProduct->save();
			
        }
		if(isset($_REQUEST['order_change_price'])){
			$taxRuleI18n = TaxRuleI18nQuery::create()->filterByTitle($orderProduct->getTaxRuleTitle())->findOne();
			$order = $orderProduct->getOrder();
			$deliveryAddress = $order->getOrderAddressRelatedByDeliveryOrderAddressId();
			$taxCountry = $deliveryAddress->getCountry();

			$calculator = new Calculator();
			$calculator->loadTaxRuleWithoutProduct($taxRuleI18n->getTaxRule(), $taxCountry);
			if(null !== $orderProductTax = OrderProductTaxQuery::create()->filterByOrderProductId($_REQUEST['change_order_product_id'])->findOne()){
				if($orderProduct->getWasInPromo())
					$orderProductTax->setPromoAmount($calculator->getTaxAmountFromUntaxedPrice($_REQUEST['order_change_price']));
				else
					$orderProductTax->setAmount($calculator->getTaxAmountFromUntaxedPrice($_REQUEST['order_change_price']));
				 $orderProductTax->save();
			}
		}
		if(isset($_REQUEST['order_change_status']) && $_REQUEST['order_change_status']){
			if(null === $obj = OrderProductStatusQuery::create()->filterByOrderProductId($_REQUEST['change_order_product_id'])->findOne() ){
				$obj = new OrderProductStatus();
				$obj->setOrderProductId($_REQUEST['change_order_product_id']);
			}
			$obj->setStatusId($_REQUEST['order_change_status'])->save();
		}

		if(!empty($_REQUEST["stop"])) exit;
		if(!empty($_REQUEST["success_url"])) return $response = $this->generateRedirect($_REQUEST["success_url"]);
	}
	
	public function deleteProductOrder(){
		if(null !== $orderProduct = OrderProductQuery::create()->findPk($_REQUEST['change_order_product_id'])){
			if(null !== $obj = OrderProductStatusQuery::create()->filterByOrderProductId($_REQUEST['change_order_product_id'])->findOne() ){
				$obj->delete();
			}
			$orderProduct->delete();
		}
		if(!empty($_REQUEST["stop"])) exit;
		if(!empty($_REQUEST["success_url"])) return $response = $this->generateRedirect($_REQUEST["success_url"]);
	}
	

}