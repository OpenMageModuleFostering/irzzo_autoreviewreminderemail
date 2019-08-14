<?php

/*
 * @category    Community
 * @package     MC_Mcautoreviewreminderemail
 * @Document    ProductsController.php
 * @Created on  April 11, 2012, 7:05 PM
 * @copyright   Copyright (c) 2012 Magento Complete
 */


require_once(Mage::getBaseDir() . '/app/code/core/Mage/Review/controllers/ProductController.php');

class MC_Mcautoreviewreminderemail_ProductController extends Mage_Review_ProductController {

     /**
     * Checking is order has reviewed
     * @param numeric $orderId and numeric $productId
     * @return boolean (true/false )
     */
    
    private function isAlreadyReview($orderId, $productId){

        $collection = Mage::getModel('review/review')->getCollection()
                    ->addFieldToFilter('entity_pk_value', $productId)
                    ->addFieldToFilter('mc_mcautoreviewemailorder_id', $orderId);

        if($collection->count() > 0){

            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_already_submited");
            
            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");
            
            return false;
        }

        return true;
        
    }
    
    /**
     * Checking is order has reviewed
     * @param numeric $orderId and numeric $productId
     * @return boolean (true/false )
     */
    
    private function validateReviewParameters($orderId, $productId){

        if (!$productId) {
            
            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");

            return false;

        }

        if(!is_numeric($productId)){

            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");

            return false;
            
        }
        
        $product = Mage::getModel('catalog/product')->load($productId);

        if(!$product->getId()){

            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");

            return false;

        }

        if (!$orderId) {
            
            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");

            return false;

        }

        if(!is_numeric($orderId)){
            
            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);
            
            $this->_redirect("*/*/index/");

            return false;
            
        }
        
        $order = Mage::getModel('sales/order')->load($orderId);

        if(!$order->getId()){
            
            $errorMessage = Mage::getStoreConfig("mcautoreviewreminderemail/constant/error_message_incorrect_parameter");

            Mage::getSingleton('core/session')->addError($errorMessage);

            $this->_redirect("*/*/index/");

            return false;

        }

        return true;
        
    }
    
    /*
     *
     */
    
    public function indexAction(){

        $this->loadLayout();
        
        $this->renderLayout();

    }

    public function orderItemsAction(){ 
        
        $this->loadLayout();

        $this->renderLayout();
        
    }
    
    public function reviewFormAction(){

        if(Mage::getSingleton('core/session')->hasData('created_review')){
            
            Mage::getSingleton('core/session')->unsetData('created_review');

            $this->_redirect("*/*/index/");
            
            return false;

        }

        $productId = $this->getRequest()->getParam('id');
        
        $orderId = base64_decode($this->getRequest()->getParam('oid'));

        $flag = $this->isAlreadyReview($orderId, $productId);
        
        if(!$flag){

            return false;
            
        }
        
        $flag = $this->validateReviewParameters($orderId, $productId);

        if(!$flag){
            
            return false;

        }

        if ($product = $this->_initProduct()) {
            
            Mage::register('productId', $product->getId());

            $this->_initProductLayout($product);

            $this->renderLayout();
            
        }elseif (!$this->getResponse()->isRedirect()) {

            $this->_forward('noRoute');

        }

    }

    /**
    * - Users logged in to the site will only be able to submit the reviews for products they purchased
    * - Guest customers will receive an email with a link to submit their review for the product they bought
    * - Links received by logged in customers and guests are only valid for one submission only.
    *   If the user tries to submit a review again the system will tell them that they have already
    *   submitted the review on their order
    * - People that have not bought the particular product are not allowed to add reviews
    * 
    *   @return
    */
    
    public function postAction(){

        $productId = $this->getRequest()->getParam('id');

        $orderId = base64_decode($this->getRequest()->getPost('oid'));

        $flag = $this->validateReviewParameters($orderId, $productId);
        
        if(!$flag){

            return false;
            
        }
        
        $flag = $this->isAlreadyReview($orderId, $productId);

        if(!$flag){
            
            return false;

        }

        $this->getRequest()->setPost('mc_mcautoreviewemailorder_id', $orderId);
        
        Mage::getSingleton('core/session')->setData("created_review", 1);

        parent::postAction();
        
    }
        
    public function loadmorereviewsAction(){

        $product_id = $this->getRequest()->getParam('product_id');

        $reviewFrom = $this->getRequest()->getParam('reviewFrom');

        $reviewTo = $this->getRequest()->getParam('reviewTo');
            
        Mage::register('productId', $product_id);

        Mage::register('reviewFrom', $reviewFrom);

        Mage::register('reviewTo', $reviewTo);

        $this->loadLayout();

        $this->renderLayout();

    }
    
}
