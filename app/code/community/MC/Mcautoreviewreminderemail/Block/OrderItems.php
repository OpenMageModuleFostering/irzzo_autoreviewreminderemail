<?php
 /*
 * @category    Community
 * @package     MC_Mcautoreviewreminderemail
 * @Document    OrderItems.php
 * @Created on  April 11, 2012, 7:05 PM
 * @copyright   Copyright (c) 2012 Magento Complete
 */

 
class MC_Mcautoreviewreminderemail_Block_OrderItems extends Mage_Core_Block_Template {
    
    private $order;

    private $billingAddress;

    private $orderItems;

    private $reviewedItems;

    public function _construct(){

        $orderId = $this->getRequest()->getParam('oid'); 

        $orderId = base64_decode($orderId);

        if($orderId && is_numeric($orderId)){

            $order = Mage::getModel('sales/order')->load($orderId);
            
            if($order->getId()){
                
                $this->order = $order;

                $this->billingAddress = $order->getBillingAddress();

                $this->orderItems = $order->getAllVisibleItems();

                echo $collection = Mage::getModel('review/review')
                        ->getCollection()
                        ->addFieldToFilter('mc_mcautoreviewemailorder_id', $orderId);

                if($collection){

                    foreach($collection as $c){
                        
                        $this->reviewedItems[$c->getEntityPkValue()] = $c->getReviewId();

                   }

                }

            }

        }

    }

    public function getOrder(){

        return $this->order;

    }

    public function getBillingAddress(){

        return $this->billingAddress;

    }

    public function getOrderItems(){

        return $this->orderItems;

    }

    public function isAlreadyReviewed($productId){

        $reviewId = 0;

        if($this->reviewedItems && array_key_exists($productId, $this->reviewedItems)){

            $reviewId = $this->reviewedItems[$productId];

        }

        return $reviewId;

    }
    
}
