<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */



class Grizzly_Productaction_Adminhtml_MassassignController extends Mage_Adminhtml_Controller_Action
{
   

    public function getOptionArray(Mage_Catalog_Model_Product_Option $option)
  {
    $commonArgs = array(
      'is_delete',
      'previous_type',
      'previous_group',
      'title',
      'type',
      'is_require',
      'sort_order',
      'values',
    );
    $priceArgs = array(
      'price_type',
      'price',
      'sku',
    );
    $txtArgs = array('max_characters');
    $fileArgs = array(
      'file_extension',
      'image_size_x',
      'image_size_y'
    );
    $multiArgs = array(
      'is_delete',
      'title',
      'sort_order',
    );

    $multi = array(
      'drop_down',
      'radio',
      'checkbox',
      'multiple',
    );

    $valueArgs = array_merge($multiArgs, $priceArgs);

    $type = $option->getType();
    switch ($type) {
      case 'file':
        $optionArgs = array_merge($commonArgs, $priceArgs, $fileArgs);
        break;
      case 'field':
      case 'area':
        $optionArgs = array_merge($commonArgs, $priceArgs, $txtArgs);
        break;
      case 'date':
      case 'date_time':
      case 'time':
        $optionArgs = array_merge($commonArgs, $priceArgs);
        break;
      default :
        $optionArgs = $commonArgs;
        break;
    }

    $optionArray = $option->toArray($optionArgs);
    if (in_array($type, $multi)) {
      $optionArray['values'] = array();
      foreach ($option->getValues() as $value) {
        $optionArray['values'][] = $value->toArray($valueArgs);
      }
    }

    return $optionArray;
  }


  //for setting product options starts
    protected function copyOptionsToProduct($id, $productOptions)
    {
        $product = Mage::getModel('catalog/product');
        $product->reset()->load($id);
        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->setProductOptions($productOptions); 
        $product->setCanSaveCustomOptions(!$product->getOptionsReadonly()); 
        $this->deleteCurrentOptions($product);
        $product->save();
    }

    protected function deleteCurrentOptions($product)
    {
        $option = $product->getOptionInstance();
        $optionsCollection = $option->getProductOptionCollection($product);
        $optionsCollection->walk('delete');
        $option->unsetOptions();
    }


    protected function getoptionsdata($productid)
    {

        $options = Mage::getModel('catalog/product_option')
        ->getCollection()
        ->addTitleToResult(Mage::app()->getStore()->getId())
        ->addPriceToResult(Mage::app()->getStore()->getId())
        ->addProductToFilter($productid)
        ->addValuesToResult();   

        return $options;

    }

    

    protected function _initCustomer($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Products'));

        $productId = (int) $this->getRequest()->getParam($idFieldName);
        $product = Mage::getModel('catalog/product');

        if ($productId) 
            {
               $product = Mage::getModel('catalog/product')
                  ->setStoreId(Mage::app()->getStore()->getId())
                   ->load($productId);
               if ($product->getId()) 
               {
                   return $product;
                }
           }
    }

    public function indexAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    //custom added for assigncategory starts //
    public function assigncatAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $newcatids = $this->getRequest()->getPost('category');

                    if (ctype_alpha($newcatids)) 
                    {
                        $this->_getSession()->addError('Please enter Category ID or Category IDs(comma separated) in order to assign products.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                    $newcatids = explode(',', $newcatids);
                    $existingcatids = $product->getCategoryIds();
                    $finalcatids = array_unique(array_merge($newcatids,$existingcatids), SORT_REGULAR);
                    try
                    {         

                        $product->setCategoryIds(array($finalcatids));
                        $product->save();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     
              
        
        

        }

    }


    public function attrsetAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();
        $attrset = $this->getRequest()->getPost('attributeset');
        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }
        else
        {
            foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    
                    try
                    {
                        $product->setAttributeSetId($attrset)->save();
                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }                    

                }    
        
            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Attribute set have been updated successfully.');   
            Mage::app()->cleanCache();

        }



    }


    //custom added for removing from category starts //
    public function removecatAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $newcatids = $this->getRequest()->getPost('removecat');

                    if (ctype_alpha($newcatids)) 
                    {
                        $this->_getSession()->addError('Please enter Category ID or Category IDs(comma separated) in order to remove products from it.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                    $newcatids = explode(',', $newcatids);
                    $existingcatids = $product->getCategoryIds();
                    $finalcatids = array_diff($existingcatids, $newcatids);
                    try
                    {         

                        $product->setCategoryIds(array($finalcatids));
                        $product->save();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     
              
        
        

        }

    }



    //custom added for updating price starts (Flat)//
    public function incpriceflatAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $incflat = $this->getRequest()->getPost('incflat');
                    $currentprice = $product->getPrice();
                    $finalprice = $currentprice + $incflat;
                    
                    if(!is_numeric($incflat)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }   

                    try
                    {         

                        $product->setPrice($finalprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

                 
              
        
        

        }

    }

    //custom added for updating price ends (Flat)//



    //custom added for updating price starts (percentage)//
    public function incpricepercAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $incperc = $this->getRequest()->getPost('incfperc');
                    $currentprice = $product->getPrice();
                    $finalpropotion = ($currentprice * $incperc) / 100;
                    $finalprice =  ($currentprice + $finalpropotion);

                    if(!is_numeric($incperc)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    } 
                    
                    try
                    {         

                        $product->setPrice($finalprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache(); 

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

                
              
        
        

        }

    }


    //custom added for updating special price starts (flat)//
    public function incspclflatAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $incspclflat = $this->getRequest()->getPost('incspclflat');

                    $currentfinalprice = $product->getFinalPrice();
                    $currentspecialprice = $product->getSpecialPrice();
                    $finalspecialprice = $currentspecialprice + $incspclflat;

                    if(!is_numeric($incspclflat)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    } 
                    
                    try
                    {         

                        $product->setSpecialPrice($finalspecialprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();     

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

            
              
        
        

        }

    }


    //custom added for updating special price starts (percentage)//
    public function incspclpercAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $incspclperc = $this->getRequest()->getPost('incspclperc');

                    $currentfinalprice = $product->getFinalPrice();
                    $currentspecialprice = $product->getSpecialPrice();
                    $finalpropotion = ($currentspecialprice * $incspclperc) / 100;

                    $finalspecialprice = $currentspecialprice + $finalpropotion;


                    if(!is_numeric($incspclperc)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                    
                    try
                    {         

                        $product->setSpecialPrice($finalspecialprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache(); 

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

                
              
        
        

        }

    }



    //custom added for modifying special price relative to base price starts (flat)//
    public function spclrelbaseflatAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $spclrelbaseflat = $this->getRequest()->getPost('spclrelbaseflat');
                    $currentbaseprice = $product->getPrice();
                    $currentspecialprice = $product->getSpecialPrice();

                    $finalspecialprice = $currentbaseprice + $spclrelbaseflat;

                    if(!is_numeric($spclrelbaseflat)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                    
                    try
                    {         

                        $product->setSpecialPrice($finalspecialprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

                 
              
        
        

        }

    }




    //custom added for modifying special price relative to base price starts (percentage)//
    public function spclrelbaseAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $spclrelbase = $this->getRequest()->getPost('spclrelbase');

                    $currentbaseprice = $product->getPrice();
                    $currentspecialprice = $product->getSpecialPrice();
                    $finalpropotion = ($currentbaseprice * $spclrelbase) / 100;

                    $finalspecialprice = $currentbaseprice + $finalpropotion;

                    if(!is_numeric($spclrelbase)) 
                    {

                        $this->_getSession()->addError('Please enter valid value. e.g 4,-3.2,+10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                    
                    try
                    {         

                        $product->setSpecialPrice($finalspecialprice);
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

                 
              
        
        

        }

    }

	//wholesale price update
	public function wholesalepriceAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();
        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }
        else
        {
            foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);				
                    $wholesaleprice = $this->getRequest()->getPost('wholesaleprice');
                    if(!is_numeric($wholesaleprice)) 
                    {
					   $this->_getSession()->addError('Please enter valid value. e.g 4,3.2,10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }                    
                    try
                    {   
						$g_PricingData = array();
						$currentg_PricingDatas = $product->getData('group_price');
						foreach($currentg_PricingDatas as $currentg_PricingData){
							if($currentg_PricingData['cust_group']!=2){
								$g_PricingData[] = $currentg_PricingData;
							}							
						}
						$g_PricingData[] = array ('website_id'=>0, 'cust_group'=>2, 'price'=>$wholesaleprice);						
						$product->setData('group_price',$g_PricingData); 
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();
                    }
                    catch(Exception $e)
                    {           
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                }
        }
    }

	//retailer price update
	public function retailerpriceAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();
        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }
        else
        {
            foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);				
                    $retailerprice = $this->getRequest()->getPost('retailerprice');
                    if(!is_numeric($retailerprice)) 
                    {
					   $this->_getSession()->addError('Please enter valid value. e.g 4,3.2,10');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }                    
                    try
                    {   
						$g_PricingData = array();
						$currentg_PricingDatas = $product->getData('group_price');
						foreach($currentg_PricingDatas as $currentg_PricingData){
							if($currentg_PricingData['cust_group']!=3){
								$g_PricingData[] = $currentg_PricingData;
							}							
						}
						$g_PricingData[] = array ('website_id'=>0, 'cust_group'=>3, 'price'=>$retailerprice);						
						$product->setData('group_price',$g_PricingData); 
                        $product->save();
                        $this->_redirect('*/catalog_product/index');
                        Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                        Mage::app()->cleanCache();
                    }
                    catch(Exception $e)
                    {           
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }
                }
        }
    }



    //custom added for updating visibility starts//
    public function modvisAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $incspclperc = $this->getRequest()->getPost('uptvisibility');
                    
                    try
                    {         

                        $product->setVisibility($incspclperc);
                        $product->save();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     
              
        
        

        }

    }


    //custom added for updating status starts//
    public function modstatusAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $prodstatus = $this->getRequest()->getPost('uptstatus');
                    
                    try
                    {         

                        $product->setStatus($prodstatus);
                        $product->save();

                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     
              
        
        

        }

    }





    //custom added for copy product options starts//
    public function copyprodoptAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    
                    $selectedproductid = $this->getRequest()->getPost('copyprodopt');
                    $selectedproduct = Mage::getModel('catalog/product')->load($selectedproductid);
                    
                    $options = $this->getoptionsdata($selectedproductid);
                    $CheckProduct = $selectedproduct->getId();
                    if(!is_numeric($selectedproductid)) 
                    {

                        $this->_getSession()->addError('Please enter a valid Product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                    if(!$CheckProduct)
                    {

                        $this->_getSession()->addError('The Product ID you entered does not exist,please enter a valid product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;

                    }
                    

                    if (!count($options)) 
                    {
                        $this->_getSession()->addError('Incorrect source product! Product has no custom options or does not exist.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }


                    $productOptions = array();

                    foreach ($options as $option) 
                    { 
                        $productOptions[] = $this->getOptionArray($option);
                    }

                    if (!empty($productOptions)) 
                    {
                      
                          try 
                          {
                            foreach ($productIds as $id)
                             {
                              if ($srcId == $id) 
                              {
                                continue;
                              }
                              
                                $this->copyOptionsToProduct($productId, $productOptions);
                                $this->_redirect('*/catalog_product/index');
                                Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
                                Mage::app()->cleanCache();
                              
                            }
                          } 
                          catch (Exception $err) 
                          {
                            $this->_getSession()->addError($err->getMessage());
                            $this->_getSession()->addError('<pre>' . $err->getTraceAsString() . '</pre>');
                            
                            return false;
                          }
                    }

                
                
                }  

                 
              
        
        

        }

    }



    //custom added for copy product images starts//
    public function copyprodimgAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    
                    $selectedproductid = $this->getRequest()->getPost('copyprodimg');
                    $selectedproduct = Mage::getModel('catalog/product')->load($selectedproductid);
                    $CheckProduct = $selectedproduct->getId();
                    if(!$CheckProduct)
                    {

                        $this->_getSession()->addError('The Product ID you entered does not exist,please enter a valid product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;

                    }
                    if(!is_numeric($selectedproductid)) 
                    {

                        $this->_getSession()->addError('Please enter a valid product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                    $image = $selectedproduct->getData('image');
					$mainimage = $selectedproduct->getData('image');
                    $small_image = $selectedproduct->getData('small_image');
                    $thumbnail = $selectedproduct->getData('thumbnail');
					
                    $product = Mage::getModel('catalog/product')->load($productId); 
                    $baseurl = "media/catalog/product";
                    $basefinalimage = $baseurl . $image;
                    $smallfinalimage = $baseurl . $small_image;
                    $thumbfinalimage = $baseurl . $thumbnail;
                    $mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')
                    ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'media_gallery');
                    $mediaGallery = $mediaGalleryAttribute->getBackend();
                    $attrCode = $mediaGalleryAttribute->getAttributeCode();
                    $mediaGalleryData = $product->getData($attrCode);
					
					$mediaGalleryDatasel = $selectedproduct->getMediaGalleryImages()->getItems();
					
					$otherImages = array();
					foreach($mediaGalleryDatasel as $allmedia){
						if($allmedia->getFile()!=$mainimage && $allmedia->getFile()!=$small_image && $allmedia->getFile()!=$thumbnail){
							$otherImages[] = $baseurl.$allmedia->getFile();
						}
					
					}
                    if (isset($mediaGalleryData['images'])) 
                    {
                        foreach ($mediaGalleryData['images'] as &$image) 
                        {
                            $image['removed'] = 1;
                        }
                        $product->setData($attrCode, $mediaGalleryData);
                    }
					
					
					//custom code starts
					if($mainimage==$small_image && $small_image==$thumbnail)
					{
						if($mainimage && $mainimage!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
							$mediaGallery->setMediaAttribute($product, 'small_image', $baseimage);
							$mediaGallery->setMediaAttribute($product, 'thumbnail', $baseimage);
                        }
					}
					elseif($mainimage==$small_image)
					{
						if($mainimage && $mainimage!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
							$mediaGallery->setMediaAttribute($product, 'small_image', $baseimage);
                        }
	                    if($thumbnail && $thumbnail!="no_selection")
                        {    
                            $thumbimage = $mediaGallery->addImage($product, $thumbfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'thumbnail', $thumbimage);							
                        }
					}
					elseif($mainimage==$thumbnail)
					{
						if($mainimage && $mainimage!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
							$mediaGallery->setMediaAttribute($product, 'thumbnail', $baseimage);
                        }
	                    if($small_image && $small_image!="no_selection")
                        {    
                            $smallimage = $mediaGallery->addImage($product, $smallfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'small_image', $smallimage);							
                        }
					}
					elseif($small_image==$thumbnail)
					{
						if($mainimage && $mainimage!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
                        }
	                    if($small_image && $small_image!="no_selection")
                        {    
                            $smallimage = $mediaGallery->addImage($product, $smallfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'small_image', $smallimage);
							$mediaGallery->setMediaAttribute($product, 'thumbnail', $smallimage);
                        }
	                    
					}
					else{
						if($mainimage && $mainimage!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
                        }
	                    if($small_image && $small_image!="no_selection")
	                        {    
	                            $smallimage = $mediaGallery->addImage($product, $smallfinalimage, null, false, false);
	                            $mediaGallery->setMediaAttribute($product, 'small_image', $smallimage);
	                        }
	                    if($thumbnail && $thumbnail!="no_selection")
	                        {
	                    $thumbimage = $mediaGallery->addImage($product, $thumbfinalimage, null, false, false);
	                    $mediaGallery->setMediaAttribute($product, 'thumbnail', $thumbimage);
	                        }
					}
					foreach($otherImages as $otherImage){
						$mediaGallery->addImage($product, $otherImage, null, false, false);
					}
					//custom code end
					//default code
                    /*if($image && $image!="no_selection")
                        {
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
                        }
                    if($small_image && $small_image!="no_selection")
                        {    
                            $smallimage = $mediaGallery->addImage($product, $smallfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'small_image', $smallimage);
                        }
                    if($thumbnail && $thumbnail!="no_selection")
                        {
                    $thumbimage = $mediaGallery->addImage($product, $thumbfinalimage, null, false, false);
                    $mediaGallery->setMediaAttribute($product, 'thumbnail', $thumbimage);
                        }*/
					//default code ends
                    $product->save();
                    $this->_redirect('*/catalog_product/index');
                    Mage::app()->cleanCache();
                
                } 

            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');    

        }

    }


    //custom added for copy product and merge images starts//
    public function copymergeprodimgAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    
                    $selectedproductid = $this->getRequest()->getPost('copymergeprodimg');
                    $selectedproduct = Mage::getModel('catalog/product')->load($selectedproductid);
                    $CheckProduct = $selectedproduct->getId();
                    if(!$CheckProduct)
                    {

                        $this->_getSession()->addError('The Product ID you entered does not exist,please enter a valid product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;

                    }
                    if(!is_numeric($selectedproductid)) 
                    {

                        $this->_getSession()->addError('Please enter a valid product ID.');
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    }

                    $image = $selectedproduct->getData('image');
                    $small_image = $selectedproduct->getData('small_image');
                    $thumbnail = $selectedproduct->getData('thumbnail');
                    $product = Mage::getModel('catalog/product')->load($productId); 
                    $baseurl = "media/catalog/product";
                    $basefinalimage = $baseurl . $image;
                    $smallfinalimage = $baseurl . $small_image;
                    $thumbfinalimage = $baseurl . $thumbnail;
                    $mediaGalleryAttribute = Mage::getModel('catalog/resource_eav_attribute')
                    ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'media_gallery');
                    $mediaGallery = $mediaGalleryAttribute->getBackend();
                    $attrCode = $mediaGalleryAttribute->getAttributeCode();
                    $mediaGalleryData = $product->getData($attrCode);

                    $checkbaseimage = $product->getImage();
                    $checksmallimage = $product->getSmallImage();
                    $checkthumbimage = $product->getThumbnail();
                    
                    if($checkbaseimage == "no_selection" && $image && $image!="no_selection")
                       { 
                            $baseimage = $mediaGallery->addImage($product, $basefinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'image', $baseimage);
                        }
                    if($checksmallimage == "no_selection" && $small_image && $small_image!="no_selection")
                       {
                            $smallimage = $mediaGallery->addImage($product, $smallfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'small_image', $smallimage);
                        }
                    if($checkthumbimage == "no_selection" && $thumbnail && $thumbnail!="no_selection")
                       {    
                            $thumbimage = $mediaGallery->addImage($product, $thumbfinalimage, null, false, false);
                            $mediaGallery->setMediaAttribute($product, 'thumbnail', $thumbimage);
                        }
                    $product->save();
                    $this->_redirect('*/catalog_product/index');
                    Mage::app()->cleanCache();
                
                } 

            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');    

        }

    }


    //custom added for assigning up-sells starts//
    public function assignupprdAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $targetprodid = $this->getRequest()->getPost('assignupprd');
                    $targetproduct = Mage::getModel('catalog/product')->load($targetprodid);
                    $param = array();
                    $upsells = $targetproduct->getUpSellProducts();
                    foreach ($upsells as $item) 
                    {
                        $param[$item->getId()] = array('position' => $item->getPosition());
                    }

                    if (!isset($param[$productId]))
                    { 
                        $param[$productId]= array(
                             'position'=>$productId
                        );
                    }

                    try
                    {         

                        $targetproduct->setUpSellLinkData($param);
                        $targetproduct->save();
                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    } 
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     

        }

    }


    //custom added for assigning cross-sells starts//
    public function assigncrossprdAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $targetprodid = $this->getRequest()->getPost('assigncrossprd');
                    $targetproduct = Mage::getModel('catalog/product')->load($targetprodid);
                    $param = array();
                    $crosssells = $targetproduct->getCrossSellProducts();

                    foreach ($crosssells as $item) 
                    {
                        $param[$item->getId()] = array('position' => $item->getPosition());
                    }

                    if (!isset($param[$productId]))
                    { 
                        $param[$productId]= array(
                             'position'=>$productId
                        );
                    }

                    try
                    {         

                        $targetproduct->setCrossSellLinkData($param);
                        $targetproduct->save();
                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    } 
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     

        }

    }



    //custom added for assigning related starts//
    public function assignrelprdAction()
    {
        
        $this->loadLayout();
        $this->renderLayout();

        $productIds = $this->getRequest()->getParam('product');
        $storeId = Mage::app()->getStore()->getId();

        if(!is_array($productIds)) 
        {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select product(s).'));
        }

        else
        {

            foreach ($productIds as $productId) 
                {

                    $product = Mage::getModel('catalog/product')->load($productId);
                    $targetprodid = $this->getRequest()->getPost('assignrelprd');
                    $targetproduct = Mage::getModel('catalog/product')->load($targetprodid);
                    $param = array();
                    $crosssells = $targetproduct->getRelatedProducts();
                    
                    foreach ($crosssells as $item) 
                    {
                        $param[$item->getId()] = array('position' => $item->getPosition());
                    }

                    if (!isset($param[$productId]))
                    { 
                        $param[$productId]= array(
                             'position'=>$productId
                        );
                    }

                    try
                    {         

                        $targetproduct->setRelatedLinkData($param);
                        $targetproduct->save();
                    }
                    catch(Exception $e)
                    {
                                
                        Mage::getSingleton('core/session')->addError($e->getMessage());
                        $this->_redirect('*/catalog_product/index');
                        return false;
                    } 
                
                }  

            $this->_redirect('*/catalog_product/index');
            Mage::getSingleton('core/session')->addSuccess('The Action have been completed successfully.');   
            Mage::app()->cleanCache();     

        }

    }





}


    

?>