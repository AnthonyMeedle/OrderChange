<?php
namespace OrderChange\Loop;

use OrderChange\Model\OrderProductStatusQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\SearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use Thelia\Model\ConfigQuery;


class OrderProductStatusLoop extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('status_id'),
            Argument::createIntListTypeArgument('order_product_id')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = OrderProductStatusQuery::create();
		
        $id = $this->getId();
        if ($id) {
            $search->filterById($id, Criteria::IN);
        }

        $status = $this->getStatusId();
        if ($status) {
            $search->filterByStatusId($status, Criteria::IN);
        }
        $order_product_id = $this->getOrderProductId();
        if ($order_product_id) {
            $search->filterByOrderProductId($order_product_id, Criteria::IN);
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $objet) {
            $loopResultRow = new LoopResultRow($objet);
			
            $loopResultRow
                ->set('ID', $objet->getId())
                ->set('STATUS_ID', $objet->getStatusId())
                ->set('ORDER_PRODUCT_ID', $objet->getOrderProductId())
			;
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}