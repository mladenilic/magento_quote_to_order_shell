<?php
require_once 'abstract.php';

class Mage_Shell_Quote_To_Order extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($id = $this->getArg('id')) {
            $quote = Mage::getModel('sales/quote');

            $storeIds = array_filter(explode(',', $this->getArg('store')));
            if (!empty($storeIds)) {
                $quote->setData('shared_store_ids', $storeIds);
            }

            $quote->load($id);

            if (!$quote->getId()) {
                echo "Error: Quote does not exist (Make sure to set store parameter).\n";
                return;
            }
            
            $quote->collectTotals();

            /** @var Mage_Sales_Model_Service_Quote $service */
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();

            /** @var Mage_Sales_Model_Order $order */
            $order = $service->getOrder();
            if (!$order) {
                echo "Error: Creating order failed.\n";
                return;
            }

            echo "Success: Created order #{$order->getIncrementId()}.\n";
        } else {
            echo $this->usageHelp();
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f quote-to-order.php -- [options]
        php -f quote-to-order.php --id 123 --store 1,2

  id         Quote Id
  store      Comma sepparated list of store ids
USAGE;
    }
}

$shell = new Mage_Shell_Quote_To_Order();
$shell->run();

